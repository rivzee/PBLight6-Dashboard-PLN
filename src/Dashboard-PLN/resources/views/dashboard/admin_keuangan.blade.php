@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin Keuangan')
@section('page_title', 'DASHBOARD KINERJA KEUANGAN')

@section('content')
<div class="container-fluid px-4">
  <div class="dashboard-content">
    <!-- Stat Cards -->
    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Anggaran Tersedia</h3>
            <div class="stat-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
          </div>
          <div class="stat-value">85%</div>
          <p class="stat-description">Persentase anggaran yang tersedia</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Laporan Keuangan</h3>
            <div class="stat-icon">
              <i class="fas fa-file-invoice"></i>
            </div>
          </div>
          <div class="stat-value">5</div>
          <p class="stat-description">Laporan yang perlu direview</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Efisiensi Keuangan</h3>
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
          <div class="stat-value">92%</div>
          <p class="stat-description">Tingkat efisiensi keuangan</p>
        </div>
      </div>
    </div>

    <!-- Bagian Target -->
    <div class="chart-container mt-4">
      <h3 class="chart-title">
        <i class="fas fa-bullseye mr-2"></i>
        Target Keuangan
      </h3>
      <div class="p-3">
        <ul class="target-list">
          <li class="completed">Laporan Kuartal Q1</li>
          <li class="completed">Audit Internal</li>
          <li>Realisasi Anggaran</li>
          <li>Perencanaan Anggaran Tahun Depan</li>
        </ul>
      </div>
    </div>

    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-tasks mr-2"></i>
            Proyek Keuangan Aktif
          </h3>
          <div class="p-3">
            <div class="task-item">Analisis Cash Flow</div>
            <div class="task-item">Evaluasi Anggaran Departemen</div>
          </div>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-history mr-2"></i>
            Laporan Keuangan Terbaru
          </h3>
          <div class="p-3">
            <div class="task-item highlight">Laporan Kuartal Q1</div>
            <div class="task-item">Evaluasi Budget Operasional</div>
            <div class="task-item">Analisis Biaya Produksi</div>
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
