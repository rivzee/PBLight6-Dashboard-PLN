<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TargetKPI;
use App\Models\Indikator;
use App\Models\TahunPenilaian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TargetKPISeeder extends Seeder
{
    public function run(): void
    {
        $tahun = now()->year;

        $tahunPenilaian = TahunPenilaian::firstOrCreate(['tahun' => $tahun]);

        $indikators = Indikator::all();
        $admin = \App\Models\User::first(); // Ambil user pertama sebagai pengisi & penyetuju (opsional)

        foreach ($indikators as $indikator) {
            $targetBulanan = [];
            $total = 0;

            for ($i = 1; $i <= 12; $i++) {
                $nilai = rand(80, 120);
                $targetBulanan[] = $nilai;
                $total += $nilai;
            }

            TargetKPI::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'tahun_penilaian_id' => $tahunPenilaian->id
                ],
                [
                    'user_id' => $admin?->id,
                    'target_tahunan' => $total,
                    'target_bulanan' => $targetBulanan,
                    'keterangan' => fake()->sentence(),
                    'disetujui' => true,
                    'disetujui_oleh' => $admin?->id,
                    'disetujui_pada' => Carbon::now(),
                ]
            );
        }
    }
}
