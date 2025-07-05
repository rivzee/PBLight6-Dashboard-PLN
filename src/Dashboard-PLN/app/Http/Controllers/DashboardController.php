<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pilar;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\Bidang;
use App\Models\AktivitasLog;
use App\Models\TargetKPI;
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
            case 'pic_spi':
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
        $jumlahPilar = $pilars->count();
        $totalNilaiPilar = 0;

        // Data utama bulan berjalan
        foreach ($pilars as $pilar) {
            $pilarData = ['nama' => $pilar->nama, 'nilai' => 0, 'indikator' => []];
            $totalNilaiIndikator = 0;
            $jumlahIndikator = count($pilar->indikators);

            foreach ($pilar->indikators as $indikator) {
                $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
                    ->whereHas('tahunPenilaian', fn($q) => $q->where('tahun', $tahun))
                    ->first();

                $targetKumulatif = $targetKPI?->target_bulanan[$bulan] ?? 0;

                $realisasiKumulatif = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('diverifikasi', true)
                    ->sum('nilai');

                $persentase = ($targetKumulatif > 0)
                    ? round(($realisasiKumulatif / $targetKumulatif) * 100, 2)
                    : 0;

                $totalNilaiIndikator += $persentase;

                $pilarData['indikator'][] = [
                    'nama' => $indikator->nama,
                    'nilai' => $persentase
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

        // === Trend NKO per bulan dari Januari hingga bulan aktif ===
        $nkoTrend = [];
        for ($i = 1; $i <= 12; $i++) {
            $totalNilaiPilar = 0;
            $jumlahPilar = $pilars->count();

            foreach ($pilars as $pilar) {
                $jumlahIndikator = count($pilar->indikators);
                $totalNilaiIndikator = 0;

                foreach ($pilar->indikators as $indikator) {
                    $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
                        ->whereHas('tahunPenilaian', fn($q) => $q->where('tahun', $tahun))
                        ->first();

                    $target = $targetKPI?->target_bulanan[$i] ?? 0;

                    $realisasi = Realisasi::where('indikator_id', $indikator->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $i)
                        ->where('diverifikasi', true)
                        ->sum('nilai');

                    $persen = ($target > 0) ? round(($realisasi / $target) * 100, 2) : null;
                    $totalNilaiIndikator += $persen ?? 0;
                }

                $rataIndikator = $jumlahIndikator > 0
                    ? round($totalNilaiIndikator / $jumlahIndikator, 2)
                    : null;

                $totalNilaiPilar += $rataIndikator ?? 0;
            }

            $nkoBulan = $jumlahPilar > 0
                ? round($totalNilaiPilar / $jumlahPilar, 2)
                : null;

            $nkoTrend[] = [
                'bulan' => \Carbon\Carbon::create()->month($i)->format('M'),
                'nko' => $nkoBulan
            ];
        }



        // === Data tambahan untuk dashboard ===
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
            'kpiStats', 'poorPerformers', 'comparisonData', 'nkoTrend'
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

                $tanggalHariIni = now()->toDateString();

        $missingInputs = Indikator::where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->whereDoesntHave('realisasis', function ($query) use ($tahun, $bulan, $tanggalHariIni) {
                $query->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->whereDate('tanggal', $tanggalHariIni);
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
            'realisasis' => fn($q) => $q->where('tahun', $tahun)->where('bulan', '<=', 12),
            'targetKPI' => fn($q) => $q
                ->whereHas('tahunPenilaian', fn($q2) => $q2->where('tahun', $tahun))
                ->where('disetujui', true),
        ]);

        if ($statusVerifikasi === 'verified') {
            $indikatorQuery->whereHas('realisasis', fn($q) => $q->where('diverifikasi', true));
        } elseif ($statusVerifikasi === 'unverified') {
            $indikatorQuery->whereHas('realisasis', fn($q) => $q->where('diverifikasi', false));
        }

        $indikators = $indikatorQuery->get();

        // Hitung persentase bulan aktif
        foreach ($indikators as $indikator) {
            $targetKPI = $indikator->targetKPI->first();
            $targetBulan = $targetKPI?->target_bulanan[$bulan] ?? 0;

            $realisasi = $indikator->realisasis
                ->where('bulan', $bulan)
                ->where('diverifikasi', true)
                ->sum('nilai');

            $persentase = ($targetBulan > 0 && $realisasi > 0)
                ? min(($realisasi / $targetBulan) * 100, 110)
                : 0;

            $indikator->persentase = round($persentase, 2);
            $indikator->target_tahunan = $targetKPI?->target_tahunan ?? 0;
        }

        // Hitung NKO Score utama
        $pilarGroups = $indikators->groupBy(fn($i) => $i->pilar->nama ?? 'Tanpa Pilar');
        $nilaiNKO = $pilarGroups->count() > 0
            ? round($pilarGroups->map(fn($group) => $group->avg('persentase'))->avg(), 2)
            : 0;

        $totalIndikator = $indikators->count();
        $totalIndikatorTercapai = $indikators->filter(fn($i) => $i->persentase >= 80)->count();

        $persenTercapai = $totalIndikator > 0
            ? round(($totalIndikatorTercapai / $totalIndikator) * 100, 2)
            : 0;

        $indikatorComposition = [
            'Tercapai' => $totalIndikatorTercapai,
            'Belum Tercapai' => $indikators->filter(fn($i) => $i->persentase > 0 && $i->persentase < 100)->count(),
            'Tanpa Data' => $indikators->filter(fn($i) => $i->persentase == 0)->count(),
        ];

        $statusMapping = $indikators->map(fn($i) => [
            'kode' => $i->kode,
            'nama' => $i->nama,
            'bidang' => $i->bidang->nama ?? '-',
            'persen' => $i->persentase,
        ])->values();

        // === Trend Historis NKO (Perbulan) ===
        $trendNKO = collect();
        foreach (range(1, 12) as $b) {
            // Hitung persentase masing-masing indikator pada bulan ke-b
            foreach ($indikators as $indikator) {
                $target = $indikator->targetKPI->first()?->target_bulanan[$b] ?? 0;
                $real = $indikator->realisasis
                    ->where('bulan', $b)
                    ->where('diverifikasi', true)
                    ->sum('nilai');

                $persen = ($target > 0 && $real > 0)
                    ? min(($real / $target) * 100, 110)
                    : 0;

                $indikator->{"persen_bulan_$b"} = $persen;
            }

            // Hitung rata-rata per pilar untuk bulan ke-b
            $pilarBulanan = $indikators->groupBy(fn($i) => $i->pilar->nama ?? 'Tanpa Pilar')
                ->map(fn($group) => $group->avg("persen_bulan_$b"));

            $nkoBulan = $pilarBulanan->avg();

            $trendNKO->push([
                'bulan' => DateTime::createFromFormat('!m', $b)->format('F') . ' ' . $tahun,
                'nko' => round($nkoBulan ?? 0, 2),
            ]);
        }

        $historicalTrend = $trendNKO;

        // === Forecast (Prediksi 10 Bulan ke Depan) ===
        $X = [];
        $Y = [];
        $counter = 1;
        $lastMonthIndex = 0;

        foreach ($trendNKO as $index => $item) {
            if ($item['nko'] > 0) {
                $X[] = $counter;
                $Y[] = $item['nko'];
                $lastMonthIndex = $index + 1;
                $counter++;
            }
        }

        $n = count($X);
        $sumX = array_sum($X);
        $sumY = array_sum($Y);
        $sumXY = array_sum(array_map(fn($x, $y) => $x * $y, $X, $Y));
        $sumX2 = array_sum(array_map(fn($x) => $x * $x, $X));

        $denom = ($n * $sumX2 - $sumX ** 2) ?: 1;
        $a = ($n * $sumXY - $sumX * $sumY) / $denom;
        $b = ($sumY * $sumX2 - $sumX * $sumXY) / $denom;

        $forecastData = collect();
        $totalForecast = 10;
        $currentMonth = $lastMonthIndex;
        $currentYear = $tahun;

        for ($i = 1; $i <= $totalForecast; $i++) {
            $x = $n + $i;
            $forecast = $a * $x + $b;

            $forecastMonth = ($currentMonth + $i) % 12;
            $forecastMonth = $forecastMonth === 0 ? 12 : $forecastMonth;
            $forecastYear = $currentYear + floor(($currentMonth + $i - 1) / 12);

            $forecastData->push([
                'bulan' => DateTime::createFromFormat('!m', $forecastMonth)->format('F') . ' ' . $forecastYear,
                'nko' => round($forecast, 2),
            ]);
        }

        $pilarData = $pilarGroups->map(fn($group) => round($group->avg('persentase'), 2));

        $bidangData = $indikators->groupBy(fn($i) => $i->bidang->nama ?? 'Tanpa Bidang')
            ->map(fn($group) => round($group->avg('persentase'), 2));

        $analisisData = [
            'tertinggi' => $indikators->sortByDesc('persentase')->take(5)->map(fn($i) => [
                'kode' => $i->kode,
                'nama' => $i->nama,
                'bidang' => $i->bidang->nama ?? '-',
                'nilai' => $i->persentase,
            ])->values()->all(),

            'terendah' => $indikators->sortBy('persentase')->take(5)->map(fn($i) => [
                'kode' => $i->kode,
                'nama' => $i->nama,
                'bidang' => $i->bidang->nama ?? '-',
                'nilai' => $i->persentase,
            ])->values()->all(),

            'perkembangan' => $trendNKO->map(function ($data, $index) use ($indikators) {
                $b = $index + 1;
                $total = $indikators->count();
                $tercapai = $indikators->filter(function ($i) use ($b) {
                    $target = $i->targetKPI->first()?->target_bulanan[$b] ?? 0;
                    $real = $i->realisasis
                        ->where('bulan', $b)
                        ->where('diverifikasi', true)
                        ->sum('nilai');
                    $persen = ($target > 0 && $real > 0)
                        ? min(($real / $target) * 100, 110)
                        : 0;
                    return $persen >= 100;
                })->count();

                return [
                    'bulan' => $data['bulan'],
                    'nko' => $data['nko'],
                    'tercapai' => $tercapai,
                    'total' => $total,
                    'persentase' => $total > 0 ? round($tercapai / $total * 100, 2) : 0,
                ];
            }),
        ];

        $pilars = $pilarGroups->map(function ($indikatorList, $pilarNama) {
            $first = $indikatorList->first();
            return (object) [
                'id' => $first->pilar->id ?? 0,
                'kode' => $first->pilar->kode ?? '-',
                'nama' => $pilarNama,
                'deskripsi' => $first->pilar->deskripsi ?? null,
                'nilai' => round($indikatorList->avg('persentase'), 2),
                'indikators_count' => $indikatorList->count(),
                'indikators_tercapai' => $indikatorList->filter(fn($i) => $i->persentase >= 100)->count(),
            ];
        })->values();

        $target = $indikators->first()?->targetKPI->first()?->target_tahunan ?? 0;

        return view('dataKinerja.index', compact(
            'tahun',
            'bulan',
            'statusVerifikasi',
            'totalIndikator',
            'totalIndikatorTercapai',
            'persenTercapai',
            'nilaiNKO',
            'trendNKO',
            'forecastData',
            'indikatorComposition',
            'statusMapping',
            'historicalTrend',
            'pilarData',
            'bidangData',
            'analisisData',
            'pilars',
            'target'
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
                ->where('diverifikasi', true) // tambahkan ini agar konsisten
                ->first();

            if ($realisasi) {
                $totalNilai += $realisasi->persentase;
                $count++;
            }
        }

        $histori[] = [
            'bulan' => DateTime::createFromFormat('!m', $bulan)->format('F'),
            'nilai' => $count > 0 ? round($totalNilai / $count, 2) : 0
        ];
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
