@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin Sekretaris Perusahaan')
@section('page_title', 'DASHBOARD KINERJA SEKRETARIS PERUSAHAAN')

@section('content')
<div class="container-fluid px-4">
  <div class="dashboard-content">
    <!-- Stat Cards -->
    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Dokumen Terdaftar</h3>
            <div class="stat-icon">
              <i class="fas fa-file-alt"></i>
            </div>
          </div>
          <div class="stat-value">24</div>
          <p class="stat-description">Total dokumen perusahaan terdaftar</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Pertemuan</h3>
            <div class="stat-icon">
              <i class="fas fa-calendar-check"></i>
            </div>
          </div>
          <div class="stat-value">8</div>
          <p class="stat-description">Pertemuan terjadwal minggu ini</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Korespondensi</h3>
            <div class="stat-icon">
              <i class="fas fa-envelope"></i>
            </div>
          </div>
          <div class="stat-value">95%</div>
          <p class="stat-description">Tingkat penyelesaian korespondensi</p>
        </div>
      </div>
    </div>

    <!-- Bagian Target -->
    <div class="chart-container mt-4">
      <h3 class="chart-title">
        <i class="fas fa-tasks mr-2"></i>
        Target Bulanan
      </h3>
      <div class="p-3">
        <ul class="target-list">
          <li class="completed">Penjadwalan Rapat Direksi</li>
          <li class="completed">Pembaruan Dokumen GCG</li>
          <li>Koordinasi Hubungan Investor</li>
          <li>Persiapan Laporan Tahunan</li>
        </ul>
      </div>
    </div>

    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-spinner mr-2"></i>
            Tugas Aktif
          </h3>
          <div class="p-3">
            <div class="task-item">Persiapan Rapat Direksi</div>
            <div class="task-item">Penyusunan Notulensi</div>
          </div>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-check mr-2"></i>
            Aktivitas Terselesaikan
          </h3>
          <div class="p-3">
            <div class="task-item highlight">Distribusi Informasi Pemegang Saham</div>
            <div class="task-item">Pembaruan Dokumen GCG</div>
            <div class="task-item">Penyusunan Agenda Direksi</div>
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
