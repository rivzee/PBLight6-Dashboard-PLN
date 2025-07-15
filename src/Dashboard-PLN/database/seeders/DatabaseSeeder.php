<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user default
        $users = [
            [
                'name' => 'Master Admin',
                'email' => 'admin@mctn.co.id',
                'password' => Hash::make('admin12345'),
                'role' => 'asisten_manager',
            ],
            [
                'name' => 'PIC Keuangan',
                'email' => 'keuangan@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_keuangan',
            ],
            [
                'name' => 'PIC Perencanaan Operasi',
                'email' => 'operasi@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_perencanaan_operasi',
            ],
            [
                'name' => 'PIC Sekretaris Perusahaan',
                'email' => 'sekretaris@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_sekretaris_perusahaan',
            ],
            [
                'name' => 'PIC Perencanaan Korporat',
                'email' => 'pkorporat@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_perencanaan_korporat',
            ],
            [
                'name' => 'PIC Manajemen Risiko',
                'email' => 'risiko@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_manajemen_risiko',
            ],
            [
                'name' => 'PIC Pengembangan Bisnis',
                'email' => 'bisnis@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_pengembangan_bisnis',
            ],
            [
                'name' => 'PIC Human Capital',
                'email' => 'hcm@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_human_capital',
            ],
            [
                'name' => 'PIC K3L',
                'email' => 'k3l@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_k3l',
            ],
            [
                'name' => 'PIC SPI',
                'email' => 'spi@mctn.co.id',
                'password' => Hash::make('pic12345'),
                'role' => 'pic_spi',
            ],
            [
                'name' => 'Karyawan',
                'email' => 'karyawan@mctn.co.id',
                'password' => Hash::make('karyawan12345'),
                'role' => 'karyawan',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Panggil seeder lain sesuai urutan
        $this->call([
            BidangSeeder::class,
            PilarSeeder::class,
            IndikatorSeeder::class,
        ]);
    }
}
