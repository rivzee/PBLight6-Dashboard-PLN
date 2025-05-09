<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Membuat pengguna baru
        User::create([
            'name' => 'Latifa Keysha',
            'email' => 'latifa@gmail.com',
            'password' => bcrypt('latifa12'), // Enkripsi password
            'role' => 'asisten_manager', // Role yang sesuai dengan role yang kamu tentukan
        ]);


    }
}
