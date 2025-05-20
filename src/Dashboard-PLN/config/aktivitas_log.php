<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aktivitas Log Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk fitur log aktivitas
    |
    */

    // Apakah fitur log aktivitas diaktifkan
    'enabled' => env('AKTIVITAS_LOG_ENABLED', true),

    // Model yang dapat dilog aktivitasnya
    'allowed_models' => [
        'App\Models\User',
        'App\Models\Pilar',
        'App\Models\Bidang',
        'App\Models\Indikator',
        'App\Models\NilaiKPI',
        'App\Models\TargetKPI',
        'App\Models\TahunPenilaian',
    ],

    // Masa retensi data log aktivitas dalam hari
    'retention_days' => env('AKTIVITAS_LOG_RETENTION_DAYS', 365),

    // Atribut yang tidak perlu dilog (sensitif/tidak relevan)
    'excluded_attributes' => [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
    ],

    // Atribut yang harus disamarkan saat dilog (dienkripsi)
    'masked_attributes' => [
        'email', // hanya sebagian yang ditampilkan
        'phone', // hanya sebagian yang ditampilkan
    ],

    // Jumlah log maksimum yang ditampilkan per halaman
    'per_page' => 20,
];
