{{-- resources/views/dashboard/master.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Admin Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="container-xl px-4 py-4">
  <div class="row mb-4">
    <div class="col-12">
      <div class="section-divider">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Master Admin</h2>
      </div>
      <p class="text-muted">Selamat datang di dashboard master admin. Silahkan pilih tab untuk melihat data.</p>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0">
            <i class="fas fa-filter mr-2"></i> Filter Data
          </h5>
        </div>
        <div class="card-body">
          <form action="{{ route('dashboard.master') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <div class="form-group mb-0 mr-2">
              <label for="tahun" class="form-label small mb-1">Tahun:</label>
              <select name="tahun" id="tahun" class="form-select form-select-sm">
                @foreach(range(date('Y') - 5, date('Y') + 1) as $year)
                  <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group mb-0 mr-2">
              <label for="bulan" class="form-label small mb-1">Bulan:</label>
              <select name="bulan" id="bulan" class="form-select form-select-sm">
                @foreach(range(1, 12) as $month)
                  <option value="{{ $month }}" {{ $bulan == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label opacity-0 d-block small mb-1">Action:</label>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter fa-sm mr-1"></i> Filter
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Informasi Input Progress -->
  <div class="dashboard-grid mb-4">
    <div class="grid-span-12">
      <div class="card">
        <div class="card-header bg-info text-white">
          <h5 class="card-title mb-0">
            <i class="fas fa-info-circle mr-2"></i> Status Input Data Bulan {{ date('F', mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            @php
              $totalIndikator = collect($data['pilar'] ?? [])->sum('total_indikator');
              $totalInput = collect($data['pilar'] ?? [])->sum('jumlah_input');
              $persentaseInput = $totalIndikator > 0 ? round(($totalInput / $totalIndikator) * 100, 1) : 0;
            @endphp
            <div class="col-md-3">
              <div class="text-center">
                <h4 class="text-primary mb-1">{{ $totalInput }}</h4>
                <p class="text-muted mb-0">Indikator Terinput</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center">
                <h4 class="text-secondary mb-1">{{ $totalIndikator }}</h4>
                <p class="text-muted mb-0">Total Indikator</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center">
                <h4 class="text-info mb-1">{{ $persentaseInput }}%</h4>
                <p class="text-muted mb-0">Progress Input</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center">
                <h4 class="text-success mb-1">
                  @if(isset($data['nko']) && $data['nko'] > 0)
                    {{ number_format($data['nko'], 1) }}%
                  @else
                    0%
                  @endif
                </h4>
                <p class="text-muted mb-0">Rata-rata Kinerja</p>
              </div>
            </div>
          </div>

            @if($persentaseInput < 100)
            <div class="alert alert-warning alert-sm py-2 px-3 mt-3 mb-0 d-flex align-items-center" style="font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle me-2" style="font-size: 1rem;"></i>
                <div>
                <strong>Perhatian:</strong> Masih ada {{ $totalIndikator - $totalInput }} indikator yang belum diinput untuk bulan ini.
                Nilai kinerja pilar dihitung berdasarkan rata-rata dari semua indikator (termasuk yang belum diinput = 0%).
                </div>
            </div>
            @endif

        </div>
      </div>
    </div>
  </div>


    <!-- Dashboard Overview -->
    <div class="dashboard-grid">
    <div class="grid-span-6">
        <div class="card chart-card p-0 overflow-hidden">
        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
            <i class="fas fa-chart-pie mr-2"></i>
            <h5 class="mb-0">Gauge NKO</h5>
        </div>

        <!-- Gauge Content -->
        <div class="d-flex flex-column align-items-center justify-content-center py-4">
            <div style="position: relative; height: 250px; width: 250px;">
            <canvas id="gaugeChart"></canvas>
            <div id="gauge-center-text" style="position: absolute; top: 60%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                <h3 class="mb-0 fw-bold">{{ $data['nko'] }}%</h3>
                <p class="mb-0 small text-muted">NKO Score</p>
            </div>
            </div>
        </div>
        </div>
    </div>

        <div class="grid-span-6">
        <div class="card chart-card p-0 overflow-hidden">
            <!-- Header -->
        <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
            <i class="fas fa-chart-line"></i>
            <h5 class="mb-0">Trend Kinerja {{ $tahun }}</h5>
            </div>

            <!-- Chart Content -->
        <div class="chart-container w-100" style="height: 350px; padding: 1.5rem;">
            <canvas id="trendChart"></canvas>
            </div>
        </div>
        </div>
    </div>


  <!-- Tab Navigation -->
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab" aria-controls="semua" aria-selected="true">
        <i class="fas fa-table me-2"></i>Semua Perspektif
        <span class="badge badge-secondary">{{ count($data['pilar'] ?? []) }}</span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tinggi-tab" data-bs-toggle="tab" data-bs-target="#kinerja-tinggi" type="button" role="tab" aria-controls="kinerja-tinggi" aria-selected="false">
        <i class="fas fa-arrow-up me-2"></i>Kinerja Tinggi
        <span class="badge badge-success">{{ collect($data['pilar'] ?? [])->where('nilai', '>=', 100)->count() }}</span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="rendah-tab" data-bs-toggle="tab" data-bs-target="#kinerja-rendah" type="button" role="tab" aria-controls="kinerja-rendah" aria-selected="false">
        <i class="fas fa-arrow-down me-2"></i>Perlu Perhatian
        <span class="badge badge-warning">{{ collect($data['pilar'] ?? [])->where('nilai', '<=', 100)->count() }}</span>
      </button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="myTabContent">
    <!-- Tab Semua Pilar -->
    <div class="tab-pane fade show active" id="semua" role="tabpanel" aria-labelledby="semua-tab">
      <div class="dashboard-grid">
        <!-- Perbandingan Antar Perspektif -->
            <div class="grid-span-6">
            <div class="card chart-card p-0 overflow-hidden">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-chart-radar me-2"></i>
                <h5 class="mb-0">Perbandingan Antar Perspektif</h5>
                </div>
                <div class="d-flex justify-content-center align-items-center py-4">
                <div style="width: 100%; max-width: 500px; height: 500px; position: relative;">
                    <canvas id="radarChart"></canvas>
                </div>
                </div>
            </div>
            </div>

            <!-- Daftar Semua Perspektif -->
            <div class="grid-span-6">
            <div class="card chart-card p-0 overflow-hidden">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-table me-2"></i>
                <h5 class="mb-0">Daftar Semua Perspektif</h5>
                </div>
                <div class="table-responsive p-3">
                <table class="data-table table">
                    <thead>
                    <tr>
                        <th>Nama Pilar</th>
                        <th>Nilai (%)</th>
                        <th>Progress Input</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($data['pilar'] ?? [] as $index => $pilar)
                        <tr class="data-row">
                        <td>{{ $pilar['nama'] ?? 'Tidak dikenal' }}</td>
                        <td>
                            @if(($pilar['nilai'] ?? 0) >= 100)
                            <strong class="text-success">{{ number_format($pilar['nilai'] ?? 0, 2) }}%</strong>
                            @elseif(($pilar['nilai'] ?? 0) >= 95)
                            <strong class="text-warning">{{ number_format($pilar['nilai'] ?? 0, 2) }}%</strong>
                            @else
                            <strong class="text-danger">{{ number_format($pilar['nilai'] ?? 0, 2) }}%</strong>
                            @endif
                        </td>
                        <td>
                            @php
                            $jumlahInput = $pilar['jumlah_input'] ?? 0;
                            $totalIndikator = $pilar['total_indikator'] ?? 1;
                            $persentaseInput = $totalIndikator > 0 ? round(($jumlahInput / $totalIndikator) * 100, 1) : 0;
                            @endphp
                            <div class="d-flex align-items-center">
                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $persentaseInput }}%"></div>
                            </div>
                            <small class="text-muted">{{ $jumlahInput }}/{{ $totalIndikator }}</small>
                            </div>
                        </td>
                        <td>
                            @if(($pilar['nilai'] ?? 0) >= 110)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle mr-1"></i> Kinerja Tinggi
                            </span>
                            @elseif(($pilar['nilai'] ?? 0) >= 100)
                            <span class="badge badge-primary">
                                <i class="fas fa-info-circle mr-1"></i> Kinerja Baik
                            </span>
                            @elseif(($pilar['jumlah_input'] ?? 0) == 0)
                            <span class="badge badge-secondary">
                                <i class="fas fa-minus-circle mr-1"></i> Belum Ada Input
                            </span>
                            @else
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-circle mr-1"></i> Perlu Perhatian
                            </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dataKinerja.pilar', $index + 1) }}" class="btn btn-xs btn-primary px-2 py-1" style="font-size: 0.75rem;">
                            <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="empty-state">
                            <i class="fas fa-database text-muted mb-3" style="font-size: 2.5rem;"></i>
                            <p class="text-muted">Tidak ada data pilar untuk ditampilkan.</p>
                            </div>
                        </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            </div>

      </div>
    </div>

    <!-- Tab Kinerja Tinggi -->
    <div class="tab-pane fade" id="kinerja-tinggi" role="tabpanel" aria-labelledby="tinggi-tab">
      <div class="dashboard-grid">
        <div class="grid-span-12">
          <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Perspektif dengan Kinerja Tinggi</h3>
            <div class="chart-container medium">
              <canvas id="highPerformanceChart"></canvas>
            </div>
            <div class="table-responsive mt-4">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Perspektif</th>
                    <th>Nilai (%)</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(collect($data['pilar'] ?? [])->where('nilai', '>=', 100) as $index => $pilar)
                  <tr class="data-row">
                    <td>{{ $pilar['nama'] }}</td>
                    <td>
                      <strong class="text-success">{{ $pilar['nilai'] }}%</strong>
                      <span class="performance-indicator high">
                        <i class="fas fa-arrow-up"></i> {{ $pilar['nilai'] }}
                      </span>
                    </td>
                    <td>
                      <span class="badge badge-success">
                        <i class="fas fa-check-circle mr-1"></i> Kinerja Tinggi
                      </span>
                    </td>
                    <td>
                      @php
                        $pilarId = collect($data['pilar'] ?? [])->search(function($p) use ($pilar) {
                          return $p['nama'] == $pilar['nama'];
                        });
                        $pilarId = $pilarId !== false ? $pilarId + 1 : 1;
                      @endphp
                      <a href="{{ route('dataKinerja.pilar', $pilarId) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye mr-1"></i> Detail
                      </a>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center py-4">
                      <div class="empty-state">
                        <i class="fas fa-chart-line text-muted mb-3" style="font-size: 2.5rem;"></i>
                        <p class="text-muted">Tidak ada Perspektif dengan kinerja tinggi saat ini.</p>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Perlu Perhatian -->
    <div class="tab-pane fade" id="kinerja-rendah" role="tabpanel" aria-labelledby="rendah-tab">
      <div class="dashboard-grid">
        <div class="grid-span-12">
          <div class="card chart-card">
            <h3 class="chart-title"><i class="fas fa-exclamation-triangle"></i> Perspektif yang Perlu Perhatian</h3>
            <div class="chart-container medium">
              <canvas id="lowPerformanceChart"></canvas>
            </div>
            <div class="table-responsive mt-4">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Perspektif</th>
                    <th>Nilai (%)</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(collect($data['pilar'] ?? [])->where('nilai', '<', 100) as $index => $pilar)
                  <tr class="data-row">
                    <td>{{ $pilar['nama'] }}</td>
                    <td>
                      <strong class="text-warning">{{ $pilar['nilai'] }}%</strong>
                      {{-- <span class="performance-indicator low">
                        <i class="fas fa-arrow-down"></i> {{ $pilar['nilai'] }}
                      </span> --}}
                    </td>
                    <td>
                      <span class="badge badge-warning">
                        <i class="fas fa-exclamation-circle mr-1"></i> Perlu Perhatian
                      </span>
                    </td>
                    <td>
                      @php
                        $pilarId = collect($data['pilar'] ?? [])->search(function($p) use ($pilar) {
                          return $p['nama'] == $pilar['nama'];
                        });
                        $pilarId = $pilarId !== false ? $pilarId + 1 : 1;
                      @endphp
                      <a href="{{ route('dataKinerja.pilar', $pilarId) }}" class="btn btn-primary btn-xs">
                        <i class="fas fa-eye mr-1"></i> Detail
                      </a>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center py-4">
                      <div class="empty-state">
                        <i class="fas fa-check-circle text-muted mb-3" style="font-size: 2.5rem;"></i>
                        <p class="text-muted">Tidak ada pilar yang perlu perhatian saat ini.</p>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Section: Kinerja Tertinggi dan Overview Tugas -->
  <div class="dashboard-grid">
    <div class="grid-span-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title"><i class="fas fa-trophy"></i> Kinerja Tertinggi</h5>
          <div>
            <button class="btn btn-sm btn-light" id="refreshTopPerformers">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            @if(isset($poorPerformers) && count($poorPerformers) > 0)
              @foreach($poorPerformers as $performer)
                <div class="col-md-6">
                  <div class="top-performer-card">
                    <div class="top-performer-header">
                      <h6 class="top-performer-title">{{ $performer->indikator->nama }}</h6>
                      <span class="top-performer-value">{{ $performer->persentase }}%</span>
                    </div>
                    <div class="top-performer-info">
                      <span class="badge bg-info">{{ $performer->indikator->bidang->nama }}</span>
                      <span class="text-muted ml-2">Kode: {{ $performer->indikator->kode }}</span>
                    </div>
                  </div>
                </div>
              @endforeach
            @else
              <div class="col-12">
                <div class="alert alert-info">
                  Tidak ada data kinerja tertinggi yang tersedia.
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="grid-span-4">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title"><i class="fas fa-tasks"></i> Overview Tugas</h5>
        </div>
        <div class="card-body">
          <div class="task-overview">
            @if(isset($needVerification) && count($needVerification) > 0)
              @foreach($needVerification as $task)
                <div class="task-item">
                  <div class="task-icon pending">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="task-content">
                    <h6 class="task-title">{{ $task->indikator->nama }}</h6>
                    <div class="task-meta">
                      <span><i class="fas fa-user"></i> {{ $task->user->name }}</span>
                      <span><i class="fas fa-calendar"></i> {{ $task->created_at->format('d M Y') }}</span>
                    </div>
                  </div>
                  <span class="task-status pending">Menunggu</span>
                </div>
              @endforeach
            @else
              <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i> Tidak ada tugas yang perlu ditindaklanjuti.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Helper function untuk mengambil variabel CSS
  function getCSSVariable(variable, fallback) {
    const computedStyle = getComputedStyle(document.documentElement);
    return computedStyle.getPropertyValue(variable).trim() || fallback;
  }

  // Chart configuration yang support dark/light mode
  function getChartConfig() {
    return {
      gridColor: getCSSVariable('--chart-grid-color', 'rgba(255, 255, 255, 0.1)'),
      textColor: getCSSVariable('--chart-text-color', '#f8fafc'),
      successColor: getCSSVariable('--chart-success-color', 'rgba(40, 167, 69, 0.7)'),
      successBorder: getCSSVariable('--chart-success-border', '#28a745'),
      warningColor: getCSSVariable('--chart-warning-color', 'rgba(255, 193, 7, 0.7)'),
      warningBorder: getCSSVariable('--chart-warning-border', '#ffc107'),
      primaryColor: getCSSVariable('--chart-primary-color', 'rgba(0, 156, 222, 0.3)'),
      primaryBorder: getCSSVariable('--chart-primary-border', '#0078b0'),
      background: getCSSVariable('--chart-background', 'rgba(255, 255, 255, 0.9)'),
      tooltipBg: getCSSVariable('--chart-tooltip-bg', 'rgba(255, 255, 255, 0.95)'),
      tooltipText: getCSSVariable('--chart-tooltip-text', '#333333'),
      tooltipBorder: getCSSVariable('--chart-tooltip-border', '#dee2e6'),
      legendText: getCSSVariable('--chart-legend-text', '#333333')
    };
  }

  // Objek untuk menyimpan instance chart
  const chartInstances = {
    gaugeChart: null,
    trendChart: null,
    radarChart: null,
    highPerformanceChart: null,
    lowPerformanceChart: null
  };

  // Fungsi untuk update semua chart ketika tema berubah
  function updateChartsForTheme() {
    console.log('Updating charts for theme change');

    // Dapatkan konfigurasi baru berdasarkan tema saat ini
    const chartConfig = getChartConfig();

    // Reinisialisasi semua chart dengan konfigurasi baru
    initGaugeChart(chartConfig);
    initTrendChart(chartConfig);

    // Reinisialisasi chart pada tab yang aktif
    const activeTab = document.querySelector('.tab-pane.active');
    if (activeTab) {
      const tabId = activeTab.id;

      if (tabId === 'semua') {
        initRadarChart(chartConfig);
      } else if (tabId === 'kinerja-tinggi') {
        initHighPerformanceChart(chartConfig);
      } else if (tabId === 'kinerja-rendah') {
        initLowPerformanceChart(chartConfig);
      }
    }

    // Update warna teks di gauge chart
    const gaugeText = document.getElementById('gauge-center-text');
    if (gaugeText) {
      const scoreElement = gaugeText.querySelector('h3');
      const labelElement = gaugeText.querySelector('p');

      if (scoreElement) {
        scoreElement.style.color = chartConfig.textColor;
      }

      if (labelElement) {
        labelElement.style.color = chartConfig.textColor;
        labelElement.style.opacity = '0.7';
      }
    }

    // Update warna teks pada tabel
    document.querySelectorAll('.data-table').forEach(table => {
      // Force reflow untuk memastikan transisi warna yang halus
      table.style.display = 'none';
      table.offsetHeight; // Trigger reflow
      table.style.display = '';
    });

    // Update badge colors
    document.querySelectorAll('.badge').forEach(badge => {
      // Force reflow untuk memastikan transisi warna yang halus
      badge.style.display = 'inline-flex';
      badge.offsetHeight; // Trigger reflow
      badge.style.display = 'inline-flex';
    });
  }

  // Inisialisasi chart
  document.addEventListener('DOMContentLoaded', function() {

    // Setup default Chart.js global options
    setupChartDefaults();

    // Tab handlers untuk Bootstrap 5
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');

    tabs.forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(event) {
        console.log('Tab activated:', event.target.getAttribute('data-bs-target'));
        const tabId = event.target.getAttribute('data-bs-target').replace('#', '');
        const chartConfig = getChartConfig();

        // Render chart berdasarkan tab yang aktif
        if (tabId === 'kinerja-tinggi') {
          initHighPerformanceChart(chartConfig);
        }
        else if (tabId === 'kinerja-rendah') {
          initLowPerformanceChart(chartConfig);
        }
        else if (tabId === 'semua') {
          initRadarChart(chartConfig);
        }

        // Trigger resize event untuk memastikan chart ditampilkan dengan benar
        setTimeout(() => {
          window.dispatchEvent(new Event('resize'));
        }, 50);
      });
    });

    // Inisialisasi chart untuk tab yang aktif saat halaman dimuat
    initAllCharts();

    // Pre-render charts untuk tab lain agar siap ketika tab diaktifkan
    setTimeout(() => {
      const chartConfig = getChartConfig();
      initHighPerformanceChart(chartConfig);
      initLowPerformanceChart(chartConfig);
    }, 100);
  });

  // Setup default Chart.js global options
  function setupChartDefaults() {
    const chartConfig = getChartConfig();

    Chart.defaults.color = chartConfig.textColor;
    Chart.defaults.borderColor = chartConfig.gridColor;
    Chart.defaults.font.family = "'Poppins', 'Helvetica', 'Arial', sans-serif";

    // Default tooltip style
    Chart.defaults.plugins.tooltip.backgroundColor = chartConfig.tooltipBg;
    Chart.defaults.plugins.tooltip.titleColor = chartConfig.tooltipText;
    Chart.defaults.plugins.tooltip.bodyColor = chartConfig.tooltipText;
    Chart.defaults.plugins.tooltip.borderColor = chartConfig.tooltipBorder;
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.displayColors = true;
    Chart.defaults.plugins.tooltip.boxPadding = 4;

    // Default legend style
    Chart.defaults.plugins.legend.labels.color = chartConfig.legendText;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;

    // Animation defaults
    Chart.defaults.animation.duration = 1000;
    Chart.defaults.animation.easing = 'easeOutQuart';
  }

  // Fungsi untuk menginisialisasi semua chart
  function initAllCharts() {
    const chartConfig = getChartConfig();

    // Gauge Chart
    initGaugeChart(chartConfig);

    // Trend Chart
    initTrendChart(chartConfig);

    // Radar Chart untuk perbandingan antar pilar
    initRadarChart(chartConfig);
  }

  // Gauge Chart
  function initGaugeChart(chartConfig = getChartConfig()) {
    const gaugeCtx = document.getElementById('gaugeChart');
    if (!gaugeCtx) return;

    const ctx = gaugeCtx.getContext('2d');
    const nkoValue = {{ $data['nko'] }};

    // Determine color based on value
    let gaugeColor = '#F44336'; // Red for low values
    if (nkoValue >= 100) {
      gaugeColor = chartConfig.successBorder; // Green for high values
    } else if (nkoValue >= 95) {
      gaugeColor = chartConfig.warningBorder; // Yellow for medium values
    }

    // Destroy existing chart if exists
    if (chartInstances.gaugeChart) {
      chartInstances.gaugeChart.destroy();
    }

    chartInstances.gaugeChart = new Chart(ctx, {
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

    // Update center text with current theme colors
    const gaugeText = document.getElementById('gauge-center-text');
    if (gaugeText) {
      const scoreElement = gaugeText.querySelector('h3');
      const labelElement = gaugeText.querySelector('p');

      if (scoreElement) {
        scoreElement.style.color = chartConfig.textColor;
        scoreElement.style.fontSize = '2rem';
        scoreElement.style.fontWeight = '700';
      }

      if (labelElement) {
        labelElement.style.color = chartConfig.textColor;
        labelElement.style.opacity = '0.7';
        labelElement.style.fontSize = '0.85rem';
      }
    }
  }

  // Trend Chart
// Trend Chart
function initTrendChart(chartConfig = getChartConfig()) {
  const trendCtx = document.getElementById('trendChart');
  if (!trendCtx) return;

  const ctx = trendCtx.getContext('2d');

  // Ambil data dari Laravel
  const trendDataRaw = @json($nkoTrend ?? []);

  const months = trendDataRaw.map(item => item.bulan);
  const values = trendDataRaw.map(item => item.nko !== null ? Math.min(item.nko, 110) : null);

  // Destroy existing chart if exists
  if (chartInstances.trendChart) {
    chartInstances.trendChart.destroy();
  }

  chartInstances.trendChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Nilai Kinerja',
        data: values,
        backgroundColor: chartConfig.primaryColor,
        borderColor: chartConfig.primaryBorder,
        borderWidth: 2,
        pointBackgroundColor: chartConfig.primaryBorder,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        tension: 0.3,
        fill: true,
        spanGaps: false // penting agar null tidak digambar
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
          backgroundColor: chartConfig.tooltipBg,
          titleColor: chartConfig.tooltipText,
          bodyColor: chartConfig.tooltipText,
          padding: 12,
          borderColor: chartConfig.tooltipBorder,
          borderWidth: 1,
          callbacks: {
            label: function (context) {
              if (context.parsed.y === null) {
                return 'Belum ada data';
              }
              return `NKO: ${context.parsed.y.toFixed(2)}%`;
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            color: chartConfig.gridColor
          },
          ticks: {
            color: chartConfig.textColor
          }
        },
        y: {
          beginAtZero: true,
          max: 110,
          grid: {
            color: chartConfig.gridColor
          },
          ticks: {
            color: chartConfig.textColor,
            callback: function (value) {
              return value + '%';
            }
          }
        }
      }
    }
  });
}


  // Radar Chart untuk perbandingan antar pilar
  function initRadarChart(chartConfig = getChartConfig()) {
    const radarCtx = document.getElementById('radarChart');
    if (!radarCtx) return;

    const pilars = @json($data['pilar']);

    if (pilars.length > 0) {
      const labels = pilars.map(pilar => pilar.nama);
      const values = pilars.map(pilar => pilar.nilai);

      // Destroy existing chart if exists
      if (chartInstances.radarChart) {
        chartInstances.radarChart.destroy();
      }

      // Data untuk radar chart
      chartInstances.radarChart = new Chart(radarCtx.getContext('2d'), {
        type: 'radar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Nilai Kinerja (%)',
            data: values,
            backgroundColor: chartConfig.primaryColor,
            borderColor: chartConfig.primaryBorder,
            borderWidth: 2,
            pointBackgroundColor: chartConfig.primaryBorder,
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            r: {
              beginAtZero: true,
              max: 110,
              ticks: {
                stepSize: 20,
                backdropColor: 'transparent',
                color: chartConfig.textColor
              },
              grid: {
                color: chartConfig.gridColor
              },
              angleLines: {
                color: chartConfig.gridColor
              },
              pointLabels: {
                color: chartConfig.textColor,
                font: {
                  size: 12
                }
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: chartConfig.tooltipBg,
              titleColor: chartConfig.tooltipText,
              bodyColor: chartConfig.tooltipText,
              titleFont: {
                size: 14,
                weight: 'bold'
              },
              bodyFont: {
                size: 13
              },
              padding: 12,
              borderColor: chartConfig.tooltipBorder,
              borderWidth: 1
            }
          },
          elements: {
            line: {
              tension: 0.1
            }
          }
        }
      });
    } else {
      // Jika tidak ada data, tampilkan pesan kosong
      const noDataDiv = document.createElement('div');
      noDataDiv.className = 'empty-state';
      noDataDiv.innerHTML = `
        <i class="fas fa-chart-pie text-muted mb-3" style="font-size: 2.5rem;"></i>
        <p class="text-muted">Tidak ada data pilar untuk ditampilkan.</p>
      `;
      radarCtx.parentNode.replaceChild(noDataDiv, radarCtx);
    }
  }

  // Kinerja Tinggi Chart - Bar Chart
  function initHighPerformanceChart(chartConfig = getChartConfig()) {
    const highPerformanceCtx = document.getElementById('highPerformanceChart');
    if (!highPerformanceCtx) return;

    const highPilar = @json(collect($data['pilar'] ?? [])->where('nilai', '>=', 100)->values()->all());

    if (highPilar.length > 0) {
      const labels = highPilar.map(pilar => pilar.nama);
      const values = highPilar.map(pilar => pilar.nilai);
      const targetValues = highPilar.map(() => 100);

      // Destroy existing chart if exists
      if (chartInstances.highPerformanceChart) {
        chartInstances.highPerformanceChart.destroy();
      }

      chartInstances.highPerformanceChart = new Chart(highPerformanceCtx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Nilai Kinerja (%)',
              data: values,
              backgroundColor: chartConfig.successColor,
              borderColor: chartConfig.successBorder,
              borderWidth: 1,
              borderRadius: 5,
              barPercentage: 0.6,
              categoryPercentage: 0.7
            },
            {
              label: 'Target Minimum Kinerja Tinggi',
              data: targetValues,
              type: 'line',
              backgroundColor: 'transparent',
              borderColor: 'rgba(0, 0, 0, 0.2)',
              borderWidth: 2,
              borderDash: [5, 5],
              pointStyle: false
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 110,
              grid: {
                color: chartConfig.gridColor
              },
              ticks: {
                color: chartConfig.textColor,
                callback: function(value) {
                  return value + '%';
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: chartConfig.textColor
              }
            }
          },
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: chartConfig.legendText,
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12
                }
              }
            },
            tooltip: {
              backgroundColor: chartConfig.tooltipBg,
              titleColor: chartConfig.tooltipText,
              bodyColor: chartConfig.tooltipText,
              padding: 12,
              mode: 'index',
              intersect: false,
              borderColor: chartConfig.tooltipBorder,
              borderWidth: 1
            }
          }
        }
      });
    } else {
      // Jika tidak ada data, tampilkan pesan kosong
      const chartContainer = highPerformanceCtx.parentNode;
      const existingEmptyState = chartContainer.querySelector('.empty-state');

      if (!existingEmptyState) {
        const noDataDiv = document.createElement('div');
        noDataDiv.className = 'empty-state';
        noDataDiv.innerHTML = `
          <i class="fas fa-chart-bar text-muted mb-3" style="font-size: 2.5rem;"></i>
          <p class="text-muted">Tidak ada data kinerja tinggi untuk ditampilkan.</p>
        `;
        chartContainer.replaceChild(noDataDiv, highPerformanceCtx);
      }
    }
  }

  // Perlu Perhatian Chart - Horizontal Bar Chart
  function initLowPerformanceChart(chartConfig = getChartConfig()) {
    const lowPerformanceCtx = document.getElementById('lowPerformanceChart');
    if (!lowPerformanceCtx) return;

    const lowPilar = @json(collect($data['pilar'] ?? [])->where('nilai', '<', 100)->values()->all());

    if (lowPilar.length > 0) {
      const labels = lowPilar.map(pilar => pilar.nama);
      const values = lowPilar.map(pilar => pilar.nilai);
      const targetValues = lowPilar.map(() => 100); // Target 70%

      // Destroy existing chart if exists
      if (chartInstances.lowPerformanceChart) {
        chartInstances.lowPerformanceChart.destroy();
      }

      chartInstances.lowPerformanceChart = new Chart(lowPerformanceCtx.getContext('2d'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Nilai Kinerja (%)',
              data: values,
              backgroundColor: chartConfig.warningColor,
              borderColor: chartConfig.warningBorder,
              borderWidth: 1,
              borderRadius: 5,
              barPercentage: 0.6,
              categoryPercentage: 0.7
            },
            {
              label: 'Target Minimum',
              data: targetValues,
              type: 'line',
              backgroundColor: 'transparent',
              borderColor: 'rgba(0, 0, 0, 0.2)',
              borderWidth: 2,
              borderDash: [5, 5],
              pointStyle: false
            }
          ]
        },
        options: {
          indexAxis: 'y', // Horizontal bar
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              beginAtZero: true,
              max: 110,
              grid: {
                color: chartConfig.gridColor
              },
              ticks: {
                color: chartConfig.textColor,
                callback: function(value) {
                  return value + '%';
                }
              }
            },
            y: {
              grid: {
                display: false
              },
              ticks: {
                color: chartConfig.textColor
              }
            }
          },
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: chartConfig.legendText,
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12
                }
              }
            },
            tooltip: {
              backgroundColor: chartConfig.tooltipBg,
              titleColor: chartConfig.tooltipText,
              bodyColor: chartConfig.tooltipText,
              padding: 12,
              mode: 'index',
              intersect: false,
              borderColor: chartConfig.tooltipBorder,
              borderWidth: 1
            }
          }
        }
      });
    } else {
      // Jika tidak ada data, tampilkan pesan kosong
      const chartContainer = lowPerformanceCtx.parentNode;
      const existingEmptyState = chartContainer.querySelector('.empty-state');

      if (!existingEmptyState) {
        const noDataDiv = document.createElement('div');
        noDataDiv.className = 'empty-state';
        noDataDiv.innerHTML = `
          <i class="fas fa-check-circle text-muted mb-3" style="font-size: 2.5rem;"></i>
          <p class="text-muted">Tidak ada pilar yang perlu perhatian saat ini.</p>
        `;
        chartContainer.replaceChild(noDataDiv, lowPerformanceCtx);
      }
    }
  }
</script>
@endsection
