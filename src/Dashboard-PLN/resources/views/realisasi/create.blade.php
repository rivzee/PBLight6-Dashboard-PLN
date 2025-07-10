
@extends('layouts.app')

@section('title', 'Input Realisasi KPI')
@section('page_title', 'INPUT REALISASI KPI')

@section('styles')
<style>
    /* Main Container */
    .dashboard-content {
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Page Header - Modern UI */
    .page-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
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

    .page-header-actions {
        display: flex;
        gap: 10px;
    }

    /* Card Styling - Support Dark/Light Mode */
    .form-card {
        border-radius: 16px;
        box-shadow: 0 8px 20px var(--pln-shadow);
        background-color: var(--pln-surface);
        margin-bottom: 25px;
        overflow: hidden;
        color: var(--pln-text);
    }

    .form-card .card-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: none;
    }

    .form-card .card-body {
        padding: 20px;
    }

    /* Info Box - Support Dark/Light Mode */
    .info-box {
        background-color: var(--pln-accent-bg);
        border-left: 4px solid var(--pln-light-blue);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .info-box h6 {
        color: var(--pln-blue);
        margin-bottom: 15px;
        font-weight: 600;
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

    /* Form Styling - Support Dark/Light Mode */
    .form-group label {
        font-weight: 600;
        color: var(--pln-text);
        margin-bottom: 10px;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid var(--pln-border);
        padding: 12px 15px;
        font-size: 0.875rem;
        background-color: var(--pln-surface);
        color: var(--pln-text);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: var(--pln-light-blue);
        box-shadow: 0 0 0 0.25rem rgba(0, 156, 222, 0.25);
        background-color: var(--pln-surface);
        color: var(--pln-text);
    }

    /* Target Graphic - Support Dark/Light Mode */
    .target-visual {
        height: 50px;
        background: var(--pln-accent-bg);
        border-radius: 8px;
        position: relative;
        margin-bottom: 25px;
        border: 1px solid var(--pln-border);
        overflow: hidden;
    }

    .target-progress {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
        transition: width 0.5s ease;
    }

    .target-value {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
        color: white;
        z-index: 2;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    /* Action Buttons */
    .form-actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .btn-action {
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-action i {
        margin-right: 8px;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px var(--pln-shadow);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .page-header-actions {
            width: 100%;
            justify-content: flex-start;
            margin-top: 10px;
        }
    }
</style>
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
    