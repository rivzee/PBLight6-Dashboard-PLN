@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin Pengembangan Bisnis')
@section('page_title', 'DASHBOARD KINERJA PENGEMBANGAN BISNIS')

@section('content')
<div class="container-fluid px-4">
  <div class="dashboard-content">
    <!-- Stat Cards -->
    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Proyek Berjalan</h3>
            <div class="stat-icon">
              <i class="fas fa-project-diagram"></i>
            </div>
          </div>
          <div class="stat-value">5</div>
          <p class="stat-description">Jumlah proyek dalam pengerjaan</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Proyek Selesai</h3>
            <div class="stat-icon">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
          <div class="stat-value">2</div>
          <p class="stat-description">Proyek yang telah selesai</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Pertumbuhan Bisnis</h3>
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
          <div class="stat-value">75%</div>
          <p class="stat-description">Tingkat pertumbuhan bisnis</p>
        </div>
      </div>
    </div>

    <!-- Pipeline Proyek -->
    <div class="chart-container mt-4">
      <h3 class="chart-title">
        <i class="fas fa-tasks mr-2"></i>
        Target Bisnis Strategis
      </h3>
      <div class="p-3">
        <ul class="target-list">
          <li class="completed">Pengembangan Kemitraan Strategis</li>
          <li>Identifikasi Peluang Bisnis Baru</li>
          <li>Peluncuran Produk Baru</li>
          <li>Ekspansi Pangsa Pasar</li>
        </ul>
      </div>
    </div>

    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-rocket mr-2"></i>
            Proyek Aktif
          </h3>
          <div class="p-3">
            <div class="task-item">Riset Pasar</div>
            <div class="task-item">Negosiasi Kemitraan Bisnis</div>
          </div>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-lightbulb mr-2"></i>
            Inisiatif Bisnis Terbaru
          </h3>
          <div class="p-3">
            <div class="task-item highlight">Peluncuran Produk Baru</div>
            <div class="task-item">Strategi Positioning Brand</div>
            <div class="task-item">Akuisisi Strategis</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  .target-list {
    list-style-type: none;
    padding-left: 10px;
  }

  .target-list li {
    padding: 8px 0;
    position: relative;
    padding-left: 25px;
    color: var(--pln-text-secondary);
  }

  .target-list li:before {
    content: "○";
    position: absolute;
    left: 0;
    color: var(--pln-text-secondary);
  }

  .target-list li.completed {
    color: var(--pln-text);
  }

  .target-list li.completed:before {
    content: "✓";
    color: var(--pln-light-blue);
  }

  .task-item {
    background: var(--pln-surface);
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border-left: 3px solid var(--pln-light-blue);
    transition: all 0.3s ease;
  }

  .task-item:hover {
    transform: translateX(5px);
  }

  .task-item.highlight {
    background: rgba(0, 156, 222, 0.1);
    border-left: 3px solid var(--pln-light-blue);
  }
</style>
@endsection
