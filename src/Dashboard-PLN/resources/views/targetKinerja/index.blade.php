@extends('layouts.app')

@section('title', 'Target Kinerja Bidang')
@section('page_title', 'TARGET KINERJA BIDANG')

@section('styles')
<style>
    /* Main Container */
    .dashboard-content {
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Page Header - Modern UI */
    .page-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .page-header-subtitle {
        margin-top: 5px;
        font-weight: 400;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .page-header-actions {
        display: flex;
        gap: 10px;
    }

    .page-header-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
    }

    .page-header-badge i {
        margin-right: 5px;
    }

    /* Grid System */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-gap: 20px;
        margin-bottom: 30px;
    }

    .grid-span-3 {
        grid-column: span 3;
    }

    .grid-span-4 {
        grid-column: span 4;
    }

    .grid-span-6 {
        grid-column: span 6;
    }

    .grid-span-8 {
        grid-column: span 8;
    }

    .grid-span-12 {
        grid-column: span 12;
    }

    /* Card Styling - Support Dark/Light Mode */
    .stat-card {
        background: var(--pln-surface);
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

    /* Card Pilar - Support Dark/Light Mode */
    .pilar-card {
        background: var(--pln-surface);
        border-radius: 16px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--pln-border);
        box-shadow: 0 8px 20px var(--pln-shadow);
        transition: all 0.3s ease;
    }

    .pilar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px var(--pln-shadow);
    }

    .pilar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
    }

    .pilar-card .card-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border: none;
        border-radius: 16px 16px 0 0;
        padding: 15px 20px;
    }

    /* Table Styling - Support Dark/Light Mode */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        color: var(--pln-text);
    }

    .data-table th,
    .data-table td {
        padding: 15px;
        border-bottom: 1px solid var(--pln-border);
    }

    .data-table th {
        background-color: var(--pln-accent-bg);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--pln-text);
    }

    .data-table tbody tr:hover {
        background-color: var(--pln-accent-bg);
    }

    /* Satuan Column Styling */
    .data-table td:nth-child(4) {
        text-align: center;
        vertical-align: middle;
    }

    .data-table td:nth-child(4) .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 4px 8px;
        background-color: #6c757d !important;
        color: white;
        border: none;
        white-space: nowrap;
        min-width: 35px;
        display: inline-block;
    }

    /* Satuan header styling */
    .data-table th:nth-child(4) {
        text-align: center;
        font-weight: 700;
    }

    /* Target Button Groups */
    .target-actions {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .target-actions .btn {
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    /* Tahun Selector - Support Dark/Light Mode */
    .tahun-selector {
        background-color: var(--pln-surface);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 20px var(--pln-shadow);
        margin-bottom: 20px;
        color: var(--pln-text);
        border: 1px solid var(--pln-border);
        position: relative;
        overflow: hidden;
    }

    .tahun-selector::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
    }

    .tahun-selector .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .tahun-selector .btn {
        border-radius: 50px;
        padding: 8px 18px;
    }

    /* Monthly Target Preview */
    .monthly-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 8px;
    overflow-x: auto;       /* scroll horizontal jika penuh */

    }

    .monthly-preview-item {
        width: 22px;
        height: 8px;
        border-radius: 4px;
        background-color: var(--pln-surface-2);
        transition: height 0.2s ease;
    }

    .monthly-preview-item.filled {
        background-color: var(--pln-light-blue);
    }

    .section-divider {
        display: flex;
        align-items: center;
        margin: 30px 0 20px;
        color: var(--pln-light-blue);
    }

    .section-divider h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        color: var(--pln-text);
    }

    .section-divider i {
        margin-right: 10px;
        color: var(--pln-light-blue);
    }

    /* Alert Styles */
    .alert-custom {
        background-color: var(--pln-surface);
        border-left: 4px solid;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 12px var(--pln-shadow);
    }

    .alert-custom .alert-icon {
        font-size: 24px;
        margin-right: 15px;
    }

    .alert-custom.alert-info {
        border-color: var(--pln-blue);
    }

    .alert-custom.alert-info .alert-icon {
        color: var(--pln-blue);
    }

    /* Modal Footer - Compact */
    .modal-footer {
        padding: 10px 16px !important;
        border-top: 1px solid #dee2e6 !important;
        justify-content: center !important;
    }

    .modal-footer .btn {
        padding: 8px 16px !important;
        font-size: 0.85rem !important;
    }

    /* Info Alert - Compact */
    .alert.alert-info {
        margin-bottom: 0.5rem !important;
        margin-top: 0.5rem !important;
        padding: 8px 12px !important;
    }

    .alert.alert-info small {
        font-size: 0.75rem !important;
        line-height: 1.3 !important;
    }

    /* Table responsive container - Balanced padding */
    .table-responsive {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: repeat(6, 1fr);
        }

        .grid-span-3 {
            grid-column: span 3;
        }
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: repeat(1, 1fr);
        }

        .grid-span-3,
        .grid-span-4,
        .grid-span-6,
        .grid-span-8,
        .grid-span-12 {
            grid-column: span 1;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .page-header-actions {
            width: 100%;
            justify-content: flex-start;
            margin-top: 10px;
        }
    }

    @media (max-width: 576px) {
        .stat-card {
            padding: 15px;
        }

        .tahun-selector .btn {
            font-size: 0.8rem;
            padding: 6px 10px;
        }
    }

    /* Modal Styling */
    .modal-lg {
        max-width: 600px;
    }

    .modal-header.bg-primary {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue)) !important;
        padding: 12px 16px;
    }

    .modal-header .modal-title {
        font-size: 1rem;
        font-weight: 600;
    }

    .modal-body {
        padding: 12px 16px !important;
    }

    .modal-content {
        border-radius: 8px !important;
    }

    .modal-header {
        padding: 14px 18px !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    .modal-title {
        font-size: 1.1rem !important;
        font-weight: 600 !important;
        margin: 0 !important;
    }

    /* Custom Modal Table Styling - Compact but Readable */
    .modal-table {
        margin: 0 !important;
        border-collapse: collapse !important;
        width: 100% !important;
        border: none !important;
        font-size: 0.8rem !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

    .modal-table thead th {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue)) !important;
        color: white !important;
        font-weight: 600 !important;
        text-align: center !important;
        padding: 6px 8px !important;
        border: none !important;
        font-size: 0.75rem !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        vertical-align: middle !important;
        height: 28px !important;
    }

    .modal-table tbody tr {
        margin: 0 !important;
        border: none !important;
        height: 26px !important;
    }

    .modal-table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }

    .modal-table tbody td {
        padding: 4px 8px !important;
        vertical-align: middle !important;
        border-top: 1px solid #e9ecef !important;
        font-size: 0.75rem !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        height: 26px !important;
        text-align: center !important;
    }

    .modal-table tbody tr:first-child td {
        border-top: none !important;
    }

    /* Styling untuk highlight Desember */
    .modal-table tbody tr.table-warning {
        background: #fff3cd !important;
        border-left: 3px solid #ffc107 !important;
    }

    .modal-table tbody tr.table-warning:hover {
        background: #ffe8a1 !important;
    }

    /* Styling untuk total tahunan */
    .modal-table tbody tr.table-primary {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue)) !important;
        color: white !important;
        font-weight: 600 !important;
    }

    .modal-table tbody tr.table-primary:hover {
        background: linear-gradient(135deg, var(--pln-dark-blue), var(--pln-blue)) !important;
    }

    .modal-table tbody tr.table-primary td {
        border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    /* Icon styling dalam tabel */
    .modal-table .fa-star {
        color: #ffc107 !important;
        font-size: 0.7rem !important;
        margin-left: 4px !important;
    }

    .modal-table .fa-calendar-check {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 0.7rem !important;
    }

    /* Responsive modal table */
    @media (max-width: 768px) {
        .modal-table thead th,
        .modal-table tbody td {
            padding: 3px 6px !important;
            font-size: 0.7rem !important;
        }

        .modal-lg {
            max-width: 95% !important;
        }

        /* Hide Satuan column on mobile to save space */
        .data-table th:nth-child(4),
        .data-table td:nth-child(4) {
            display: none;
        }

        /* Adjust column widths for mobile */
        .data-table th:nth-child(1),
        .data-table td:nth-child(1) {
            width: 8% !important;
        }
        
        .data-table th:nth-child(2),
        .data-table td:nth-child(2) {
            width: 15% !important;
        }
        
        .data-table th:nth-child(3),
        .data-table td:nth-child(3) {
            width: 40% !important;
        }
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    /* Card styling enhancements */
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-width: 2px;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card.border-primary:hover {
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    }

    .card.border-success:hover {
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
    }

    .card.border-info:hover {
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
    }

    /* Enhanced badge styling */
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.75em;
        border-radius: 0.375rem;
    }

    /* Chart container styling */
    #targetChart {
        max-height: 300px;
    }

    /* Table enhancements */
    .table thead th {
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    /* Link styling for "Lihat detail" */
    .lihat-target {
        color: var(--pln-blue);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .lihat-target:hover {
        color: var(--pln-dark-blue);
        text-decoration: underline;
    }

    /* Badge improvements */
    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
    }
</style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Modern Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-bullseye me-2"></i>Target Kinerja Bidang</h2>
            <div class="page-header-subtitle">
                Pengelolaan target dan evaluasi kinerja seluruh bidang
            </div>
        </div>
        <div class="page-header-actions">
            @if($tahunPenilaian)
            <div class="page-header-badge">
                <i class="fas fa-chart-pie"></i>
                Total Indikator: {{ $totalIndikators ?? $pilars->sum(function($pilar) { return $pilar->indikators->count(); }) }}
            </div>
            @endif
        </div>
    </div>

    @include('components.alert')

    @if(!$tahunPenilaian)
    <!-- Pesan tidak ada tahun penilaian - Dengan desain yang lebih modern -->
    <div class="alert-custom alert-info">
        <div class="alert-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <h5 class="font-weight-bold mb-1">Tidak Ada Tahun Penilaian</h5>
            <p class="mb-2">Tidak ada tahun penilaian yang tersedia. Silakan buat tahun penilaian terlebih dahulu untuk melanjutkan.</p>
            <div class="mt-2">
                <a href="{{ route('tahunPenilaian.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Buat Tahun Penilaian Baru
                </a>
                <a href="{{ route('tahunPenilaian.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="fas fa-list me-1"></i> Lihat Daftar Tahun Penilaian
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Tahun Selector dengan tampilan yang lebih modern -->
    <div class="tahun-selector">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-calendar-alt me-2"></i>Tahun Penilaian: {{ $tahunPenilaian->tahun }}</h5>

            @if($tahunPenilaian->is_locked)
            <div class="badge bg-warning p-2 rounded-pill">
                <i class="fas fa-lock me-1"></i> Tahun Terkunci
            </div>
            @endif
        </div>

        <div class="btn-group">
            @foreach(\App\Models\TahunPenilaian::orderBy('tahun', 'desc')->get() as $tp)
                <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tp->id]) }}"
                   class="btn {{ $tp->id == $tahunPenilaian->id ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $tp->tahun }}
                    @if($tp->is_aktif)
                        <i class="fas fa-star ms-1" data-bs-toggle="tooltip" title="Tahun Aktif"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Dashboard Stats dengan card yang lebih modern -->
    <div class="dashboard-grid">
        <!-- Total Indikator -->
        <div class="grid-span-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-title">Total Indikator</div>
                <div class="stat-value">{{ $pilars->sum(function($pilar) { return $pilar->indikators->count(); }) }}</div>
                <div class="stat-description">Jumlah indikator yang perlu ditargetkan</div>
            </div>
        </div>

        <!-- Target Sudah Diatur -->
        <div class="grid-span-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-title">Target Sudah Diatur</div>
                <div class="stat-value">
                    @php
                        $totalSet = 0;
                        foreach($pilars as $pilar) {
                            foreach($pilar->indikators as $indikator) {
                                if(isset($indikator->target_data)) {
                                    $totalSet++;
                                }
                            }
                        }
                        echo $totalSet;
                    @endphp
                </div>
                <div class="stat-description">Indikator dengan target yang sudah diatur</div>
            </div>
        </div>

        <!-- Belum Diatur -->
        <div class="grid-span-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-title">Belum Diatur</div>
                <div class="stat-value">
                    @php
                        $totalMissing = 0;
                        foreach($pilars as $pilar) {
                            foreach($pilar->indikators as $indikator) {
                                if(!isset($indikator->target_data)) {
                                    $totalMissing++;
                                }
                            }
                        }
                        echo $totalMissing;
                    @endphp
                </div>
                <div class="stat-description">Indikator tanpa target</div>
            </div>
        </div>
    </div>

    <!-- Accordion Pilar dengan desain yang lebih modern -->
    <div class="section-divider mb-4">
        <h2><i class="fas fa-layer-group"></i> Target Per-Pilar</h2>
    </div>

    <!-- Tampilkan semua pilar secara berurutan dengan desain kartu yang lebih modern -->
    @foreach($pilars as $index => $pilar)
        <div class="pilar-card mb-4" id="pilar-{{ $pilar->kode }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0"><strong>{{ $pilar->kode }}</strong> - {{ $pilar->nama }}
                    <span class="badge bg-light text-dark ms-2">{{ $pilar->indikators->count() }} indikator</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode</th>
                                <th width="25%">Indikator</th>
                                <th width="10%">Satuan</th>
                                <th width="8%">Bobot</th>
                                <th width="12%">Target Tahunan</th>
                                <th width="18%">Target Bulanan</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pilar->indikators as $indIndex => $indikator)
                                <tr>
                                    <td>{{ $indIndex + 1 }}</td>
                                    <td><strong>{{ $indikator->kode }}</strong></td>
                                    <td>
                                        <strong>{{ $indikator->nama }}</strong>
                                        @if($indikator->deskripsi)
                                            <div class="small text-muted">{{ Str::limit($indikator->deskripsi, 60) }}</div>
                                        @endif
                                        <div class="badge bg-info rounded-pill">{{ $indikator->bidang->nama }}</div>
                                        @if(!$indikator->aktif)
                                            <div class="badge bg-warning rounded-pill">Tidak Aktif</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $indikator->satuan ?? '-' }}</span>
                                    </td>
                                    <td>{{ $indikator->bobot }}</td>
                                    <td>
                                        @if(isset($indikator->target_data))
                                            <span class="font-weight-bold">{{ number_format($indikator->target_data->target_tahunan, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-danger">Belum Diatur</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($indikator->target_data) && is_array($indikator->target_data->target_bulanan))
                                            <div class="monthly-preview">
                                                @php
                                                    $bulanan = $indikator->target_data->target_bulanan;
                                                    $max = count($bulanan) > 0 ? max($bulanan) : 0;
                                                @endphp
                                                @foreach($bulanan as $bulan)
                                                    <div class="monthly-preview-item filled" style="height: {{ $max > 0 ? (4 + ($bulan/$max * 8)) : 4 }}px"></div>
                                                @endforeach
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-info-circle"></i>
                                                <a href="#"
                                                    class="lihat-target"
                                                    data-kode="{{ $indikator->kode }}"
                                                    data-nama="{{ $indikator->nama }}"
                                                    data-satuan="{{ $indikator->satuan }}"
                                                    data-bulanan='@json($indikator->target_data->target_bulanan)'
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#monthlyModal">
                                                    Lihat detail bulanan
                                                </a>
                                            </div>

                                        @else
                                            <div class="monthly-preview">
                                                @for($i = 0; $i < 12; $i++)
                                                    <div class="monthly-preview-item"></div>
                                                @endfor
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-minus-circle"></i> Belum diatur
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$tahunPenilaian->is_locked)
                                            <div class="target-actions">
                                                @if(isset($indikator->target_data))
                                                    <a href="{{ route('targetKinerja.edit', $indikator->target_data->id) }}"
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @else
                                                    <a href="{{ route('targetKinerja.create', ['indikator_id' => $indikator->id, 'tahun_penilaian_id' => $tahunPenilaian->id]) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-plus"></i> Tambah Target
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Terkunci
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada indikator untuk pilar ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    @endif
</div>
<div class="modal fade" id="monthlyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fas fa-chart-line me-2" style="font-size: 0.9rem;"></i>
          <span id="modal-title-text">Target Bulanan Detail</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <!-- Info Card -->
        <div class="alert alert-info mx-3 mt-2 mb-2" style="background: linear-gradient(135deg, #d1ecf1, #bee5eb); border: none; border-radius: 6px; padding: 8px 12px;">
          <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-2 text-primary" style="font-size: 0.85rem;"></i>
            <small class="mb-0" style="font-size: 0.75rem;">Target bulanan menunjukkan target yang harus dicapai pada bulan tersebut. <strong>Target tahunan = nilai Desember</strong></small>
          </div>
        </div>

        <!-- Table -->
        <div class="table-responsive px-3 pb-3">
          <table class="table modal-table">
            <thead>
              <tr>
                <th width="50%">
                  <i class="fas fa-calendar me-2" style="font-size: 0.7rem;"></i>Bulan
                </th>
                <th width="50%">
                  <i class="fas fa-target me-2" style="font-size: 0.7rem;"></i>Target Bulanan
                </th>
              </tr>
            </thead>
            <tbody id="tbody-target">
              <!-- Data akan diisi oleh JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Tutup
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Initialize tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
           .forEach(el => new bootstrap.Tooltip(el));

  const modal = document.getElementById('monthlyModal');
  const tbody = modal.querySelector('#tbody-target');
  const modalTitle = modal.querySelector('#modal-title-text');

  // Debug: Check if all elements exist
  console.log('Modal elements check:');
  console.log('modal:', modal);
  console.log('tbody:', tbody);
  console.log('modalTitle:', modalTitle);

  // Additional check
  if (!modal) {
    console.error('Modal #monthlyModal not found!');
    return;
  }

  if (!tbody) {
    console.error('tbody #tbody-target not found!');
    console.log('Available tbody elements:', document.querySelectorAll('tbody'));
    return;
  }

  console.log('All modal elements found successfully');

  // Nama bulan dalam bahasa Indonesia
  const namaBulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];

  // Function untuk format angka dengan format Indonesia (koma untuk desimal, titik untuk ribuan)
  // Tampilan tabel menggunakan 2 angka di belakang koma
  function formatNumber(num) {
    return Number(num).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  // Event listener untuk tombol "Lihat detail"
  const detailButtons = document.querySelectorAll('.lihat-target');
  console.log('Found detail buttons:', detailButtons.length);

  if (detailButtons.length === 0) {
    console.warn('No .lihat-target buttons found! Make sure the PHP data is rendered correctly.');
  }

  detailButtons.forEach((btn, index) => {
    console.log(`Button ${index + 1}:`, btn);
    console.log(`  - data-kode: ${btn.dataset.kode}`);
    console.log(`  - data-bulanan: ${btn.dataset.bulanan}`);

    btn.addEventListener('click', (e) => {
      e.preventDefault();

      console.log('Button clicked:', btn);

      try {
        const dataStr = btn.dataset.bulanan;
        const kode = btn.dataset.kode;
        const nama = btn.dataset.nama || kode;
        const satuan = btn.dataset.satuan || '';

        console.log('Raw data string:', dataStr);
        console.log('Kode:', kode);
        console.log('Satuan:', satuan);

        // Parse data bulanan
        let data;
        try {
          data = JSON.parse(dataStr);
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          data = [0,0,0,0,0,0,0,0,0,0,0,0]; // Default 12 bulan dengan nilai 0
        }

        console.log('Parsed data:', data);

        // Pastikan data adalah array dengan 12 elemen
        if (!Array.isArray(data)) {
          data = [0,0,0,0,0,0,0,0,0,0,0,0];
        }

        // Pastikan ada 12 bulan
        while (data.length < 12) {
          data.push(0);
        }

        // Update modal title
        modalTitle.textContent = `Target Bulanan: ${nama} (${kode})`;        // Generate table rows dengan target bulanan (bukan kumulatif)
        const tableRows = data.map((targetBulanan, index) => {
          // Highlight bulan Desember
          const isDesember = index === 11;
          const rowClass = isDesember ? 'table-warning' : '';

          return `
            <tr class="${rowClass}">
              <td>
                <strong>${namaBulan[index]}</strong>
                ${isDesember ? '<i class="fas fa-star ms-2" title="Target Tahunan"></i>' : ''}
              </td>
              <td class="text-end">
                <strong>${formatNumber(targetBulanan)} ${satuan}</strong>
              </td>
            </tr>
          `;
        }).join('');

        // Tambahkan baris total tahunan (dari Desember)
        const targetTahunan = data[11] || 0; // Target tahunan = nilai Desember

        const summaryRow = `
          <tr class="table-primary">
            <td>
              <strong><i class="fas fa-calendar-check me-2"></i>Target Tahunan</strong>
            </td>
            <td class="text-end">
              <strong>${formatNumber(targetTahunan)} ${satuan}</strong>
            </td>
          </tr>
        `;

        console.log('Generated table rows:', tableRows);

        // Pastikan tbody ada dan update kontennya
        if (tbody) {
          tbody.innerHTML = tableRows + summaryRow;
          console.log('Table updated successfully');
        } else {
          console.error('tbody element not found');
        }

      } catch (error) {
        console.error('Error processing data:', error);

        // Fallback: tampilkan pesan error di tabel
        if (tbody) {
          tbody.innerHTML = `
            <tr>
              <td colspan="2" class="text-center text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading data: ${error.message}
              </td>
            </tr>
          `;
        }
      }
    });
  });
});
</script>
@endsection
