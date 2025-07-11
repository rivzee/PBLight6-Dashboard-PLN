<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TargetKPI;
use App\Models\Indikator;
use App\Models\TahunPenilaian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TargetKPISeeder extends Seeder
{
    public function run(): void
    {
        $tahun = now()->year;

        // Pastikan tahun penilaian ada dan aktif
        $tahunPenilaian = TahunPenilaian::firstOrCreate(
            ['tahun' => $tahun],
            ['is_aktif' => true]
        );

        // Ambil semua indikator
        $indikators = Indikator::all();

        // Ambil satu user yang dianggap Master Admin
        $admin = User::where('role', 'asisten_manager')->first() ?? User::first();

        foreach ($indikators as $indikator) {
            $targetBulanan = [];

            // Buat target bulanan kumulatif
            $awal = rand(80, 100); // nilai awal bulan Januari
            $targetBulanan[0] = $awal;

            for ($i = 1; $i < 12; $i++) {
                $kenaikan = rand(5, 15); // pertambahan tiap bulan
                $targetBulanan[$i] = $targetBulanan[$i - 1] + $kenaikan;
            }

            // Sesuai controller: target_tahunan = bulan Desember (index ke-11)
            $targetTahunan = $targetBulanan[11];

            TargetKPI::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'tahun_penilaian_id' => $tahunPenilaian->id,
                ],
                [
                    'user_id' => $admin?->id,
                    'target_tahunan' => $targetTahunan,
                    'target_bulanan' => $targetBulanan,
                    'disetujui' => true,
                    'disetujui_oleh' => $admin?->id,
                    'disetujui_pada' => Carbon::now(),
                ]
            );
        }

        $this->command->info("✅ Target KPI kumulatif berhasil disiapkan untuk tahun $tahun.");
    }
}
