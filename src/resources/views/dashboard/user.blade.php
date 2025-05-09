{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Karyawan')
@section('page_title', 'DASHBOARD KARYAWAN')

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

  /* Gauge Meter */
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

  /* Chart Container */
  .chart-container {
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 25px;
    transition: all 0.3s ease;
    border: 1px solid var(--pln-border);
    box-shadow: 0 8px 20px var(--pln-shadow);
    position: relative;
    overflow: hidden;
    max-height: 350px;
    overflow-y: hidden;
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

  .chart-title {
    font-size: 18px;
    color: var(--pln-light-blue);
    margin-bottom: 20px;
    font-weight: 600;
  }

  /* Progress */
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
    transition: width 0.5s ease-in-out;
    background-size: 15px 15px;
    animation: none;
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

  @media (max-width: 992px) {
    .dashboard-col {
      min-width: 200px;
    }
  }

  @media (max-width: 768px) {
    .dashboard-col {
      flex: 0 0 100%;
    }
    .bidang-grid {
      grid-template-columns: 1fr;
    }

    .chart-container, .bidang-grid-container {
      max-height: 450px;
    }
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
  }

  /* Bidang Grid Container */
  .bidang-grid-container {
    background: var(--pln-accent-bg);
    border-radius: 16px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid var(--pln-border);
    box-shadow: 0 8px 20px var(--pln-shadow);
    position: relative;
    overflow: hidden;
    margin-top: 30px;
    max-height: 600px;
    overflow-y: auto;
  }

  .bidang-grid-container::-webkit-scrollbar {
    width: 8px;
  }

  .bidang-grid-container::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .bidang-grid-container::-webkit-scrollbar-thumb {
    background: var(--pln-light-blue);
    border-radius: 10px;
  }

  .bidang-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }

  .bidang-grid-title {
    font-size: 18px;
    color: var(--pln-light-blue);
    margin-bottom: 20px;
    font-weight: 600;
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

  .bidang-card {
    background: var(--pln-surface);
    border-radius: 12px;
    padding: 16px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 10px var(--pln-shadow);
    border: 1px solid var(--pln-border);
    height: 220px;
    display: flex;
    flex-direction: column;
  }

  .bidang-card canvas {
    margin-top: auto;
    max-height: 80px !important;
  }

  .bidang-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--pln-text);
    margin-bottom: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .bidang-value {
    font-size: 22px;
    font-weight: 700;
    color: var(--pln-light-blue);
    margin-bottom: 5px;
  }

  /* Optimasi rendering */
  .chart-container canvas,
  .bidang-card canvas {
    will-change: transform;
    transform: translateZ(0);
    backface-visibility: hidden;
  }

  /* Media queries untuk responsif */
  @media (max-width: 576px) {
    .meter-container {
      width: 200px;
      height: 150px;
    }

    .nko-label {
      font-size: 18px;
    }

    .nko-value {
      font-size: 22px;
    }

    .bidang-grid-container {
      padding: 15px;
      max-height: 500px;
    }

    .bidang-card {
      height: 180px;
    }

    .chart-container {
      padding: 15px;
      max-height: 300px;
    }
  }

  /* Mencegah glitch dan flickering */
  * {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
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
        @php
        $rataRataNKO = $bidangData->avg('nilai');
        $bidangTerbaik = $bidangData->sortByDesc('nilai')->first() ?? ['nama' => '-', 'nilai' => 0];
        @endphp
        <div class="stat-value">{{ number_format($rataRataNKO, 1) }}</div>
        <p class="stat-description">Nilai Kinerja Organisasi</p>
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
        <div class="stat-value">{{ count($bidangData) }}</div>
        <p class="stat-description">Jumlah Bidang</p>
      </div>
    </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Terbaik</h3>
          <div class="stat-icon">
            <i class="fas fa-trophy"></i>
          </div>
        </div>
        <div class="stat-value">{{ $bidangTerbaik['nama'] }}</div>
        <p class="stat-description">Bidang Nilai Tertinggi</p>
      </div>
    </div>
    <div class="dashboard-col">
      <div class="stat-card">
        <div class="stat-header">
          <h3 class="stat-title">Periode</h3>
          <div class="stat-icon">
            <i class="fas fa-calendar-alt"></i>
          </div>
        </div>
        <div class="stat-value">{{ $bulan }}/{{ $tahun }}</div>
        <p class="stat-description">Periode Penilaian</p>
      </div>
    </div>
  </div>

  <!-- Performance Gauge & Chart -->
  <div class="row align-items-center">
    <div class="col-md-3">
      <div class="meter-container">
        <canvas id="gaugeChart"></canvas>
        <div class="nko-label">NKO</div>
        <div class="nko-value">{{ number_format($rataRataNKO, 1) }}</div>
      </div>
    </div>

    <div class="col-md-9">
      <div class="chart-container">
        <h3 class="chart-title">
          <i class="fas fa-chart-bar mr-2"></i>
          Perbandingan Kinerja Bidang
        </h3>
        <canvas id="bidangChart" style="max-height: 280px;"></canvas>
      </div>
    </div>
  </div>

  <!-- Bidang Cards -->
  <div class="bidang-grid-container">
    <h3 class="bidang-grid-title">
      <i class="fas fa-building mr-2"></i>
      Detail Kinerja Per Bidang
    </h3>

    <!-- Tabs untuk filter kategori bidang -->
    <ul class="nav nav-tabs" id="bidangTabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="semua-tab" data-toggle="tab" href="#semua-bidang" role="tab">
          Semua Bidang <span class="badge badge-pill badge-secondary ml-1">{{ count($bidangData) }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="terbaik-tab" data-toggle="tab" href="#terbaik-bidang" role="tab">
          Kinerja Terbaik <span class="badge badge-pill badge-success ml-1">{{ $bidangData->where('nilai', '>=', 80)->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="terendah-tab" data-toggle="tab" href="#terendah-bidang" role="tab">
          Kinerja Terendah <span class="badge badge-pill badge-warning ml-1">{{ $bidangData->where('nilai', '<', 70)->count() }}</span>
        </a>
      </li>
    </ul>

    <div class="tab-content" id="bidangTabsContent">
      <!-- Tab Semua Bidang -->
      <div class="tab-pane fade show active" id="semua-bidang" role="tabpanel">
        <div class="bidang-grid">
          @foreach($bidangData->take(6) as $index => $bidang)
          <div class="bidang-card">
            <h3 class="bidang-title">{{ $bidang['nama'] }}</h3>
            <div class="bidang-value">{{ $bidang['nilai'] }}%</div>

            @php
              $progressClass = 'progress-red';
              if ($bidang['nilai'] >= 70) {
                $progressClass = 'progress-green';
              } elseif ($bidang['nilai'] >= 50) {
                $progressClass = 'progress-yellow';
              }
            @endphp
            <div class="progress">
              <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $bidang['nilai'] }}%"></div>
            </div>

            <canvas id="bidangDetailChart{{ $index }}" style="max-height: 120px;"></canvas>
          </div>
          @endforeach
        </div>

        @if(count($bidangData) > 6)
        <div class="pagination-container">
          <ul class="pagination">
            <li><a href="#" class="active">1</a></li>
            <li><a href="#">2</a></li>
            @if(count($bidangData) > 12)
            <li><a href="#">3</a></li>
            @endif
            <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
          </ul>
        </div>
        @endif
      </div>

      <!-- Tab Bidang Terbaik -->
      <div class="tab-pane fade" id="terbaik-bidang" role="tabpanel">
        <div class="bidang-grid">
          @foreach($bidangData->where('nilai', '>=', 80)->take(6) as $index => $bidang)
          <div class="bidang-card">
            <h3 class="bidang-title">{{ $bidang['nama'] }}</h3>
            <div class="bidang-value">{{ $bidang['nilai'] }}%</div>

            <div class="progress">
              <div class="progress-bar progress-green" role="progressbar" style="width: {{ $bidang['nilai'] }}%"></div>
            </div>

            <canvas id="bidangTerbaikChart{{ $index }}" style="max-height: 120px;"></canvas>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Tab Bidang Terendah -->
      <div class="tab-pane fade" id="terendah-bidang" role="tabpanel">
        <div class="bidang-grid">
          @foreach($bidangData->where('nilai', '<', 70)->take(6) as $index => $bidang)
          <div class="bidang-card">
            <h3 class="bidang-title">{{ $bidang['nama'] }}</h3>
            <div class="bidang-value">{{ $bidang['nilai'] }}%</div>

            @php
              $progressClass = 'progress-red';
              if ($bidang['nilai'] >= 50) {
                $progressClass = 'progress-yellow';
              }
            @endphp
            <div class="progress">
              <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $bidang['nilai'] }}%"></div>
            </div>

            <canvas id="bidangTerendahChart{{ $index }}" style="max-height: 120px;"></canvas>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Gauge Chart
    const gaugeCtx = document.getElementById('gaugeChart').getContext('2d');
    const nkoValue = {{ number_format($rataRataNKO, 1) }};

    // Determine color based on value
    let gaugeColor = '#F44336'; // Red for low values
    if (nkoValue >= 70) {
      gaugeColor = '#4CAF50'; // Green for high values
    } else if (nkoValue >= 50) {
      gaugeColor = '#FFC107'; // Yellow for medium values
    }

    // Konfigurasi chart dengan pengaturan kinerja tinggi
    Chart.defaults.elements.point.radius = 0; // Mengurangi penggunaan titik yang memberatkan
    Chart.defaults.elements.line.borderWidth = 2; // Mengurangi ketebalan garis
    Chart.defaults.font.size = 11; // Ukuran font lebih kecil
    Chart.defaults.animation.duration = 400; // Animasi lebih cepat

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
          animateScale: false
        }
      }
    });

    // Bidang Comparison Chart - dengan lazy loading
    setTimeout(() => {
      const bidangCtx = document.getElementById('bidangChart').getContext('2d');
      const bidangData = @json($bidangData);

      new Chart(bidangCtx, {
        type: 'bar',
        data: {
          labels: bidangData.map(item => item.nama),
          datasets: [{
            label: 'Nilai Kinerja',
            data: bidangData.map(item => item.nilai),
            backgroundColor: bidangData.map(item => {
              const value = item.nilai;
              if (value >= 70) return 'rgba(76, 175, 80, 0.7)';
              else if (value >= 50) return 'rgba(255, 193, 7, 0.7)';
              else return 'rgba(244, 67, 54, 0.7)';
            }),
            borderColor: bidangData.map(item => {
              const value = item.nilai;
              if (value >= 70) return '#4CAF50';
              else if (value >= 50) return '#FFC107';
              else return '#F44336';
            }),
            borderWidth: 1,
            borderRadius: 5,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              titleFont: {
                size: 13,
                weight: 'bold'
              },
              bodyFont: {
                size: 12
              },
              padding: 10,
              displayColors: false,
              callbacks: {
                label: function(context) {
                  return context.parsed.y + '%';
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: 'var(--pln-text-secondary)',
                autoSkip: true,
                maxRotation: 45,
                minRotation: 45
              }
            },
            y: {
              beginAtZero: true,
              max: 100,
              grid: {
                color: 'rgba(255, 255, 255, 0.05)'
              },
              ticks: {
                color: 'var(--pln-text-secondary)',
                stepSize: 25,
                callback: function(value) {
                  return value + '%';
                }
              }
            }
          },
          animation: {
            duration: 500
          }
        }
      });
    }, 300); // Delay untuk mengurangi beban rendering awal

    // Fungsi untuk membuat detail chart yang dioptimalkan
    function createBidangDetailChart(chartId, bidangNilai) {
      if (!document.getElementById(chartId)) return;

      const bidangDetailCtx = document.getElementById(chartId).getContext('2d');
      const monthlyData = generateMonthlyData(bidangNilai);

      new Chart(bidangDetailCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
          datasets: [{
            label: 'Tren Nilai',
            data: monthlyData,
            backgroundColor: 'rgba(0, 156, 222, 0.1)',
            borderColor: '#009cde',
            borderWidth: 1.5,
            pointRadius: 0,
            pointHoverRadius: 3,
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
            },
            tooltip: {
              enabled: false
            }
          },
          scales: {
            x: {
              display: false
            },
            y: {
              display: false,
              beginAtZero: true,
              max: 100
            }
          },
          elements: {
            line: {
              tension: 0.3
            }
          },
          animation: {
            duration: 300
          }
        }
      });
    }

    // Delay chart rendering untuk tab pertama (aktif)
    setTimeout(() => {
      // Untuk tab semua bidang (hanya render 6 pertama)
      @foreach($bidangData->take(6) as $index => $bidang)
        createBidangDetailChart(`bidangDetailChart{{ $index }}`, {{ $bidang['nilai'] }});
      @endforeach
    }, 500);

    // Lazy loading untuk tab lainnya - hanya render ketika tab diklik
    $('#bidangTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      const target = $(e.target).attr("href");

      if (target === '#terbaik-bidang') {
        // Render charts untuk tab bidang terbaik
        @foreach($bidangData->where('nilai', '>=', 80)->take(6) as $index => $bidang)
          createBidangDetailChart(`bidangTerbaikChart{{ $index }}`, {{ $bidang['nilai'] }});
        @endforeach
      }
      else if (target === '#terendah-bidang') {
        // Render charts untuk tab bidang terendah
        @foreach($bidangData->where('nilai', '<', 70)->take(6) as $index => $bidang)
          createBidangDetailChart(`bidangTerendahChart{{ $index }}`, {{ $bidang['nilai'] }});
        @endforeach
      }
    });

    // Fungsi untuk menghasilkan data bulanan (lebih sederhana)
    function generateMonthlyData(finalValue) {
      const data = [];
      const min = Math.max(finalValue - 20, 0);

      // Kurangi jumlah iterasi untuk meningkatkan performa
      for (let i = 0; i < 5; i++) {
        data.push(Math.floor(min + (Math.random() * 20)));
      }

      // Tambahkan nilai akhir sebagai data bulan terakhir
      data.push(finalValue);

      return data;
    }

    // Animate progress bars dengan transisi lebih cepat
    const progressBars = document.querySelectorAll('.progress-bar');
    setTimeout(() => {
      progressBars.forEach(bar => {
        bar.style.width = bar.getAttribute('style').replace('width: ', '');
      });
    }, 200);

    // Pagination dengan event handler yang lebih ringan
    document.querySelectorAll('.pagination a').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.pagination a.active').forEach(item => {
          item.classList.remove('active');
        });
        this.classList.add('active');
      });
    });
  });
</script>
@endsection
