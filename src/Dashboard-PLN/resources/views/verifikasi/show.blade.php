@extends('layouts.app')

@section('title', 'Detail Verifikasi KPI')
@section('page_title', 'DETAIL VERIFIKASI KPI')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/verifikasi.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="detail-title">
        <h2>Detail KPI</h2>
        <a href="{{ route('verifikasi.index') }}" class="btn-kembali">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if(isset($isPeriodeLocked) && $isPeriodeLocked)
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> <strong>Peringatan!</strong> Periode penilaian tahun {{ $realisasi->tahun }} sedang terkunci. Anda tidak dapat melakukan verifikasi pada periode ini.
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6 fade-in" style="animation-delay: 0.1s">
            <div class="info-panel">
                <div class="info-panel-header primary-header">
                    <i class="fas fa-chart-bar"></i> Informasi Indikator
                </div>
                <div class="info-panel-body">
                    <table class="info-table">
                        <tr>
                            <th>Kode KPI</th>
                            <td>{{ $realisasi->indikator->kode }}</td>
                        </tr>
                        <tr>
                            <th>Nama KPI</th>
                            <td>{{ $realisasi->indikator->nama }}</td>
                        </tr>
                        <tr>
                            <th>Bidang</th>
                            <td>{{ $realisasi->indikator->bidang->nama }}</td>
                        </tr>
                        <tr>
                            <th>Pilar</th>
                            <td>{{ $realisasi->indikator->pilar->nama }}</td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>{{ $realisasi->indikator->satuan }}</td>
                        </tr>
                        <tr>
                            <th>Bobot</th>
                            <td>{{ $realisasi->indikator->bobot }}%</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 fade-in" style="animation-delay: 0.2s">
            <div class="info-panel">
                <div class="info-panel-header info-header">
                    <i class="fas fa-tachometer-alt"></i> Informasi Nilai
                </div>
                <div class="info-panel-body">
                    <table class="info-table">
                        <tr>
                            <th>Periode</th>
                            <td>{{ $realisasi->tahun }} - {{ date('F', mktime(0, 0, 0, $realisasi->bulan, 1)) }}</td>
                        </tr>
                        <tr>
                            <th>Nilai</th>
                            <td>{{ number_format($realisasi->nilai, 2, ',', '.') }} {{ $realisasi->indikator->satuan }}</td>
                        </tr>
                        <tr>
                            <th>Target Bulan {{ $realisasi->bulan }}</th>
                            <td>
                                @if($targetKPI && isset($targetKPI->target_bulanan[$realisasi->bulan - 1]))
                                    {{ number_format($targetKPI->target_bulanan[$realisasi->bulan - 1], 2, ',', '.') }} {{ $realisasi->indikator->satuan }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis Polaritas</th>
                            <td>
                                @if($realisasi->jenis_polaritas == 'positif')
                                    <span class="badge bg-success">Positif</span>
                                @else
                                    <span class="badge bg-warning">Negatif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Nilai Polaritas</th>
                            <td>
                                @if($realisasi->jenis_polaritas === 'positif')
                                    {{ number_format($realisasi->nilai_polaritas ?? 0, 2) }}%
                                @else
                                    {{ number_format($realisasi->nilai_polaritas ?? 0, 2) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Persentase</th>
                            <td>
                                <div class="progress mb-2">
                                    @php
                                        $progressClass = 'bg-danger';
                                        if ($realisasi->persentase >= 70) {
                                            $progressClass = 'bg-success';
                                        } elseif ($realisasi->persentase >= 50) {
                                            $progressClass = 'bg-warning';
                                        }
                                    @endphp
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $realisasi->persentase }}%" aria-valuenow="{{ $realisasi->persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                {{ number_format($realisasi->persentase, 2) }}%
                            </td>
                        </tr>
                        <tr>
                            <th>Diinput Oleh</th>
                            <td>{{ $realisasi->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Input</th>
                            <td>{{ $realisasi->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="info-panel fade-in" style="animation-delay: 0.3s">
        <div class="info-panel-header secondary-header">
            <i class="fas fa-clipboard-list"></i> Informasi Pendukung
        </div>
        <div class="info-panel-body">
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <div class="form-keterangan">
                    {!! nl2br(e($realisasi->keterangan)) ?: '<em>Tidak ada keterangan</em>' !!}
                </div>
            </div>

            @if($realisasi->bukti_url)
                <div class="mb-3">
                    <label class="form-label">Bukti Pendukung</label>
                    <div class="bukti-file">
                        <i class="fas fa-file-alt"></i>
                        <a href="{{ asset('storage/' . $realisasi->bukti_url) }}" target="_blank" class="btn-unduh">
                            <i class="fas fa-download"></i> Lihat/Unduh Bukti
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="action-buttons fade-in" style="animation-delay: 0.4s">
        @if(!isset($isPeriodeLocked) || !$isPeriodeLocked)
        <form action="{{ route('verifikasi.update', $realisasi->id) }}" method="POST" class="me-2">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-success" onclick="return confirm('Anda yakin ingin memverifikasi nilai KPI ini?')">
                <i class="fas fa-check"></i> Verifikasi KPI
            </button>
        </form>

        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolak">
            <i class="fas fa-times"></i> Tolak KPI
        </button>
        @else
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> Periode penilaian terkunci. Tidak dapat melakukan verifikasi atau penolakan.
        </div>
        @endif
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('verifikasi.destroy', $realisasi->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTolakLabel">
                        <i class="fas fa-times-circle text-danger"></i> Tolak Nilai KPI
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required></textarea>
                        <small class="text-muted">Berikan alasan yang jelas mengapa nilai KPI ini ditolak</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-kembali" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak KPI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tambahkan efek ripple pada tombol-tombol
        const buttons = document.querySelectorAll('.btn, .btn-kembali, .btn-unduh');
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
    });
</script>
@endsection
