@extends('layouts.app')

@section('title', 'Edit Tahun Penilaian')
@section('page_title', 'EDIT TAHUN PENILAIAN')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/tahunPenilaian.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="tahun-title">Form Edit Tahun Penilaian</h2>

    <div class="mb-4">
        <a href="{{ route('tahunPenilaian.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('tahunPenilaian.update', $tahunPenilaian->id) }}" method="POST" class="edit-form">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tahun" name="tahun" min="2020" max="2100"
                               value="{{ old('tahun', $tahunPenilaian->tahun) }}" required>
                        <small class="form-text">Tahun berupa angka 4 digit, minimal 2020</small>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2">{{ old('deskripsi', $tahunPenilaian->deskripsi) }}</textarea>
                        <small class="form-text">Berikan deskripsi singkat untuk tahun penilaian ini</small>
                    </div>

                    <div class="form-group">
                        <label for="tipe_periode" class="form-label">Tipe Periode <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipe_periode" name="tipe_periode" required>
                            <option value="tahunan" {{ old('tipe_periode', $tahunPenilaian->tipe_periode) == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            <option value="semesteran" {{ old('tipe_periode', $tahunPenilaian->tipe_periode) == 'semesteran' ? 'selected' : '' }}>Semesteran</option>
                            <option value="triwulanan" {{ old('tipe_periode', $tahunPenilaian->tipe_periode) == 'triwulanan' ? 'selected' : '' }}>Triwulanan</option>
                            <option value="bulanan" {{ old('tipe_periode', $tahunPenilaian->tipe_periode) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        <small class="form-text">Pilih tipe periode untuk penilaian KPI</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                               value="{{ old('tanggal_mulai', $tahunPenilaian->tanggal_mulai ? $tahunPenilaian->tanggal_mulai->format('Y-m-d') : '') }}">
                        <small class="form-text">Jika dikosongkan, akan diisi otomatis berdasarkan tipe periode</small>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                               value="{{ old('tanggal_selesai', $tahunPenilaian->tanggal_selesai ? $tahunPenilaian->tanggal_selesai->format('Y-m-d') : '') }}">
                        <small class="form-text">Jika dikosongkan, akan diisi otomatis berdasarkan tipe periode</small>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1"
                                   {{ old('is_aktif', $tahunPenilaian->is_aktif) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="form-check-label">Jadikan sebagai tahun aktif</span>
                        </label>
                        <small class="form-text">Jika dicentang, tahun lain yang aktif akan dinonaktifkan</small>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_locked" name="is_locked" value="1"
                                   {{ old('is_locked', $tahunPenilaian->is_locked) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="form-check-label">Kunci periode penilaian</span>
                        </label>
                        <small class="form-text">Jika dicentang, data periode ini tidak dapat diubah kecuali oleh Master Admin</small>
                    </div>
                </div>
            </div>

            <div class="button-bar">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('tahunPenilaian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk mengatur tanggal otomatis berdasarkan tipe periode yang dipilih
        const tipePeriodeSelect = document.getElementById('tipe_periode');
        const tahunInput = document.getElementById('tahun');
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        const tanggalSelesaiInput = document.getElementById('tanggal_selesai');

        tipePeriodeSelect.addEventListener('change', function() {
            const tahun = tahunInput.value;
            if (!tahun || isNaN(tahun) || tahun < 2020) return;

            // Hanya isi otomatis jika kedua field tanggal kosong
            if (!tanggalMulaiInput.value && !tanggalSelesaiInput.value) {
                switch(this.value) {
                    case 'tahunan':
                        tanggalMulaiInput.value = `${tahun}-01-01`;
                        tanggalSelesaiInput.value = `${tahun}-12-31`;
                        break;
                    case 'semesteran':
                        tanggalMulaiInput.value = `${tahun}-01-01`;
                        tanggalSelesaiInput.value = `${tahun}-06-30`;
                        break;
                    case 'triwulanan':
                        tanggalMulaiInput.value = `${tahun}-01-01`;
                        tanggalSelesaiInput.value = `${tahun}-03-31`;
                        break;
                    case 'bulanan':
                        tanggalMulaiInput.value = `${tahun}-01-01`;
                        tanggalSelesaiInput.value = `${tahun}-01-31`;
                        break;
                }
            }
        });

        // Jika tanggal kosong, trigger change event untuk mengisi tanggal otomatis
        if (!tanggalMulaiInput.value && !tanggalSelesaiInput.value) {
            tipePeriodeSelect.dispatchEvent(new Event('change'));
        }

        // Efek animasi saat halaman dimuat
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach((group, index) => {
            group.style.opacity = '0';
            setTimeout(() => {
                group.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                group.style.opacity = '1';
            }, 100 + (index * 50));
        });

        // Efek ripple pada tombol
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (button.type === 'submit') return;

                e.preventDefault();

                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();

                    // Navigasi ke halaman jika ini adalah link
                    if (this.tagName === 'A' && this.href) {
                        window.location.href = this.href;
                    }
                }, 600);
            });
        });

        // Animasi untuk checkbox
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkmark = this.nextElementSibling;
                checkmark.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    checkmark.style.transform = 'scale(1)';
                }, 200);
            });
        });
    });
</script>
@endsection
