@extends('layouts.app')

@section('title', 'Tambah Target Kinerja')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/targetKinerja.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-plus-circle me-2"></i>Tambah Target Kinerja</h2>
            <div class="page-header-subtitle">
                Tentukan target bulanan untuk indikator kinerja
            </div>
        </div>
    </div>

    @include('components.alert')

    <div class="card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Form Target Kinerja Baru</h6>
        </div>
        <div class="card-body">
            <!-- Informasi Indikator -->
            <div class="info-box">
                <h6><i class="fas fa-info-circle me-2"></i>Informasi Indikator</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Kode:</div>
                            <div class="info-value">{{ $indikator->kode }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nama:</div>
                            <div class="info-value">{{ $indikator->nama }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Bobot:</div>
                            <div class="info-value">{{ $indikator->bobot }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Satuan:</div>
                            <div class="info-value">{{ $indikator->satuan }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Pilar:</div>
                            <div class="info-value">{{ $indikator->pilar->nama }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Bidang:</div>
                            <div class="info-value">{{ $indikator->bidang->nama }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tahun:</div>
                            <div class="info-value">{{ $tahunPenilaian->tahun }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Input Target -->
            <form action="{{ route('targetKinerja.store') }}" method="POST" id="targetForm">
                @csrf
                <input type="hidden" name="indikator_id" value="{{ $indikator->id }}">
                <input type="hidden" name="tahun_penilaian_id" value="{{ $tahunPenilaian->id }}">

                {{-- <!-- Penjelasan Input Target -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Input Target Kumulatif:</strong><br>
                    • Masukkan target kumulatif untuk setiap bulan<br>
                    • Target tahunan diambil dari nilai bulan Desember<br>
                    • Contoh: Jan=100, Feb=250, Mar=400, dst.
                </div> --}}

                <!-- Input Target Bulanan -->
                <div class="form-group">
                    <label><strong>Target Kumulatif per Bulan ({{ $indikator->satuan }})</strong> <span class="text-danger">*</span></label>
                    <div class="monthly-grid">
                        @php
                            $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        @endphp
                        @foreach($namaBulan as $i => $namaBlnIni)
                            <div class="monthly-input">
                                <label>{{ $namaBlnIni }} {{ $tahunPenilaian->tahun }}</label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control target-input @error('target_bulanan.'.$i) is-invalid @enderror"
                                           name="target_bulanan[{{ $i }}]"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('target_bulanan.'.$i, 0) }}"
                                           required
                                           placeholder="0"
                                           data-month="{{ $i }}">
                                    <span class="input-group-text">{{ $indikator->satuan }}</span>
                                </div>
                                @error('target_bulanan.'.$i)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                    @error('target_bulanan')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                    </div>
                    @error('target_bulanan')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Display Total Target -->
                <div class="total-display">
                    <i class="fas fa-chart-line me-2"></i>
                    <strong>Target Tahunan: <span id="targetTahunan">0</span> {{ $indikator->satuan }}</strong>
                </div>

                {{-- <!-- Keterangan -->
                <div class="form-group mt-4">
                    <label for="keterangan">Keterangan (Opsional)</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan"
                              name="keterangan"
                              rows="3"
                              placeholder="Masukkan keterangan tambahan jika diperlukan">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Target
                    </button>
                    <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tahunPenilaian->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update target tahunan dan validasi kumulatif
    function updateTargetTahunan() {
        const inputs = document.querySelectorAll('.target-input');
        const desemberInput = inputs[11]; // Desember adalah index 11
        const targetTahunan = parseFloat(desemberInput.value) || 0;

        document.getElementById('targetTahunan').textContent = targetTahunan.toFixed(2);

        // Update display berdasarkan apakah target tahunan sudah diisi
        const totalDisplay = document.querySelector('.total-display');
        if (targetTahunan > 0) {
            totalDisplay.style.backgroundColor = '#e8f5e8';
            totalDisplay.style.borderColor = '#b8e6b8';
        } else {
            totalDisplay.style.backgroundColor = '#f8f9fc';
            totalDisplay.style.borderColor = '#e3e6f0';
        }
    }

    // Function to validate kumulatif (nilai harus naik)
    function validateKumulatif() {
        const inputs = document.querySelectorAll('.target-input');
        let previousValue = 0;
        let allValid = true;

        inputs.forEach((input, index) => {
            const currentValue = parseFloat(input.value) || 0;

            if (currentValue > 0) {
                if (currentValue < previousValue) {
                    // Nilai tidak boleh turun dari bulan sebelumnya
                    input.style.borderColor = '#dc3545';
                    input.style.backgroundColor = '#ffe6e6';
                    allValid = false;
                } else {
                    // Nilai valid
                    input.style.borderColor = '#28a745';
                    input.style.backgroundColor = '#f8fff8';
                }
                previousValue = currentValue;
            } else {
                // Reset styling jika kosong
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        });

        return allValid;
    }

    // Add event listeners to all target inputs
    document.querySelectorAll('.target-input').forEach((input, index) => {
        input.addEventListener('input', function() {
            updateTargetTahunan();
            validateKumulatif();
        });

        // Auto-fill suggestion untuk memudahkan input kumulatif
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

    // Update initial target tahunan
    updateTargetTahunan();

    // Form validation
    document.getElementById('targetForm').addEventListener('submit', function(e) {
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
});
</script>
@endsection

