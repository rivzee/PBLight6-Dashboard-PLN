@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/logAktifitas.css') }}">
@endsection



@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-content">

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Log</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Hari Ini</div>
                <div class="stat-value">{{ number_format($stats['today']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Minggu Ini</div>
                <div class="stat-value">{{ number_format($stats['week']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Bulan Ini</div>
                <div class="stat-value">{{ number_format($stats['month']) }}</div>
            </div>
        </div>

        <div class="dashboard-grid">

            <!-- Filter Column -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-filter"></i> Filter Log Aktivitas
                    </h5>
                </div>

                <div class="card-body">
                    <form method="get" action="{{ route('aktivitasLog.index') }}">
                        <div class="form-filters">

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Pengguna</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">Semua Pengguna</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="tipe" class="form-label">Tipe Aktivitas</label>
                                <select name="tipe" id="tipe" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    @foreach($tipes as $tipe)
                                        <option value="{{ $tipe }}" {{ request('tipe') == $tipe ? 'selected' : '' }}>
                                            {{ ucfirst($tipe) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                            </div>

                            <div class="mb-3">
                                <label for="model_type" class="form-label">Model</label>
                                <select name="model_type" id="model_type" class="form-control">
                                    <option value="">Semua Model</option>
                                    @foreach($modelTypes as $modelType)
                                        <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                            {{ class_basename($modelType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="keyword" class="form-label">Kata Kunci</label>
                                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari..." value="{{ request('keyword') }}">
                            </div>

                        </div>

                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('aktivitasLog.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            {{-- <a href="{{ route('aktivitasLog.eksporCsv', request()->all()) }}" class="btn btn-secondary">
                                <i class="fas fa-file-csv"></i> Ekspor CSV
                            </a> --}}
                        </div>
                    </form>

                    <div class="cleanup-section mt-4">
                        <h6 class="cleanup-title">
                            <i class="fas fa-trash-alt"></i> Pembersihan Log
                        </h6>

                        <form method="post" action="{{ route('aktivitasLog.hapusLogLama') }}" class="d-inline" id="deleteOldForm">
                            @csrf
                            <div class="cleanup-options">
                                <select name="periode" class="form-control" required>
                                    <option value="1">1 Bulan</option>
                                    <option value="3">3 Bulan</option>
                                    <option value="6">6 Bulan</option>
                                    <option value="12">12 Bulan</option>
                                </select>
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus log lama? Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="fas fa-trash-alt"></i> Hapus Log Lama
                                </button>
                            </div>

                            <small class="cleanup-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Peringatan: Tindakan ini akan menghapus semua log aktivitas yang lebih lama dari periode yang dipilih
                            </small>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Column -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Aktivitas
                    </h5>

                    <div class="d-flex align-items-center gap-3">
                        <div class="per-page-selector">
                            <form method="GET" action="{{ route('aktivitasLog.index') }}" id="perPageForm">
                                <select name="per_page" id="per_page" class="form-control form-control-sm" onchange="document.getElementById('perPageForm').submit()">
                                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10 per halaman</option>
                                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25 per halaman</option>
                                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50 per halaman</option>
                                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100 per halaman</option>
                                </select>

                                @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>
                        </div>
                        <span>{{ $logs->total() }} total log</span>
                    </div>
                </div>

                <form id="deleteMultipleForm" method="post" action="{{ route('aktivitasLog.hapusMultiple') }}">
                    @csrf

                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="40px"><input type="checkbox" class="select-all-checkbox"></th>
                                        <th width="240px">Waktu</th>
                                        <th width="180px">Pengguna</th>
                                        <th width="100px">Tipe</th>
                                        <th>Detail</th>
                                        <th width="100px">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td><input type="checkbox" name="log_ids[]" value="{{ $log->id }}" class="select-checkbox"></td>
                                        <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                        <td>
                                            @if($log->user)
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($log->user->profile_photo)
                                                        <img src="{{ Storage::url($log->user->profile_photo) }}" alt="{{ $log->user->name }}" class="user-avatar" style="width:32px;height:32px;border-radius:50%;">
                                                    @else
                                                        <div class="user-avatar" style="width:32px;height:32px;border-radius:50%;background:#007bff;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div>{{ $log->user->name }}</div>
                                                        <div class="text-muted small">{{ $log->user->email }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Sistem</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $log->getTipeColor() }}">
                                                <i class="fas {{ $log->getTipeIcon() }}"></i>
                                                {{ $log->getTipeLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div><strong>{{ $log->judul }}</strong></div>
                                            <div class="text-muted small">{{ $log->deskripsi }}</div>
                                            @if ($log->loggable_type)
                                            <div class="text-muted small">
                                                <i class="fas fa-link"></i>
                                                {{ class_basename($log->loggable_type) }}
                                                @if ($log->getLoggableTitle())
                                                    - {{ $log->getLoggableTitle() }}
                                                @endif
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('aktivitasLog.show', $log->id) }}" class="btn btn-primary btn-sm btn-icon" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="post" action="{{ route('aktivitasLog.destroy', $log->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus log ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus Log">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                <h6>Tidak ada log aktivitas ditemukan</h6>
                                                <p class="text-muted">Coba ubah filter atau reset pencarian</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="table-actions d-flex justify-content-between align-items-center mt-3">
                            <button type="submit" class="btn btn-danger" id="deleteMultipleBtn" disabled
                                onclick="return confirm('Apakah Anda yakin ingin menghapus log yang dipilih?')">
                                <i class="fas fa-trash-alt"></i> Hapus Log Terpilih
                            </button>

                            <div class="pagination-nav">
                                @if ($logs->hasPages())
                                <div class="pagination-buttons">
                                    <a href="{{ $logs->appends(request()->except('page'))->url(1) }}" class="btn btn-sm btn-secondary {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                    <a href="{{ $logs->appends(request()->except('page'))->previousPageUrl() }}" class="btn btn-sm btn-secondary {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                                        <i class="fas fa-angle-left"></i> Sebelumnya
                                    </a>
                                    <span class="pagination-info">
                                        Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
                                    </span>
                                    <a href="{{ $logs->appends(request()->except('page'))->nextPageUrl() }}" class="btn btn-sm btn-secondary {{ !$logs->hasMorePages() ? 'disabled' : '' }}">
                                        Selanjutnya <i class="fas fa-angle-right"></i>
                                    </a>
                                    <a href="{{ $logs->appends(request()->except('page'))->url($logs->lastPage()) }}" class="btn btn-sm btn-secondary {{ !$logs->hasMorePages() ? 'disabled' : '' }}">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        function syncWithSystemTheme() {
            const currentTheme = document.body.getAttribute('data-theme') || 'dark';
            const tables = document.querySelectorAll('.table');
            const badges = document.querySelectorAll('.badge');

            tables.forEach(table => {
                table.classList.remove('table-dark', 'table-light');
                table.classList.add(currentTheme === 'dark' ? 'table-dark' : 'table-light');
            });

            badges.forEach(badge => {
                badge.style.fontWeight = currentTheme === 'light' ? '600' : '500';
            });
        }

        syncWithSystemTheme();

        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('change', () => setTimeout(syncWithSystemTheme, 50));
        }

        const selectAllCheckbox = document.querySelector('.select-all-checkbox');
        const checkboxes = document.querySelectorAll('.select-checkbox');
        const deleteMultipleBtn = document.getElementById('deleteMultipleBtn');

        if (selectAllCheckbox && checkboxes.length > 0) {
            selectAllCheckbox.addEventListener('change', function () {
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                updateDeleteButton();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    updateDeleteButton();
                    updateSelectAllCheckbox();
                });
            });

            function updateDeleteButton() {
                const checked = document.querySelectorAll('.select-checkbox:checked');
                deleteMultipleBtn.disabled = checked.length === 0;
            }

            function updateSelectAllCheckbox() {
                const checked = document.querySelectorAll('.select-checkbox:checked');
                selectAllCheckbox.checked = checked.length === checkboxes.length;
            }
        }

    });
</script>
@endsection
