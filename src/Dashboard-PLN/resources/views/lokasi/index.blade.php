@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Lokasi Kantor PLN MCTN</h3>
    <p>{{ $alamat }}</p>
    <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc;"></div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lat = {{ $lat }};
        var lng = {{ $lng }};

        var map = L.map('map').setView([lat, lng], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup("{{ $alamat }}")
            .openPopup();
    });
</script>
@endsection
