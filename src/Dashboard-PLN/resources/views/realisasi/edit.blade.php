@extends('layouts.app')

@section('title', 'Edit Realisasi KPI')
@section('page_title', 'EDIT REALISASI KPI')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/realisasi.css') }}">
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
                   <input type="text" class="form-control @error('nilai') is-invalid @enderror"
                        id="nilai" name="nilai"
                        value="{{ old('nilai', $realisasi->nilai) }}"
                        {{ $realisasi->diverifikasi && !auth()->user()->isMasterAdmin() ? 'readonly' : '' }} required>

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
function parseLocalizedNumber(str) {
    if (!str) return 0;
    return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
}

const arrowContainer = document.getElementById('arrowContainer');
const arrowSymbol = document.getElementById('arrowSymbol');

function updateArrow(nilai, target, polaritas) {
    let result = 0;

    if (polaritas === "positif") {
        result = (nilai / target) * 100;
        arrowSymbol.innerHTML = result >= 100
            ? '<i class="fas fa-arrow-up text-success"></i>' // baik
            : '<i class="fas fa-arrow-down text-danger"></i>'; // kurang
    } else if (polaritas === "negatif") {
        result = (2 - (nilai / target)) * 100;
        arrowSymbol.innerHTML = nilai <= target
            ? '<i class="fas fa-arrow-down text-success"></i>' // baik
            : '<i class="fas fa-arrow-up text-danger"></i>'; // buruk
    } else if (polaritas === "netral") {
        let deviation = Math.abs(nilai - target) / target;
        arrowSymbol.innerHTML = deviation <= 0.05
            ? '<i class="fas fa-arrows-alt-h text-info"></i>' // netral
            : '<i class="fas fa-arrow-down text-danger"></i>'; // terlalu menyimpang
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
            targetProgress.style.background = '#6c757d'; // abu-abu
        } else if (percentage > 100) {
            targetProgress.style.background = '#28a745'; // hijau
        } else if (percentage >= 95) {
            targetProgress.style.background = '#ffc107'; // kuning
        } else {
            targetProgress.style.background = '#dc3545'; // merah
        }
    }

    function updateProgress(nilai) {
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
        targetProgress.style.width = Math.min(rawPercentage, 100) + '%';
        targetValue.textContent = cappedPercentage.toFixed(1) + '%';
        targetValue.title = 'Persentase Asli: ' + rawPercentage.toFixed(1) + '%';
        targetVisual.style.display = 'block';
        setProgressColor(rawPercentage, nilai);
    }

    // Input perubahan nilai realisasi
    nilaiInput.addEventListener('input', function() {
        const nilai = parseLocalizedNumber(this.value);
        updateProgress(nilai);
    });

    // Tampilan awal saat load
    const initialNilai = parseLocalizedNumber("{{ $realisasi->nilai }}");
    updateProgress(initialNilai);

    // Submit form
    form.addEventListener('submit', function(e) {
        const rawInput = nilaiInput.value;
        const cleaned = rawInput.replace(/\./g, '').replace(',', '.');
        nilaiInput.value = cleaned;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
    });
});
</script>

@endsection
