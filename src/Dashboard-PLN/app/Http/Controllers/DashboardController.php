<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pilar;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\Bidang;
use App\Models\AktivitasLog;
use DateTime;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;

        switch ($role) {
            case 'asisten_manager':
                return $this->master($request);
            case 'karyawan':
                return $this->user($request);
            case 'pic_keuangan':
            case 'pic_manajemen_risiko':
            case 'pic_sekretaris_perusahaan':
            case 'pic_perencanaan_operasi':
            case 'pic_pengembangan_bisnis':
            case 'pic_human_capital':
            case 'pic_k3l':
            case 'pic_perencanaan_korporat':
            case 'pic_hukum':
                return $this->admin($request);
            default:
                return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }
    }

public function master(Request $request)
{
    if (Auth::user()->role !== 'asisten_manager') {
        return redirect()->route('dashboard');
    }

    $tahun = (int) $request->input('tahun', date('Y'));
    $bulan = (int) $request->input('bulan', date('m'));

    $pilars = Pilar::with(['indikators' => function ($query) {
        $query->where('aktif', true);
    }])->orderBy('urutan')->get();

    $data = ['nko' => 0, 'pilar' => []];
    $totalNilaiPilar = 0;
    $jumlahPilar = $pilars->count();
    $totalHariDalamBulan = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;

    foreach ($pilars as $pilar) {
        $pilarData = ['nama' => $pilar->nama, 'nilai' => 0, 'indikator' => []];
        $totalNilaiIndikator = 0;
        $jumlahIndikator = count($pilar->indikators);

        foreach ($pilar->indikators as $indikator) {
            $nilaiTotal = 0;

            for ($i = 1; $i <= $totalHariDalamBulan; $i++) {
                $tanggal = \Carbon\Carbon::create($tahun, $bulan, $i)->toDateString();

                $nilaiHariIni = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->whereDate('tanggal', $tanggal)
                    ->where('diverifikasi', true)
                    ->avg('persentase') ?? 0;

                $nilaiTotal += $nilaiHariIni;
            }

            $nilai = $totalHariDalamBulan > 0
                ? round($nilaiTotal / $totalHariDalamBulan, 2)
                : 0;

            $totalNilaiIndikator += $nilai;

            $pilarData['indikator'][] = [
                'nama' => $indikator->nama,
                'nilai' => $nilai
            ];
        }

        $pilarData['nilai'] = $jumlahIndikator > 0
            ? round($totalNilaiIndikator / $jumlahIndikator, 2)
            : 0;

        $totalNilaiPilar += $pilarData['nilai'];
        $data['pilar'][] = $pilarData;
    }

    $data['nko'] = $jumlahPilar > 0
        ? round($totalNilaiPilar / $jumlahPilar, 2)
        : 0;

    // Tambahan data lainnya
    $latestActivities = AktivitasLog::with('user')->latest()->take(10)->get();

    $needVerification = Realisasi::with(['indikator.bidang', 'user'])
        ->where('diverifikasi', false)
        ->latest()
        ->take(5)
        ->get();

    $kpiStats = $this->getKpiStats($tahun, $bulan);

    $poorPerformers = Realisasi::with('indikator.bidang')
        ->where('tahun', $tahun)
        ->where('bulan', $bulan)
        ->where('persentase', '<', 70)
        ->orderBy('persentase')
        ->take(5)
        ->get();

    $prevMonth = $bulan === 1 ? 12 : $bulan - 1;
    $prevYear = $bulan === 1 ? $tahun - 1 : $tahun;

    $comparisonData = $this->getPilarComparisonData($pilars, $tahun, $bulan, $prevYear, $prevMonth);

    return view('dashboard.master', compact(
        'data', 'tahun', 'bulan', 'latestActivities', 'needVerification',
        'kpiStats', 'poorPerformers', 'comparisonData'
    ));
}

    public function admin(Request $request)
    {
        $user = Auth::user();
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('m'));

        $bidang = Bidang::where('role_pic', $user->role)->first();

        if (!$bidang) {
            return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
        }

        $indikators = Indikator::where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->orderBy('kode')
            ->get();

        foreach ($indikators as $indikator) {
            $realisasi = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();

            $indikator->nilai_persentase = $realisasi ? $realisasi->persentase : 0;
            $indikator->nilai_absolut = $realisasi ? $realisasi->nilai : 0;
            $indikator->diverifikasi = $realisasi ? $realisasi->diverifikasi : false;
        }

        $totalNilai = $indikators->sum('nilai_persentase');
        $rataRata = $indikators->count() > 0 ? round($totalNilai / $indikators->count(), 2) : 0;

        $historiData = $this->getHistoriData($bidang->id, $tahun);

