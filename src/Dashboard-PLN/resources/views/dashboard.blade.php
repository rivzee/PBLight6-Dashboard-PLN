@extends('layouts.app')

@section('title', 'Dashboard Kinerja PLN')
@section('page_title', 'DASHBOARD KINERJA')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboardUtama.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
  <!-- Stat Cards -->
  <div class="dashboard-row">
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">NKO Score</h3>
          <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
      </div>
        </div>
        <div class="stat-value">{{ $data['nko'] }}</div>
        <p class="stat-description">Nilai Kinerja Organisasi</p>
      </div>
    </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Target</h3>
          <div class="stat-icon">
            <i class="fas fa-bullseye"></i>
          </div>
        </div>
        <div class="stat-value">100</div>
        <p class="stat-description">Target Pencapaian</p>
      </div>
            </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Indikator</h3>
          <div class="stat-icon">
            <i class="fas fa-tasks"></i>
          </div>
        </div>
        <div class="stat-value">{{ count($data['pilar']) }}</div>
        <p class="stat-description">Pilar Penilaian</p>
      </div>
    </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Progress</h3>
          <div class="stat-icon">
            <i class="fas fa-spinner"></i>
          </div>
        </div>
        <div class="stat-value">{{ $data['nko'] }}%</div>
        <p class="stat-description">Persentase Pencapaian</p>
      </div>
    </div>
  </div>

  <!-- NKO Gauge Meter -->
  <div class="row align-items-center">
    <div class="col-md-3">
      <div class="meter-container">
        <canvas id="gaugeChart"></canvas>
        <div class="nko-label">NKO</div>
        <div class="nko-value">{{ $data['nko'] }}</div>
      </div>
    </div>

    <div class="col-md-9">
      <div class="pillar-container">
        @foreach($data['pilar'] as $index => $pilar)
        <div class="pillar-item">
          <div class="pillar-title">{{ $pilar['nama'] }}</div>
          <div class="pillar-value">{{ $pilar['nilai'] }}%</div>
          <div class="circle-progress">
            <canvas id="pilar{{ $index+1 }}Chart"></canvas>
            <div class="circle-progress-value">{{ $pilar['nilai'] }}%</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Detail Indicators -->
  <div class="details-grid">
    @foreach($data['pilar'] as $index => $pilar)
    <div class="detail-section">
      <h3 class="detail-title">
        <i class="fas fa-chart-pie"></i>
        {{ $pilar['nama'] }}
      </h3>

      @foreach($pilar['indikator'] as $indikator)
      <div class="progress-item">
        <div class="progress-label">
          <span class="progress-name">{{ $indikator['nama'] }}</span>
          <span class="progress-value">{{ $indikator['nilai'] }}<span class="progress-value-of"> dari target 100</span></span>
        </div>
        @php
          $progressClass = 'progress-red';
          if ($indikator['nilai'] >= 70) {
            $progressClass = 'progress-green';
          } elseif ($indikator['nilai'] >= 50) {
            $progressClass = 'progress-yellow';
          }
        @endphp
        <div class="progress">
          <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $indikator['nilai'] }}%"></div>
        </div>
      </div>
      @endforeach
    </div>
    @endforeach
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk membuat gauge chart
    function createGaugeChart() {
      const ctx = document.getElementById('gaugeChart').getContext('2d');

      // Nilai NKO dari data
      const nkoValue = {{ $data['nko'] }};

      // Tentukan warna berdasarkan nilai
      let gaugeColor = '#F44336'; // Red for low values
      if (nkoValue >= 70) {
        gaugeColor = '#4CAF50'; // Green for high values
      } else if (nkoValue >= 50) {
        gaugeColor = '#FFC107'; // Yellow for medium values
      }

      // Buat chart
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          datasets: [{
            data: [nkoValue, 100 - nkoValue],
            backgroundColor: [
              gaugeColor,
              'rgba(200, 200, 200, 0.1)'
            ],
            borderWidth: 0,
            circumference: 180,
            rotation: 270,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              enabled: false
            }
          }
        }
      });
    }

    // Fungsi untuk membuat circle chart untuk setiap pilar
    function createPilarCharts() {
      @foreach($data['pilar'] as $index => $pilar)
        const pilar{{ $index+1 }}Ctx = document.getElementById('pilar{{ $index+1 }}Chart').getContext('2d');
        const pilar{{ $index+1 }}Value = {{ $pilar['nilai'] }};

        // Tentukan warna berdasarkan nilai
        let pilar{{ $index+1 }}Color = '#F44336'; // Red for low values
        if (pilar{{ $index+1 }}Value >= 70) {
          pilar{{ $index+1 }}Color = '#4CAF50'; // Green for high values
        } else if (pilar{{ $index+1 }}Value >= 50) {
          pilar{{ $index+1 }}Color = '#FFC107'; // Yellow for medium values
        }

        new Chart(pilar{{ $index+1 }}Ctx, {
          type: 'doughnut',
          data: {
            datasets: [{
              data: [pilar{{ $index+1 }}Value, 100 - pilar{{ $index+1 }}Value],
              backgroundColor: [
                pilar{{ $index+1 }}Color,
                'rgba(200, 200, 200, 0.1)'
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                enabled: false
              }
            },
            animation: {
              animateRotate: true,
              animateScale: true
            }
          }
        });
      @endforeach
    }

    // Buat semua chart
    createGaugeChart();
    createPilarCharts();

    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-bar');
    setTimeout(() => {
      progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.width = width;
        }, 100);
      });
    }, 300);
  });
</script>
@endsection
