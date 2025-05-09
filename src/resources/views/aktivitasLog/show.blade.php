@extends('layouts.app')

@section('title', 'Detail Log Aktivitas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-header" style="background-color: #0a4d85; border-bottom: 1px solid #1a6baa;">
                    <h3 class="card-title">Detail Log Aktivitas #{{ $log->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('aktivitasLog.index') }}" class="btn btn-sm" style="background-color: #333; color: #fff; border: 1px solid #444;">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body" style="background-color: #222;">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-dark">
                                <tr>
                                    <th style="width: 200px; background-color: #333; border-color: #444;">ID</th>
                                    <td style="border-color: #444;">{{ $log->id }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color: #333; border-color: #444;">User</th>
                                    <td style="border-color: #444;">{{ $log->user ? $log->user->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color: #333; border-color: #444;">Tipe</th>
                                    <td style="border-color: #444;">
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
                                </tr>
                                <tr>
                                    <th style="background-color: #333; border-color: #444;">Deskripsi</th>
                                    <td style="border-color: #444;">{{ $log->deskripsi }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color: #333; border-color: #444;">IP Address</th>
                                    <td style="border-color: #444;">{{ $log->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color: #333; border-color: #444;">User Agent</th>
                                    <td style="border-color: #444;"><small>{{ $log->user_agent }}</small></td>
                                </tr>
                                <tr>
                                    <th style="background-color: #333; border-color: #444;">Tanggal & Waktu</th>
                                    <td style="border-color: #444;">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            @if($log->data)
                                <div class="card bg-dark">
                                    <div class="card-header" style="background-color: #0a4d85; border-bottom: 1px solid #1a6baa;">
                                        <h3 class="card-title">Data</h3>
                                    </div>
                                    <div class="card-body" style="background-color: #222;">
                                        <pre style="color: #ddd; background-color: #333; padding: 10px; border-radius: 5px;">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="alert" style="background-color: #1a6baa; color: white; border-color: #007bff;">
                                    Tidak ada data tambahan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
