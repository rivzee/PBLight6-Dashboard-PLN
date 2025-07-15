<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Bidang;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\TargetKPI;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DataKinerjaController extends Controller
{
    /**
     * Memastikan user telah login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan dashboard analitik utama
     */
    public function index(Request $request)
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

        Log::info("DEBUG NKO FINAL - Total Nilai Akhir: {$totalNilaiAkhir}, Total Bobot: {$totalBobot}, NKO: {$nilaiNKO}");

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
    /**
     * Menampilkan data kinerja per pilar
     */
    public function pilar(Request $request, $id = null)
    {
        $tahun = $request->tahun ?? now()->year;
            $bulan = $request->bulan ?? now()->month;

            if ($id) {
                // Ambil pilar & indikator aktif
                $pilar = Pilar::with(['indikators' => function ($q) {
                    $q->where('aktif', true)->with('bidang');
                }])->findOrFail($id);

                $indikators = $pilar->indikators;

                $persenList = [];
                foreach ($indikators as $indikator) {
                    // Ambil target KPI untuk tahun ini
                    $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
                        ->where('disetujui', true)
                        ->whereHas('tahunPenilaian', fn ($q) => $q->where('tahun', $tahun))
                        ->first();

                    // Ambil realisasi untuk bulan tertentu
                    $realisasi = Realisasi::where('indikator_id', $indikator->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('diverifikasi', true)
                        ->first();

                    // Pastikan target_bulanan adalah array dan ada data untuk bulan ini
                    if ($targetKPI && isset($targetKPI->target_bulanan) && is_array($targetKPI->target_bulanan)) {
                        $targetBulan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
                    } else {
                        $targetBulan = 0;
                    }

                    $targetTahunan = $targetKPI?->target_tahunan ?? 0;
                    $nilai = $realisasi?->nilai ?? 0;

                    // Hitung persentase berdasarkan target bulanan untuk bulan tertentu
                    $persentase = ($targetBulan > 0)
                        ? min(($nilai / $targetBulan) * 100, 110) // Maksimal 110%
                        : 0;

                    $indikator->target_tahunan = $targetTahunan;
                    $indikator->target_bulanan = $targetBulan;
                    $indikator->nilai_aktual = $nilai;
                    $indikator->persentase = round($persentase, 2);

                    // Masukkan ke array, walaupun belum diinput (akan 0)
                    $persenList[] = $indikator->persentase;
                }

                // Hitung nilai pilar sebagai rata-rata persentase semua indikator aktif (termasuk yang belum diinput)
                $pilar->nilai = count($persenList) > 0 ? round(array_sum($persenList) / count($persenList), 2) : 0;
                $pilar->indikators_count = $indikators->count();
                $pilar->indikators_tercapai = collect($persenList)->filter(fn($p) => $p >= 90)->count();

                // Data untuk grafik
                $indikatorChartData = $indikators->map(fn ($i) => [
                    'kode' => $i->kode,
                    'nama' => $i->nama,
                    'persentase' => $i->persentase,
                ]);

                $trendPilar = $this->getTrendPilar($id, $tahun);
                $trendBulanan = collect($trendPilar)->map(fn ($t) => [
                    'bulan' => $t['bulan'],
                    'nilai' => $t['nilai'],
                ]);

                return view('dataKinerja.pilar_detail', compact(
                    'pilar', 'tahun', 'bulan', 'indikators', 'indikatorChartData', 'trendPilar', 'trendBulanan'
                ));
            }
                else {
            // INDEX
            $pilars = Pilar::with(['indikators' => function ($q) {
                $q->where('aktif', true)->with('bidang');
            }])->orderBy('urutan')->get();

            foreach ($pilars as $pilar) {
                $indikators = $pilar->indikators;
                $persenList = [];
                foreach ($indikators as $indikator) {
                    $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
                        ->where('disetujui', true)
                        ->whereHas('tahunPenilaian', fn ($q) => $q->where('tahun', $tahun))
                        ->first();

                    $realisasi = Realisasi::where('indikator_id', $indikator->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('diverifikasi', true)
                        ->first();

                    if ($targetKPI && isset($targetKPI->target_bulanan) && is_array($targetKPI->target_bulanan)) {
                        $targetBulan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
                    } else {
                        $targetBulan = 0;
                    }

                    $targetTahunan = $targetKPI?->target_tahunan ?? 0;
                    $nilai = $realisasi?->nilai ?? 0;

                    $persentase = ($targetBulan > 0)
                        ? min(($nilai / $targetBulan) * 100, 110) // Konsisten dengan detail
                        : 0;

                    $indikator->target_tahunan = $targetTahunan;
                    $indikator->target_bulanan = $targetBulan;
                    $indikator->nilai_aktual = $nilai;
                    $indikator->persentase = round($persentase, 2);

                    $persenList[] = $indikator->persentase;
                }

                $pilar->nilai = count($persenList) > 0 ? round(array_sum($persenList) / count($persenList), 2) : 0;
                $pilar->indikators_count = $indikators->count();
                $pilar->indikators_tercapai = collect($persenList)->filter(fn($p) => $p >= 90)->count();
            }

            $pilarChartData = $pilars->map(fn($pilar) => [
                'kode' => $pilar->kode,
                'nama' => $pilar->nama,
                'nilai' => $pilar->nilai
            ]);

            // Indikator utama tetap seperti sebelumnya
            $indikatorUtama = Indikator::with(['pilar', 'bidang'])
                ->where('aktif', true)
                ->orderBy('is_utama', 'desc')
                ->orderBy('prioritas', 'desc')
                ->take(5)
                ->get();

            return view('dataKinerja.pilar_index', compact(
                'pilars', 'tahun', 'bulan', 'indikatorUtama', 'pilarChartData'
            ));
        }
    }


    /**
     * Menampilkan data kinerja per bidang
     */
    public function bidang(Request $request, $id = null)
    {
        $tahun = $request->tahun ?? Carbon::now()->year;
        $bulan = $request->bulan ?? Carbon::now()->month;

        if ($id) {
            // Jika ID bidang disebutkan, tampilkan detail bidang
            $bidang = Bidang::with(['indikators' => function($query) {
                $query->where('aktif', true)->with('pilar');
            }])->findOrFail($id);

            // Data nilai indikator dalam bidang
            foreach ($bidang->indikators as $indikator) {
                $nilai = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', 'bulanan')
                    ->first();

                $indikator->nilai = $nilai ? $nilai->persentase : 0;
            }

            // Data tren bidang bulanan
            $trendBidang = $this->getTrendBidang($id, $tahun);

            return view('dataKinerja.bidang_detail', compact('bidang', 'tahun', 'bulan', 'trendBidang'));
        } else {
            // Jika tidak, tampilkan daftar semua bidang
            $bidangs = Bidang::all();

            foreach ($bidangs as $bidang) {
                $bidang->nilai = $bidang->getNilaiRata($tahun, $bulan);
            }

            return view('dataKinerja.bidang_index', compact('bidangs', 'tahun', 'bulan'));
        }
    }

    /**
     * Menampilkan data kinerja per indikator
     */
    public function indikator(Request $request, $id)
    {
        $indikator = Indikator::with(['pilar', 'bidang', 'targetKPI.tahunPenilaian'])->findOrFail($id);
        $tahun = $request->tahun ?? Carbon::now()->year;

        // Ambil data realisasi bulanan
        $realisasiBulanan = Realisasi::where('indikator_id', $id)
            ->where('tahun', $tahun)
            ->orderBy('bulan')
            ->get();

        // Ambil target KPI yang disetujui
        $targetKPI = TargetKPI::where('indikator_id', $id)
            ->where('disetujui', true)
            ->whereHas('tahunPenilaian', fn($q) => $q->where('tahun', $tahun))
            ->first();

        $targetTahunan = $targetKPI ? $targetKPI->target_tahunan : 0;

        // Siapkan data realisasi + hitung persentase
        $realisasi = collect();

        for ($i = 1; $i <= 12; $i++) {
            $real = $realisasiBulanan->firstWhere('bulan', $i);
            $nilai = $real ? $real->nilai : 0;

            // Pastikan target_bulanan adalah array dan ada data untuk bulan ini
            if ($targetKPI && isset($targetKPI->target_bulanan) && is_array($targetKPI->target_bulanan)) {
                $targetBulan = $targetKPI->target_bulanan[$i - 1] ?? 0;
            } else {
                $targetBulan = 0;
            }

            // Hitung persentase dengan batas maksimal 110% sesuai ketentuan
            $persentase = ($targetBulan > 0)
                ? min(($nilai / $targetBulan) * 100, 110) // Maksimal 110%
                : 0;

            $realisasi->push((object)[
                'bulan' => $i,
                'nilai' => $nilai,
                'target_bulanan' => $targetBulan,
                'persentase' => $persentase
            ]);
        }

        // Siapkan data untuk chart (gunakan persentase untuk tren visual)
        $chartData = $realisasi->map(function ($item) {
            return [
                'bulan' => Carbon::create(null, $item->bulan, 1)->locale('id')->monthName,
                'nilai' => $item->persentase
            ];
        });

        // Ambil realisasi terbaru yang ada nilainya
        $realisasiTerakhir = $realisasi->sortByDesc('bulan')->firstWhere('nilai', '>', 0);

        // Tentukan status berdasarkan persentase terbaru
        $statusText = '-';
        $statusClass = 'info-card';

        if ($realisasiTerakhir) {
            $persen = $realisasiTerakhir->persentase;

            if ($persen >= 90) {
                $statusText = 'Tercapai';
                $statusClass = 'info-card success';
            } elseif ($persen >= 70) {
                $statusText = 'Perlu Perhatian';
                $statusClass = 'info-card warning';
            } else {
                $statusText = 'Tidak Tercapai';
                $statusClass = 'info-card danger';
            }
        }

        // Set nilai target ke dalam objek indikator agar bisa ditampilkan di Blade
        $indikator->target = $targetTahunan;

        return view('dataKinerja.indikator', compact(
            'indikator',
            'tahun',
            'realisasi',
            'chartData',
            'statusText',
            'statusClass',
            'realisasiTerakhir'
        ));
    }

    /**
     * Menampilkan analisis perbandingan
     */
    public function perbandingan(Request $request)
    {
        $tahun1 = $request->tahun1 ?? Carbon::now()->year;
        $tahun2 = $request->tahun2 ?? Carbon::now()->subYear()->year;

        // Bandingkan nilai NKO
        $nko1 = $this->hitungNKO($tahun1, null);
        $nko2 = $this->hitungNKO($tahun2, null);
        $perbedaanNKO = $nko1 - $nko2;

        // Bandingkan nilai pilar
        $pilars = Pilar::orderBy('urutan')->get();
        $perbandinganPilar = [];

        foreach ($pilars as $pilar) {
            $nilai1 = 0;
            $nilai2 = 0;

            for ($i = 1; $i <= 12; $i++) {
                $nilai1 += $pilar->getNilai($tahun1, $i);
                $nilai2 += $pilar->getNilai($tahun2, $i);
            }

            $nilai1 = $nilai1 / 12; // Rata-rata bulanan
            $nilai2 = $nilai2 / 12;

            $perbandinganPilar[] = [
                'pilar' => $pilar,
                'nilai1' => round($nilai1, 2),
                'nilai2' => round($nilai2, 2),
                'perbedaan' => round($nilai1 - $nilai2, 2),
            ];
        }

        // Perbandingan bidang
        $bidangs = Bidang::all();
        $perbandinganBidang = [];

        foreach ($bidangs as $bidang) {
            $nilai1 = 0;
            $nilai2 = 0;

            for ($i = 1; $i <= 12; $i++) {
                $nilai1 += $bidang->getNilaiRata($tahun1, $i);
                $nilai2 += $bidang->getNilaiRata($tahun2, $i);
            }

            $nilai1 = $nilai1 / 12; // Rata-rata bulanan
            $nilai2 = $nilai2 / 12;

            $perbandinganBidang[] = [
                'bidang' => $bidang,
                'nilai1' => round($nilai1, 2),
                'nilai2' => round($nilai2, 2),
                'perbedaan' => round($nilai1 - $nilai2, 2),
            ];
        }

        return view('dataKinerja.perbandingan', compact(
            'tahun1',
            'tahun2',
            'nko1',
            'nko2',
            'perbedaanNKO',
            'perbandinganPilar',
            'perbandinganBidang'
        ));
    }

    /**
     * Mendapatkan jumlah indikator yang tercapai (>=90%)
     */
    private function getIndikatorTercapai($tahun)
    {
        $count = 0;
        $indikators = Indikator::where('aktif', true)->get();
        $bulan = Carbon::now()->month;

        foreach ($indikators as $indikator) {
            $nilai = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', 'bulanan')
                ->first();

            if ($nilai && $nilai->persentase >= 90) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Menghitung NKO (Nilai Kinerja Organisasi)
     * Formula: (Σ realisasi.nilai / Σ indikator.bobot) × 100
     */
private function hitungNKO($tahun, $bulan = null)
{
    $bulan = $bulan ?? Carbon::now()->month;
    $pilars = Pilar::with(['indikators' => fn($q) => $q->where('aktif', true)])->get();

    $totalNilai = 0;
    $totalBobot = 0;

    foreach ($pilars as $pilar) {
        foreach ($pilar->indikators as $indikator) {
            $realisasi = $indikator->realisasis
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('diverifikasi', true)
                ->sum('nilai');

            $bobotIndikator = $indikator->bobot ?? 0;

            $totalNilai += $realisasi;
            $totalBobot += $bobotIndikator;
        }
    }

    $nko = $totalBobot > 0
        ? round(($totalNilai / $totalBobot) * 100, 2)
        : 0;

    return $nko;
}

    /**
     * Mendapatkan tren NKO bulanan dengan formula baru
     */
    private function getTrendNKO($tahun)
    {
        $result = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // Dapatkan bulan saat ini
        $bulanSekarang = Carbon::now()->month;

        // Cek apakah ada data realisasi untuk tahun ini
        $adaDataRealisasi = Realisasi::where('tahun', $tahun)->exists();

        // Log untuk debugging
        Log::info("Generating NKO Trend for year: {$tahun}");
        Log::info("Data realisasi exists: " . ($adaDataRealisasi ? 'Yes' : 'No'));

        // Jika tidak ada data realisasi sama sekali, buat data dummy untuk visualisasi
        if (!$adaDataRealisasi) {
            Log::info("No realization data found, creating dummy data for visualization");

            // Buat data dummy dengan tren naik untuk visualisasi
            $baseValue = 60; // Nilai awal

            for ($i = 1; $i <= 12; $i++) {
                // Buat tren yang naik secara bertahap
                $dummyValue = $baseValue + ($i * 1.5);
                // Tambahkan sedikit variasi
                $dummyValue += rand(-3, 3);
                // Pastikan nilai tetap dalam range yang valid
                $dummyValue = max(0, min(110, $dummyValue));

                $result[] = [
                    'bulan' => $namaBulan[$i],
                    'nilai' => round($dummyValue, 2),
                ];
            }

            return $result;
        }

        // Jika ada data realisasi, hitung NKO aktual untuk setiap bulan dengan formula baru
        $lastValidNKO = null;

        for ($i = 1; $i <= 12; $i++) {
            // Hitung NKO untuk bulan yang sudah lewat atau bulan saat ini
            if ($i <= $bulanSekarang) {
                $nko = $this->hitungNKO($tahun, $i); // Gunakan method yang sudah diperbarui

                // Simpan nilai NKO valid terakhir
                if ($nko > 0) {
                    $lastValidNKO = $nko;
                }

                // Log nilai NKO per bulan
                Log::info("NKO for month {$i}: {$nko}");
            } else {
                // Untuk bulan yang belum datang, gunakan proyeksi sederhana
                // berdasarkan nilai terakhir yang valid
                $nko = $lastValidNKO ?? 0;
            }

            // Jika masih tidak ada nilai valid, gunakan nilai default
            if ($nko <= 0) {
                if ($i == 1) {
                    $nko = 65;
                } else if (isset($result[$i-2])) {
                    $nko = $result[$i-2]['nilai'] + rand(-2, 5);
                } else {
                    $nko = 65 + rand(-2, 5);
                }
                $nko = max(0, min(110, $nko));
            }

            $result[] = [
                'bulan' => $namaBulan[$i],
                'nilai' => round($nko, 2),
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan data pilar untuk visualisasi
     */
    private function getDataPilar($tahun)
    {
        $result = [];
        $pilars = Pilar::orderBy('urutan')->get();
        $bulan = Carbon::now()->month;

        foreach ($pilars as $pilar) {
            $nilai = $pilar->getNilai($tahun, $bulan);
            // Pastikan nilai tidak 0
            if ($nilai == 0) {
                $nilai = rand(70, 90); // Nilai default jika tidak ada data
            }

            $result[] = [
                'nama' => $pilar->nama,
                'kode' => $pilar->kode,
                'nilai' => $nilai,
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan data bidang untuk visualisasi
     */
    private function getDataBidang($tahun, $bulan, $statusVerifikasi)
    {
        $result = [];
        $bidangs = Bidang::all();

        foreach ($bidangs as $bidang) {
            $nilai = $bidang->getNilaiRata($tahun, $bulan);
            // Pastikan nilai tidak 0
            if ($nilai == 0) {
                $nilai = rand(70, 90); // Nilai default jika tidak ada data
            }

            $verifikasi = $bidang->verifikasi;

            if ($statusVerifikasi === 'all' || ($statusVerifikasi === 'verified' && $verifikasi) || ($statusVerifikasi === 'unverified' && !$verifikasi)) {
                $result[] = [
                    'nama' => $bidang->nama,
                    'kode' => $bidang->kode,
                    'nilai' => $nilai,
                ];
            }
        }

        return $result;
    }

    /**
     * Mendapatkan indikator dengan nilai tertinggi
     */
    private function getIndikatorTertinggi($tahun)
    {
        $bulan = Carbon::now()->month;

        $realisasi = Realisasi::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('periode_tipe', 'bulanan')
            ->orderBy('persentase', 'desc')
            ->first();

        if ($realisasi) {
            $indikator = Indikator::with(['pilar', 'bidang'])->find($realisasi->indikator_id);
            return [
                'indikator' => $indikator,
                'nilai' => $realisasi->persentase,
            ];
        }

        // Jika tidak ada data, ambil indikator pertama dan buat data dummy
        $indikator = Indikator::with(['pilar', 'bidang'])->first();
        if ($indikator) {
            return [
                'indikator' => $indikator,
                'nilai' => 95.0, // Nilai dummy tinggi
            ];
        }

        return null;
    }

    /**
     * Mendapatkan indikator dengan nilai terendah
     */
    private function getIndikatorTerendah($tahun)
    {
        $bulan = Carbon::now()->month;

        $realisasi = Realisasi::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('periode_tipe', 'bulanan')
            ->where('persentase', '>', 0) // Hindari nilai 0 yang mungkin belum diinput
            ->orderBy('persentase', 'asc')
            ->first();

        if ($realisasi) {
            $indikator = Indikator::with(['pilar', 'bidang'])->find($realisasi->indikator_id);
            return [
                'indikator' => $indikator,
                'nilai' => $realisasi->persentase,
            ];
        }

        // Jika tidak ada data, ambil indikator terakhir dan buat data dummy
        $indikator = Indikator::with(['pilar', 'bidang'])->orderBy('id', 'desc')->first();
        if ($indikator) {
            return [
                'indikator' => $indikator,
                'nilai' => 65.0, // Nilai dummy rendah
            ];
        }

        return null;
    }

    /**
     * Mendapatkan perkembangan bulanan
     */
    private function getPerkembanganBulanan($tahun)
    {
        $bulan = Carbon::now()->month;
        $bulanSebelumnya = $bulan > 1 ? $bulan - 1 : 12;
        $tahunSebelumnya = $bulan > 1 ? $tahun : $tahun - 1;

        $nkoBulanIni = $this->hitungNKO($tahun, $bulan);
        $nkoBulanSebelumnya = $this->hitungNKO($tahunSebelumnya, $bulanSebelumnya);

        return [
            'bulan_ini' => [
                'bulan' => Carbon::create(null, $bulan, 1)->locale('id')->monthName,
                'tahun' => $tahun,
                'nilai' => $nkoBulanIni,
            ],
            'bulan_sebelumnya' => [
                'bulan' => Carbon::create(null, $bulanSebelumnya, 1)->locale('id')->monthName,
                'tahun' => $tahunSebelumnya,
                'nilai' => $nkoBulanSebelumnya,
            ],
            'perubahan' => round($nkoBulanIni - $nkoBulanSebelumnya, 2),
            'perubahan_persen' => $nkoBulanSebelumnya > 0
                ? round((($nkoBulanIni - $nkoBulanSebelumnya) / $nkoBulanSebelumnya) * 100, 2)
                : 0,
        ];
    }

    /**
     * Mendapatkan tren nilai pilar per bulan
     */
        /**
     * Mendapatkan tren nilai pilar per bulan dalam setahun.
     */
    private function getTrendPilar($pilarId, $tahun)
    {
        $trend = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pilar = Pilar::with('indikators')->find($pilarId);
            $nilai = $pilar ? $pilar->getNilai($tahun, $bulan) : 0;
            $trend[] = [
                'bulan' => date('F', mktime(0, 0, 0, $bulan, 1)),
                'nilai' => $nilai
            ];
        }

        return $trend;
    }
    /**
     * Mendapatkan tren nilai bidang per bulan
     */
    private function getTrendBidang($bidangId, $tahun)
    {
        $result = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $bidang = Bidang::find($bidangId);

        if (!$bidang) return [];

        for ($i = 1; $i <= 12; $i++) {
            $nilai = $bidang->getNilaiRata($tahun, $i);

            $result[] = [
                'bulan' => $namaBulan[$i],
                'nilai' => $nilai,
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan komposisi indikator (tercapai, belum tercapai)
     */
    private function getIndikatorComposition($tahun)
    {
        $indikators = Indikator::where('aktif', true)->get();
        $tercapai = 0;
        $belumTercapai = 0;
        $kritisPerluPerhatian = 0;
        $bulan = Carbon::now()->month;

        foreach ($indikators as $indikator) {
            $nilai = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', 'bulanan')
                ->first();

            if ($nilai) {
                if ($nilai->persentase >= 90) {
                    $tercapai++;
                } elseif ($nilai->persentase >= 70) {
                    $kritisPerluPerhatian++;
                } else {
                    $belumTercapai++;
                }
            } else {
                $belumTercapai++;
            }
        }

        // Jika tidak ada data sama sekali, buat data dummy
        if ($tercapai == 0 && $kritisPerluPerhatian == 0 && $belumTercapai == 0) {
            $totalIndikator = $indikators->count();
            if ($totalIndikator > 0) {
                $tercapai = ceil($totalIndikator * 0.6); // 60% tercapai
                $kritisPerluPerhatian = ceil($totalIndikator * 0.3); // 30% perlu perhatian
                $belumTercapai = $totalIndikator - $tercapai - $kritisPerluPerhatian; // sisanya tidak tercapai
            } else {
                $tercapai = 6;
                $kritisPerluPerhatian = 3;
                $belumTercapai = 1;
            }
        }

        return [
            ['status' => 'Tercapai', 'jumlah' => $tercapai],
            ['status' => 'Perlu Perhatian', 'jumlah' => $kritisPerluPerhatian],
            ['status' => 'Tidak Tercapai', 'jumlah' => $belumTercapai]
        ];
    }

    /**
     * Mendapatkan pemetaan status indikator
     */
    private function getStatusMapping($tahun)
    {
        $indikators = Indikator::where('aktif', true)->get();
        $mapping = [
            'sangat_baik' => 0,  // >= 90%
            'baik' => 0,         // >= 80% & < 90%
            'cukup' => 0,        // >= 70% & < 80%
            'kurang' => 0,       // >= 60% & < 70%
            'sangat_kurang' => 0 // < 60%
        ];
        $bulan = Carbon::now()->month;

        foreach ($indikators as $indikator) {
            $nilai = Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', 'bulanan')
                ->first();

            if ($nilai) {
                if ($nilai->persentase >= 90) {
                    $mapping['sangat_baik']++;
                } elseif ($nilai->persentase >= 80) {
                    $mapping['baik']++;
                } elseif ($nilai->persentase >= 70) {
                    $mapping['cukup']++;
                } elseif ($nilai->persentase >= 60) {
                    $mapping['kurang']++;
                } else {
                    $mapping['sangat_kurang']++;
                }
            } else {
                $mapping['sangat_kurang']++;
            }
        }

        // Jika tidak ada data sama sekali, buat data dummy
        if ($mapping['sangat_baik'] == 0 && $mapping['baik'] == 0 && $mapping['cukup'] == 0 && $mapping['kurang'] == 0 && $mapping['sangat_kurang'] == 0) {
            $totalIndikator = $indikators->count();
            if ($totalIndikator > 0) {
                $mapping['sangat_baik'] = ceil($totalIndikator * 0.4); // 40% sangat baik
                $mapping['baik'] = ceil($totalIndikator * 0.3); // 30% baik
                $mapping['cukup'] = ceil($totalIndikator * 0.15); // 15% cukup
                $mapping['kurang'] = ceil($totalIndikator * 0.1); // 10% kurang
                $mapping['sangat_kurang'] = $totalIndikator - $mapping['sangat_baik'] - $mapping['baik'] - $mapping['cukup'] - $mapping['kurang']; // sisanya sangat kurang
            } else {
                $mapping['sangat_baik'] = 4;
                $mapping['baik'] = 3;
                $mapping['cukup'] = 2;
                $mapping['kurang'] = 1;
                $mapping['sangat_kurang'] = 1;
            }
        }

        return [
            ['status' => 'Sangat Baik', 'jumlah' => $mapping['sangat_baik'], 'color' => '#1cc88a'],
            ['status' => 'Baik', 'jumlah' => $mapping['baik'], 'color' => '#36b9cc'],
            ['status' => 'Cukup', 'jumlah' => $mapping['cukup'], 'color' => '#f6c23e'],
            ['status' => 'Kurang', 'jumlah' => $mapping['kurang'], 'color' => '#e74a3b'],
            ['status' => 'Sangat Kurang', 'jumlah' => $mapping['sangat_kurang'], 'color' => '#858796']
        ];
    }

    /**
     * Mendapatkan tren historis tahunan
     */
private function getHistoricalTrend()
{
    $trendData = [];
    $currentYear = Carbon::now()->year;

    for ($year = $currentYear - 4; $year <= $currentYear; $year++) {
        $nko = $this->hitungNKO($year, null); // pakai realisasi murni

        $trendData[] = [
            'tahun' => $year,
            'nilai' => round($nko, 2)
        ];
    }

    return $trendData;
}

    /**
     * Mendapatkan data forecast untuk 6 bulan ke depan
     */
    private function getForecastData($tahun)
    {
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;
        $forecastData = [];

        // Nilai awal untuk forecast
        $nilaiAwal = $this->hitungNKO($tahunSekarang, $bulanSekarang);

        // Data historikal untuk bulan-bulan sebelumnya tahun ini
        for ($bulan = 1; $bulan <= $bulanSekarang; $bulan++) {
            // Untuk bulan-bulan sebelumnya, gunakan data aktual jika ada
            // atau buat data dengan tren naik jika tidak ada
            $nko = $this->hitungNKO($tahunSekarang, $bulan);

            // Tambahkan sedikit variasi untuk bulan-bulan sebelumnya
            if ($bulan < $bulanSekarang) {
                // Semakin ke belakang, semakin rendah nilainya (tren naik)
                $monthDiff = $bulanSekarang - $bulan;
                $nko = max(50, $nko - ($monthDiff * 1.5 + rand(-1, 1)));
            }

            $forecastData[] = [
                'bulan' => Carbon::create(null, $bulan, 1)->locale('id')->monthName,
                'nilai' => $nko,
                'tipe' => 'Aktual'
            ];
        }

        // Forecast untuk bulan selanjutnya hingga akhir tahun
        // Menggunakan tren naik sederhana
        $trendKenaikan = 1.5; // Kenaikan 1.5% per bulan

        for ($bulan = $bulanSekarang + 1; $bulan <= 12; $bulan++) {
            $monthDiff = $bulan - $bulanSekarang;
            $forecastNilai = $nilaiAwal + ($trendKenaikan * $monthDiff) + rand(-1, 1);

            // Pastikan nilai forecast tidak melebihi 100%
            $forecastNilai = min(round($forecastNilai, 2), 100);

            $forecastData[] = [
                'bulan' => Carbon::create(null, $bulan, 1)->locale('id')->monthName,
                'nilai' => $forecastNilai,
                'tipe' => 'Forecast'
            ];
        }

        return $forecastData;
    }
}
