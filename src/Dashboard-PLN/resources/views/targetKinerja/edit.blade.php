@extends('layouts.app')

@section('title', 'Edit Target Kinerja')
@section('page_title', 'EDIT TARGET KINERJA')

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

    .status-badge.approved {
        background-color: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }

    .status-badge.pending {
        background-color: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }

    /* Target Table Styling */
    .target-table {
        margin-top: 15px;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .target-table th {
        padding: 15px 8px;
        font-weight: 600;
        text-align: center;
        font-size: 0.9rem;
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        border: none;
        position: relative;
    }

    .target-table th.bg-warning {
        background: linear-gradient(135deg, #ffc107, #ffcd39) !important;
        color: #000 !important;
        font-weight: 700;
    }

    .target-table td {
        padding: 12px 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        background: white;
    }

    .target-table td.bg-warning {
        background: #fff3cd !important;
        border-color: #ffc107 !important;
    }

    .target-table .monthly-target {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 8px;
        font-size: 0.95rem;
        font-weight: 500;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        min-height: 44px;
        width: 100%;
        min-width: 120px; /* Lebih lebar untuk angka besar */
    }

    .target-table .monthly-target:focus {
        border-color: var(--pln-light-blue);
        background: white;
        box-shadow: 0 0 0 0.25rem rgba(0, 156, 222, 0.25);
        outline: none;
    }

    .target-table .monthly-target.december-target {
        background: #fff3cd;
        border-color: #ffc107;
        font-weight: 700;
        color: #000;
    }

    .target-table .monthly-target.december-target:focus {
        background: #fff3cd;
        border-color: #ff9800;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }

    /* Responsive table */
    @media (max-width: 1200px) {
        .target-table th,
        .target-table td {
            padding: 10px 6px;
        }

        .target-table th {
            font-size: 0.8rem;
        }

        .target-table .monthly-target {
            font-size: 0.85rem;
            padding: 10px 6px;
        }
    }

    @media (max-width: 768px) {
        .target-table th,
        .target-table td {
            padding: 8px 4px;
        }

        .target-table th {
            font-size: 0.75rem;
        }

        .target-table .monthly-target {
            font-size: 0.8rem;
            padding: 8px 4px;
            min-height: 40px;
        }
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
        color: var(--pln-text);
        z-index: 2;
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

    .alert-custom.alert-info {
        background-color: rgba(23, 162, 184, 0.15);
        border-color: #17a2b8;
        color: var(--pln-text);
    }

    /* Text-muted - Support Dark/Light Mode */
    .text-muted {
        color: var(--pln-text-secondary) !important;
    }

    /* Form control help text */
    .form-text {
        margin-top: 5px;
        font-size: 0.8rem;
    }

    /* Distribution Button */
    .distr-btn {
        padding: 8px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        background-color: var(--pln-surface);
        border: 1px solid var(--pln-border);
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .distr-btn:hover {
        background-color: var(--pln-accent-bg);
        transform: translateY(-2px);
    }

    .distr-btn i {
        margin-right: 5px;
    }
 /* ✅ Penambahan untuk target bulanan format grid */
    .monthly-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }

    .monthly-input {
        display: flex;
        flex-direction: column;
    }

    .monthly-input label {
        font-weight: 500;
        margin-bottom: 6px;
    }

    .target-input {
        width: 100%;
    }

  /* Tampilan total target tahunan */

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
