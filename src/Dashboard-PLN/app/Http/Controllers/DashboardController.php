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

        foreach ($pilars as $pilar) {
            $pilarData = ['nama' => $pilar->nama, 'nilai' => 0, 'indikator' => []];
            $totalNilaiIndikator = 0;
            $jumlahIndikatorDenganData = 0;
            $totalSemuaIndikator = $pilar->indikators->count();

            // Hitung SEMUA indikator dalam pilar (termasuk yang belum ada input = 0)
            foreach ($pilar->indikators as $indikator) {
                $targetKPI = $indikator->targetKPI
                    ->where('tahunPenilaian.tahun', $tahun)
                    ->first();

                $target = 0;
                if ($targetKPI && is_array($targetKPI->target_bulanan) && isset($targetKPI->target_bulanan[$bulan - 1])) {
                    $target = $targetKPI->target_bulanan[$bulan - 1];
                }

                $realisasi = $indikator->realisasis
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('diverifikasi', true)
                    ->sum('nilai');

                $persen = 0;
                if ($target > 0 && $realisasi > 0) {
                    $persen = min(($realisasi / $target) * 100, 110);
                    $jumlahIndikatorDenganData++;
                }

                $totalNilaiIndikator += $persen;

                $pilarData['indikator'][] = [
                    'nama' => $indikator->nama,
                    'nilai' => $persen
                ];
            }

            // Nilai pilar = rata-rata dari SEMUA indikator (termasuk yang belum input = 0)
            $pilarData['nilai'] = $totalSemuaIndikator > 0
                ? round($totalNilaiIndikator / $totalSemuaIndikator, 2)
                : 0;

            // Tambahkan informasi tambahan untuk debugging
            $pilarData['jumlah_input'] = $jumlahIndikatorDenganData;
            $pilarData['total_indikator'] = $totalSemuaIndikator;

            $data['pilar'][] = $pilarData;
            $totalNilaiPilar += $pilarData['nilai'];
        }

        // Hitung NKO berdasarkan rata-rata semua pilar
        $totalNilaiAkhir = 0;
        $totalBobot = 0;

        foreach ($pilars as $pilar) {
            foreach ($pilar->indikators as $indikator) {
                $realisasiAkhir = $indikator->realisasis
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('diverifikasi', true)
                    ->sum('nilai_akhir');

                $bobotIndikator = $indikator->bobot ?? 0;

                $totalNilaiAkhir += $realisasiAkhir;
                $totalBobot += $bobotIndikator;
            }
        }

        $data['nko'] = $totalBobot > 0
            ? min(round(($totalNilaiAkhir / $totalBobot) * 100, 2), 110)
            : 0;


                // Tentukan status NKO berdasarkan nilai
                if ($data['nko'] >= 100) {
                    $data['nko_status'] = 'Tercapai';
                    $data['nko_color'] = 'success';
                } elseif ($data['nko'] >= 95) {
                    $data['nko_status'] = 'Hampir Tercapai';
                    $data['nko_color'] = 'warning';
                } else {
                    $data['nko_status'] = 'Perlu Peningkatan';
                    $data['nko_color'] = 'danger';
                }

                // === Trend NKO per bulan dari Januari hingga bulan aktif ===
                $nkoTrend = [];

        for ($i = 1; $i <= 12; $i++) {
            $totalNilaiAkhirBulan = 0;
            $totalBobotBulan = 0;

            foreach ($pilars as $pilar) {
                foreach ($pilar->indikators as $indikator) {
                    $nilaiAkhir = $indikator->realisasis
                        ->where('tahun', $tahun)
                        ->where('bulan', $i)
                        ->where('diverifikasi', true)
                        ->sum('nilai_akhir');

                    $bobot = $indikator->bobot ?? 0;

                    $totalNilaiAkhirBulan += $nilaiAkhir;
                    $totalBobotBulan += $bobot;
                }
            }

            $nkoBulan = $totalBobotBulan > 0
                ? min(round(($totalNilaiAkhirBulan / $totalBobotBulan) * 100, 2), 110)
                : 0;

            $nkoTrend[] = [
                'bulan' => \Carbon\Carbon::create()->month($i)->format('M'),
                'nko' => $nkoBulan,
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
            ->where('persentase', '>=', 100)
            ->orderBy('persentase')
            ->take(10)
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

        $indikators = Indikator::with('targetKPI')
            ->where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->orderBy('kode')
            ->get();

        foreach ($indikators as $indikator) {
            // Ambil realisasi bulan berjalan
            $realisasi = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();

            $indikator->nilai_persentase = $realisasi?->persentase ?? 0;
            $indikator->nilai_absolut = $realisasi?->nilai ?? 0;
            $indikator->diverifikasi = $realisasi?->diverifikasi ?? false;

            // Ambil target KPI untuk indikator ini (TANPA cek tahun_penilaian, ambil langsung terbaru)
            $targetKPI = $indikator->targetKPI->sortByDesc('created_at')->first();

            // Ambil target bulanan dengan logika: bulan ke-(n-1)
            $target_bulanan = 0;
            if ($targetKPI && is_array($targetKPI->target_bulanan)) {
                $target_bulanan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
            }

            // Simpan ke properti indikator agar bisa ditampilkan di blade
            $indikator->target_nilai = $target_bulanan;
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
            'realisasis' => fn($q) => $q->where('tahun', $tahun)->where('bulan', $bulan)->where('diverifikasi', true),
            'targetKPI' => fn($q) => $q
                ->whereHas('tahunPenilaian', fn($q2) => $q2->where('tahun', $tahun))
                ->where('disetujui', true),
        ])->where('aktif', true);

        if ($statusVerifikasi === 'verified') {
            $indikatorQuery->whereHas('realisasis', fn($q) => $q->where('diverifikasi', true));
        } elseif ($statusVerifikasi === 'unverified') {
            $indikatorQuery->whereHas('realisasis', fn($q) => $q->where('diverifikasi', false));
        }

        $indikators = $indikatorQuery->get();

        // Hitung persentase bulan aktif
        foreach ($indikators as $indikator) {
            $targetKPI = $indikator->targetKPI->first();
            $targetBulan = ($targetKPI && is_array($targetKPI->target_bulanan))
                ? $targetKPI->target_bulanan[$bulan - 1] ?? 0
                : 0;

            $realisasiAkhir = $indikator->realisasis->sum('nilai');

            $persentase = ($targetBulan > 0)
                ? min(($realisasiAkhir / $targetBulan) * 100, 110)
                : 0;

            $indikator->persentase = round($persentase, 2);
            $indikator->target_tahunan = $targetKPI?->target_tahunan ?? 0;
            $indikator->target_bulanan = $targetBulan;
            $indikator->realisasi_bulanan = $realisasiAkhir;
        }

        // Hitung NKO Score utama dengan nilai_akhir
        $pilars = Pilar::with(['indikators' => fn($q) => $q->where('aktif', true)])->get();

        $totalNilaiAkhir = 0;
        $totalBobot = 0;

        foreach ($pilars as $pilar) {
            foreach ($pilar->indikators as $indikator) {
                $realisasiAkhir = $indikator->realisasis
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('diverifikasi', true)
                    ->sum('nilai_akhir');

                $bobotIndikator = $indikator->bobot ?? 0;

                $totalNilaiAkhir += $realisasiAkhir;
                $totalBobot += $bobotIndikator;
            }
        }

        $nilaiNKO = $totalBobot > 0
            ? round(($totalNilaiAkhir / $totalBobot) * 100, 2)
            : 0;


        $totalIndikator = $indikators->count();
        $totalIndikatorTercapai = $indikators->filter(fn($i) => $i->persentase >= 95)->count();
        $persenTercapai = $totalIndikator > 0
            ? round(($totalIndikatorTercapai / $totalIndikator) * 100, 2)
            : 0;

        // Trend Historis NKO (Perbulan) dengan nilai_akhir
        $trendNKO = collect();
        foreach (range(1, 12) as $b) {
            $totalNilaiBulanAkhir = 0;
            $totalBobotBulan = 0;

            foreach ($pilars as $pilar) {
                foreach ($pilar->indikators as $indikator) {
                    $realAkhir = $indikator->realisasis
                        ->where('tahun', $tahun)
                        ->where('bulan', $b)
                        ->where('diverifikasi', true)
                        ->sum('nilai_akhir');

                    $bobotIndikator = $indikator->bobot ?? 0;

                    $totalNilaiBulanAkhir += $realAkhir;
                    $totalBobotBulan += $bobotIndikator;
                }
            }

            $nkoBulan = $totalBobotBulan > 0
                ? round($totalNilaiBulanAkhir / $totalBobotBulan * 100, 2)
                : 0;

            $trendNKO->push([
                'bulan' => DateTime::createFromFormat('!m', $b)->format('F') . ' ' . $tahun,
                'nko' => $nkoBulan,
            ]);
        }
        $historicalTrend = $trendNKO;
        // Hitung nilai pilar (rata-rata persentase indikator aktif di pilar)
        foreach ($pilars as $pilar) {
            $persenList = [];
            foreach ($pilar->indikators as $indikator) {
                // Ambil target KPI dan target bulanan
                $targetKPI = $indikator->targetKPI->first();
                $targetBulan = ($targetKPI && is_array($targetKPI->target_bulanan))
                    ? $targetKPI->target_bulanan[$bulan - 1] ?? 0
                    : 0;

                // Ambil realisasi nilai (bukan nilai_akhir)
                $realisasi = $indikator->realisasis
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('diverifikasi', true)
                    ->sum('nilai');

                // Hitung persentase, jika target bulanan 0 maka persentase = 0
                $persentase = ($targetBulan > 0)
                    ? min(($realisasi / $targetBulan) * 100, 110)
                    : 0;

                // Jika belum ada realisasi, persentase tetap 0
                $persenList[] = round($persentase, 2);
            }
            // Set nilai_perhitungan sebagai rata-rata persentase indikator di pilar (termasuk yang belum diinput)
            $pilar->nilai_perhitungan = count($persenList) > 0 ? round(array_sum($persenList) / count($persenList), 2) : 0;
        }

        // Data pilar untuk chart
        $pilarData = $pilars->mapWithKeys(function ($pilar) {
            return [$pilar->nama => $pilar->nilai_perhitungan ?? 0];
        });



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

        'perkembangan' => $trendNKO->map(function ($data, $index) use ($pilars, $tahun) {
            $b = $index + 1;
            $indikatorsBulan = collect($pilars)->flatMap->indikators->filter(function($indikator) use ($tahun, $b) {
                $targetKPI = $indikator->targetKPI->first();
                $targetBulan = ($targetKPI && is_array($targetKPI->target_bulanan))
                    ? $targetKPI->target_bulanan[$b - 1] ?? 0
                    : 0;
                $realisasi = $indikator->realisasis
                    ->where('tahun', $tahun)
                    ->where('bulan', $b)
                    ->where('diverifikasi', true)
                    ->sum('nilai');
                $persentase = ($targetBulan > 0)
                    ? min(($realisasi / $targetBulan) * 100, 110)
                    : 0;
                $indikator->persentase_bulan = round($persentase, 2);
                return true;
            });

                $totalIndikator = $indikatorsBulan->count();
                $totalIndikatorTercapai = $indikatorsBulan->filter(fn($i) => $i->persentase_bulan >= 95)->count();
                $persenTercapai = $totalIndikator > 0
                    ? round(($totalIndikatorTercapai / $totalIndikator) * 100, 2)
                    : 0;

                $nkoBulan = $data['nko'];

                return [
                    'bulan' => $data['bulan'],
                    'nko' => $nkoBulan,
                    'tercapai' => $totalIndikatorTercapai,
                    'total' => $totalIndikator,
                    'persentase' => $persenTercapai,
                ];
            }),
        ];

        $pilars = $pilars->map(function ($pilar) {
            return (object) [
                'id' => $pilar->id,
                'kode' => $pilar->kode,
                'nama' => $pilar->nama,
                'deskripsi' => $pilar->deskripsi,
                'nilai' => $pilar->nilai_perhitungan ?? 0,
                'indikators_count' => $pilar->indikators->count(),
                'indikators_tercapai' => $pilar->indikators->filter(function($i) {
                    return isset($i->persentase) && $i->persentase >= 100;
                })->count(),
            ];
        });

        // $target = $indikators->first()?->targetKPI->first()?->target_bulanan[$bulan - 1] ?? 0;
        $target = max(100,0);

        return view('dataKinerja.index', compact(
            'tahun',
            'bulan',
            'statusVerifikasi',
            'totalIndikator',
            'totalIndikatorTercapai',
            'persenTercapai',
            'nilaiNKO',
            'trendNKO',
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
