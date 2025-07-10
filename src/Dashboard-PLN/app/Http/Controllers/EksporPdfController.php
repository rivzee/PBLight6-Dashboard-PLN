<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Bidang;
use App\Models\Indikator;
use App\Models\Realisasi;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EksporPdfController extends Controller
{
    public function index()
    {
        $bidangs = Bidang::all();
        $pilars = Pilar::all();
        return view('eksporPdf.index', compact('bidangs', 'pilars'));
    }

    public function eksporKeseluruhan(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:' . date('Y'),
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $tanggal = Carbon::createFromDate($tahun, $bulan, 1);

        $pilars = Pilar::with([
            'indikators' => function ($q) {
                $q->orderBy('kode');
            },
            'indikators.bidang',
            'indikators.realisasis' => function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            },
            'indikators.targetKPI.tahunPenilaian'
        ])->orderBy('urutan')->get();

        $totalIndikator = 0;
        $tercapai = 0;
        $belumTercapai = 0;

        $pilars = $pilars->map(function ($pilar) use ($tahun, $bulan, &$totalIndikator, &$tercapai, &$belumTercapai) {
            $indikators = $pilar->indikators->map(function ($indikator) use ($tahun, $bulan, &$totalIndikator, &$tercapai, &$belumTercapai) {
                $totalIndikator++;

                $realisasi = $indikator->realisasis->first();
                $targetKPI = $indikator->targetKPI->where('tahunPenilaian.tahun', $tahun)->first();

                $targetTahunan = $targetKPI?->target_tahunan ?? 0;
                $targetBulanan = $targetKPI?->getTargetBulan($bulan) ?? 0;
                $nilai = $realisasi?->nilai ?? 0;
                $bobot = $indikator->bobot ?? 0;
                $jenis = $realisasi?->jenis_polaritas ?? 'netral';

                // Hitung capaian (%)
                if ($targetBulanan > 0) {
                    if ($jenis === 'positif') {
                        $persentase = ($nilai / $targetBulanan) * 100;
                    } elseif ($jenis === 'negatif') {
                        $persentase = (2 - ($nilai / $targetBulanan)) * 100;
                    } else {
                        $deviasi = abs($nilai - $targetBulanan) / $targetBulanan;
                        $persentase = $deviasi <= 0.05 ? 100 : 0;
                    }
                } else {
                    $persentase = 0;
                }

                $persentase = min(max($persentase, 0), 110);

                // Nilai indikator (max 1.1), dikali bobot
                if ($jenis === 'positif') {
                    $nilaiIndikator = min(max($nilai / $targetBulanan, 0), 1.1);
                } elseif ($jenis === 'negatif') {
                    $nilaiIndikator = min(max(2 - ($nilai / $targetBulanan), 0), 1.1);
                } else {
                    $nilaiIndikator = ($nilai == $targetBulanan) ? 1 : 0;
                }

                $nilaiAkhir = $nilaiIndikator * $bobot;

                // Keterangan
                if ($persentase < 95) {
                    $keterangan = 'Masalah';
                } elseif ($persentase < 100) {
                    $keterangan = 'Hati-hati';
                } else {
                    $keterangan = 'Baik';
                }

                if ($persentase >= 100) $tercapai++;
                elseif ($nilai > 0) $belumTercapai++;

                return [
                    'kode' => $indikator->kode,
                    'nama' => $indikator->nama,
                    'bidang_nama' => $indikator->bidang->nama ?? '-',
                    'target_tahunan' => $targetTahunan,
                    'target_bulanan' => $targetBulanan,
                    'realisasi_nilai' => $nilai,
                    'bobot' => $bobot,
                    'jenis_polaritas' => $jenis,
                    'nilai_polaritas' => round($persentase, 2),
                    'nilai_akhir' => round($nilaiAkhir, 2),
                    'keterangan' => $keterangan,
                ];
            });

            $pilar->indikators = $indikators;
            return $pilar;
        });

        $rataRataPencapaian = $totalIndikator > 0 ? round(($tercapai / $totalIndikator) * 100, 2) : 0;

        $data = [
            'title' => 'Laporan KPI Keseluruhan',
            'subtitle' => 'Periode: ' . $tanggal->translatedFormat('F Y'),
            'tanggal_cetak' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i'),
            'pilars' => $pilars,
            'totalIndikator' => $totalIndikator,
            'tercapai' => $tercapai,
            'belumTercapai' => $belumTercapai,
            'rataRataPencapaian' => $rataRataPencapaian,
        ];

        $pdf = PDF::loadView('eksporPdf.keseluruhan', $data);
        return $pdf->download("Laporan_KPI_Keseluruhan_{$tahun}_{$bulan}.pdf");
    }
}