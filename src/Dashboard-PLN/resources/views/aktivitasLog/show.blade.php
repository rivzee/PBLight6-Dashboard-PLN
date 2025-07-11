@extends('layouts.app')

@section('title', 'Detail Log Aktivitas')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/logAktifitas.css') }}">
@endsection

@section('content')
<div class="container-fluid detail-container">
    <!-- Main Content -->
    <div class="content-section">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h2 class="card-title mb-0">
                    <i class="fas {{ $log->getTipeIcon() }}"></i>
                    {{ $log->judul }}
                </h2>
                <span class="log-badge text-white bg-{{ $log->getTipeColor() }} ml-3">
                    <i class="fas {{ $log->getTipeIcon() }}"></i>
                    {{ $log->getTipeLabel() }}
                </span>
            </div>
            <div>
                <button type="button" class="btn btn-action btn-delete" data-toggle="modal" data-target="#deleteLogModal">
                    <i class="fas fa-trash"></i>
                    Hapus Log
                </button>
                <a href="{{ route('aktivitasLog.index') }}" class="btn btn-outline-secondary ml-2">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="meta-info">
            <div class="meta-item">
                <div class="meta-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="meta-text">
                    <span class="meta-label">Tanggal</span>
                    <span class="meta-value">{{ $log->created_at->format('d F Y') }}</span>
                </div>
            </div>

            <div class="meta-item">
                <div class="meta-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="meta-text">
                    <span class="meta-label">Waktu</span>
                    <span class="meta-value">{{ $log->created_at->format('H:i:s') }}</span>
                </div>
            </div>

            <div class="meta-item">
                <div class="meta-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="meta-text">
                    <span class="meta-label">User</span>
                    <span class="meta-value">{{ $log->user ? $log->user->name : 'Sistem' }}</span>
                </div>
            </div>

            @if($log->ip_address)
            <div class="meta-item">
                <div class="meta-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div class="meta-text">
                    <span class="meta-label">IP Address</span>
                    <span class="meta-value">{{ $log->ip_address }}</span>
                </div>
            </div>
            @endif

            @if($log->loggable_type)
            <div class="meta-item">
                <div class="meta-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="meta-text">
                    <span class="meta-label">Model</span>
                    <span class="meta-value">{{ class_basename($log->loggable_type) }} #{{ $log->loggable_id }}</span>
                </div>
            </div>
            @endif
        </div>

        <p class="my-3">{{ $log->deskripsi }}</p>

        <div class="section-divider"></div>

        <div class="grid-layout">
            <!-- Informasi Pengguna -->
            @if($log->user)
            <div class="content-card">
                <div class="card-header" style="padding-left: 0; padding-right: 0;">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i>
                        Informasi Pengguna
                    </h3>
                </div>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ $log->user->name }}</div>
                        <div class="user-role">
                            <i class="fas fa-id-badge"></i>
                            @if($log->user->role == 'asisten_manager')
                                <span class="badge-role role-master">Master Admin</span>
                            @elseif(Str::startsWith($log->user->role, 'pic_'))
                                <span class="badge-role role-pic">PIC {{ Str::ucfirst(Str::after($log->user->role, 'pic_')) }}</span>
                            @elseif($log->user->role == 'karyawan')
                                <span class="badge-role role-user">Karyawan</span>
                            @else
                                <span class="badge-role role-user">{{ $log->user->role }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <table class="info-table">
                    <tr>
                        <th>Email</th>
                        <td>{{ $log->user->email }}</td>
                    </tr>
                    @if($log->user->isAdmin() && $log->user->getBidang())
                        <tr>
                            <th>Bidang</th>
                            <td>{{ $log->user->getBidang()->nama }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            @endif

            <!-- Detail Data -->
            @if($log->data)
                <div class="content-card">
                    <div class="card-header" style="padding-left: 0; padding-right: 0;">
                        <h3 class="card-title">
                            <i class="fas fa-code-branch"></i>
                            Detail Perubahan
                        </h3>
                    </div>

                    @if($log->hasPerubahanData())
                        <div class="changes-table-container">
                            <table class="changes-table">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Nilai Sebelumnya</th>
                                        <th>Nilai Baru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->getPerubahan() as $field => $values)
                                        <tr>
                                            <td class="field-name">{{ $field }}</td>
                                            <td class="old-value">
                                                @if(is_array($values['sebelum']))
                                                    <div class="code-block">{{ json_encode($values['sebelum'], JSON_PRETTY_PRINT) }}</div>
                                                @elseif(is_null($values['sebelum']))
                                                    <span class="value-null">NULL</span>
                                                @else
                                                    {{ $values['sebelum'] }}
                                                @endif
                                            </td>
                                            <td class="new-value">
                                                @if(is_array($values['sesudah']))
                                                    <div class="code-block">{{ json_encode($values['sesudah'], JSON_PRETTY_PRINT) }}</div>
                                                @elseif(is_null($values['sesudah']))
                                                    <span class="value-null">NULL</span>
                                                @else
                                                    {{ $values['sesudah'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="code-block">
                            <pre>{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            @endif

            <!-- User Agent -->
            <div class="grid-full content-card">
                <div class="card-header" style="padding-left: 0; padding-right: 0;">
                    <h3 class="card-title">
                        <i class="fas fa-laptop"></i>
                        User Agent
                    </h3>
                </div>
                <div class="code-block">
                    {{ $log->user_agent ?: 'Tidak ada informasi user agent' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteLogModal" tabindex="-1" role="dialog" aria-labelledby="deleteLogModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLogModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus log aktivitas ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Perhatian:</strong> Aksi ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ url('aktivitas-log', $log->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

