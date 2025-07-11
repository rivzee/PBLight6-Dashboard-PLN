@extends('layouts.app')

@section('title', 'Verifikasi KPI')
@section('page_title', 'VERIFIKASI KPI')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/verifikasi.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="verifikasi-title">Daftar KPI yang Menunggu Verifikasi</h2>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    @if(isset($isPeriodeLocked) && $isPeriodeLocked)
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> <strong>Peringatan!</strong> Periode penilaian tahun {{ $tahun }} sedang terkunci. Anda tidak dapat melakukan verifikasi pada periode ini.
        </div>
    @endif

    <!-- Form Filter -->
    <div class="filter-panel">
        <div class="filter-panel-header">
            <h6 class="filter-panel-title"><i class="fas fa-filter"></i> Filter Data</h6>
        </div>
        <form action="{{ route('verifikasi.index') }}" method="GET" class="row align-items-end">
            <div class="col-md-3 mb-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select class="form-select" id="tahun" name="tahun">
                    @php
                        $currentYear = date('Y');
                        $startYear = 2020;
                    @endphp
                    @for($y = $currentYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select class="form-select" id="bulan" name="bulan">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ sprintf('%02d', $m) == $bulan ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="bidang_id" class="form-label">Bidang</label>
                <select class="form-select" id="bidang_id" name="bidang_id">
                    <option value="">-- Semua Bidang --</option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}" {{ $bidangId == $bidang->id ? 'selected' : '' }}>{{ $bidang->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> <span>Filter</span>
                </button>
                <a href="{{ route('verifikasi.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-sync"></i> <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    @if($realisasis->count() > 0)
     <form action="/verifikasi-massal" method="POST" id="form-verifikasi-massal">
            @csrf
            <div class="mb-3">
                <button type="submit" class="btn btn-success" id="btn-verifikasi-massal" disabled>
                    <i class="fas fa-check-double"></i> Verifikasi Terpilih
                </button>
            </div>
    <div class="table-container">
        <table class="verifikasi-table">
            <thead>
                <tr>
                    <th width="5%">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAll" {{ isset($isPeriodeLocked) && $isPeriodeLocked ? 'disabled' : '' }}>
                         </div>
                    </th>
                    <th width="10%">KPI</th>
                    <th width="20%">Indikator</th>
                    <th width="15%">Bidang</th>
                    <th width="10%">Periode</th>
                    <th width="8%">Nilai</th>
                    <th width="10%">Uploaded By</th>
                    <th width="15%">Status</th>
                    <th width="12%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($realisasis as $realisasi)
                    <tr>
                       <td>
                            <div class="form-check">
                                 <input class="form-check-input check-item" type="checkbox" name="nilai_ids[]" value="{{ $realisasi->id }}"
                                            {{ isset($isPeriodeLocked) && $isPeriodeLocked ? 'disabled' : '' }}>

                                    </div>
                                </td>

                        <td>{{ $realisasi->indikator->kode }}</td>
                        <td>{{ $realisasi->indikator->nama }}</td>
                        <td>{{ $realisasi->indikator->bidang->nama }}</td>
                        <td>{{ $realisasi->tahun }}-{{ $realisasi->bulan }}</td>
                        <td>{{ $realisasi->nilai }}</td>
                        <td>{{ $realisasi->user->name }}</td>
                        <td>
                            @if ($realisasi->diverifikasi)
                                <span class="badge bg-success text-white fw-bold rounded-pill">
                                    Telah Diverifikasi
                                </span>
                            @else
                                <span class="badge bg-warning text-dark fw-bold rounded-pill">
                                    Belum Diverifikasi
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('verifikasi.show', $realisasi->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>

                                @if (!$realisasi->diverifikasi && empty($isPeriodeLocked))
                                    <form action="{{ route('verifikasi.update', $realisasi->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Yakin verifikasi?')">
                                            <i class="fas fa-check"></i> Verifikasi
                                        </button>
                                    </form>
                                @else
                                    <span class="btn btn-success btn-sm disabled">
                                        <i class="fas fa-check"></i> Sudah Diverifikasi
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </form>

    <div class="mt-4">
        {{ $realisasis->appends(['tahun' => $tahun, 'bulan' => $bulan, 'bidang_id' => $bidangId])->links() }}
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Tidak ada data KPI yang menunggu verifikasi untuk periode ini.
    </div>
@endif
</div>
@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk cek apakah ada checkbox yang dicentang
        function checkSelected() {
            const checkboxes = document.querySelectorAll('.check-item:checked');
            document.getElementById('btn-verifikasi-massal').disabled = checkboxes.length === 0;
        }

        // Check all / uncheck all
        const checkAllBox = document.getElementById('checkAll');
        if (checkAllBox) {
            checkAllBox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.check-item');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAllBox.checked;
                });
                checkSelected();
            });
        }

        // Individual check
        const checkItems = document.querySelectorAll('.check-item');
        checkItems.forEach(item => {
            item.addEventListener('change', function() {
                checkSelected();

                // Update checkAll status
                const allChecked = document.querySelectorAll('.check-item:checked').length === checkItems.length;
                if (checkAllBox) {
                    checkAllBox.checked = allChecked;
                }
            });
        });

        // Form submit confirm
        const form = document.getElementById('form-verifikasi-massal');
        if (form) {
            form.addEventListener('submit', function(event) {
                const checkboxes = document.querySelectorAll('.check-item:checked');
                if (checkboxes.length === 0) {
                    event.preventDefault();
                    alert('Silakan pilih setidaknya satu KPI untuk diverifikasi.');
                    return false;
                }

                return confirm('Anda yakin ingin memverifikasi ' + checkboxes.length + ' KPI yang dipilih?');
            });
        }

        // Tambahkan efek ripple pada tombol-tombol
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (button.type !== 'submit' || !this.form) { // Skip for form submit buttons
                    const x = e.clientX - e.target.getBoundingClientRect().left;
                    const y = e.clientY - e.target.getBoundingClientRect().top;

                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                }
            });
        });

        // Tambahkan animasi untuk baris tabel saat pertama kali dimuat
        const tableRows = document.querySelectorAll('.verifikasi-table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(20px)';

            setTimeout(() => {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
    });
//     fetch('/verifikasi-massal', {
//     method: 'POST', // pastikan ini POST, bukan PUT
//     headers: {
//         'Content-Type': 'application/json',
//         'X-CSRF-TOKEN': '{{ csrf_token() }}'
//     },
//     body: JSON.stringify({ nilai_ids: [1, 2, 3] })
// });
</script>
@endsection
