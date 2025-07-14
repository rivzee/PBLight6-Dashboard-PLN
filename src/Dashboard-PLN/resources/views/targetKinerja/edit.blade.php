@extends('layouts.app')

@section('title', 'Edit Target Kinerja')
@section('page_title', 'EDIT TARGET KINERJA')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/targetKinerja.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-edit me-2"></i>Edit Target Kinerja</h2>
            <p class="page-header-subtitle">Ubah dan sesuaikan target untuk indikator kinerja</p>
        </div>
        <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tahunPenilaian->id]) }}" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @include('components.alert')

    <div class="form-card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Form Edit Target Kinerja</h6>
        </div>
        <div class="card-body">

            <div class="info-box mb-4">
                <h6><i class="fas fa-info-circle me-2"></i>Informasi Indikator</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row"><strong>Kode:</strong> {{ $indikator->kode }}</div>
                        <div class="info-row"><strong>Nama:</strong> {{ $indikator->nama }}</div>
                        <div class="info-row"><strong>Bobot:</strong> {{ $indikator->bobot }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row"><strong>Pilar:</strong> {{ $indikator->pilar->nama }}</div>
                        <div class="info-row"><strong>Bidang:</strong> {{ $indikator->bidang->nama }}</div>
                        <div class="info-row"><strong>Tahun:</strong> {{ $tahunPenilaian->tahun }}</div>
                    </div>
                </div>
                @if($indikator->deskripsi)
                    <div class="info-row mt-2"><strong>Deskripsi:</strong> {{ $indikator->deskripsi }}</div>
                @endif
            </div>

            <form action="{{ route('targetKinerja.update', $target->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Input Target Bulanan -->
                <div class="form-group">
                    <label><strong>Target Kumulatif per Bulan ({{ $indikator->satuan }})</strong> <span class="text-danger">*</span></label>
                    <div class="monthly-grid">
                        @php
                            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $target_bulanan = old('target_bulanan', $target->target_bulanan ?? []);
                            for ($i = count($target_bulanan); $i < 12; $i++) {
                                $target_bulanan[$i] = 0;
                            }
                        @endphp

                        @foreach ($bulan as $i => $nama)
                            <div class="monthly-input">
                                <label>{{ $nama }} {{ $tahunPenilaian->tahun }}</label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control monthly-target {{ $i === 11 ? 'december-target' : '' }} @error('target_bulanan.'.$i) is-invalid @enderror"
                                        name="target_bulanan[{{ $i }}]"
                                        id="target_bulanan_{{ $i }}"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('target_bulanan.'.$i, $target_bulanan[$i]) }}"
                                        {{ $target->disetujui && !auth()->user()->isMasterAdmin() ? 'readonly' : '' }}
                                        required
                                        placeholder="0">
                                    <span class="input-group-text">{{ $indikator->satuan }}</span>
                                </div>
                                @error('target_bulanan.'.$i)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Target Tahunan Display -->
                <div class="total-display mt-3">
                    <i class="fas fa-chart-line me-2"></i>
                    <strong>Target Tahunan: <span id="targetTahunan">{{ number_format($target_bulanan[11], 3) }}</span> {{ $indikator->satuan }}</strong>
                </div>

                @if(!$target->disetujui || auth()->user()->isMasterAdmin())
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                        <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tahunPenilaian->id]) }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Batal</a>
                        {{-- <button type="button" class="btn btn-warning ms-2" id="distributeBtn">
                            <i class="fas fa-random me-1"></i> Distribusi Otomatis
                        </button> --}}
                    </div>
                @else
                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Target yang sudah disetujui hanya dapat diubah oleh Master Admin.
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const updateTargetTahunan = () => {
        const desemberValue = parseFloat(document.querySelector('.december-target')?.value || 0);
        document.getElementById('targetTahunan').innerText = desemberValue.toFixed(3);
    };

    const highlightInputs = () => {
        document.querySelectorAll('.monthly-target').forEach((input, index) => {
            input.style.backgroundColor = index === 11 ? '#fff3cd' : '#ffffff';
            input.style.borderColor = index === 11 ? '#ffc107' : '#ced4da';
        });
    };

    const validateKumulatif = () => {
        const inputs = document.querySelectorAll('.monthly-target');
        let valid = true;
        let prev = 0;

        inputs.forEach((input, i) => {
            const val = parseFloat(input.value) || 0;
            if (val < prev) {
                input.style.borderColor = '#dc3545';
                input.style.backgroundColor = '#ffe6e6';
                valid = false;
            } else {
                input.style.borderColor = '#ced4da';
                input.style.backgroundColor = '#ffffff';
                prev = val;
            }
        });

        return valid;
    };

    document.querySelectorAll('.monthly-target').forEach((input, index) => {
        input.addEventListener('input', () => {
            if (parseFloat(input.value) < 0) input.value = 0;
            updateTargetTahunan();
            validateKumulatif();
        });
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const isValid = validateKumulatif();
        const desemberValue = parseFloat(document.querySelector('.december-target')?.value || 0);

        if (desemberValue <= 0) {
            alert("Nilai bulan Desember wajib diisi sebagai target tahunan.");
            e.preventDefault();
            return false;
        }

        if (!isValid) {
            alert("Input target bulanan harus kumulatif (tidak boleh menurun).");
            e.preventDefault();
            return false;
        }
    });

    highlightInputs();
    updateTargetTahunan();

    // Tombol distribusi otomatis (jika diaktifkan)
    document.getElementById('distributeBtn')?.addEventListener('click', () => {
        const total = parseFloat(document.querySelector('.december-target')?.value || 0);
        if (total <= 0) {
            alert('Isi target bulan Desember terlebih dahulu!');
            return;
        }
        const perBulan = total / 12;
        document.querySelectorAll('.monthly-target').forEach((input, i) => {
            if (i !== 11) {
                input.value = (perBulan * (i + 1)).toFixed(3);
            }
        });
        updateTargetTahunan();
        validateKumulatif();
        alert(`Target didistribusi otomatis!\nTotal: ${total.toFixed(3)}, per bulan: ${perBulan.toFixed(3)}`);
    });
});
</script>
@endsection

