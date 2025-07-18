@extends('layouts.app')

@section('title', 'Data Kinerja - Analisis Pilar')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dataKinerja.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <div class="section-divider mb-4">
        <h2><i class="fas fa-layer-group"></i> Analisis Per-Perspektif</h2>
    </div>

    @if($pilars->isEmpty())
        <div class="text-center text-muted py-4">
            <em>Belum ada data perspektif untuk periode ini.</em>
        </div>
    @else
        <div class="dashboard-grid">
            @foreach($pilars as $index => $pilar)
            <div class="grid-span-4">
                <a href="{{ route('dataKinerja.pilar', ['id' => $pilar->id, 'tahun' => $tahun, 'bulan' => $bulan]) }}" style="text-decoration: none">
                    <div class="pilar-card pilar-{{ chr(97 + $index) }}">
                        <div class="pilar-header">
                            <h5 class="pilar-title">{{ strtoupper($pilar->nama) }}</h5>
                            <div class="pilar-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                        </div>
                        <div class="pilar-value">{{ number_format($pilar->nilai, 2) }}%</div>
                        <div class="pilar-description">Rata-rata capaian indikator</div>
                        <div class="pilar-progress">
                            <div class="pilar-progress-bar" style="width: {{ $pilar->nilai }}%"></div>
                        </div>
                        <div class="pilar-indicator-count">
                            <div class="indicator-stat">
                                <div class="indicator-stat-label">Jumlah Indikator</div>
                                <div class="indicator-stat-value">{{ $pilar->indikators_count ?? $pilar->jumlah_indikator ?? 0 }}</div>
                            </div>
                            <div class="indicator-stat">
                                <div class="indicator-stat-label">Tercapai</div>
                                <div class="indicator-stat-value">
                                    {{
                                        ($pilar->indikators_count ?? $pilar->jumlah_indikator ?? 0) > 0
                                            ? $pilar->indikators_tercapai ?? round(($pilar->nilai / 100) * ($pilar->jumlah_indikator ?? 0))
                                            : 0
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const progressBars = document.querySelectorAll('.pilar-progress-bar');
    progressBars.forEach(bar => {
        // Ambil persentase dari style.width
        const percent = parseFloat(bar.style.width);
        // Set warna sesuai ketentuan
        if (percent < 95) {
            bar.style.backgroundColor = '#e74a3b'; // merah
        } else if (percent >= 95 && percent < 100) {
            bar.style.backgroundColor = '#f6c23e'; // kuning
        } else if (percent >= 100) {
            bar.style.backgroundColor = '#1cc88a'; // hijau
        }
        // Animasi progres
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = percent + '%';
        }, 100);
    });
});
</script>
@endsection
