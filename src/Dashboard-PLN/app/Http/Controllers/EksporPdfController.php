<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Bidang;
use App\Models\Realisasi; 
use App\Models\Indikator;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EksporPdfController extends Controller
{
    /**
     * Halaman awal ekspor PDF
     */
    public function index()
    {
        $user = Auth::user();

        // Hanya admin dan master admin yang boleh mengakses
        if (!($user->isAdmin() || $user->isMasterAdmin())) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $bidangs = Bidang::all();
        $pilars = Pilar::all();

        return view('eksporPdf.index', compact('bidangs', 'pilars'));
    }

    /**
     * Ekspor laporan KPI bidang
     */
    public function eksporBidang(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bidang_id' => 'required|exists:bidangs,id',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $bidang = Bidang::findOrFail($request->bidang_id);

        // Hanya admin bidang terkait dan master admin yang boleh mengakses
        if (!($user->isMasterAdmin() || ($user->isAdmin() && $user->getBidang() && $user->getBidang()->id == $bidang->id))) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke laporan ini.');
        }

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $indikators = Indikator::where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->with(['realisasis' => function($query) use ($tahun, $bulan) {
                $query->where('tahun', $tahun)
                      ->where('bulan', $bulan)
                      ->where('periode_tipe', 'bulanan');
            }])
            ->get();

        // Siapkan data untuk PDF
        $data = [
            'title' => 'Laporan KPI Bidang ' . $bidang->nama,
            'subtitle' => 'Periode: ' . $namaBulan[$bulan] . ' ' . $tahun,
            'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y H:i'),
            'bidang' => $bidang,
            'indikators' => $indikators,
        ];

        $pdf = PDF::loadView('eksporPdf.bidang', $data);

        return $pdf->download('Laporan_KPI_' . $bidang->kode . '_' . $tahun . '_' . $bulan . '.pdf');
    }

    /**
     * Ekspor laporan KPI pilar
     */
    public function eksporPilar(Request $request)
    {
        $user = Auth::user();

        // Hanya master admin yang boleh mengakses
        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $request->validate([
            'pilar_id' => 'required|exists:pilars,id',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $pilar = Pilar::findOrFail($request->pilar_id);
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $indikators = $pilar->indikators()
            ->where('aktif', true)
            ->with(['realisasis' => function($query) use ($tahun, $bulan) {
                $query->where('tahun', $tahun)
                      ->where('bulan', $bulan)
                      ->where('periode_tipe', 'bulanan');
            }, 'bidang'])
            ->get();

        // Siapkan data untuk PDF
        $data = [
            'title' => 'Laporan KPI Pilar ' . $pilar->nama,
            'subtitle' => 'Periode: ' . $namaBulan[$bulan] . ' ' . $tahun,
            'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y H:i'),
            'pilar' => $pilar,
            'indikators' => $indikators,
        ];

        $pdf = PDF::loadView('eksporPdf.pilar', $data);

        return $pdf->download('Laporan_KPI_Pilar_' . $pilar->kode . '_' . $tahun . '_' . $bulan . '.pdf');
    }

    /**
     * Ekspor laporan KPI keseluruhan
     */
    public function eksporKeseluruhan(Request $request)
    {
        $user = Auth::user();

        // Hanya master admin yang boleh mengakses
        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $pilars = Pilar::with(['indikators' => function($query) use ($tahun, $bulan) {
            $query->where('aktif', true)
                  ->with(['realisasis' => function($q) use ($tahun, $bulan) {
                      $q->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('periode_tipe', 'bulanan');
                  }, 'bidang']);
        }])
        ->orderBy('urutan')
        ->get();

        // Siapkan data untuk PDF
        $data = [
            'title' => 'Laporan KPI Keseluruhan',
            'subtitle' => 'Periode: ' . $namaBulan[$bulan] . ' ' . $tahun,
            'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y H:i'),
            'pilars' => $pilars,
        ];

        $pdf = PDF::loadView('eksporPdf.keseluruhan', $data);

        return $pdf->download('Laporan_KPI_Keseluruhan_' . $tahun . '_' . $bulan . '.pdf');
    }
}
