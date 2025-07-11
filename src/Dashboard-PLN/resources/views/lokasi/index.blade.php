@extends('layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/lokasi.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        @foreach ($lokasiList as $index => $lokasi)
        <div class="col-md-4 mb-4 d-flex align-items-stretch">
            <div class="lokasi-card w-100 shadow-sm">
                <!-- Strip gradasi atas -->
                <div class="lokasi-card-gradient-top"></div>

                <!-- Konten dalam card -->
                <div class="p-3 pb-0">
                    <h5 class="fw-bold mb-3">{{ $lokasi['nama'] }}</h5>
                </div>

                <div id="map{{ $index }}" class="lokasi-map"></div>

                <div class="p-3 pt-2">
                    <p class="text-muted mb-0 small">{{ $lokasi['alamat'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lokasiList = @json($lokasiList);

        lokasiList.forEach(function (lokasi, index) {
            const map = L.map('map' + index).setView([parseFloat(lokasi.lat), parseFloat(lokasi.lng)], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            L.marker([lokasi.lat, lokasi.lng]).addTo(map)
                .bindPopup(lokasi.alamat);
        });
    });
</script>
@endsection
