@extends('layouts.app')

@section('title', 'Dashboard Kinerja - Admin Risiko & Manajemen')
@section('page_title', 'DASHBOARD KINERJA RISIKO & MANAJEMEN')

@section('content')
<div class="container-fluid px-4">
  <div class="dashboard-content">
    <!-- Stat Cards -->
    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Risiko Teridentifikasi</h3>
            <div class="stat-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
          <div class="stat-value">18</div>
          <p class="stat-description">Total risiko yang teridentifikasi</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Risiko Mitigasi</h3>
            <div class="stat-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
          </div>
          <div class="stat-value">12</div>
          <p class="stat-description">Risiko yang sudah dimitigasi</p>
        </div>
      </div>

      <div class="dashboard-col">
        <div class="stat-card">
          <div class="stat-header">
            <h3 class="stat-title">Tingkat Keamanan</h3>
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
          <div class="stat-value">85%</div>
          <p class="stat-description">Tingkat keamanan operasional</p>
        </div>
      </div>
    </div>

    <!-- Bagian Target -->
    <div class="chart-container mt-4">
      <h3 class="chart-title">
        <i class="fas fa-tasks mr-2"></i>
        Target Manajemen Risiko
      </h3>
      <div class="p-3">
        <ul class="target-list">
          <li class="completed">Penilaian Risiko Triwulan</li>
          <li class="completed">Implementasi Mitigasi Utama</li>
          <li>Pembaruan Matriks Risiko</li>
          <li>Pelatihan Manajemen Risiko</li>
        </ul>
      </div>
    </div>

    <div class="dashboard-row mt-4">
      <div class="dashboard-col">
        <div class="chart-container">
          <h3 class="chart-title">
            <i class="fas fa-spinner mr-2"></i>
            Proyek Mitigasi Aktif
          </h3>
          <div class="p-3">
            <div class="task-item">Penyusunan Business Continuity Plan</div>
            <div class="task-item">Audit Kepatuhan Regulasi</div>
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
            <div class="task-item highlight">Penilaian Risiko Triwulan</div>
            <div class="task-item">Pembaruan SOP Manajemen Krisis</div>
            <div class="task-item">Evaluasi Risiko Sistem IT</div>
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
