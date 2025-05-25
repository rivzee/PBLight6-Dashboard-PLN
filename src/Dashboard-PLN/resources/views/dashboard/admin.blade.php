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

  /* Tambahan gaya untuk komponen terintegrasi */
  .missing-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 300px;
    overflow-y: auto;
  }

  .missing-item {
    display: flex;
    align-items: center;
    background: var(--pln-surface);
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
  }

  .missing-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .missing-kode {
    background: var(--pln-light-blue);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
    margin-right: 12px;
    font-size: 14px;
  }

  .missing-content {
    flex: 1;
  }

  .missing-title {
    font-size: 16px;
    margin: 0 0 5px;
    color: var(--pln-text);
    font-weight: 600;
  }

  .missing-target {
    font-size: 14px;
    color: var(--pln-text-secondary);
    margin: 0;
  }

  .missing-action {
    margin-left: 10px;
  }

  /* Notification styles */
  .notification-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-height: 300px;
    overflow-y: auto;
  }

  .notification-item {
    display: flex;
    background: var(--pln-surface);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
  }

  .notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    color: white;
  }

  .bg-info {
    background: var(--pln-light-blue);
  }

  .bg-success {
    background: #4CAF50;
  }

  .bg-warning {
    background: #FFC107;
  }

  .bg-danger {
    background: #F44336;
  }

  .notification-content {
    flex: 1;
  }

  .notification-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 5px;
    color: var(--pln-text);
  }

  .notification-message {
    font-size: 14px;
    color: var(--pln-text-secondary);
    margin: 0 0 5px;
  }

  .notification-time {
    font-size: 12px;
    color: var(--pln-text-secondary);
    font-style: italic;
  }

  /* Activity styles */
  .activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-height: 300px;
    overflow-y: auto;
  }

  .activity-item {
    display: flex;
    background: var(--pln-surface);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
  }

  .activity-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    background: var(--pln-light-blue);
    color: white;
  }

  .activity-content {
    flex: 1;
  }

  .activity-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--pln-text);
  }

  .activity-details {
    font-size: 12px;
    color: var(--pln-text-secondary);
  }

  .activity-user {
    font-weight: 600;
    margin-right: 10px;
  }

  .activity-time {
    font-style: italic;
  }

  /* Empty state */
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
    text-align: center;
    color: var(--pln-text-secondary);
  }

  .empty-icon {
    font-size: 40px;
    margin-bottom: 15px;
    opacity: 0.6;
  }

  /* Status badge */
  .status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 10px;
  }

  .status-verified {
    background-color: rgba(76, 175, 80, 0.15);
    color: #4CAF50;
  }

  .status-pending {
    background-color: rgba(255, 193, 7, 0.15);
    color: #FFC107;
  }

  /* Button styles */
  .btn-action {
    background: var(--pln-light-blue);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    transition: all 0.2s ease;
  }

  .btn-action:hover {
    background: var(--pln-blue);
    color: white;
    transform: translateY(-2px);
  }

  .btn-action.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
  }

  .btn-secondary {
    background: var(--pln-surface);
    color: var(--pln-text);
    border: 1px solid var(--pln-border);
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    transition: all 0.2s ease;
  }

  .btn-secondary:hover {
    background: var(--pln-hover);
    transform: translateY(-2px);
  }

  .btn-secondary.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
  }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Filter dan Periode -->
    <div class="filter-row row mb-4">
        <div class="col-md-8">
            <form action="{{ route('dashboard.admin') }}" method="GET" class="d-flex">
                <div class="form-group mr-3">
                    <label for="tahun">Tahun</label>
                    <select class="form-control" id="tahun" name="tahun" onchange="this.form.submit()">
                        @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ (isset($tahun) && $tahun == $i) ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label for="bulan">Bulan</label>
                    <select class="form-control" id="bulan" name="bulan" onchange="this.form.submit()">
                        @php
                            $namaBulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach ($namaBulan as $key => $nama)
                            <option value="{{ $key }}" {{ (isset($bulan) && $bulan == $key) ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="periode_tipe">Periode</label>
                    <select class="form-control" id="periode_tipe" name="periode_tipe" onchange="this.form.submit()">
                        <option value="bulanan" {{ (isset($periodeTipe) && $periodeTipe == 'bulanan') ? 'selected' : '' }}>Bulanan</option>
                        <option value="mingguan" {{ (isset($periodeTipe) && $periodeTipe == 'mingguan') ? 'selected' : '' }}>Mingguan</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Row 1: Statistik Ringkasan -->
    <div class="dashboard-row">
        <div class="dashboard-col">
            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">Nilai Rata-rata</h2>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ isset($rataRata) ? $rataRata : '0' }}%</h3>
                <p class="stat-description">Rata-rata nilai KPI {{ isset($bidang) ? $bidang->nama : '-' }}</p>
            </div>
        </div>

        <div class="dashboard-col">
            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">Total Indikator</h2>
                    <div class="stat-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ is_countable($indikators) ? count($indikators) : '0' }}</h3>
                <p class="stat-description">Total indikator aktif di bidang ini</p>
            </div>
        </div>

        <div class="dashboard-col">
            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">KPI Belum Diinput</h2>
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ is_countable($missingInputs) ? count($missingInputs) : '0' }}</h3>
                <p class="stat-description">KPI yang belum diinput bulan ini</p>
            </div>
        </div>

        <div class="dashboard-col">
            <div class="stat-card">
                <div class="stat-header">
                    <h2 class="stat-title">Posisi Bidang</h2>
                    <div class="stat-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                </div>
                @php
                    $position = 0;
                    $totalBidang = 0;
                    if (isset($bidangComparison) && is_array($bidangComparison) && isset($bidang)) {
                        $posIndex = array_search($bidang->nama, array_column($bidangComparison, 'nama'));
                        $position = $posIndex !== false ? $posIndex + 1 : count($bidangComparison) + 1;
                        $totalBidang = count($bidangComparison) + 1;
                    }
                @endphp
                <h3 class="stat-value">#{{ $position > 0 ? $position : '-' }}</h3>
                <p class="stat-description">Dari {{ $totalBidang > 0 ? $totalBidang : '-' }} bidang</p>
            </div>
        </div>
    </div>

    <!-- Row 2: Grafik dan KPI yang belum diinput -->
    <div class="row mt-4">
        <div class="col-md-7">
            <div class="dashboard-card">
                <h2 class="card-title">Trend Kinerja {{ isset($tahun) ? $tahun : '-' }}</h2>
                <div class="chart-container mt-3">
                    <canvas id="historyChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="dashboard-card">
                <h2 class="card-title">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    KPI Yang Belum Diinput
                </h2>

                @if (is_countable($missingInputs) && count($missingInputs) > 0)
                    <div class="missing-list">
                        @foreach ($missingInputs as $missing)
                            <div class="missing-item">
                                <div class="missing-kode">{{ $missing->kode ?? '-' }}</div>
                                <div class="missing-content">
                                    <h4 class="missing-title">{{ $missing->nama ?? '-' }}</h4>
                                    <p class="missing-target">Target: {{ $missing->target ?? '-' }}</p>
                                </div>
                                <div class="missing-action">
                                    <a href="#" class="btn btn-action btn-sm" onclick="return false;">
                                        <i class="fas fa-plus"></i> Input
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle empty-icon"></i>
                        <p>Semua KPI sudah diinput untuk periode ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Row 3: Log Aktivitas -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="dashboard-card">
                <h2 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Aktivitas Terbaru Bidang
                </h2>

                @if (is_countable($latestActivities) && count($latestActivities) > 0)
                    <div class="activity-list">
                        @foreach ($latestActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-{{
                                        $activity->tipe == 'create' ? 'plus-circle' :
                                        ($activity->tipe == 'update' ? 'edit' : 'trash-alt')
                                    }}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $activity->judul ?? '-' }}</div>
                                    <div class="activity-details">
                                        <span class="activity-user">{{ $activity->user->name ?? 'Unknown' }}</span>
                                        <span class="activity-time">{{ isset($activity->created_at) ? $activity->created_at->diffForHumans() : '' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-action">Lihat Semua Aktivitas</a>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-history empty-icon"></i>
                        <p>Tidak ada aktivitas terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('historyChart').getContext('2d');

        // Cek apakah historiData ada dan array
        const historiData = @json($historiData ?? []);

        // Buat labels dan dataValues dari historiData
        const labels = historiData.map(item => item.bulan ?? 'Unknown');
        const dataValues = historiData.map(item => item.nilai ?? 0);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai KPI (%)',
                    data: dataValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
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

@endsection