$latestActivities = AktivitasLog::with('user')
    ->where('loggable_type', 'App\Models\Realisasi')
    ->latest()
    ->take(10)
    ->get()
    ->filter(function ($log) use ($bidang) {
        return $log->loggable &&
            method_exists($log->loggable, 'indikator') &&
            $log->loggable->indikator &&
            $log->loggable->indikator->bidang_id == $bidang->id;
    });

        $missingInputs = Indikator::where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->whereDoesntHave('realisasis', function ($query) use ($tahun, $bulan) {
                $query->where('tahun', $tahun)->where('bulan', $bulan);
            })
            ->get();

        $bidangComparison = $this->getBidangComparison($bidang->id, $tahun, $bulan);

        return view('dashboard.admin', compact(
            'bidang', 'indikators', 'rataRata', 'historiData',
            'tahun', 'bulan', 'latestActivities', 'missingInputs', 'bidangComparison'
        ));
    }

    public function user(Request $request)
{
    $tahun = $request->input('tahun', now()->year);
    $bulan = $request->input('bulan', now()->month);
    $statusVerifikasi = $request->input('status_verifikasi', 'all');

    $indikatorQuery = Indikator::with([
        'bidang',
        'pilar',
        'realisasis' => function ($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', $bulan);
        }
    ]);

    if ($statusVerifikasi === 'verified') {
        $indikatorQuery->whereHas('realisasis', fn($q) => $q->where('diverifikasi', true));
    } elseif ($statusVerifikasi === 'unverified') {
        $indikatorQuery->whereHas('realisasis', fn($q) => $q->where('diverifikasi', false));
    }

    $indikators = $indikatorQuery->get();

    // Ringkasan
    $totalIndikator = $indikators->count();
    $totalIndikatorTercapai = $indikators->filter(fn($i) => $i->getPersentase($tahun, $bulan) >= 100)->count();
    $persenTercapai = $totalIndikator > 0 ? round(($totalIndikatorTercapai / $totalIndikator) * 100, 2) : 0;
    $nilaiNKO = $indikators->avg(fn($i) => $i->getPersentase($tahun, $bulan));

    // Komposisi Indikator
    $indikatorComposition = [
        'Tercapai' => $totalIndikatorTercapai,
        'Belum Tercapai' => $indikators->filter(fn($i) => $i->getPersentase($tahun, $bulan) > 0 && $i->getPersentase($tahun, $bulan) < 100)->count(),
        'Tanpa Data' => $indikators->filter(fn($i) => $i->getPersentase($tahun, $bulan) == 0)->count(),
    ];

    // Pemetaan status indikator
    $statusMapping = $indikators->map(fn($i) => [
        'kode' => $i->kode,
        'nama' => $i->nama,
        'bidang' => $i->bidang->nama ?? '-',
        'persen' => $i->getPersentase($tahun, $bulan),
    ])->values();

    // Tren historis (bulan lalu sampai bulan ini)
    $historicalTrend = collect(range(1, $bulan))->map(fn($b) => [
        'bulan' => DateTime::createFromFormat('!m', $b)->format('F'),
        'nko' => round($indikators->avg(fn($i) => $i->getPersentase($tahun, $b)), 2),
    ])->toArray();

    // Forecast (bulan setelah ini sampai Desember, dummy prediksi)
    $forecastData = collect(range($bulan + 1, 12))->map(fn($b) => [
        'bulan' => DateTime::createFromFormat('!m', $b)->format('F'),
        'nko' => round(rand(70, 100) + rand(0, 99) / 100, 2), // Dummy prediksi
    ])->toArray();

    // Data per pilar
    $pilarData = $indikators->groupBy(fn($i) => $i->pilar->nama ?? 'Tanpa Pilar')->map(function ($group) use ($tahun, $bulan) {
        $rata = $group->avg(fn($i) => $i->getPersentase($tahun, $bulan));
        return round($rata, 2);
    });

    // Data per bidang
    $bidangData = $indikators->groupBy(fn($i) => $i->bidang->nama ?? 'Tanpa Bidang')->map(function ($group) use ($tahun, $bulan) {
        $rata = $group->avg(fn($i) => $i->getPersentase($tahun, $bulan));
        return round($rata, 2);
    });

    // Tren NKO tahunan
    $trendNKO = collect(range(1, 12))->map(fn($bln) => [
        'bulan' => DateTime::createFromFormat('!m', $bln)->format('F'),
        'nko' => round($indikators->avg(fn($i) => $i->getPersentase($tahun, $bln)), 2),
    ])->toArray();

    // Tertinggi dan terendah
    $analisisData = [
        'tertinggi' => $indikators->sortByDesc(fn($i) => $i->getPersentase($tahun, $bulan))->take(5)->map(fn($i) => [
            'kode' => $i->kode,
            'nama' => $i->nama,
            'bidang' => $i->bidang->nama ?? '-',
            'nilai' => round($i->getPersentase($tahun, $bulan), 2),
        ])->values()->all(),

        'terendah' => $indikators->sortBy(fn($i) => $i->getPersentase($tahun, $bulan))->take(5)->map(fn($i) => [
            'kode' => $i->kode,
            'nama' => $i->nama,
            'bidang' => $i->bidang->nama ?? '-',
            'nilai' => round($i->getPersentase($tahun, $bulan), 2),
        ])->values()->all(),

        'perkembangan' => collect(range(1, 12))->map(function ($bln) use ($indikators, $tahun) {
            $total = $indikators->count();
            $tercapai = $indikators->filter(fn($i) => $i->getPersentase($tahun, $bln) >= 100)->count();
            $persen = $total > 0 ? round($tercapai / $total * 100, 2) : 0;

            return [
                'bulan' => DateTime::createFromFormat('!m', $bln)->format('F'),
                'nko' => round($indikators->avg(fn($i) => $i->getPersentase($tahun, $bln)), 2),
                'tercapai' => $tercapai,
                'total' => $total,
                'persentase' => $persen,
            ];
        })->toArray(),
    ];

    return view('dataKinerja.index', compact(
        'tahun',
        'bulan',
        'statusVerifikasi',
        'totalIndikator',
        'totalIndikatorTercapai',
        'persenTercapai',
        'nilaiNKO',
        'trendNKO',
        'indikatorComposition',
        'statusMapping',
        'historicalTrend',
        'forecastData',
        'pilarData',
        'bidangData',
        'analisisData'
    ));
}

    private function getKpiStats(int $tahun, int $bulan): array
    {
        $totalIndikator = Indikator::where('aktif', true)->count();
        $totalTercapai = Realisasi::where('tahun', $tahun)->where('bulan', $bulan)->where('persentase', '>=', 100)->count();
        $totalBelumTercapai = Realisasi::where('tahun', $tahun)->where('bulan', $bulan)->where('persentase', '<', 100)->count();
        $totalInput = Realisasi::where('tahun', $tahun)->where('bulan', $bulan)->count();
        $totalBelumDiinput = $totalIndikator - $totalInput;

        return compact('totalIndikator', 'totalTercapai', 'totalBelumTercapai', 'totalBelumDiinput');
    }

    private function getPilarComparisonData($pilars, $tahun, $bulan, $prevYear, $prevMonth): array
    {
        return $pilars->map(function ($pilar) use ($tahun, $bulan, $prevYear, $prevMonth) {
            $currentValue = $pilar->getNilai($tahun, $bulan);
            $prevValue = $pilar->getNilai($prevYear, $prevMonth);

            return [
                'name' => $pilar->nama,
                'current' => $currentValue,
                'previous' => $prevValue,
                'change' => $currentValue - $prevValue,
            ];
        })->toArray();
    }

    private function getHistoriData(int $bidangId, int $tahun): array
    {
        $histori = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $indikators = Indikator::where('bidang_id', $bidangId)->where('aktif', true)->get();

            $totalNilai = 0;
            $count = 0;

            foreach ($indikators as $indikator) {
                $realisasi = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();

                if ($realisasi) {
                    $totalNilai += $realisasi->persentase;
                    $count++;
                }
            }

            $histori[] = $count > 0 ? round($totalNilai / $count, 2) : 0;
        }

        return $histori;
    }

    private function getBidangComparison(int $bidangId, int $tahun, int $bulan): array
    {
        $indikators = Indikator::where('bidang_id', $bidangId)->where('aktif', true)->get();
        $comparison = [];

        foreach ($indikators as $indikator) {
            $current = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();

            $previous = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $bulan == 1 ? $tahun - 1 : $tahun)
                ->where('bulan', $bulan == 1 ? 12 : $bulan - 1)
                ->first();

            $comparison[] = [
                'nama' => $indikator->nama,
                'current' => $current ? $current->persentase : 0,
                'previous' => $previous ? $previous->persentase : 0,
            ];
        }

        return $comparison;
    }
}
