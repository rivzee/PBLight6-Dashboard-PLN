@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-header" style="background-color: #0a4d85; border-bottom: 1px solid #1a6baa;">
                    <h3 class="card-title">Log Aktivitas</h3>
                </div>
                <div class="card-body" style="background-color: #222;">
                    <form action="{{ route('aktivitasLog.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="user_id">User</label>
                                <select name="user_id" id="user_id" class="form-control bg-dark text-white border-secondary">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="tipe">Tipe Aktivitas</label>
                                <select name="tipe" id="tipe" class="form-control bg-dark text-white border-secondary">
                                    <option value="">Semua Tipe</option>
                                    @foreach($tipes as $tipe)
                                        <option value="{{ $tipe }}" {{ request('tipe') == $tipe ? 'selected' : '' }}>
                                            {{ ucfirst($tipe) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="tanggal_mulai">Tanggal Mulai</label>
                                <input type="date" class="form-control bg-dark text-white border-secondary" id="tanggal_mulai" name="tanggal_mulai"
                                    value="{{ request('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="tanggal_akhir">Tanggal Akhir</label>
                                <input type="date" class="form-control bg-dark text-white border-secondary" id="tanggal_akhir" name="tanggal_akhir"
                                    value="{{ request('tanggal_akhir') }}">
                            </div>
                            <div class="col-md-12 mb-2 text-right">
                                <button type="submit" class="btn" style="background-color: #009cde; color: white;">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('aktivitasLog.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                                <a href="{{ route('aktivitasLog.eksporCsv') }}?{{ http_build_query(request()->except('page')) }}"
                                   class="btn" style="background-color: #0a4d85; color: white;">
                                    <i class="fas fa-file-csv"></i> Ekspor CSV
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-dark">
                            <thead style="background-color: #0a4d85;">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Tipe</th>
                                    <th>Deskripsi</th>
                                    <th>IP Address</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->user ? $log->user->name : 'N/A' }}</td>
                                        <td>
                                            <span class="badge @if($log->tipe == 'login') bg-success
                                                @elseif($log->tipe == 'logout') bg-warning text-dark
                                                @elseif($log->tipe == 'create') text-white" style="background-color: #009cde;
                                                @elseif($log->tipe == 'update') text-white" style="background-color: #0a4d85;
                                                @elseif($log->tipe == 'delete') bg-danger
                                                @elseif($log->tipe == 'verify') bg-secondary
                                                @endif">
                                                {{ ucfirst($log->tipe) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->deskripsi }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('aktivitasLog.show', $log->id) }}" class="btn btn-sm" style="background-color: #009cde; color: white;">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data log aktivitas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->appends(request()->except('page'))->links() }}
                    </div>
                </div>

                <div class="card-footer" style="background-color: #1c1c1c; border-top: 1px solid #333;">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('aktivitasLog.hapusLogLama') }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus log lama?');">
                                @csrf
                                <div class="input-group">
                                    <input type="number" name="bulan" class="form-control bg-dark text-white border-secondary"
                                           placeholder="Jumlah bulan" min="1" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Hapus Log Lama
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    Menghapus log yang lebih lama dari X bulan yang lalu.
                                </small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
