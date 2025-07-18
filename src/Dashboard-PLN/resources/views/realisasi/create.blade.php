
@extends('layouts.app')

@section('title', 'Input Realisasi KPI')
@section('page_title', 'INPUT REALISASI KPI')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/realisasi.css') }}">
@endsection


@section('content')
    <div class="dashboard-content">
        <!-- Modern Page Header -->
        <div class="page-header">
            <div>
                <h2><i class="fas fa-plus-circle me-2"></i>Input Realisasi KPI</h2>
            </div>
            <div class="page-header-actions">
                <a href="{{ route('realisasi.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        @include('components.alert')

        <div class="form-card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Form Input Realisasi</h6>
            </div>
            <div class="card-body">
                <div class="info-box mb-4">
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
                            </div>                                <div class="info-row">
                                    <div class="info-label">Target:</div>
                                    <div class="info-value">
                                        <strong>{{ number_format($targetBulanan, 2) }}</strong>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Bidang:</div>
                                <div class="info-value">{{ $indikator->bidang->nama }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Periode:</div>
                                <div class="info-value">
                                    <strong>{{ \Carbon\Carbon::create()->month((int)$bulan)->format('F') }} {{ $tahun }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <form id="formRealisasi" method="POST" action="{{ route('realisasi.store', $indikator->id) }}">
                    @csrf

                    <input type="hidden" name="indikator_id" value="{{ $indikator->id }}">

                <!-- Bulan dan Tahun Realisasi (Otomatis dari halaman index) -->
                <div class="form-group mb-4">
                    <label for="bulan_tahun_display">Periode</label>
                    <input type="text" class="form-control" id="bulan_tahun_display" value="{{ \Carbon\Carbon::create()->month((int)$bulan)->format('F') }} {{ $tahun }}" disabled>

                    <!-- Input tersembunyi untuk dikirim ke backend -->
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                </div>

                <!-- Polaritas -->
                <div class="form-group mb-4">
                    <label for="polaritas">Jenis Polaritas <span class="text-danger">*</span></label>
                    <select name="polaritas" id="polaritas" class="form-select @error('polaritas') is-invalid @enderror" required>
                        <option value="">-- Pilih Polaritas --</option>
                        <option value="Positif" {{ old('polaritas') == 'Positif' ? 'selected' : '' }}>Positif</option>
                        <option value="Negatif" {{ old('polaritas') == 'Negatif' ? 'selected' : '' }}>Negatif</option>
                        <option value="Netral" {{ old('polaritas') == 'Netral' ? 'selected' : '' }}>Netral</option>
                    </select>
                    @error('polaritas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-adjust me-1"></i> Pilih jenis polaritas indikator ini.
                    </small>
                </div>


                <!-- Nilai -->
                <div class="form-group mb-4">
                    <label for="nilai">Nilai Realisasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nilai') is-invalid @enderror"
                     id="nilai" name="nilai" value="{{ old('nilai') }}" required >

                    @error('nilai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                

                <!-- Visual Progress (opsional, bisa aktifkan via JS jika ingin) -->
                <div id="targetVisualContainer" class="mb-4" style="display: none;">
                    <div class="target-visual">
                        <div class="target-progress" id="targetProgress" style="width: 0%"></div>
                        <div class="target-value" id="targetValue">0%</div>
                    </div>
                </div>
                <!-- Visual Panah Polaritas -->
                <div id="arrowContainer" class="mb-3" style="display: none;">
                    <label class="form-label">Arah Polaritas</label>
                    <div class="d-flex align-items-center">
                        <span id="arrowSymbol" style="font-size: 1.8rem; margin-right: 8px;"></span>
                        <small class="text-muted">Panah menunjukkan arah pencapaian berdasarkan jenis polaritas</small>
                    </div>
                </div>


                <!-- Keterangan -->
                <div class="form-group mb-4">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-edit me-1"></i> Tambahkan keterangan, kendala, atau catatan tambahan.
                    </small>
                </div>

                <!-- Tombol -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-action" id="btnSubmit">
                        <i class="fas fa-save"></i> Simpan Realisasi
                    </button>
                    <a href="{{ route('realisasi.index') }}" class="btn btn-secondary btn-action">
                        <i class="fas fa-times"></i> Batal
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
    const form = document.getElementById('formRealisasi');
    const submitBtn = document.getElementById('btnSubmit');
    const nilaiInput = document.getElementById('nilai');
    const targetProgress = document.getElementById('targetProgress');
    const targetValue = document.getElementById('targetValue');
    const targetVisual = document.getElementById('targetVisualContainer');
    const targetBulanan = {{ $targetBulanan }};
    const polaritasSelect = document.getElementById('polaritas');
    const arrowContainer = document.getElementById('arrowContainer');
    const arrowSymbol = document.getElementById('arrowSymbol');

    function updateArrow(nilai, target, polaritas) {
        let result = 0;
        let arrow = "→";

        if (polaritas === "Positif") {
            result = (nilai / target) * 100;
            arrow = result >= 100 ? "↑" : "↓";
        } else if (polaritas === "Negatif") {
            result = (2 - (nilai / target)) * 100;
            arrow = nilai <= target ? "↑" : "↓";
        } else if (polaritas === "Netral") {
            let deviation = Math.abs(nilai - target) / target;
            arrow = deviation <= 0.05 ? "→" : "↓";
        }

        arrowSymbol.innerHTML = arrow === '↑' ? '<i class="fas fa-arrow-up text-success"></i>' :
                            arrow === '↓' ? '<i class="fas fa-arrow-down text-danger"></i>' :
                            '<i class="fas fa-arrows-alt-h text-info"></i>';
        arrowContainer.style.display = "block";
    }

    // Trigger saat nilai diinput
    nilaiInput.addEventListener('input', function() {
    const rawInput = this.value;
    const cleanValue = rawInput.replace(/\./g, '').replace(',', '.');
    const nilai = parseFloat(cleanValue) || 0;

    const rawPercentage = (nilai / targetBulanan) * 100;
    const cappedPercentage = Math.max(0, Math.min(rawPercentage, 110)); // Batasi hanya untuk teks

    if (nilai >= 0) {
        targetVisual.style.display = 'block';

        const progressWidth = Math.min(rawPercentage, 100); // Jangan lebih dari 100% untuk lebar bar
        targetProgress.style.width = progressWidth + '%';

        targetValue.textContent = cappedPercentage.toFixed(1) + '%';
        targetValue.title = 'Persentase Asli: ' + rawPercentage.toFixed(1) + '%';

        if (nilai <= 0) {
            targetProgress.style.background = '#6c757d';
        } else if (rawPercentage >= 100) {
            targetProgress.style.background = '#28a745';
        } else if (rawPercentage >= 95) {
            targetProgress.style.background = '#ffc107';
        } else {
            targetProgress.style.background = '#dc3545';
        }
    }

    const selectedPolaritas = polaritasSelect.value;
    if (selectedPolaritas) {
        updateArrow(nilai, targetBulanan, selectedPolaritas);
    }
});


    // Trigger saat polaritas diganti
    polaritasSelect.addEventListener('change', function() {
        const nilai = parseFloat(nilaiInput.value) || 0;
        const selectedPolaritas = this.value;
        if (selectedPolaritas && nilai >= 0) {
            updateArrow(nilai, targetBulanan, selectedPolaritas);
        }
    });

    // Auto-trigger jika sudah ada nilai di awal
    if (nilaiInput.value) {
        nilaiInput.dispatchEvent(new Event('input'));
    }

    // Form submit - gabung jadi satu fungsi
form.addEventListener('submit', function(e) {
    // Bersihkan nilai agar jadi angka valid
    const rawInput = nilaiInput.value;
    const cleaned = rawInput.replace(/\./g, '').replace(',', '.');
    nilaiInput.value = cleaned;

    // Tombol loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

    return true;
});

});

</script>
@endsection
    