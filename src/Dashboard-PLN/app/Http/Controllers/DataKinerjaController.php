<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Bidang;
use App\Models\Indikator;
use App\Models\Realisasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $tahun = $request->tahun ?? Carbon::now()->year;
        $bulan = $request->bulan ?? Carbon::now()->month;
        $user = Auth::user();

        // Ambil data untuk ringkasan KPI
        $totalIndikator = Indikator::where('aktif', true)->count();
        $totalIndikatorTercapai = $this->getIndikatorTercapai($tahun);
        $persenTercapai = $totalIndikator > 0 ? round(($totalIndikatorTercapai / $totalIndikator) * 100) : 0;

        // Data untuk gauge meter
        $nilaiNKO = $this->hitungNKO($tahun, null);

        // Data tren NKO bulanan untuk grafik
        $trendNKO = $this->getTrendNKO($tahun);

        // Data perbandingan per-pilar
        $pilarData = $this->getDataPilar($tahun);

        // Data perbandingan per-bidang
        $bidangData = $this->getDataBidang($tahun);

        // Data untuk analisis
        $analisisData = [
            'tertinggi' => $this->getIndikatorTertinggi($tahun),
            'terendah' => $this->getIndikatorTerendah($tahun),
            'perkembangan' => $this->getPerkembanganBulanan($tahun),
        ];

        // Data untuk chart tambahan
        $indikatorComposition = $this->getIndikatorComposition($tahun);
        $statusMapping = $this->getStatusMapping($tahun);
        $historicalTrend = $this->getHistoricalTrend();
        $forecastData = $this->getForecastData($tahun);

        return view('dataKinerja.index', compact(
            'tahun',
            'bulan',
            'totalIndikator',
            'totalIndikatorTercapai',
            'persenTercapai',
            'nilaiNKO',
            'trendNKO',
            'pilarData',
            'bidangData',
            'analisisData',
            'indikatorComposition',
            'statusMapping',
            'historicalTrend',
            'forecastData'
        ));
    }

    /**
     * Menampilkan data kinerja per pilar
     */
    public function pilar(Request $request, $id = null)
    {
        $tahun = $request->tahun ?? Carbon::now()->year;
        $bulan = $request->bulan ?? Carbon::now()->month;

        if ($id) {
            // Jika ID pilar disebutkan, tampilkan detail pilar
            $pilar = Pilar::with(['indikators' => function($query) {
                $query->where('aktif', true)->with('bidang');
            }])->findOrFail($id);

            // Data nilai indikator dalam pilar
            foreach ($pilar->indikators as $indikator) {
                $nilai = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', 'bulanan')
                    ->first();

                $indikator->nilai = $nilai ? $nilai->persentase : 0;
            }

            // Data tren pilar bulanan
            $trendPilar = $this->getTrendPilar($id, $tahun);

            return view('dataKinerja.pilar_detail', compact('pilar', 'tahun', 'bulan', 'trendPilar'));
        } else {
            // Jika tidak, tampilkan daftar semua pilar
            $pilars = Pilar::with('indikators')->orderBy('urutan')->get();

            foreach ($pilars as $pilar) {
                $pilar->nilai = $pilar->getNilai($tahun, $bulan);
            }

            return view('dataKinerja.pilar_index', compact('pilars', 'tahun', 'bulan'));
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
        $indikator = Indikator::with(['pilar', 'bidang'])->findOrFail($id);
        $tahun = $request->tahun ?? Carbon::now()->year;

        // Ambil data historis nilai indikator
        $realisasi = Realisasi::where('indikator_id', $id)
            ->where('tahun', $tahun)
            ->where('periode_tipe', 'bulanan')
            ->orderBy('bulan')
            ->get();

        // Siapkan data untuk chart
        $chartData = [];

        for ($i = 1; $i <= 12; $i++) {
            $nilai = $realisasi->where('bulan', $i)->first();
            $chartData[] = [
                'bulan' => Carbon::create(null, $i, 1)->locale('id')->monthName,
                'nilai' => $nilai ? $nilai->persentase : 0,
            ];
        }

        return view('dataKinerja.indikator', compact('indikator', 'tahun', 'realisasis', 'chartData'));
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
     */
    private function hitungNKO($tahun, $bulan = null)
    {
        $bulan = $bulan ?? Carbon::now()->month;
        $pilars = Pilar::all();
        $totalNilai = 0;

        foreach ($pilars as $pilar) {
            $totalNilai += $pilar->getNilai($tahun, $bulan);
        }

        return $pilars->count() > 0 ? round($totalNilai / $pilars->count(), 2) : 0;
    }

    /**
     * Mendapatkan tren NKO bulanan
     */
    private function getTrendNKO($tahun)
    {
        $result = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $nko = $this->hitungNKO($tahun, $i);

            $result[] = [
                'bulan' => $namaBulan[$i],
                'nilai' => $nko,
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
            $result[] = [
                'nama' => $pilar->nama,
                'kode' => $pilar->kode,
                'nilai' => $pilar->getNilai($tahun, $bulan),
            ];
        }

        return $result;
    }

    /**
     * Mendapatkan data bidang untuk visualisasi
     */
    private function getDataBidang($tahun)
    {
        $result = [];
        $bidangs = Bidang::all();
        $bulan = Carbon::now()->month;

        foreach ($bidangs as $bidang) {
            $result[] = [
                'nama' => $bidang->nama,
                'kode' => $bidang->kode,
                'nilai' => $bidang->getNilaiRata($tahun, $bulan),
            ];
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
    private function getTrendPilar($pilarId, $tahun)
    {
        $result = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $pilar = Pilar::find($pilarId);

        if (!$pilar) return [];

        for ($i = 1; $i <= 12; $i++) {
            $nilai = $pilar->getNilai($tahun, $i);

            $result[] = [
                'bulan' => $namaBulan[$i],
                'nilai' => $nilai,
            ];
        }

        return $result;
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

        foreach ($indikators as $indikator) {
            $nilaiRata = 0;
            $count = 0;

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $nilai = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', 'bulanan')
                    ->first();

                if ($nilai) {
                    $nilaiRata += $nilai->persentase;
                    $count++;
                }
            }

            if ($count > 0) {
                $nilaiRata = $nilaiRata / $count;

                if ($nilaiRata >= 85) {
                    $tercapai++;
                } elseif ($nilaiRata >= 70) {
                    $kritisPerluPerhatian++;
                } else {
                    $belumTercapai++;
                }
            } else {
                $belumTercapai++;
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

        foreach ($indikators as $indikator) {
            $nilaiRata = 0;
            $count = 0;

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $nilai = Realisasi::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', 'bulanan')
                    ->first();

                if ($nilai) {
                    $nilaiRata += $nilai->persentase;
                    $count++;
                }
            }

            if ($count > 0) {
                $nilaiRata = $nilaiRata / $count;

                if ($nilaiRata >= 90) {
                    $mapping['sangat_baik']++;
                } elseif ($nilaiRata >= 80) {
                    $mapping['baik']++;
                } elseif ($nilaiRata >= 70) {
                    $mapping['cukup']++;
                } elseif ($nilaiRata >= 60) {
                    $mapping['kurang']++;
                } else {
                    $mapping['sangat_kurang']++;
                }
            } else {
                $mapping['sangat_kurang']++;
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

        // Ambil data untuk 5 tahun terakhir
        for ($year = $currentYear - 4; $year <= $currentYear; $year++) {
            $nko = $this->hitungNKO($year, null);
            $trendData[] = [
                'tahun' => $year,
                'nilai' => $nko
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

        // Data historikal untuk bulan-bulan sebelumnya tahun ini
        for ($bulan = 1; $bulan <= $bulanSekarang; $bulan++) {
            $nko = $this->hitungNKO($tahunSekarang, $bulan);
            $forecastData[] = [
                'bulan' => Carbon::create(null, $bulan, 1)->locale('id')->monthName,
                'nilai' => $nko,
                'tipe' => 'Aktual'
            ];
        }

        // Forecast untuk bulan selanjutnya hingga akhir tahun
        // Menggunakan simple moving average untuk prediksi
        $rataRata = 0;
        $jumlahData = 0;

        for ($bulan = 1; $bulan <= $bulanSekarang; $bulan++) {
            $nko = $this->hitungNKO($tahunSekarang, $bulan);
            if ($nko > 0) {
                $rataRata += $nko;
                $jumlahData++;
            }
        }

        if ($jumlahData > 0) {
            $rataRata = $rataRata / $jumlahData;

            // Gunakan trend sederhana (naik 1.5% per bulan)
            $trendKenaikan = 1.5;

            for ($bulan = $bulanSekarang + 1; $bulan <= 12; $bulan++) {
                $forecastNilai = $rataRata + ($trendKenaikan * ($bulan - $bulanSekarang));
                // Pastikan nilai forecast tidak melebihi 100%
                $forecastNilai = min($forecastNilai, 100);

                $forecastData[] = [
                    'bulan' => Carbon::create(null, $bulan, 1)->locale('id')->monthName,
                    'nilai' => $forecastNilai,
                    'tipe' => 'Forecast'
                ];
            }
        }

        return $forecastData;
    }
}
