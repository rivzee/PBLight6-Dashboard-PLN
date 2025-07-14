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

            <form id="targetForm" action="{{ route('targetKinerja.update', $target->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                        class="form-control target-input {{ $i === 11 ? 'december-target' : '' }} @error('target_bulanan.'.$i) is-invalid @enderror"
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

                <div class="total-display mt-3">
                    <i class="fas fa-chart-line me-2"></i>
                    <strong>Target Tahunan: <span id="targetTahunan">{{ number_format($target_bulanan[11], 3) }}</span> {{ $indikator->satuan }}</strong>
                </div>

                @if(!$target->disetujui || auth()->user()->isMasterAdmin())
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                        <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tahunPenilaian->id]) }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Batal</a>
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
document.addEventListener('DOMContentLoaded', function() {
    function updateTargetTahunan() {
        const inputs = document.querySelectorAll('.target-input');
        const desemberInput = inputs[11]; 
        const targetTahunan = parseFloat(desemberInput.value) || 0;

        document.getElementById('targetTahunan').textContent = targetTahunan.toFixed(3);

        const totalDisplay = document.querySelector('.total-display');
        if (targetTahunan > 0) {
            totalDisplay.style.backgroundColor = '#e8f5e8';
            totalDisplay.style.borderColor = '#b8e6b8';
        } else {
            totalDisplay.style.backgroundColor = '#f8f9fc';
            totalDisplay.style.borderColor = '#e3e6f0';
        }
    }

    function validateKumulatif() {
        const inputs = document.querySelectorAll('.target-input');
        let previousValue = 0;
        let allValid = true;

        inputs.forEach((input, index) => {
            const currentValue = parseFloat(input.value) || 0;

            if (currentValue > 0) {
                if (currentValue < previousValue) {
                    input.style.borderColor = '#dc3545';
                    input.style.backgroundColor = '#ffe6e6';
                    allValid = false;
                } else {
                    input.style.borderColor = '#28a745';
                    input.style.backgroundColor = '#f8fff8';
                }
                previousValue = currentValue;
            } else {
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        });

        return allValid;
    }

    document.querySelectorAll('.target-input').forEach((input, index) => {
        input.addEventListener('input', function() {
            updateTargetTahunan();
            validateKumulatif();
        });

        input.addEventListener('focus', function() {
            if (index > 0) {
                const previousInput = document.querySelectorAll('.target-input')[index - 1];
                const previousValue = parseFloat(previousInput.value) || 0;

                if (previousValue > 0 && !this.value) {
                    this.placeholder = `Min: ${previousValue.toFixed(2)}`;
                }
            }
        });
    });

    updateTargetTahunan();

    const form = document.getElementById('targetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const desemberValue = parseFloat(document.querySelectorAll('.target-input')[11].value) || 0;

            if (desemberValue <= 0) {
                e.preventDefault();
                alert('Target bulan Desember harus diisi untuk menentukan target tahunan!');
                return false;
            }

            if (!validateKumulatif()) {
                e.preventDefault();
                alert('Target kumulatif tidak valid! Nilai target harus selalu naik atau sama dari bulan sebelumnya.');
                return false;
            }
        });
    }
});
</script>
@endsection
