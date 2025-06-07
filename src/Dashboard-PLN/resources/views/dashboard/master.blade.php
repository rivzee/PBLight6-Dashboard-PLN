{{-- resources/views/dashboard/master.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Master Admin')
@section('page_title', 'DASHBOARD MASTER ADMIN')

@section('styles')
<style>
  .dashboard-content {
    max-width: 1800px;
    margin: 0 auto;
  }

  /* Stat Cards */
  .dashboard-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -12px;
  }

  .dashboard-col {
    flex: 1;
    padding: 0 12px;
    min-width: 250px;
  }

  .stat-card {
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
    border: 1px solid var(--pln-border);
    box-shadow: 0 8px 20px var(--pln-shadow);
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px var(--pln-shadow);
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .stat-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--pln-text);
    margin: 0;
  }

  .stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
    color: white;
    font-size: 20px;
    box-shadow: 0 5px 15px rgba(0, 156, 222, 0.3);
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--pln-text);
    margin: 15px 0 5px;
  }

  .stat-description {
    font-size: 13px;
    color: var(--pln-text-secondary);
    margin: 0;
  }

  /* NKO Gauge Meter */
  .meter-container {
    position: relative;
    width: 300px;
    height: 200px;
    margin: 0 auto 20px;
    transition: all 0.3s ease;
  }

  .meter-container:hover {
    transform: translateY(-5px);
  }

  .nko-value {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    color: var(--pln-text);
    text-shadow: 0 2px 5px var(--pln-shadow);
    transition: all 0.3s ease;
  }

  .nko-label {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 22px;
    font-weight: 600;
    color: var(--pln-light-blue);
    text-shadow: 0 2px 5px var(--pln-shadow);
    letter-spacing: 1px;
  }

  /* Dashboard Grid Layout */
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
    max-height: 1200px;
    overflow-y: auto;
  }

  .dashboard-grid::-webkit-scrollbar {
    width: 8px;
  }

  .dashboard-grid::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .dashboard-grid::-webkit-scrollbar-thumb {
    background: var(--pln-light-blue);
    border-radius: 10px;
  }

  .dashboard-card {
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid var(--pln-border);
    box-shadow: 0 8px 20px var(--pln-shadow);
    position: relative;
    overflow: hidden;
    max-height: 500px;
    overflow-y: auto;
  }

  .dashboard-card::-webkit-scrollbar {
    width: 8px;
  }

  .dashboard-card::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .dashboard-card::-webkit-scrollbar-thumb {
    background: var(--pln-light-blue);
    border-radius: 10px;
  }

  .dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
  }

  .card-title {
    font-size: 18px;
    color: var(--pln-light-blue);
    margin-bottom: 20px;
    font-weight: 600;
  }

  .gauge-card {
    grid-column: span 1;
  }

  .pilar-card {
    grid-column: span 2;
  }

  .full-width {
    grid-column: 1 / -1;
    height: 300px;
  }

  /* Pilar Container */
  .pillar-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: nowrap;
    margin: 15px 0;
    gap: 15px;
    overflow-x: auto;
    padding-bottom: 5px;
  }

  .pillar-container::-webkit-scrollbar {
    height: 5px;
  }

  .pillar-container::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
    border-radius: 10px;
  }

  .pillar-container::-webkit-scrollbar-thumb {
    background: var(--pln-light-blue);
    border-radius: 10px;
  }

  .pillar-item {
    flex: 0 0 auto;
    width: 150px;
    text-align: center;
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 15px 10px;
    transition: all 0.3s ease;
    border: 1px solid var(--pln-border);
    box-shadow: 0 4px 10px var(--pln-shadow);
    position: relative;
    overflow: hidden;
  }

  .pillar-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .pillar-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px var(--pln-shadow);
  }

  .pillar-item:hover::before {
    opacity: 1;
  }

  .pillar-title {
    font-size: 15px;
    margin-bottom: 8px;
    color: var(--pln-text);
    text-align: center;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .pillar-value {
    display: block;
    margin-top: 8px;
    font-size: 14px;
    color: var(--pln-light-blue);
    text-align: center;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  .circle-progress {
    width: 110px;
    height: 110px;
    margin: 0 auto;
    position: relative;
    transition: all 0.3s ease;
  }

  .pillar-item:hover .circle-progress {
    transform: scale(1.05);
  }

  .circle-progress-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    font-weight: 700;
    color: var(--pln-text);
    transition: all 0.3s ease;
  }

  /* Tasks */
  .tasks {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .task {
    background: var(--pln-accent-bg);
    padding: 15px;
    border-radius: 12px;
    flex: 1 1 280px;
    border: 1px solid var(--pln-border);
    box-shadow: 0 5px 15px var(--pln-shadow);
    transition: all 0.3s ease;
    color: var(--pln-text);
  }

  .task:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px var(--pln-shadow);
  }

  .task.highlight {
    border-left: 4px solid var(--pln-light-blue);
  }

  /* Media Queries for Responsiveness */
  @media (max-width: 1200px) {
    .dashboard-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    .gauge-card, .pilar-card {
      grid-column: span 1;
    }
    .pillar-container {
      flex-wrap: nowrap;
      overflow-x: auto;
      justify-content: flex-start;
      padding-bottom: 10px;
    }
    .pillar-item {
      flex: 0 0 130px;
    }
  }

  @media (max-width: 992px) {
    .dashboard-col {
      min-width: 200px;
    }
  }

  @media (max-width: 768px) {
    .dashboard-col {
      flex: 0 0 100%;
    }
    .dashboard-grid {
      grid-template-columns: 1fr;
    }
    .meter-container {
      width: 250px;
      height: 180px;
    }
  }
</style>
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
          <h3 class="stat-title">Indikator</h3>
          <div class="stat-icon">
            <i class="fas fa-tasks"></i>
          </div>
        </div>
        <div class="stat-value">{{ count($data['pilar']) * 4 }}</div>
        <p class="stat-description">Total Indikator</p>
      </div>
    </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Bidang</h3>
          <div class="stat-icon">
            <i class="fas fa-building"></i>
          </div>
        </div>
        <div class="stat-value">{{ count($data['pilar']) }}</div>
        <p class="stat-description">Total Bidang</p>
      </div>
    </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Terverifikasi</h3>
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
        </div>
        <div class="stat-value">{{ round($data['nko']) }}%</div>
        <p class="stat-description">Data Terverifikasi</p>
      </div>
    </div>
  </div>

  <!-- Tabs untuk filter status -->
  <ul class="nav nav-tabs" id="indikatorTabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="semua-tab" data-toggle="tab" href="#semua" role="tab">
        Semua <span class="badge badge-pill badge-secondary ml-1">{{ count($data['pilar']) }}</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="tinggi-tab" data-toggle="tab" href="#kinerja-tinggi" role="tab">
        Kinerja Tinggi <span class="badge badge-pill badge-success ml-1">
          {{ collect($data['pilar'])->where('nilai', '>=', 80)->count() }}
        </span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="rendah-tab" data-toggle="tab" href="#kinerja-rendah" role="tab">
        Perlu Perhatian <span class="badge badge-pill badge-warning ml-1">
          {{ collect($data['pilar'])->where('nilai', '<', 70)->count() }}
        </span>
      </a>
    </li>
  </ul>

  <!-- Dashboard Grid Layout -->
  <div class="dashboard-grid">
    <!-- NKO Gauge Meter Card -->
    <div class="dashboard-card gauge-card">
      <h3 class="card-title">
        <i class="fas fa-tachometer-alt mr-2"></i>
        Nilai Kinerja Organisasi
      </h3>
      <div class="meter-container">
        <canvas id="gaugeChart"></canvas>
        <div class="nko-label">NKO</div>
        <div class="nko-value">{{ $data['nko'] }}</div>
      </div>
    </div>

    <!-- Pilar Performance Card -->
    <div class="dashboard-card pilar-card">
      <h3 class="card-title">
        <i class="fas fa-chart-pie mr-2"></i>
        Nilai Kinerja Per Pilar
      </h3>
      <div class="pillar-container">
        @foreach($data['pilar'] as $index => $pilar)
        @if($index < 3)
        <div class="pillar-item">
          <div class="pillar-title">{{ strtoupper($pilar['nama']) }}</div>
          <div class="pillar-value">{{ $pilar['nilai'] }}%</div>
          <div class="circle-progress">
            <canvas id="pilar{{ $index+1 }}Chart"></canvas>
            <div class="circle-progress-value">{{ $pilar['nilai'] }}%</div>
          </div>
        </div>
        @endif
        @endforeach
      </div>
      <div class="pillar-container">
        @foreach($data['pilar'] as $index => $pilar)
        @if($index >= 3 && $index < 6)
        <div class="pillar-item">
          <div class="pillar-title">{{ strtoupper($pilar['nama']) }}</div>
          <div class="pillar-value">{{ $pilar['nilai'] }}%</div>
          <div class="circle-progress">
            <canvas id="pilar{{ $index+1 }}Chart"></canvas>
            <div class="circle-progress-value">{{ $pilar['nilai'] }}%</div>
          </div>
        </div>
        @endif
        @endforeach
      </div>
    </div>

    <!-- Tren Chart -->
    <div class="dashboard-card full-width">
      <h3 class="card-title">
        <i class="fas fa-chart-line mr-2"></i>
        Tren Nilai Kinerja Bulanan {{ $tahun }}
      </h3>
      <canvas id="trendChart"></canvas>
    </div>

    <!-- Tasks Overview -->
    <div class="dashboard-card">
      <h3 class="card-title">
        <i class="fas fa-clipboard-list mr-2"></i>
        Overview Tugas Penting
      </h3>
      <div class="tasks">
        <div class="task highlight">
          <h4>Verifikasi Data KPI</h4>
          <p>5 data menunggu verifikasi dari bidang Keuangan</p>
          <a href="#" class="btn btn-sm btn-primary">Verifikasi Sekarang</a>
        </div>
        <div class="task">
          <h4>Manajemen Akun</h4>
          <p>2 permintaan akun baru menunggu persetujuan</p>
          <a href="#" class="btn btn-sm btn-outline-primary">Kelola Akun</a>
        </div>
        <div class="task">
          <h4>Laporan Bulanan</h4>
          <p>Ekspor laporan KPI bulan {{ date('F', mktime(0, 0, 0, $bulan, 10)) }} {{ $tahun }}</p>
          <a href="#" class="btn btn-sm btn-outline-primary">Ekspor PDF</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Gauge Chart
    const gaugeCtx = document.getElementById('gaugeChart').getContext('2d');
    const nkoValue = {{ $data['nko'] }};

    // Determine color based on value
    let gaugeColor = '#F44336'; // Red for low values
    if (nkoValue >= 70) {
      gaugeColor = '#4CAF50'; // Green for high values
    } else if (nkoValue >= 50) {
      gaugeColor = '#FFC107'; // Yellow for medium values
    }

    new Chart(gaugeCtx, {
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

    // Pilar Charts
    const pilarValues = @json(array_column($data['pilar'], 'nilai'));
    const pilarColors = pilarValues.map(value => {
      if (value >= 70) return '#4CAF50';  // Green for high values
      else if (value >= 50) return '#FFC107';  // Yellow for medium values
      else return '#F44336';  // Red for low values
    });

    for (let i = 1; i <= pilarValues.length; i++) {
      const pilarCtx = document.getElementById(`pilar${i}Chart`);
      if (!pilarCtx) continue;

      const pilarValue = pilarValues[i-1];
      const pilarColor = pilarColors[i-1];

      new Chart(pilarCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
          datasets: [{
            data: [pilarValue, 100 - pilarValue],
            backgroundColor: [
              pilarColor,
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
    }

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    // Simulasikan data tren (ini dapat diganti dengan data aktual)
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const currentMonth = {{ $bulan }};
    const trendData = [];

    // Generate trend data up to current month
    for (let i = 1; i <= 12; i++) {
      if (i <= currentMonth) {
        // Generate random values within ±10% of final NKO value for past months
        const min = Math.max({{ $data['nko'] }} - 10, 0);
        const max = Math.min({{ $data['nko'] }} + 5, 100);
        const randomValue = (i < currentMonth)
          ? (Math.random() * (max - min) + min).toFixed(1)
          : {{ $data['nko'] }};
        trendData.push(randomValue);
      } else {
        trendData.push(null); // Future months have null values
      }
    }

    new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: months,
        datasets: [{
          label: 'Nilai Kinerja',
          data: trendData,
          backgroundColor: 'rgba(0, 156, 222, 0.2)',
          borderColor: '#009cde',
          borderWidth: 2,
          pointBackgroundColor: '#0a4d85',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            padding: 12,
            displayColors: false
          }
        },
        scales: {
          x: {
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: 'var(--pln-text-secondary)'
            }
          },
          y: {
            beginAtZero: true,
            max: 100,
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: 'var(--pln-text-secondary)',
              callback: function(value) {
                return value + '%';
              }
            }
          }
        }
      }
    });
  });
</script>
@endsection
