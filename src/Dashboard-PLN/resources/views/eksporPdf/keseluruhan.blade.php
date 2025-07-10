<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #009CDE;
            padding-bottom: 10px;
        }
        .logo {
            width: 80px; /* atau 60px, tergantung kebutuhan */
            margin: 0 auto 10px;
        }

        h1 {
            font-size: 16px;
            margin: 5px 0;
            color: #0A4D85;
        }
        h2 {
            font-size: 14px;
            margin: 15px 0 10px;
            color: #009CDE;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            color: #555;
            margin: 2px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 10px;
        }
        th {
            background-color: #009CDE;
            color: #fff;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .summary-table {
            width: 400px;
            margin: 0 auto 20px;
        }
        .summary-table th {
            width: 60%;
        }
        .pilar-header {
            background-color: #f0f0f0;
            border-left: 4px solid #009CDE;
            padding: 8px 12px;
            margin: 20px 0 10px;
            font-weight: bold;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 9px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .keterangan-baik {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        .keterangan-hati-hati {
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
        }
        .keterangan-masalah {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

    </style>
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
