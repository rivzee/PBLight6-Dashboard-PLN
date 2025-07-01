<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class LokasiController extends Controller
{
    public function index()
    {
        // Daftar lokasi dengan alamat dan (opsional) koordinat
        $lokasiList = [
            [
                'nama' => 'Head Office I',
                'alamat' => 'Plaza Simatupang, Jakarta',
            ],
            [
                'nama' => 'Head Office II',
                'alamat' => 'Jl. Musyawarah, Payung Sekaki, Pekanbaru, Riau',
            ],
            [
                'nama' => 'Operating Office',
                'alamat' => 'North Duri Cogeneration Plant Lapangan Minyak, Duri, Riau',
                'lat' => 1.309878,
                'lng' => 101.108318,
            ],
        ];

        $lokasiDenganKoordinat = [];

        foreach ($lokasiList as $lokasi) {
            // Gunakan koordinat manual jika sudah disediakan
            if (isset($lokasi['lat']) && isset($lokasi['lng'])) {
                $lokasiDenganKoordinat[] = $lokasi;
                continue;
            }

            // Panggil Nominatim untuk mendapatkan koordinat dari alamat
            $response = Http::withHeaders([
                'User-Agent' => 'DashboardPLN-MCTN/1.0 (admin@pln.co.id)' // WAJIB agar tidak diblokir
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => trim($lokasi['alamat']),
                'format' => 'json',
                'limit' => 1
            ]);

            $data = $response->json();

            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                $lokasiDenganKoordinat[] = [
                    'nama' => $lokasi['nama'],
                    'alamat' => $lokasi['alamat'],
                    'lat' => $data[0]['lat'],
                    'lng' => $data[0]['lon'],
                ];
            }
        }

        return view('lokasi.index', [
            'lokasiList' => $lokasiDenganKoordinat
        ]);
    }
}
