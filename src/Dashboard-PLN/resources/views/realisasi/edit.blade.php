@extends('layouts.app')

@section('title', 'Edit Realisasi KPI')
@section('page_title', 'EDIT REALISASI KPI')

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
        background: linear-gradient(135deg, var(--pln                <div class="text-center text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Ketentuan Warna (Visual):</strong>
                    <span class="badge bg-success">Tercapai (≥100%)</span>
                    <span class="badge bg-warning">Hampir Tercapai (95-99%)</span>
                    <span class="badge bg-danger">Perlu Peningkatan (<95%)</span>
                    <span class="badge bg-secondary">Belum Diukur</span>
                </div>e), var(--pln-light-blue));
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

    .page-header-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
    }

    .page-header-badge i {
        margin-right: 5px;
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

    /* Status Badge */
    .status-badge {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }

    .status-badge i {
        margin-right: 5px;
    }

    .status-badge.verified {
        background-color: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }

    .status-badge.pending {
        background-color: rgba(255, 193, 7, 0.15);
        color: #ffc107;
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

    /* Alert Styles - Support Dark/Light Mode */
    .alert-custom {
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-left: 4px solid;
        display: flex;
        align-items: flex-start;
    }

    .alert-custom i {
        margin-right: 10px;
        font-size: 1.1rem;
        margin-top: 2px;
    }

    .alert-custom.alert-warning {
        background-color: rgba(255, 193, 7, 0.15);
        border-color: #ffc107;
        color: var(--pln-text);
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
            <h2><i class="fas fa-edit me-2"></i>Edit Realisasi KPI</h2>
            <div class="page-header-subtitle">
                Ubah data realisasi kinerja untuk periode:
                {{ \Carbon\Carbon::create(null, $realisasi->bulan, 1)->locale('id')->monthName }} {{ $realisasi->tahun }}
            </div>
        </div>
        <div class="page-header-actions">
            @if($realisasi->diverifikasi)
                <div class="page-header-badge">
                    <i class="fas fa-check-circle"></i> Sudah Diverifikasi
                </div>
            @else
                <div class="page-header-badge">
                    <i class="fas fa-clock"></i> Menunggu Verifikasi
                </div>
            @endif
            <a href="{{ route('realisasi.index', ['tahun' => $realisasi->tahun, 'bulan' => $realisasi->bulan]) }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @include('components.alert')

    <div class="form-card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Form Edit Realisasi</h6>
            @if($realisasi->diverifikasi)
                <span class="status-badge verified">
                    <i class="fas fa-check-circle"></i> Sudah Diverifikasi
                </span>
            @else
                <span class="status-badge pending">
                    <i class="fas fa-clock"></i> Menunggu Verifikasi
                </span>
            @endif
        </div>
        <div class="card-body">
            <div class="info-box mb-4">
                <h6><i class="fas fa-info-circle me-2"></i>Informasi Indikator</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Kode:</div>
                            <div class="info-value">{{ $realisasi->indikator->kode }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nama:</div>
                            <div class="info-value">{{ $realisasi->indikator->nama }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Target Bulanan:</div>
                            <div class="info-value">{{ number_format($targetBulanan, 2) }} {{ $realisasi->indikator->satuan }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Jenis Polaritas:</div>
                            <div class="info-value">
                                @if($realisasi->jenis_polaritas == 'positif')
                                    <span class="badge bg-success">Positif</span>
                                @else
                                    <span class="badge bg-warning">Negatif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Bidang:</div>
                            <div class="info-value">{{ $realisasi->indikator->bidang->nama }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Periode:</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::create(null, $realisasi->bulan, 1)->locale('id')->monthName }} {{ $realisasi->tahun }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tipe:</div>
                            <div class="info-value">Bulanan</div>
                        </div>
                    </div>
                </div>
                @if($realisasi->indikator->deskripsi)
                    <div class="info-row mt-2">
                        <div class="info-label">Deskripsi:</div>
                        <div class="info-value">{{ $realisasi->indikator->deskripsi }}</div>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('realisasi.update', $realisasi->id) }}" id="formEdit">
                @csrf
                @method('PUT')

                <div class="form-group mb-4">
                    <label for="nilai">Nilai Realisasi <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('nilai') is-invalid @enderror"
                           id="nilai" name="nilai" value="{{ old('nilai', $realisasi->nilai) }}" required
                           {{ $realisasi->diverifikasi && !auth()->user()->isMasterAdmin() ? 'readonly' : '' }}>
                    @error('nilai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i> Masukkan nilai realisasi bulanan untuk indikator ini.
                    </small>
                </div>

                <div class="target-visual mb-4" id="targetVisual">
                    <div class="target-progress" id="targetProgress" style="width: {{ min(($realisasi->nilai_polaritas ?? 0), 100) }}%"></div>
                    <div class="target-value" id="targetValue">{{ number_format($realisasi->nilai_polaritas ?? 0, 1) }}%</div>
                </div>
                <!-- Visual Arah Panah Polaritas -->
                <div id="arrowContainer" class="mb-4" style="display: none;">
                    <label class="form-label">Arah Pencapaian</label>
                    <div class="d-flex align-items-center">
                        <span id="arrowSymbol" style="font-size: 1.8rem; margin-right: 8px;"></span>
                        <small class="text-muted">Panah menunjukkan arah berdasarkan jenis polaritas dan nilai realisasi</small>
                    </div>
                </div>


                <div class="text-center text-muted small mb-4">
                    <i class="fas fa-info-circle me-1"></i>
                    Persentase pencapaian terhadap target bulanan.
                </div>

                <div class="form-group mb-4">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan" name="keterangan" rows="3"
                              {{ $realisasi->diverifikasi && !auth()->user()->isMasterAdmin() ? 'readonly' : '' }}>{{ old('keterangan', $realisasi->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-edit me-1"></i> Tambahkan keterangan, kendala, atau informasi tambahan jika diperlukan.
                    </small>
                </div>

                @if(!$realisasi->diverifikasi || auth()->user()->isMasterAdmin())
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-action" id="btnSubmit">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>

                        {{-- @if(auth()->user()->isMasterAdmin() && !$realisasi->diverifikasi)
                            <a href="{{ route('realisasi.verify', $realisasi->id) }}" class="btn btn-success btn-action">
                                <i class="fas fa-check-circle"></i> Verifikasi
                            </a>
                        @endif

                        @if(auth()->user()->isMasterAdmin() && $realisasi->diverifikasi)
                            <a href="{{ route('realisasi.unverify', $realisasi->id) }}" class="btn btn-warning btn-action">
                                <i class="fas fa-times-circle"></i> Batal Verifikasi
                            </a>
                        @endif --}}

                        <a href="{{ route('realisasi.index', ['tahun' => $realisasi->tahun, 'bulan' => $realisasi->bulan]) }}" class="btn btn-secondary btn-action">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                @else
                    <div class="alert-custom alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>Data yang sudah diverifikasi tidak dapat diubah kecuali oleh administrator.</div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
const arrowContainer = document.getElementById('arrowContainer');
const arrowSymbol = document.getElementById('arrowSymbol');

function updateArrow(nilai, target, polaritas) {
    let result = 0;

    if (polaritas === "positif") {
        result = (nilai / target) * 100;
        if (result >= 100) {
            arrowSymbol.innerHTML = '<i class="fas fa-arrow-up text-success"></i>'; // baik
        } else {
            arrowSymbol.innerHTML = '<i class="fas fa-arrow-down text-danger"></i>'; // buruk
        }
    } else if (polaritas === "negatif") {
        result = (2 - (nilai / target)) * 100;
        if (nilai <= target) {
            arrowSymbol.innerHTML = '<i class="fas fa-arrow-down text-success"></i>'; // baik (lebih kecil = lebih bagus)
        } else {
            arrowSymbol.innerHTML = '<i class="fas fa-arrow-up text-danger"></i>'; // buruk
        }
    } else if (polaritas === "netral") {
        let deviation = Math.abs(nilai - target) / target;
        if (deviation <= 0.05) {
            arrowSymbol.innerHTML = '<i class="fas fa-arrows-alt-h text-info"></i>'; // netral
        } else {
            arrowSymbol.innerHTML = '<i class="fas fa-arrow-down text-danger"></i>'; // terlalu menyimpang
        }
    }

    arrowContainer.style.display = "block";
}


document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formEdit');
    const submitBtn = document.getElementById('btnSubmit');
    const nilaiInput = document.getElementById('nilai');
    const targetProgress = document.getElementById('targetProgress');
    const targetValue = document.getElementById('targetValue');
    const targetVisual = document.getElementById('targetVisual');
    const target = {{ $targetBulanan > 0 ? $targetBulanan : 1 }};
    const polaritas = "{{ $realisasi->jenis_polaritas }}";

   function setProgressColor(percentage, nilai) {
    if (nilai <= 0) {
        targetProgress.style.background = '#6c757d'; // abu-abu jika nol
    } else {
        if (percentage > 100) {
            targetProgress.style.background = '#28a745'; // hijau
        } else if (percentage >= 95) {
            targetProgress.style.background = '#ffc107'; // kuning
        } else {
            targetProgress.style.background = '#dc3545'; // merah
        }
    }
}


    nilaiInput.addEventListener('input', function() {
        const nilai = parseFloat(this.value) || 0;
        let rawPercentage = 0;

        if (polaritas === 'positif') {
            rawPercentage = (nilai / target) * 100;
        } else if (polaritas === 'negatif') {
            rawPercentage = (2 - (nilai / target)) * 100;
        } else if (polaritas === 'netral') {
            rawPercentage = (Math.abs(nilai - target) <= (0.05 * target)) ? 100 : 0;
        }

        const cappedPercentage = Math.max(0, Math.min(rawPercentage, 110));

        updateArrow(nilai, target, polaritas);

        if (nilai >= 0) {
            targetVisual.style.display = 'block';
            const progressWidth = Math.min(rawPercentage, 100);
            targetProgress.style.width = progressWidth + '%';
            targetValue.textContent = cappedPercentage.toFixed(1) + '%';
            targetValue.title = 'Persentase Asli: ' + rawPercentage.toFixed(1) + '%';

            setProgressColor(rawPercentage, nilai);
        }
    });

    // Tampilan awal
    const initialNilai = {{ $realisasi->nilai }};
    let initialPercentage = 0;

    if (polaritas === 'positif') {
        initialPercentage = (initialNilai / target) * 100;
    } else if (polaritas === 'negatif') {
        initialPercentage = (2 - (initialNilai / target)) * 100;
    } else if (polaritas === 'netral') {
        initialPercentage = (Math.abs(initialNilai - target) <= (0.05 * target)) ? 100 : 0;
    }

    const initialProgressWidth = Math.min(initialPercentage, 100);
    updateArrow(initialNilai, target, polaritas);


    targetProgress.style.width = initialProgressWidth + '%';
    targetValue.textContent = initialPercentage.toFixed(1) + '%';
    targetValue.title = 'Persentase Asli: ' + initialPercentage.toFixed(1) + '%';
    targetVisual.style.display = 'block';
    setProgressColor(initialPercentage, initialNilai);

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            return true;
        });
    }
});
</script>
@endsection
