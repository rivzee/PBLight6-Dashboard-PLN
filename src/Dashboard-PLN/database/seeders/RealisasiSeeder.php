<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Realisasi;
use App\Models\TargetKPI;
use App\Models\Indikator;
use App\Models\User;
use Carbon\Carbon;

class RealisasiSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = now()->year;
        $user = User::first(); // pengguna penginput dan verifikator

        $targetKPIs = TargetKPI::with('indikator')->get();

        foreach ($targetKPIs as $target) {
            $indikatorId = $target->indikator_id;
            $targetBulanan = $target->target_bulanan;


            if (!$targetBulanan) continue;

            for ($bulan = 1; $bulan <= 11; $bulan++) {
                $tanggal = Carbon::create($tahun, $bulan, rand(1, 28));
                $targetBulan = $targetBulanan[$bulan - 1] ?? 100;

                // Nilai random mendekati target bulanan
                $nilai = round(rand($targetBulan * 80, $targetBulan * 120) / 100, 2);

                $persentase = $targetBulan > 0 ? round(($nilai / $targetBulan) * 100, 2) : 0;

                // Ambil bobot indikator
                $bobot = $target->indikator->bobot ?? 0;

                // nilai_akhir = bobot * (persentase / 100)
                $nilaiAkhir = round(($bobot * ($persentase / 100)), 2);

                // Cegah duplikasi input harian
                Realisasi::updateOrCreate([
                    'indikator_id' => $indikatorId,
                    'tanggal' => $tanggal->toDateString(),
                ], [
                    'user_id' => $user?->id,
                    'nilai' => $nilai,
                    'persentase' => $persentase,
                    'nilai_akhir' => $nilaiAkhir,
                    'keterangan' => 'Auto dummy generated',
                    'diverifikasi' => true,
                    'verifikasi_oleh' => $user?->id,
                    'verifikasi_pada' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}