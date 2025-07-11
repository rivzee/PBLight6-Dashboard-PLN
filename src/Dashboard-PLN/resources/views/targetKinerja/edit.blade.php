@extends('layouts.app')

@section('title', 'Edit Target Kinerja')
@section('page_title', 'EDIT TARGET KINERJA')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/targetKinerja.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Modern Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-edit me-2"></i>Edit Target Kinerja</h2>
            <div class="page-header-subtitle">
                Ubah dan sesuaikan target untuk indikator kinerja
            </div>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tahunPenilaian->id]) }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
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
                @if($indikator->deskripsi)
                    <div class="info-row mt-2">
                        <div class="info-label">Deskripsi:</div>
                        <div class="info-value">{{ $indikator->deskripsi }}</div>
                    </div>
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
            $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $target_bulanan_array = old('target_bulanan', $target->target_bulanan) ?? [];

            if (is_array($target_bulanan_array)) {
                $target_bulanan_values = array_values($target_bulanan_array);
            } else {
                $target_bulanan_values = [];
            }

            for ($i = count($target_bulanan_values); $i < 12; $i++) {
                $target_bulanan_values[$i] = 0;
            }
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
                           value="{{ old('target_bulanan.'.$i, $target_bulanan_values[$i] ?? 0) }}"
                           {{ $target->disetujui && !auth()->user()->isMasterAdmin() ? 'readonly' : '' }}
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

<!-- Display Total Target -->
<div class="total-display mt-3">
    <i class="fas fa-chart-line me-2"></i>
    <strong>Target Tahunan: <span id="targetTahunan">
        {{ array_sum($target_bulanan_values) }}
    </span> {{ $indikator->satuan }}</strong>
</div>


                @if(!$target->disetujui || auth()->user()->isMasterAdmin())
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-action">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>

                        <a href="{{ route('targetKinerja.index', ['tahun_penilaian_id' => $tahunPenilaian->id]) }}" class="btn btn-secondary btn-action">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                @else
                    <div class="alert-custom alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>Target yang sudah disetujui tidak dapat diubah kecuali oleh Master Admin.</div>
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
        // Fungsi untuk highlight Desember
        function highlightDecember() {
            const decemberInput = document.querySelector('.december-target');
            if (decemberInput) {
                decemberInput.style.backgroundColor = '#fff3cd';
                decemberInput.style.fontWeight = 'bold';
                decemberInput.style.borderColor = '#ffc107';
            }
        }

        // Fungsi untuk update visual feedback
        function updateVisualFeedback() {
            const decemberValue = parseFloat(document.querySelector('.december-target').value) || 0;

            // Update tooltip untuk Desember
            const decemberInput = document.querySelector('.december-target');
            decemberInput.title = `Target Tahunan: ${decemberValue.toFixed(3)}`;

            // Visual feedback untuk semua input
            document.querySelectorAll('.monthly-target').forEach(function(input, index) {
                const value = parseFloat(input.value) || 0;

                if (value > 0) {
                    if (index === 11) {
                        input.style.backgroundColor = '#fff3cd';
                        input.style.borderColor = '#ffc107';
                    } else {
                        input.style.backgroundColor = '#f8f9fa';
                        input.style.borderColor = '#28a745';
                    }
                } else {
                    if (index === 11) {
                        input.style.backgroundColor = '#fff3cd';
                        input.style.borderColor = '#ffc107';
                    } else {
                        input.style.backgroundColor = '#f8f9fa';
                        input.style.borderColor = '#e9ecef';
                    }
                }
            });
        }

        // Event listener untuk setiap input bulanan
        document.querySelectorAll('.monthly-target').forEach(function(input, index) {
            input.addEventListener('input', function() {
                // Validasi nilai tidak negatif
                if (parseFloat(this.value) < 0) {
                    this.value = 0;
                }

                updateVisualFeedback();

                // Jika ini adalah input Desember, berikan feedback khusus
                if (index === 11) {
                    const value = parseFloat(this.value) || 0;
                    console.log('Target Tahunan (Desember):', value);
                }
            });

            // Highlight saat focus
            input.addEventListener('focus', function() {
                this.style.boxShadow = '0 0 0 0.25rem rgba(0, 123, 255, 0.25)';
            });

            input.addEventListener('blur', function() {
                this.style.boxShadow = 'none';
            });
        });

        // Tombol distribusi otomatis
        document.getElementById('distributeBtn').addEventListener('click', function() {
            const decemberValue = parseFloat(document.querySelector('.december-target').value) || 0;

            if (decemberValue <= 0) {
                alert('Silakan masukkan nilai target Desember (target tahunan) terlebih dahulu!');
                document.querySelector('.december-target').focus();
                return;
            }

            // Distribusi nilai secara merata dalam bentuk kumulatif
            const monthlyValue = decemberValue / 12;

            document.querySelectorAll('.monthly-target').forEach(function(input, index) {
                // Kecuali Desember, isi dengan nilai kumulatif
                if (index !== 11) {
                    const kumulatifValue = monthlyValue * (index + 1);
                    input.value = kumulatifValue.toFixed(3);
                }
            });

            updateVisualFeedback();

            // Tampilkan konfirmasi
            const confirmMsg = `Target bulanan berhasil didistribusi!\nTarget per bulan: ${monthlyValue.toFixed(3)}\nTarget tahunan (Desember): ${decemberValue.toFixed(3)}`;
            alert(confirmMsg);
        });

        // Inisialisasi
        highlightDecember();
        updateVisualFeedback();

        // // Pesan bantuan untuk user
        // const helpMessage = document.createElement('div');
        // helpMessage.className = 'alert alert-info mt-3';
        // helpMessage.innerHTML = `
        //     <i class="fas fa-lightbulb"></i>
        //     <strong>Tips:</strong>
        //     <ul class="mb-0 mt-2">
        //         <li>Isi nilai target <strong>kumulatif</strong> untuk setiap bulan sesuai rencana</li>
        //         <li>Nilai bulan <strong>Desember</strong> akan menjadi target tahunan secara otomatis</li>
        //         <li>Gunakan tombol "Distribusi Otomatis" untuk mengisi target bulanan secara merata</li>
        //         <li>Target tahunan = nilai yang Anda masukkan di kolom Desember</li>
        //         <li>Contoh: Target per bulan 10, maka input: Jan=10, Feb=20, Mar=30, dst.</li>
        //     </ul>
        // `;

        // Tambahkan setelah tabel
        const tableContainer = document.querySelector('.table-responsive');
        if (tableContainer) {
            tableContainer.parentNode.insertBefore(helpMessage, tableContainer.nextSibling);
        }
    });
</script>
@endsection
