<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Bidang;
use App\Models\Indikator;
use App\Models\NilaiKPI;
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

        return view('dataKinerja.index', compact(
            'tahun',
            'totalIndikator',
            'totalIndikatorTercapai',
            'persenTercapai',
            'nilaiNKO',
            'trendNKO',
            'pilarData',
            'bidangData',
            'analisisData'
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
                $nilai = NilaiKPI::where('indikator_id', $indikator->id)
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
                $nilai = NilaiKPI::where('indikator_id', $indikator->id)
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
        $nilaiKPIs = NilaiKPI::where('indikator_id', $id)
            ->where('tahun', $tahun)
            ->where('periode_tipe', 'bulanan')
            ->orderBy('bulan')
            ->get();

        // Siapkan data untuk chart
        $chartData = [];

        for ($i = 1; $i <= 12; $i++) {
            $nilai = $nilaiKPIs->where('bulan', $i)->first();
            $chartData[] = [
                'bulan' => Carbon::create(null, $i, 1)->locale('id')->monthName,
                'nilai' => $nilai ? $nilai->persentase : 0,
            ];
        }

        return view('dataKinerja.indikator', compact('indikator', 'tahun', 'nilaiKPIs', 'chartData'));
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
            $nilai = NilaiKPI::where('indikator_id', $indikator->id)
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

        $nilaiKPI = NilaiKPI::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('periode_tipe', 'bulanan')
            ->orderBy('persentase', 'desc')
            ->first();

        if ($nilaiKPI) {
            $indikator = Indikator::with(['pilar', 'bidang'])->find($nilaiKPI->indikator_id);
            return [
                'indikator' => $indikator,
                'nilai' => $nilaiKPI->persentase,
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

        $nilaiKPI = NilaiKPI::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('periode_tipe', 'bulanan')
            ->where('persentase', '>', 0) // Hindari nilai 0 yang mungkin belum diinput
            ->orderBy('persentase', 'asc')
            ->first();

        if ($nilaiKPI) {
            $indikator = Indikator::with(['pilar', 'bidang'])->find($nilaiKPI->indikator_id);
            return [
                'indikator' => $indikator,
                'nilai' => $nilaiKPI->persentase,
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
}
