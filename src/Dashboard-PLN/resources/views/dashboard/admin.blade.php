@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin')
@section('page_title', 'DASHBOARD KINERJA ' . strtoupper($bidang->nama))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="container-xl px-4 py-4">
  <div class="row mb-4">
    <div class="col-12">
      <div class="section-divider">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard {{ $bidang->nama }}</h2>
      </div>
      <p class="text-muted">Selamat datang di dashboard admin bidang. Silahkan kelola data kinerja bidang Anda.</p>
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
          <form action="{{ route('dashboard.admin') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
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
              <a href="{{ route('realisasi.index') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle mr-1"></i> Input Realisasi
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @include('components.alert')

  <!-- Ringkasan Statistik -->
  <div class="dashboard-grid">
    <div class="grid-span-3">
      <div class="stat-card fade-in delay-1">
        <div class="stat-header">
          <h3 class="stat-title">Nilai Rata-Rata</h3>
          <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
          </div>
        </div>
        <div class="stat-value">{{ $rataRata }}</div>
        <div class="progress">
          <div class="progress-bar {{ $rataRata >= 100 ? 'progress-green' : ($rataRata >= 95 ? 'progress-yellow' : 'progress-red') }}" role="progressbar" style="width: 0%"></div>
        </div>
        <p class="stat-description">Rata-rata nilai indikator bidang</p>
      </div>
    </div>

    <div class="grid-span-3">
      <div class="stat-card fade-in delay-2">
        <div class="stat-header">
          <h3 class="stat-title">Indikator</h3>
          <div class="stat-icon">
            <i class="fas fa-tasks"></i>
          </div>
        </div>
        <div class="stat-value">{{ $indikators->count() }}</div>
        <p class="stat-description">Total indikator dalam bidang</p>
      </div>
    </div>

    <div class="grid-span-3">
      <div class="stat-card fade-in delay-3 {{ $missingInputs->count() > 0 ? 'pulse' : '' }}">
        <div class="stat-header">
          <h3 class="stat-title">Belum Diinput</h3>
          <div class="stat-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
        </div>
        <div class="stat-value">{{ $missingInputs->count() }}</div>
        <p class="stat-description">Indikator yang belum diinput</p>
      </div>
    </div>

    <div class="grid-span-3">
      <div class="stat-card fade-in delay-4">
        <div class="stat-header">
          <h3 class="stat-title">Terverifikasi</h3>
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
        </div>
        <div class="stat-value">{{ $indikators->where('diverifikasi', true)->count() }}</div>
        <p class="stat-description">Indikator yang sudah diverifikasi</p>
      </div>
    </div>
  </div>

  <!-- Tren Kinerja dan KPI yang belum diinput -->
  <div class="dashboard-grid">
    <div class="grid-span-8">
      <div class="card fade-in delay-1">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title"><i class="fas fa-chart-line"></i> Tren Kinerja {{ $tahun }}</h5>
          <button class="btn btn-sm btn-light" id="refreshTrendChart">
            <i class="fas fa-sync-alt"></i> Refresh
          </button>
        </div>
        <div class="card-body chart-card">
          <div class="chart-container">
            <div class="trend-chart-loading" id="trendChartLoading">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
              </div>
              <span>Memuat data...</span>
            </div>
            <canvas id="trendChart"></canvas>
          </div>
          <div class="trend-legend mt-3">
            <div class="trend-legend-item">
              <div class="trend-legend-color" style="background-color: #4e73df;"></div>
              <div class="trend-legend-label">Nilai Rata-rata</div>
            </div>
            <div class="trend-legend-item">
              <div class="trend-legend-color" style="background-color: #1cc88a;"></div>
              <div class="trend-legend-label">Target</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid-span-4">
      <div class="card fade-in delay-2 {{ $missingInputs->count() > 0 ? 'pulse' : '' }}">
        <div class="card-header bg-warning">
          <h5 class="card-title"><i class="fas fa-exclamation-circle"></i> KPI Belum Diinput</h5>
          <span class="badge bg-light text-dark">{{ $missingInputs->count() }} item</span>
        </div>
        <div class="card-body">
          <div class="missing-inputs-list" style="max-height: 300px; overflow-y: auto;">
            @if($missingInputs->count() > 0)
              @foreach($missingInputs as $indikator)
                <div class="missing-inputs-item fade-in" style="animation-delay: {{ 0.1 * $loop->iteration }}s">
                  <h4 class="missing-inputs-item-title">{{ $indikator->nama }}</h4>
                  <span class="missing-inputs-item-code">{{ $indikator->kode }}</span>
                  <a href="{{ route('realisasi.create', ['indikator' => $indikator->id]) }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus-circle"></i> Input
                  </a>
                </div>
              @endforeach
            @else
              <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i> Semua KPI sudah diinput untuk periode ini.
              </div>
            @endif
          </div>

          @if($missingInputs->count() > 0)
            <div class="missing-inputs-action mt-3 text-center">
              <a href="{{ route('realisasi.index') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Input Semua KPI
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

      <!-- Daftar Indikator -->
  <div class="dashboard-grid">
    <div class="grid-span-12">
      <div class="card fade-in delay-4">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title"><i class="fas fa-list-ul"></i> Daftar Indikator</h5>
        </div>
        <div class="card-body">
          <div class="indikator-grid">
            @foreach($indikators as $indikator)
              <div class="indikator-card fade-in" style="animation-delay: {{ 0.1 * $loop->iteration }}s">
                <div class="indikator-header">
                  <h3 class="indikator-title">{{ $indikator->nama }}</h3>
                  <span class="indikator-code">{{ $indikator->kode }}</span>
                </div>

                <div class="indikator-value">{{ $indikator->nilai_persentase }}%</div>
                <div class="indikator-target">
                    <i class="fas fa-bullseye mr-1"></i>
                    Target Bulan Ini:
                    {{ number_format($indikator->target_nilai, 2) }}
                </div>
                <div class="progress">
                  <div class="progress-bar {{ $indikator->nilai_persentase >= 100 ? 'progress-green' : ($indikator->nilai_persentase >= 95 ? 'progress-yellow' : 'progress-red') }}" role="progressbar" style="width: 0%" data-width="{{ $indikator->nilai_persentase }}%"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <div>
                    @if($indikator->diverifikasi)
                      <span class="badge bg-success text-white">Terverifikasi</span>
                    @else
                      <span class="badge bg-warning text-dark">Belum Diverifikasi</span>
                    @endif
                  </div>

                  <div>
                    <a href="{{ route('realisasi.edit', ['indikator' => $indikator->id]) }}" class="btn btn-sm btn-primary">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('dataKinerja.indikator', ['id' => $indikator->id]) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

      <!-- KPI Status Card -->
  <div class="dashboard-grid">
    <div class="grid-span-12">
      <div class="card fade-in delay-3">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title"><i class="fas fa-clipboard-check"></i> Status Approval KPI</h5>
        </div>
        <div class="card-body">
          @php
            $totalKPI = $indikators->count();
            $belumDisetujui = $indikators->filter(function($indikator) use ($tahun, $bulan) {
              $realisasi = App\Models\Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
              return !$realisasi || $realisasi->getCurrentApprovalLevel() === 0;
            })->count();

            $disetujuiPIC = $indikators->filter(function($indikator) use ($tahun, $bulan) {
              $realisasi = App\Models\Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
              return $realisasi && $realisasi->getCurrentApprovalLevel() === 1;
            })->count();

            $disetujuiManager = $indikators->filter(function($indikator) use ($tahun, $bulan) {
              $realisasi = App\Models\Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
              return $realisasi && $realisasi->getCurrentApprovalLevel() === 2;
            })->count();

            $terverifikasi = $indikators->filter(function($indikator) use ($tahun, $bulan) {
              $realisasi = App\Models\Realisasi::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
              return $realisasi && $realisasi->getCurrentApprovalLevel() === 3;
            })->count();
          @endphp

          <div class="approval-stats">
            <div class="approval-stat-item fade-in" style="animation-delay: 0.5s">
              <div class="approval-icon bg-warning">
                <i class="fas fa-hourglass-start"></i>
              </div>
              <div class="approval-details">
                <h3>{{ $belumDisetujui }}</h3>
                <p>Belum Disetujui</p>
              </div>
            </div>
            <div class="approval-stat-item fade-in" style="animation-delay: 0.6s">
              <div class="approval-icon bg-primary">
                <i class="fas fa-check"></i>
              </div>
              <div class="approval-details">
                <h3>{{ $disetujuiPIC }}</h3>
                <p>Disetujui PIC</p>
              </div>
            </div>
            <div class="approval-stat-item fade-in" style="animation-delay: 0.7s">
              <div class="approval-icon bg-info">
                <i class="fas fa-check-double"></i>
              </div>
              <div class="approval-details">
                <h3>{{ $disetujuiManager }}</h3>
                <p>Disetujui Manager</p>
              </div>
            </div>
            <div class="approval-stat-item fade-in" style="animation-delay: 0.8s">
              <div class="approval-icon bg-success">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="approval-details">
                <h3>{{ $terverifikasi }}</h3>
                <p>Terverifikasi</p>
              </div>
            </div>
          </div>

          <div class="approval-progress mt-3 fade-in" style="animation-delay: 0.9s">
            <div class="progress" style="height: 20px;">
              @php
                $belumDisetujuiPercent = $totalKPI > 0 ? ($belumDisetujui / $totalKPI) * 100 : 0;
                $disetujuiPICPercent = $totalKPI > 0 ? ($disetujuiPIC / $totalKPI) * 100 : 0;
                $disetujuiManagerPercent = $totalKPI > 0 ? ($disetujuiManager / $totalKPI) * 100 : 0;
                $terverifikasiPercent = $totalKPI > 0 ? ($terverifikasi / $totalKPI) * 100 : 0;
              @endphp
              <div class="progress-bar bg-warning progress-bar-animated progress-bar-striped" role="progressbar" style="width: 0%" data-width="{{ $belumDisetujuiPercent }}%" title="Belum Disetujui: {{ $belumDisetujui }}"></div>
              <div class="progress-bar bg-primary progress-bar-animated progress-bar-striped" role="progressbar" style="width: 0%" data-width="{{ $disetujuiPICPercent }}%" title="Disetujui PIC: {{ $disetujuiPIC }}"></div>
              <div class="progress-bar bg-info progress-bar-animated progress-bar-striped" role="progressbar" style="width: 0%" data-width="{{ $disetujuiManagerPercent }}%" title="Disetujui Manager: {{ $disetujuiManager }}"></div>
              <div class="progress-bar bg-success progress-bar-animated progress-bar-striped" role="progressbar" style="width: 0%" data-width="{{ $terverifikasiPercent }}%" title="Terverifikasi: {{ $terverifikasi }}"></div>
            </div>
          </div>
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
  // Inisialisasi Chart Tren Kinerja
  initTrendChart();

  // Animasi progress bar
  animateProgressBars();

  // Refresh Chart ketika tombol refresh diklik
  document.getElementById('refreshTrendChart').addEventListener('click', function() {
    document.getElementById('trendChartLoading').style.display = 'flex';
    setTimeout(function() {
      initTrendChart();
    }, 500);
  });

  function animateProgressBars() {
    // Animasi untuk progress bar pada statistik
    setTimeout(function() {
      const statProgressBar = document.querySelector('.stat-card .progress-bar');
      if (statProgressBar) {
        const targetWidth = parseFloat(statProgressBar.getAttribute('class').includes('progress-green') ? 90 :
                                      statProgressBar.getAttribute('class').includes('progress-yellow') ? 70 : 50);
        statProgressBar.style.width = targetWidth + '%';
      }
    }, 500);

    // Animasi untuk progress bar pada indikator
    const indikatorProgressBars = document.querySelectorAll('.indikator-card .progress-bar');
    indikatorProgressBars.forEach(function(bar, index) {
      setTimeout(function() {
        const targetWidth = bar.getAttribute('data-width');
        bar.style.width = targetWidth;
      }, 800 + (index * 100));
    });

    // Animasi untuk progress bar pada approval status
    const approvalProgressBars = document.querySelectorAll('.approval-progress .progress-bar');
    approvalProgressBars.forEach(function(bar, index) {
      setTimeout(function() {
        const targetWidth = bar.getAttribute('data-width');
        bar.style.width = targetWidth;
      }, 1000 + (index * 200));
    });
  }

  function initTrendChart() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    const historiData = @json($historiData);

    // Validasi data
    if (!historiData || !Array.isArray(historiData) || historiData.length === 0) {
      console.error('Invalid or empty historical data');
      document.getElementById('trendChartLoading').innerHTML = `
        <div class="text-center">
          <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
          <p>Tidak ada data tren yang tersedia.</p>
        </div>
      `;
      return;
    }

    // Siapkan data untuk chart
    const labels = historiData.map(item => item.bulan);
    const values = historiData.map(item => item.nilai);

    // Buat array target (misalnya 80% untuk semua bulan)
    const targets = Array(labels.length).fill(100);

    // Buat chart dengan animasi
    const trendChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Nilai Rata-rata',
            data: values,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 3,
            pointBackgroundColor: '#4e73df',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.3,
            fill: true
          },
          {
            label: 'Target',
            data: targets,
            borderColor: '#1cc88a',
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 2000,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            titleFont: {
              family: "'Poppins', sans-serif",
              size: 14
            },
            bodyFont: {
              family: "'Poppins', sans-serif",
              size: 13
            },
            padding: 12,
            displayColors: false
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            max: 110,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)',
              drawBorder: false
            },
            ticks: {
              callback: function(value) {
                return value + '%';
              }
            }
          }
        }
      }
    });

    // Sembunyikan loading indicator
    document.getElementById('trendChartLoading').style.display = 'none';
  }

  // Tambahkan efek hover pada card
  const cards = document.querySelectorAll('.card');
  cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-8px)';
      this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.1)';
    });

    card.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 4px 15px var(--pln-shadow)';
    });
  });
});
</script>
@endsection
