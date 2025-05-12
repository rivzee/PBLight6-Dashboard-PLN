@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin')
@section('page_title', 'DASHBOARD KINERJA ' . strtoupper($bidang->nama))

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

  /* Progress Gauge */
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

  /* Indikator Grid dan Tab */
  .indikator-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 0;
  }

  .indikator-card {
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 20px;
    transition: all 0.3s ease;
    border: 1px solid var(--pln-border);
    box-shadow: 0 8px 20px var(--pln-shadow);
    position: relative;
    overflow: hidden;
    height: 100%;
  }

  .indikator-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
  }

  .indikator-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px var(--pln-shadow);
  }

  .indikator-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .indikator-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--pln-light-blue);
    margin: 0;
  }

  .indikator-code {
    font-size: 12px;
    font-weight: 500;
    color: var(--pln-text-secondary);
    background: var(--pln-surface);
    padding: 5px 10px;
    border-radius: 8px;
  }

  .indikator-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--pln-text);
    margin: 15px 0;
  }

  .indikator-target {
    font-size: 14px;
    color: var(--pln-text-secondary);
    margin-bottom: 15px;
  }

  .progress {
    height: 10px;
    background-color: rgba(255,255,255,0.1);
    margin: 15px 0;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
  }

  [data-theme="light"] .progress {
    background-color: rgba(0,0,0,0.1);
  }

  .progress-bar {
    height: 100%;
    border-radius: 5px;
    transition: width 1s ease-in-out;
    background-size: 15px 15px;
    background-image: linear-gradient(
      45deg,
      rgba(255, 255, 255, 0.15) 25%,
      transparent 25%,
      transparent 50%,
      rgba(255, 255, 255, 0.15) 50%,
      rgba(255, 255, 255, 0.15) 75%,
      transparent 75%,
      transparent
    );
    animation: progress-animation 2s linear infinite;
  }

  @keyframes progress-animation {
    0% {
      background-position: 0 0;
    }
    100% {
      background-position: 30px 0;
    }
  }

  .progress-red {
    background-color: #F44336;
  }

  .progress-yellow {
    background-color: #FFC107;
  }

  .progress-green {
    background-color: #4CAF50;
  }

  .indikator-status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 10px;
  }

  .status-verified {
    background-color: rgba(76, 175, 80, 0.2);
    color: #4CAF50;
  }

  .status-unverified {
    background-color: rgba(255, 193, 7, 0.2);
    color: #FFC107;
  }

  /* Chart Container */
  .chart-container {
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid var(--pln-border);
    box-shadow: 0 8px 20px var(--pln-shadow);
    position: relative;
    overflow: hidden;
    margin-top: 30px;
    max-height: 450px;
    overflow-y: auto;
  }

  .chart-container::-webkit-scrollbar {
    width: 8px;
  }

  .chart-container::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .chart-container::-webkit-scrollbar-thumb {
    background: var(--pln-light-blue);
    border-radius: 10px;
  }

  .chart-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
  }

  .chart-title {
    font-size: 18px;
    color: var(--pln-light-blue);
    margin-bottom: 20px;
    font-weight: 600;
  }

  /* Tabs Styling */
  .nav-tabs {
    border-bottom: 1px solid var(--pln-border);
    margin-bottom: 25px;
  }

  .nav-tabs .nav-link {
    color: var(--pln-text-secondary);
    border: none;
    border-bottom: 3px solid transparent;
    background: transparent;
    padding: 12px 15px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .nav-tabs .nav-link:hover {
    color: var(--pln-light-blue);
  }

  .nav-tabs .nav-link.active {
    color: var(--pln-light-blue);
    border-bottom-color: var(--pln-light-blue);
    background: transparent;
  }

  .tab-content {
    padding-top: 15px;
    max-height: 600px;
    overflow-y: auto;
  }

  .tab-content::-webkit-scrollbar {
    width: 8px;
  }

  .tab-content::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .tab-content::-webkit-scrollbar-thumb {
    background: var(--pln-light-blue);
    border-radius: 10px;
  }

  .pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }

  .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .pagination li {
    margin: 0 5px;
  }

  .pagination a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: var(--pln-accent-bg);
    color: var(--pln-text);
    text-decoration: none;
    font-weight: 600;
    border: 1px solid var(--pln-border);
    transition: all 0.3s ease;
  }

  .pagination a:hover,
  .pagination a.active {
    background: var(--pln-light-blue);
    color: white;
  }

  .widget-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 30px;
  }

  @media (max-width: 992px) {
    .dashboard-col {
      min-width: 200px;
    }
    .widget-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .dashboard-col {
      flex: 0 0 100%;
    }
    .indikator-grid {
      grid-template-columns: 1fr;
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
          <h3 class="stat-title">Rata-rata Nilai</h3>
          <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
          </div>
        </div>
        <div class="stat-value">{{ $rataRata }}</div>
        <p class="stat-description">Kinerja Rata-rata Bidang</p>
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
        <div class="stat-value">{{ count($indikators) }}</div>
        <p class="stat-description">Jumlah Indikator</p>
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
        <div class="stat-value">{{ $rataRata }}%</div>
        <p class="stat-description">Persentase Pencapaian</p>
      </div>
    </div>
  </div>

  <!-- Widget Grid: Gauge & History Charts Side by Side -->
  <div class="widget-grid">
    <!-- Performance Gauge -->
    <div class="chart-container">
      <h3 class="chart-title">
        <i class="fas fa-tachometer-alt mr-2"></i>
        Kinerja {{ $bidang->nama }}
      </h3>
      <div class="meter-container">
        <canvas id="gaugeChart" height="180"></canvas>
        <div class="nko-label">Kinerja</div>
        <div class="nko-value">{{ $rataRata }}</div>
      </div>
    </div>

    <!-- Histori Chart -->
    <div class="chart-container">
      <h3 class="chart-title">
        <i class="fas fa-chart-line mr-2"></i>
        Histori Kinerja {{ $bidang->nama }} {{ $tahun }}
      </h3>
      <canvas id="historiChart" height="180"></canvas>
    </div>
  </div>

  <!-- Indikator Cards dengan Tab dan Pagination -->
  <div class="chart-container mt-4">
    <h3 class="chart-title">
      <i class="fas fa-list-ul mr-2"></i>
      Daftar Indikator {{ $bidang->nama }}
    </h3>

    <!-- Tabs untuk filter status -->
    <ul class="nav nav-tabs" id="indikatorTabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="semua-tab" data-toggle="tab" href="#semua" role="tab">
          Semua <span class="badge badge-pill badge-secondary ml-1">{{ count($indikators) }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="terverifikasi-tab" data-toggle="tab" href="#terverifikasi" role="tab">
          Terverifikasi <span class="badge badge-pill badge-success ml-1">{{ $indikators->where('diverifikasi', true)->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="belum-terverifikasi-tab" data-toggle="tab" href="#belum-terverifikasi" role="tab">
          Belum Terverifikasi <span class="badge badge-pill badge-warning ml-1">{{ $indikators->where('diverifikasi', false)->count() }}</span>
        </a>
      </li>
    </ul>

    <div class="tab-content" id="indikatorTabsContent">
      <!-- Tab Semua Indikator -->
      <div class="tab-pane fade show active" id="semua" role="tabpanel">
        <div class="indikator-grid">
          @foreach($indikators->take(6) as $indikator)
          <div class="indikator-card">
            <div class="indikator-header">
              <h3 class="indikator-title">{{ $indikator->nama }}</h3>
              <span class="indikator-code">{{ $indikator->kode }}</span>
            </div>
            <div class="indikator-value">{{ $indikator->nilai_persentase }}%</div>
            <div class="indikator-target">
              <strong>Target:</strong> {{ $indikator->target }} {{ $indikator->satuan }}
              <span class="ml-3"><strong>Realisasi:</strong> {{ $indikator->nilai_absolut }} {{ $indikator->satuan }}</span>
            </div>

            @php
              $progressClass = 'progress-red';
              if ($indikator->nilai_persentase >= 70) {
                $progressClass = 'progress-green';
              } elseif ($indikator->nilai_persentase >= 50) {
                $progressClass = 'progress-yellow';
              }
            @endphp
            <div class="progress">
              <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $indikator->nilai_persentase }}%"></div>
            </div>

            <div>
              @if($indikator->diverifikasi)
                <span class="indikator-status status-verified">
                  <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                </span>
              @else
                <span class="indikator-status status-unverified">
                  <i class="fas fa-clock mr-1"></i> Belum Diverifikasi
                </span>
              @endif

              <a href="{{ route('realisasi.create', ['indikator_id' => $indikator->id, 'tahun' => $tahun, 'bulan' => $bulan]) }}" class="btn btn-sm btn-primary float-right">
                <i class="fas fa-edit"></i> Input
              </a>
            </div>
          </div>
          @endforeach
        </div>

        @if(count($indikators) > 6)
        <div class="pagination-container">
          <ul class="pagination">
            <li><a href="#" class="active">1</a></li>
            <li><a href="#">2</a></li>
            @if(count($indikators) > 12)
            <li><a href="#">3</a></li>
            @endif
            <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
          </ul>
        </div>
        @endif
      </div>

      <!-- Tab Terverifikasi -->
      <div class="tab-pane fade" id="terverifikasi" role="tabpanel">
        <div class="indikator-grid">
          @foreach($indikators->where('diverifikasi', true)->take(6) as $indikator)
          <div class="indikator-card">
            <div class="indikator-header">
              <h3 class="indikator-title">{{ $indikator->nama }}</h3>
              <span class="indikator-code">{{ $indikator->kode }}</span>
            </div>
            <div class="indikator-value">{{ $indikator->nilai_persentase }}%</div>
            <div class="indikator-target">
              <strong>Target:</strong> {{ $indikator->target }} {{ $indikator->satuan }}
              <span class="ml-3"><strong>Realisasi:</strong> {{ $indikator->nilai_absolut }} {{ $indikator->satuan }}</span>
            </div>

            @php
              $progressClass = 'progress-red';
              if ($indikator->nilai_persentase >= 70) {
                $progressClass = 'progress-green';
              } elseif ($indikator->nilai_persentase >= 50) {
                $progressClass = 'progress-yellow';
              }
            @endphp
            <div class="progress">
              <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $indikator->nilai_persentase }}%"></div>
            </div>

            <div>
              <span class="indikator-status status-verified">
                <i class="fas fa-check-circle mr-1"></i> Terverifikasi
              </span>

              <a href="{{ route('realisasi.create', ['indikator_id' => $indikator->id, 'tahun' => $tahun, 'bulan' => $bulan]) }}" class="btn btn-sm btn-primary float-right">
                <i class="fas fa-edit"></i> Input
              </a>
            </div>
          </div>
          @endforeach
        </div>

        @if($indikators->where('diverifikasi', true)->count() > 6)
        <div class="pagination-container">
          <ul class="pagination">
            <li><a href="#" class="active">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
          </ul>
        </div>
        @endif
      </div>

      <!-- Tab Belum Terverifikasi -->
      <div class="tab-pane fade" id="belum-terverifikasi" role="tabpanel">
        <div class="indikator-grid">
          @foreach($indikators->where('diverifikasi', false)->take(6) as $indikator)
          <div class="indikator-card">
            <div class="indikator-header">
              <h3 class="indikator-title">{{ $indikator->nama }}</h3>
              <span class="indikator-code">{{ $indikator->kode }}</span>
            </div>
            <div class="indikator-value">{{ $indikator->nilai_persentase }}%</div>
            <div class="indikator-target">
              <strong>Target:</strong> {{ $indikator->target }} {{ $indikator->satuan }}
              <span class="ml-3"><strong>Realisasi:</strong> {{ $indikator->nilai_absolut }} {{ $indikator->satuan }}</span>
            </div>

            @php
              $progressClass = 'progress-red';
              if ($indikator->nilai_persentase >= 70) {
                $progressClass = 'progress-green';
              } elseif ($indikator->nilai_persentase >= 50) {
                $progressClass = 'progress-yellow';
              }
            @endphp
            <div class="progress">
              <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $indikator->nilai_persentase }}%"></div>
            </div>

            <div>
              <span class="indikator-status status-unverified">
                <i class="fas fa-clock mr-1"></i> Belum Diverifikasi
              </span>

              <a href="{{ route('realisasi.create', ['indikator_id' => $indikator->id, 'tahun' => $tahun, 'bulan' => $bulan]) }}" class="btn btn-sm btn-primary float-right">
                <i class="fas fa-edit"></i> Input
              </a>
            </div>
          </div>
          @endforeach
        </div>

        @if($indikators->where('diverifikasi', false)->count() > 6)
        <div class="pagination-container">
          <ul class="pagination">
            <li><a href="#" class="active">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
          </ul>
        </div>
        @endif
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
    const rataRata = {{ $rataRata }};

    // Determine color based on value
    let gaugeColor = '#F44336'; // Red for low values
    if (rataRata >= 70) {
      gaugeColor = '#4CAF50'; // Green for high values
    } else if (rataRata >= 50) {
      gaugeColor = '#FFC107'; // Yellow for medium values
    }

    new Chart(gaugeCtx, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [rataRata, 100 - rataRata],
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
        maintainAspectRatio: true,
        cutout: '70%',
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
          animateScale: false,
          duration: 1000
        }
      }
    });

    // History Chart
    const historiCtx = document.getElementById('historiChart').getContext('2d');
    const historiData = @json($historiData);

    new Chart(historiCtx, {
      type: 'line',
      data: {
        labels: historiData.map(d => d.bulan),
        datasets: [{
          label: 'Nilai Kinerja',
          data: historiData.map(d => d.nilai),
          backgroundColor: 'rgba(0, 156, 222, 0.2)',
          borderColor: '#009cde',
          borderWidth: 2,
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
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
        },
        animation: {
          duration: 1000
        }
      }
    });

    // Inisialisasi lazy loading untuk tab content
    document.querySelectorAll('.nav-tabs .nav-link').forEach(function(tab) {
      tab.addEventListener('click', function() {
        // Allow DOM to update before calculating layouts
        setTimeout(function() {
          window.dispatchEvent(new Event('resize'));
        }, 50);
      });
    });
  });
</script>
@endsection
