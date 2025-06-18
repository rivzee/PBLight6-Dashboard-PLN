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



        $bidangs = Bidang::all();
        $pilars = Pilar::all();

        return view('eksporPdf.index', compact('bidangs', 'pilars'));
    }




    /**
     * Ekspor laporan KPI keseluruhan
     */
public function eksporKeseluruhan(Request $request)
{
    $user = Auth::user(); // Masih bisa dipakai kalau kamu ingin mencatat siapa yang ekspor

    // Tidak ada lagi pengecekan role di sini

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
