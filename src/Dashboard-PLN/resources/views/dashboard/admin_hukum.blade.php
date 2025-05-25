@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin Hukum')
@section('page_title', 'DASHBOARD KINERJA ' . strtoupper($bidang->nama ?? 'HUKUM'))

@section('content')
<div class="container-fluid px-4">
  <div class="dashboard-content">
    <!-- Widget Grid: Gauge & History Charts Side by Side -->
    <div class="widget-grid">
      <!-- Performance Gauge -->
      <div class="chart-container">
        <h3 class="chart-title">
          <i class="fas fa-tachometer-alt mr-2"></i>
          Kinerja {{ $bidang->nama ?? 'Hukum' }}
        </h3>
        <div class="meter-container">
          <canvas id="gaugeChart" height="180"></canvas>
          <div class="nko-label">Kinerja</div>
          <div class="nko-value">{{ $rataRata }}%</div>
        </div>
      </div>

      <!-- KPI Summary -->
      <div class="chart-container">
        <h3 class="chart-title">
          <i class="fas fa-chart-line mr-2"></i>
          Ringkasan KPI
        </h3>
        <div class="kpi-summary p-3">
          <div class="kpi-stat">
            <div class="kpi-number">{{ $indikators->count() }}</div>
            <div class="kpi-label">Total Indikator</div>
          </div>
          <div class="kpi-stat">
            <div class="kpi-number">{{ $indikators->where('nilai_persentase', '>=', 90)->count() }}</div>
            <div class="kpi-label">Tercapai (≥ 90%)</div>
          </div>
          <div class="kpi-stat">
            <div class="kpi-number">{{ $indikators->where('diverifikasi', true)->count() }}</div>
            <div class="kpi-label">Terverifikasi</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Status Dokumen -->
    <div class="chart-container mt-4">
      <h3 class="chart-title">
        <i class="fas fa-chart-pie mr-2"></i>
        Status Dokumen Hukum
      </h3>
      <div class="chart-wrapper">
        <canvas id="documentStatusChart" height="250"></canvas>
      </div>
    </div>

    <!-- Indikator Grid -->
    <h3 class="section-title mt-4">
      <i class="fas fa-list-alt mr-2"></i>
      Indikator Kinerja {{ $bidang->nama ?? 'Hukum' }}
    </h3>

    <div class="indikator-grid">
      @foreach($indikators as $indikator)
      <div class="indikator-card">
        <div class="indikator-header">
          <h4 class="indikator-title">{{ $indikator->nama }}</h4>
          <span class="indikator-code">{{ $indikator->kode }}</span>
        </div>

        <div class="indikator-value">{{ $indikator->nilai_persentase }}%</div>

        <div class="indikator-target">
          Target: {{ $indikator->target }} {{ $indikator->satuan }} |
          Realisasi: {{ $indikator->nilai_absolut }} {{ $indikator->satuan }}
        </div>

        <div class="progress indikator-progress">
          <div class="progress-bar" role="progressbar"
            style="width: {{ $indikator->nilai_persentase }}%;"
            aria-valuenow="{{ $indikator->nilai_persentase }}"
            aria-valuemin="0"
            aria-valuemax="100">
          </div>
        </div>

        <div class="indikator-status mt-2">
          @if($indikator->diverifikasi)
          <span class="status-badge verified">
            <i class="fas fa-check-circle"></i> Terverifikasi
          </span>
          @else
          <span class="status-badge unverified">
            <i class="fas fa-clock"></i> Menunggu Verifikasi
          </span>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    <!-- Target dan Tugas -->
    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-list-check mr-2"></i>
            Target Bulanan
          </h3>
          <div class="p-3">
            <ul class="target-list">
              <li class="completed">
                <div class="target-detail">
                  <span class="target-name">Review Dokumen Legal</span>
                  <span class="target-date">12 Mei 2023</span>
                </div>
              </li>
              <li class="completed">
                <div class="target-detail">
                  <span class="target-name">Update Regulasi Kepatuhan</span>
                  <span class="target-date">15 Mei 2023</span>
                </div>
              </li>
              <li>
                <div class="target-detail">
                  <span class="target-name">Persiapan Kontrak Vendor</span>
                  <span class="target-date">28 Mei 2023</span>
                </div>
              </li>
              <li>
                <div class="target-detail">
                  <span class="target-name">Pembaruan SOP</span>
                  <span class="target-date">5 Juni 2023</span>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-spinner mr-2"></i>
            Tugas Dalam Proses
          </h3>
          <div class="p-3">
            <div class="task-item">
              <div class="task-header">
                <span class="task-title">Pemeriksaan Draft Kontrak</span>
                <span class="task-badge">Prioritas Tinggi</span>
              </div>
              <div class="task-progress">
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="progress-text">65% selesai</span>
              </div>
            </div>
            <div class="task-item">
              <div class="task-header">
                <span class="task-title">Konsultasi Legal Internal</span>
                <span class="task-badge">Sedang Berjalan</span>
              </div>
              <div class="task-progress">
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="progress-text">30% selesai</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  .dashboard-content {
    max-width: 1800px;
    margin: 0 auto;
  }

  /* Widget Grid for main charts */
  .widget-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-top: 20px;
  }

  @media (max-width: 992px) {
    .widget-grid {
      grid-template-columns: 1fr;
    }
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

  /* KPI Summary */
  .kpi-summary {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 10px;
    height: 100%;
    align-items: center;
  }

  .kpi-stat {
    text-align: center;
    padding: 15px;
    background: var(--pln-surface);
    border-radius: 12px;
    min-width: 120px;
    transition: all 0.3s ease;
  }

  .kpi-stat:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px var(--pln-shadow);
  }

  .kpi-number {
    font-size: 28px;
    font-weight: 700;
    color: var(--pln-light-blue);
    margin-bottom: 5px;
  }

  .kpi-label {
    font-size: 12px;
    color: var(--pln-text-secondary);
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

  .chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px var(--pln-shadow);
  }

  .chart-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--pln-text);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
  }

  .chart-title i {
    margin-right: 10px;
    color: var(--pln-light-blue);
  }

  .chart-wrapper {
    padding: 20px 0;
  }

  /* Section Title */
  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--pln-text);
    margin: 40px 0 20px;
    display: flex;
    align-items: center;
  }

  .section-title i {
    margin-right: 10px;
    color: var(--pln-light-blue);
  }

  /* Indikator Grid */
  .indikator-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 20px;
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

  .indikator-progress {
    height: 8px;
    background-color: var(--pln-surface-2);
    border-radius: 10px;
    overflow: hidden;
  }

  .status-badge {
    display: inline-block;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 8px;
  }

  .status-badge.verified {
    background-color: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
  }

  .status-badge.unverified {
    background-color: rgba(255, 193, 7, 0.1);
    color: #FFC107;
  }

  /* Dashboard Row and Columns */
  .dashboard-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -12px;
  }

  .dashboard-col {
    flex: 1;
    padding: 0 12px;
    min-width: 300px;
  }

  /* Targets */
  .target-list {
    list-style-type: none;
    padding-left: 0;
  }

  .target-list li {
    padding: 12px 15px;
    position: relative;
    color: var(--pln-text-secondary);
    border-left: 3px solid var(--pln-surface-2);
    margin-bottom: 10px;
    background: var(--pln-surface);
    border-radius: 0 8px 8px 0;
    transition: all 0.3s ease;
  }

  .target-list li.completed {
    color: var(--pln-text);
    border-left: 3px solid var(--pln-light-blue);
  }

  .target-list li:hover {
    transform: translateX(5px);
  }

  .target-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .target-name {
    font-weight: 500;
  }

  .target-date {
    font-size: 12px;
    color: var(--pln-text-secondary);
  }

  .target-list li.completed::before {
    content: "✓";
    position: absolute;
    left: -20px;
    color: var(--pln-light-blue);
    font-weight: bold;
  }

  /* Tasks */
  .task-item {
    background: var(--pln-surface);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    border-left: 3px solid var(--pln-light-blue);
  }

  .task-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--pln-shadow);
  }

  .task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }

  .task-title {
    font-weight: 500;
  }

  .task-badge {
    background: rgba(0, 156, 222, 0.1);
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    color: var(--pln-light-blue);
  }

  .task-progress {
    margin-top: 8px;
  }

  .progress-text {
    display: block;
    font-size: 12px;
    color: var(--pln-text-secondary);
    margin-top: 5px;
  }
</style>
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
    if (rataRata >= 90) {
      gaugeColor = '#4CAF50'; // Green for high values
    } else if (rataRata >= 70) {
      gaugeColor = '#2196F3'; // Blue for medium-high values
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
        }]
      },
      options: {
        cutout: '70%',
        responsive: true,
        maintainAspectRatio: false,
        circumference: 180,
        rotation: -90,
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

    // Chart status dokumen
    const statusCtx = document.getElementById('documentStatusChart').getContext('2d');
    new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Selesai', 'Dalam Proses', 'Menunggu Review', 'Ditolak'],
        datasets: [{
          data: [6, 4, 2, 1],
          backgroundColor: [
            '#4CAF50',
            '#2196F3',
            '#FFC107',
            '#F44336'
          ],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              color: document.querySelector('html').getAttribute('data-theme') === 'light' ? '#333' : '#f8fafc',
              padding: 20,
              font: {
                size: 12
              }
            }
          }
        },
        cutout: '70%'
      }
    });
  });
</script>
@endsection
