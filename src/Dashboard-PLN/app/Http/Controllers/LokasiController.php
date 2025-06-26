<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class LokasiController extends Controller
{
    public function index()
    {
        $alamat = 'Jl. Musyawarah, Payung Sekaki, Pekanbaru, Riau';

        $response = Http::withHeaders([
            'User-Agent' => 'DashboardPLN-MCTN/1.0 (admin@pln.co.id)' // WAJIB
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $alamat,
            'format' => 'json',
            'limit' => 1
        ]);

        $data = $response->json();

        $koordinat = count($data)
            ? ['lat' => $data[0]['lat'], 'lng' => $data[0]['lon']]
            : ['lat' => -0.486702, 'lng' => 101.423199]; // Fallback jika gagal

        return view('lokasi.index', [
            'alamat' => $alamat,
            'lat' => $koordinat['lat'],
            'lng' => $koordinat['lng']
        ]);
    }
}
