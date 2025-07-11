@extends('layouts.app')

@section('title', 'Manajemen Tahun Penilaian')
@section('page_title', 'MANAJEMEN TAHUN PENILAIAN')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/tahunPenilaian.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="tahun-title">Daftar Tahun Penilaian</h2>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('tahunPenilaian.create') }}" class="btn-tambah-tahun">
            <i class="fas fa-plus-circle"></i> Tambah Tahun Penilaian
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="tahun-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Tahun</th>
                    <th width="15%">Tipe Periode</th>
                    <th width="15%">Periode</th>
                    <th width="20%">Deskripsi</th>
                    <th width="10%">Status</th>
                    <th width="25%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tahunPenilaians as $index => $tahun)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $tahun->tahun }}</strong></td>
                        <td>{{ $tahun->getTipePeriodeLabel() }}</td>
                        <td>
                            @if($tahun->tanggal_mulai && $tahun->tanggal_selesai)
                                {{ $tahun->tanggal_mulai->format('d/m/Y') }} - {{ $tahun->tanggal_selesai->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $tahun->deskripsi ?? '-' }}</td>
                        <td>
                            @if($tahun->is_aktif)
                                <span class="status-badge aktif">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="status-badge nonaktif">
                                    <i class="fas fa-times-circle"></i> Non-Aktif
                                </span>
                            @endif

                            @if($tahun->is_locked)
                                <span class="status-badge locked mt-1">
                                    <i class="fas fa-lock"></i> Terkunci
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if(!$tahun->is_aktif)
                                    <a href="{{ route('tahunPenilaian.activate', $tahun->id) }}" class="btn-action btn-success"
                                       title="Aktifkan" onclick="return confirm('Anda yakin ingin mengaktifkan tahun penilaian ini?')">
                                        <i class="fas fa-check"></i> <span>Aktifkan</span>
                                    </a>
                                @endif

                                @if(!$tahun->is_locked)
                                    <a href="{{ route('tahunPenilaian.lock', $tahun->id) }}" class="btn-action btn-warning"
                                       title="Kunci" onclick="return confirm('Anda yakin ingin mengunci tahun penilaian ini? Data yang terkunci tidak dapat diubah kecuali oleh Master Admin.')">
                                        <i class="fas fa-lock"></i> <span>Kunci</span>
                                    </a>
                                @else
                                    <a href="{{ route('tahunPenilaian.unlock', $tahun->id) }}" class="btn-action btn-info"
                                       title="Buka Kunci" onclick="return confirm('Anda yakin ingin membuka kunci tahun penilaian ini?')">
                                        <i class="fas fa-unlock"></i> <span>Buka</span>
                                    </a>
                                @endif

                                <a href="{{ route('tahunPenilaian.edit', $tahun->id) }}" class="btn-action btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i> <span>Edit</span>
                                </a>

                                @if(!$tahun->is_aktif && !$tahun->is_locked)
                                    <form action="{{ route('tahunPenilaian.destroy', $tahun->id) }}" method="POST" style="display: inline;"
                                          onsubmit="return confirm('Anda yakin ingin menghapus tahun penilaian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i> <span>Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>Belum ada data tahun penilaian</p>
                                <a href="{{ route('tahunPenilaian.create') }}" class="btn-tambah-tahun mt-3">
                                    <i class="fas fa-plus-circle"></i> Tambah Tahun Penilaian
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tambahkan efek ripple pada tombol-tombol
        const buttons = document.querySelectorAll('.btn-action, .btn-tambah-tahun');

        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const formSubmit = this.closest('form') && this.type === 'submit';
                if (formSubmit) return;

                e.preventDefault();

                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const circle = document.createElement('span');
                circle.classList.add('ripple');
                circle.style.left = x + 'px';
                circle.style.top = y + 'px';

                this.appendChild(circle);

                setTimeout(() => {
                    circle.remove();

                    // Navigasi ke halaman jika ini adalah link
                    if (this.tagName === 'A' && this.href) {
                        window.location.href = this.href;
                    }
                }, 600);
            });
        });

        // Tambahkan animasi untuk baris tabel saat pertama kali dimuat
        const tableRows = document.querySelectorAll('.tahun-table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(20px)';

            setTimeout(() => {
                row.style.transition = 'all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });

        // Animate table header
        const tableHeaders = document.querySelectorAll('.tahun-table th');
        tableHeaders.forEach((header, index) => {
            header.style.opacity = '0';
            header.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                header.style.transition = 'all 0.4s ease';
                header.style.opacity = '1';
                header.style.transform = 'translateY(0)';
            }, 50 + (index * 30));
        });
    });
</script>
@endsection
