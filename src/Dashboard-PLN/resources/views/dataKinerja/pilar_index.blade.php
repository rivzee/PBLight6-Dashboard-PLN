@extends('layouts.app')

@section('title', 'Data Kinerja - Analisis Pilar')

@section('styles')
<style>
    .dashboard-content {
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-gap: 20px;
    }

    .grid-span-4 {
        grid-column: span 4;
    }

    @media (max-width: 992px) {
        .grid-span-4 {
            grid-column: span 6;
        }
    }

    @media (max-width: 768px) {
        .grid-span-4 {
            grid-column: span 12;
        }
    }

    .pilar-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: 0.3s;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .pilar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .pilar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: #007bff;
    }

    .pilar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .pilar-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .pilar-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #007bff;
        color: white;
        font-size: 22px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .pilar-value {
        font-size: 32px;
        font-weight: 700;
        color: #007bff;
        margin: 15px 0 5px;
    }

    .pilar-description {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .pilar-progress {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .pilar-progress-bar {
        height: 100%;
        background: #007bff;
        border-radius: 4px;
        transition: width 0.5s ease-in-out;
    }

    .pilar-indicator-count {
        display: flex;
        justify-content: space-between;
    }

    .indicator-stat {
        text-align: center;
        padding: 10px;
        border-radius: 8px;
        background: rgba(0, 0, 0, 0.03);
        flex: 1;
        margin: 0 5px;
    }

    .indicator-stat-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .indicator-stat-value {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="section-divider mb-4">
        <h2><i class="fas fa-layer-group"></i> Analisis Per-Pilar</h2>
    </div>

    @if($pilars->isEmpty())
        <div class="text-center text-muted py-4">
            <em>Belum ada data pilar untuk periode ini.</em>
        </div>
    @else
        <div class="dashboard-grid">
            @foreach($pilars as $index => $pilar)
            <div class="grid-span-4">
                <a href="{{ route('dataKinerja.pilar', $pilar->nama) }}" style="text-decoration: none">
                    <div class="pilar-card pilar-{{ chr(97 + $index) }}">
                        <div class="pilar-header">
                            <h5 class="pilar-title">{{ strtoupper($pilar->nama) }}</h5>
                            <div class="pilar-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                        </div>
                        <div class="pilar-value">{{ $pilar->nilai }}%</div>
                        <div class="pilar-description">Rata-rata capaian indikator</div>
                        <div class="pilar-progress">
                            <div class="pilar-progress-bar" style="width: {{ $pilar->nilai }}%"></div>
                        </div>
                        <div class="pilar-indicator-count">
                            <div class="indicator-stat">
                                <div class="indicator-stat-label">Jumlah Indikator</div>
                                <div class="indicator-stat-value">{{ $pilar->jumlah_indikator }}</div>
                            </div>
                            <div class="indicator-stat">
                                <div class="indicator-stat-label">Tercapai</div>
                                <div class="indicator-stat-value">
                                    {{
                                        $pilar->jumlah_indikator > 0
                                            ? round(($pilar->nilai / 100) * $pilar->jumlah_indikator)
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
        const target = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = target;
        }, 100);
    });
});
</script>
@endsection
