@extends('layouts.app')

@section('title', 'Tambah Target Kinerja')

@section('styles')
<style>
    :root {
        --pln-blue: #0a4d85;
        --pln-light-blue: #009cde;
        --pln-bg: #f5f7fa;
        --pln-surface: #ffffff;
        --pln-text: #333333;
        --pln-border: #e3e6f0;
        --pln-shadow: rgba(0, 0, 0, 0.1);
        --pln-accent-bg: #f8f9fc;
        --pln-text-secondary: #6c757d;
    }

    .dashboard-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
    }

    .page-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .page-header-subtitle {
        margin-top: 5px;
        font-weight: 400;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 8px 20px var(--pln-shadow);
        background-color: var(--pln-surface);
        border: 1px solid var(--pln-border);
        margin-bottom: 25px;
    }

    .card-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        padding: 15px 20px;
        border-radius: 16px 16px 0 0;
        border: none;
    }

    .info-box {
        background-color: var(--pln-accent-bg);
        border-left: 4px solid var(--pln-light-blue);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .info-row {
        display: flex;
        margin-bottom: 10px;
    }

    .info-label {
        font-weight: 600;
        min-width: 120px;
        color: var(--pln-text-secondary);
    }

    .info-value {
        flex: 1;
        color: var(--pln-text);
    }

    .monthly-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .monthly-input label {
        display: block;
        font-weight: 500;
        font-size: 0.9rem;
        margin-bottom: 5px;
        color: var(--pln-text);
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid var(--pln-border);
        padding: 10px 15px;
        font-size: 0.9rem;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: var(--pln-light-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 156, 222, 0.25);
    }

    .alert-info {
        background-color: rgba(23, 162, 184, 0.1);
        border: 1px solid #bee5eb;
        color: #0c5460;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .btn-primary {
        background-color: var(--pln-blue);
        border-color: var(--pln-blue);
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: var(--pln-light-blue);
        border-color: var(--pln-light-blue);
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }

    .input-group-text {
        background-color: var(--pln-accent-bg);
        border: 1px solid var(--pln-border);
        border-left: none;
        color: var(--pln-text-secondary);
        font-size: 0.85rem;
    }

    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .total-display {
        background-color: #e8f5e8;
        border: 1px solid #b8e6b8;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        text-align: center;
    }

    .total-display strong {
        color: var(--pln-blue);
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .monthly-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>
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
document.addEventListener('DOMContentLoaded', function () {
    const satuan = "{{ strtolower($indikator->satuan) }}";
    const isTanggal = satuan.includes('waktu') || satuan.includes('tanggal');
    const inputs = document.querySelectorAll('.target-input');
    const totalDisplay = document.querySelector('.total-display');

    function formatRibuan(nilai) {
        if (!nilai) return '';
        const angka = parseFloat(nilai.toString().replaceAll('.', '').replace(',', '.'));
        if (isNaN(angka)) return '';
        return angka.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function parseToFloat(str) {
        return parseFloat(str.replaceAll('.', '').replace(',', '.')) || 0;
    }

    function updateTargetTahunan() {
        const desemberInput = inputs[11];
        if (isTanggal) {
            const tanggal = desemberInput.value;
            const tampilanTanggal = tanggal
                ? new Date(tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                : '-';
            document.getElementById('targetTahunan').textContent = tampilanTanggal;
            totalDisplay.style.display = 'block';
        } else {
            const nilai = parseToFloat(desemberInput.value);
            document.getElementById('targetTahunan').textContent = nilai.toLocaleString('id-ID', { minimumFractionDigits: 2 });
            totalDisplay.style.display = 'block';
        }
    }

    function validateKumulatif() {
        if (isTanggal) return true;
        let previous = 0;
        let valid = true;

        inputs.forEach(input => {
            const val = parseToFloat(input.value);
            if (val > 0) {
                if (val < previous) {
                    input.style.borderColor = '#dc3545';
                    input.style.backgroundColor = '#ffe6e6';
                    valid = false;
                } else {
                    input.style.borderColor = '#28a745';
                    input.style.backgroundColor = '#f8fff8';
                }
                previous = val;
            } else {
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        });

        return valid;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            updateTargetTahunan();
            validateKumulatif();
        });

        input.addEventListener('blur', function () {
            if (!isTanggal) this.value = formatRibuan(this.value);
        });

        input.addEventListener('focus', function () {
            if (!isTanggal) this.value = this.value.replaceAll('.', '').replace(',', '.');

            if (index > 0) {
                const previous = parseToFloat(inputs[index - 1].value);
                if (previous > 0 && !this.value) {
                    this.placeholder = `Min: ${previous.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
                }
            }
        });
    });

    updateTargetTahunan();

    document.getElementById('targetForm').addEventListener('submit', function (e) {
        const desemberInput = inputs[11];
        const isAnyFilled = Array.from(inputs).some(input => parseToFloat(input.value) > 0);

        if (!isAnyFilled) {
            e.preventDefault();
            alert('Minimal target untuk satu bulan harus diisi!');
            return;
        }

        if (isTanggal) {
            if (!desemberInput.value) {
                e.preventDefault();
                alert('Target bulan Desember harus diisi sebagai patokan target tahunan!');
                return;
            }
        } else {
            const desemberValue = parseToFloat(desemberInput.value);
            if (desemberValue <= 0) {
                e.preventDefault();
                alert('Target bulan Desember harus diisi untuk menentukan target tahunan!');
                return;
            }

            if (!validateKumulatif()) {
                e.preventDefault();
                alert('Target kumulatif tidak valid! Nilai harus meningkat atau sama tiap bulan.');
                return;
            }

            // ✅ Format input jadi angka murni sebelum kirim
            inputs.forEach(input => {
                input.value = parseToFloat(input.value);
            });
        }
    });
});

</script>

@endsection

