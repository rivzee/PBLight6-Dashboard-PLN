<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo_pln.png') }}" alt="Logo PLN" class="logo">
        <h1>{{ $title }}</h1>
        <p class="subtitle">{{ $subtitle }}</p>
        <p class="subtitle">PT PLN (Persero)</p>
    </div>

    <div class="info-section">
        <p><span class="info-label">Tanggal Cetak:</span> {{ $tanggal_cetak }}</p>
    </div>

    <h2>Ringkasan Pencapaian KPI</h2>
    <table class="summary-table">
        <thead>
            <tr>
                <th class="text-left">Metrik</th>
                <th class="text-right">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Total Indikator</td><td class="text-right">{{ $totalIndikator }}</td></tr>
            <tr><td>Indikator Tercapai</td><td class="text-right">{{ $tercapai }}</td></tr>
            <tr><td>Indikator Belum Tercapai</td><td class="text-right">{{ $belumTercapai }}</td></tr>
            <tr><td>Rata-rata Pencapaian</td><td class="text-right">{{ $rataRataPencapaian }}%</td></tr>
        </tbody>
    </table>

    <h2>Detail Indikator Kinerja Per-Pilar</h2>
    @foreach($pilars as $pilar)
        <div class="pilar-header">Pilar {{ $pilar->kode }}: {{ $pilar->nama }}</div>

        @if($pilar->indikators->isEmpty())
            <p><em>Tidak ada indikator untuk pilar ini.</em></p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Indikator</th>
                        <th>Bidang</th>
                        <th>Target Tahunan</th>
                        <th>Target Bulanan</th>
                        <th>Realisasi</th>
                        <th>Bobot</th>
                        <th>Polaritas</th>
                        <th>Capaian</th>
                        <th>Nilai</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
@foreach($pilar->indikators as $index => $indikator)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td class="text-center">{{ $indikator['kode'] }}</td>
        <td class="text-left">{{ $indikator['nama'] }}</td>
        <td class="text-left">{{ $indikator['bidang_nama'] }}</td>
        <td class="text-right">
            {{ number_format($indikator['target_tahunan'], 2, ',', '.') }}
        </td>
        <td class="text-right">
            {{ number_format($indikator['target_bulanan'], 2, ',', '.') }}
        </td>
        <td class="text-right">
            @if($indikator['realisasi_nilai'] > 0)
                {{ number_format($indikator['realisasi_nilai'], 2, ',', '.') }}
            @else
                -
            @endif
        </td>
        <td class="text-right">
            @if($indikator['realisasi_nilai'] > 0)
                {{ number_format($indikator['bobot'], 2, ',', '.') }}
            @else
                -
            @endif
        </td>
        <td class="text-center">
            @if($indikator['realisasi_nilai'] > 0)
                {{ ucfirst($indikator['jenis_polaritas']) }}
            @else
                -
            @endif
        </td>
        <td class="text-right">
            @if($indikator['realisasi_nilai'] > 0)
                {{ number_format($indikator['nilai_polaritas'], 2, ',', '.') }}%
            @else
                -
            @endif
        </td>
        <td class="text-right">
            @if($indikator['realisasi_nilai'] > 0)
                {{ number_format($indikator['nilai_akhir'], 2, ',', '.') }}
            @else
                -
            @endif
        </td>
        @php
            $keterangan = strtolower($indikator['keterangan']);
            $class = match(true) {
                str_contains($keterangan, 'baik') => 'keterangan-baik',
                str_contains($keterangan, 'hati') => 'keterangan-hati-hati',
                str_contains($keterangan, 'masalah') => 'keterangan-masalah',
                default => '',
            };
        @endphp
        <td class="text-center {{ $class }}">
            @if($indikator['realisasi_nilai'] > 0)
                {{ $indikator['keterangan'] }}
            @else
                -
            @endif
        </td>

    </tr>
@endforeach
</tbody>


            </table>
        @endif

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="footer">
        <p>Dokumen ini dicetak oleh Sistem Manajemen Kinerja PLN pada {{ $tanggal_cetak }}</p>
        <p>&copy; {{ date('Y') }} PT PLN (Persero). Hak Cipta Dilindungi.</p>
    </div>
</body>
</html>
