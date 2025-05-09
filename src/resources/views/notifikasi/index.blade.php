@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page_title', 'NOTIFIKASI')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Notifikasi</h5>
            <div>
                <button id="markAllRead" class="btn btn-sm btn-primary">Tandai Semua Dibaca</button>
                <button id="deleteRead" class="btn btn-sm btn-danger">Hapus yang Sudah Dibaca</button>
            </div>
        </div>
        <div class="card-body">
            @if($notifikasis->count() > 0)
                <div class="list-group">
                    @foreach($notifikasis as $notifikasi)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $notifikasi->dibaca ? '' : 'bg-light' }}">
                            <div>
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $notifikasi->judul }}</h5>
                                    <small class="text-muted">{{ $notifikasi->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notifikasi->pesan }}</p>
                                @if($notifikasi->url)
                                    <a href="{{ $notifikasi->url }}" class="btn btn-sm btn-info">Lihat Detail</a>
                                @endif
                                @if(!$notifikasi->dibaca)
                                    <a href="{{ route('notifikasi.tandaiDibaca', $notifikasi->id) }}" class="btn btn-sm btn-secondary mark-read" data-id="{{ $notifikasi->id }}">Tandai Dibaca</a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('notifikasi.destroy', $notifikasi->id) }}" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-notif" data-id="{{ $notifikasi->id }}">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    {{ $notifikasis->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada notifikasi untuk ditampilkan.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark all as read
        document.getElementById('markAllRead').addEventListener('click', function() {
            if(confirm('Tandai semua notifikasi sebagai dibaca?')) {
                fetch('{{ route("notifikasi.tandaiSemuaDibaca") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.location.reload();
                    }
                });
            }
        });

        // Delete all read notifications
        document.getElementById('deleteRead').addEventListener('click', function() {
            if(confirm('Hapus semua notifikasi yang sudah dibaca?')) {
                fetch('{{ route("notifikasi.hapusSudahDibaca") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.location.reload();
                    }
                });
            }
        });

        // Mark individual notification as read
        document.querySelectorAll('.mark-read').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                fetch(`/notifikasi/${id}/tandai-dibaca`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            window.location.reload();
                        }
                    });
            });
        });

        // Delete individual notification
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if(confirm('Hapus notifikasi ini?')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
