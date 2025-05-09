@extends('layouts.app')
{{-- @extends('layouts.master') --}}

@section('title', 'Kelola Akun - PLN')
@section('page_title', 'DATA AKUN')

@section('styles')
<style>
    /* Container Style */
    .akun-container {
        background: var(--pln-accent-bg);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px var(--pln-shadow);
        width: 100%;
        margin-bottom: 30px;
        backdrop-filter: blur(10px);
        border: 1px solid var(--pln-border);
        transition: all 0.3s ease;
    }

    .akun-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--pln-border);
    }

    .akun-title {
        margin: 0;
        font-size: 1.8rem;
        color: var(--pln-text);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .btn-tambah-akun {
        background: linear-gradient(135deg, var(--pln-light-blue), var(--pln-blue));
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        font-weight: 500;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0, 156, 222, 0.3);
    }

    .btn-tambah-akun i {
        margin-right: 10px;
        font-size: 16px;
    }

    .btn-tambah-akun:hover {
        background: linear-gradient(135deg, #0094d3, #08406d);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 156, 222, 0.4);
    }

    .alert-success {
        background-color: rgba(25, 135, 84, 0.2);
        color: #4CAF50;
        padding: 18px;
        border-radius: 12px;
        margin-bottom: 25px;
        border-left: 5px solid #4CAF50;
        display: flex;
        align-items: center;
        animation: fadeIn 0.5s ease-out;
    }

    .alert-success i {
        font-size: 18px;
        margin-right: 10px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
        border-radius: 16px;
        background: var(--pln-accent-bg);
        padding: 10px;
        border: 1px solid var(--pln-border);
        box-shadow: 0 8px 25px var(--pln-shadow);
    }

    .akun-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
        table-layout: auto;
    }

    .akun-table thead tr {
        background: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
        color: white;
        text-align: left;
        border-radius: 12px;
        overflow: hidden;
    }

    .akun-table th {
        padding: 18px 20px;
        font-weight: 600;
        letter-spacing: 0.5px;
        border: none;
    }

    .akun-table th:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .akun-table th:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .akun-table td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--pln-border);
        vertical-align: middle;
    }

    .akun-table tbody tr {
        transition: all 0.3s ease;
        position: relative;
    }

    .akun-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--pln-shadow);
        z-index: 1;
    }

    .akun-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Role badges dengan style yang ditingkatkan */
    .role-badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .role-badge i {
        margin-right: 5px;
        font-size: 11px;
    }

    /* Default (Karyawan) */
    .role-badge {
        background: linear-gradient(135deg, #00a8e8 0%, #0094d3 100%);
        color: white;
        border: none;
    }

    /* Admin / Manager */
    .role-badge.admin {
        background: linear-gradient(135deg, #4CAF50 0%, #3d9140 100%);
        color: white;
    }

    /* PIC */
    .role-badge.pic {
        background: linear-gradient(135deg, #FFC107 0%, #e5ac00 100%);
        color: #333;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 8px 14px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-action i {
        margin-right: 6px;
    }

    .btn-detail {
        background: rgba(23, 162, 184, 0.15);
        color: #17a2b8;
        border: 1px solid rgba(23, 162, 184, 0.3);
    }

    .btn-detail:hover {
        background: rgba(23, 162, 184, 0.25);
        box-shadow: 0 4px 10px rgba(23, 162, 184, 0.2);
        transform: translateY(-2px);
    }

    .btn-edit {
        background: rgba(255, 193, 7, 0.15);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .btn-edit:hover {
        background: rgba(255, 193, 7, 0.25);
        box-shadow: 0 4px 10px rgba(255, 193, 7, 0.2);
        transform: translateY(-2px);
    }

    .btn-hapus {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .btn-hapus:hover {
        background: rgba(220, 53, 69, 0.25);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2);
        transform: translateY(-2px);
    }

    .form-inline {
        display: inline;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--pln-text-secondary);
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 0;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: rgba(255, 255, 255, 0.15);
    }

    .empty-state p {
        font-size: 18px;
        color: var(--pln-text-secondary);
        letter-spacing: 0.5px;
    }

    /* Modal Styles yang Lebih Modern */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: var(--pln-surface);
        border-radius: 20px;
        width: 95%;
        max-width: 500px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        border: 1px solid var(--pln-border);
        transform: translateY(20px);
        transition: all 0.4s ease;
        overflow: hidden;
    }

    .modal-backdrop.show .modal-content {
        transform: translateY(0);
    }

    .modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--pln-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(to right, rgba(10, 77, 133, 0.1), rgba(0, 156, 222, 0.1));
    }

    .modal-title {
        font-size: 1.25rem;
        color: var(--pln-text);
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: var(--pln-text);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid var(--pln-border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: linear-gradient(to right, rgba(10, 77, 133, 0.05), rgba(0, 156, 222, 0.05));
    }

    .modal-btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-btn-cancel {
        background: rgba(255, 255, 255, 0.1);
        color: var(--pln-text);
        border: 1px solid var(--pln-border);
    }

    .modal-btn-cancel:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .modal-btn-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    .modal-btn-danger:hover {
        background: linear-gradient(135deg, #c82333, #bd2130);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
    }

    .modal-btn-primary {
        background: linear-gradient(135deg, var(--pln-light-blue), var(--pln-blue));
        color: white;
        box-shadow: 0 4px 15px rgba(0, 156, 222, 0.3);
    }

    .modal-btn-primary:hover {
        background: linear-gradient(135deg, #0094d3, #08406d);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 156, 222, 0.4);
    }

    .user-detail-item {
        margin-bottom: 20px;
    }

    .user-detail-label {
        display: flex;
        align-items: center;
        color: var(--pln-text-secondary);
        font-size: 13px;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .user-detail-value {
        display: block;
        color: var(--pln-text);
        font-size: 16px;
        background: var(--pln-accent-bg);
        padding: 15px;
        border-radius: 12px;
        border: 1px solid var(--pln-border);
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /* Style untuk form pada modal */
    .modal-body .form-control {
        width: 100%;
        background: var(--pln-accent-bg);
        border: 1px solid var(--pln-border);
        padding: 15px;
        border-radius: 12px;
        color: var(--pln-text);
        font-size: 15px;
        transition: all 0.3s ease;
        margin-top: 5px;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .modal-body .form-control:focus {
        border-color: var(--pln-light-blue);
        background: rgba(255, 255, 255, 0.08);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 156, 222, 0.25);
    }

    .modal-body .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        color: var(--pln-text);
        padding-right: 40px;
    }

    .modal-body .form-select option {
        background-color: var(--pln-surface);
        color: var(--pln-text);
        padding: 10px;
    }

    /* Style dropdown lebih modern */
    .modal-body .form-select::-ms-expand {
        display: none;
    }

    .modal-body .form-select:focus {
        border-color: var(--pln-light-blue);
        box-shadow: 0 0 0 3px rgba(0, 156, 222, 0.25);
    }

    .modal-body .form-validation-error {
        color: #f44336;
        font-size: 12px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    .modal-backdrop .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 5px solid #dc3545;
    }

    .modal-backdrop .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--pln-light-blue);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #0094d3;
    }

    /* Style untuk animasi transition pada modal */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-backdrop.show .modal-content {
        animation: modalFadeIn 0.35s forwards;
    }

    @media (max-width: 768px) {
        .akun-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .btn-tambah-akun {
            width: 100%;
            justify-content: center;
        }

        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }

        .btn-action {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="akun-container">
    <div class="akun-header">
        <h2 class="akun-title">Daftar Akun</h2>
        <button type="button" class="btn-tambah-akun" onclick="showAddModal()">
            <i class="fas fa-plus-circle"></i> Tambah Akun
        </button>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="akun-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleClass = 'role-badge';
                                $roleIcon = 'fa-user';

                                if (strpos($user->role, 'asisten_manager') !== false) {
                                    $roleClass .= ' admin';
                                    $roleIcon = 'fa-user-shield';
                                } elseif (strpos($user->role, 'pic') !== false) {
                                    $roleClass .= ' pic';
                                    $roleIcon = 'fa-user-tie';
                                }
                            @endphp
                            <span class="{{ $roleClass }}">
                                <i class="fas {{ $roleIcon }}"></i>
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn-action btn-detail"
                                    onclick="showDetailModal('{{ $user->name }}', '{{ $user->email }}', '{{ ucwords(str_replace('_', ' ', $user->role)) }}')">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <button type="button" class="btn-action btn-edit"
                                    onclick="showEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn-action btn-hapus"
                                    onclick="showDeleteModal({{ $user->id }}, '{{ $user->name }}')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if ($users->isEmpty())
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>Belum ada data akun</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail yang lebih modern -->
<div class="modal-backdrop" id="detailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-user-circle me-2"></i>Detail Akun</h3>
            <button type="button" class="modal-close" onclick="closeModal('detailModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="user-detail-item">
                <span class="user-detail-label"><i class="fas fa-id-card me-2"></i>Nama Lengkap</span>
                <span class="user-detail-value" id="detailName"></span>
            </div>
            <div class="user-detail-item">
                <span class="user-detail-label"><i class="fas fa-envelope me-2"></i>Email</span>
                <span class="user-detail-value" id="detailEmail"></span>
            </div>
            <div class="user-detail-item">
                <span class="user-detail-label"><i class="fas fa-user-tag me-2"></i>Peran</span>
                <span class="user-detail-value" id="detailRole"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('detailModal')">
                <i class="fas fa-times me-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete yang lebih modern -->
<div class="modal-backdrop" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus</h3>
            <button type="button" class="modal-close" onclick="closeModal('deleteModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-4">
                <i class="fas fa-trash-alt text-danger" style="font-size: 4rem; opacity: 0.8;"></i>
            </div>
            <p class="text-center">Apakah Anda yakin ingin menghapus akun <strong id="deleteUserName"></strong>?</p>
            <p class="text-center text-muted">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('deleteModal')">
                <i class="fas fa-times me-2"></i>Batal
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn modal-btn-danger">
                    <i class="fas fa-trash-alt me-2"></i>Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit yang lebih modern -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Akun</h3>
            <button type="button" class="modal-close" onclick="closeModal('editModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-id-card me-2"></i>Nama Lengkap</span>
                    <input type="text" id="editName" name="name" class="form-control" required>
                </div>
                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-envelope me-2"></i>Email</span>
                    <input type="email" id="editEmail" name="email" class="form-control" required>
                </div>
                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-user-tag me-2"></i>Peran</span>
                    <select id="editRole" name="role" class="form-control form-select" required>
                        <option value="asisten_manager">Asisten Manager</option>
                        <option value="pic_keuangan">PIC Bidang Keuangan</option>
                        <option value="pic_manajemen_risiko">PIC Manajemen Risiko</option>
                        <option value="pic_sekretaris_perusahaan">PIC Sekretaris Perusahaan</option>
                        <option value="pic_perencanaan_operasi">PIC Perencanaan Operasi</option>
                        <option value="pic_pengembangan_bisnis">PIC Pengembangan Bisnis</option>
                        <option value="pic_human_capital">PIC Human Capital</option>
                        <option value="pic_k3l">PIC K3L</option>
                        <option value="pic_perencanaan_korporat">PIC Perencanaan Korporat</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('editModal')">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="modal-btn modal-btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun yang lebih modern -->
<div class="modal-backdrop" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah Akun Baru</h3>
            <button type="button" class="modal-close" onclick="closeModal('addModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addForm" method="POST" action="{{ route('akun.store') }}">
                @csrf

                @if ($errors->any())
                <div class="alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-id-card me-2"></i>Nama Lengkap</span>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="form-validation-error"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-envelope me-2"></i>Email</span>
                    <input type="email" name="email" class="form-control" placeholder="contoh@email.com" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="form-validation-error"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-lock me-2"></i>Password</span>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <span class="form-validation-error"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="user-detail-item">
                    <span class="user-detail-label"><i class="fas fa-user-tag me-2"></i>Peran</span>
                    <select name="role" class="form-control form-select" required>
                        <option value="">-- Pilih Peran --</option>
                        <option value="asisten_manager" {{ old('role') == 'asisten_manager' ? 'selected' : '' }}>Asisten Manager</option>
                        <option value="pic_keuangan" {{ old('role') == 'pic_keuangan' ? 'selected' : '' }}>PIC Bidang Keuangan</option>
                        <option value="pic_manajemen_risiko" {{ old('role') == 'pic_manajemen_risiko' ? 'selected' : '' }}>PIC Manajemen Risiko</option>
                        <option value="pic_sekretaris_perusahaan" {{ old('role') == 'pic_sekretaris_perusahaan' ? 'selected' : '' }}>PIC Sekretaris Perusahaan</option>
                        <option value="pic_perencanaan_operasi" {{ old('role') == 'pic_perencanaan_operasi' ? 'selected' : '' }}>PIC Perencanaan Operasi</option>
                        <option value="pic_pengembangan_bisnis" {{ old('role') == 'pic_pengembangan_bisnis' ? 'selected' : '' }}>PIC Pengembangan Bisnis</option>
                        <option value="pic_human_capital" {{ old('role') == 'pic_human_capital' ? 'selected' : '' }}>PIC Human Capital</option>
                        <option value="pic_k3l" {{ old('role') == 'pic_k3l' ? 'selected' : '' }}>PIC K3L</option>
                        <option value="pic_perencanaan_korporat" {{ old('role') == 'pic_perencanaan_korporat' ? 'selected' : '' }}>PIC Perencanaan Korporat</option>
                        <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                    @error('role')
                        <span class="form-validation-error"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal('addModal')">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="modal-btn modal-btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Tampilkan modal tambah akun dengan animasi
    function showAddModal() {
        const modal = document.getElementById('addModal');
        modal.style.display = 'flex';
        // Gunakan timeout untuk memastikan transisi berjalan dengan baik
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        // Disable scrolling pada body
        document.body.style.overflow = 'hidden';
    }

    // Tampilkan modal detail dengan animasi
    function showDetailModal(name, email, role) {
        // Set nilai-nilai dalam modal
        document.getElementById('detailName').textContent = name;
        document.getElementById('detailEmail').textContent = email;
        document.getElementById('detailRole').textContent = role;

        // Tampilkan modal dengan animasi
        const modal = document.getElementById('detailModal');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    // Tampilkan modal edit dengan animasi
    function showEditModal(userId, name, email, role) {
        // Set nilai dalam form
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;

        // Set URL form action
        const form = document.getElementById('editForm');
        form.action = "{{ route('akun.update', '') }}/" + userId;

        // Tampilkan modal dengan animasi
        const modal = document.getElementById('editModal');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    // Tampilkan modal hapus dengan animasi
    function showDeleteModal(userId, userName) {
        // Set nama user yang akan dihapus
        document.getElementById('deleteUserName').textContent = userName;

        // Set URL form action
        const form = document.getElementById('deleteForm');
        form.action = "{{ route('akun.destroy', '') }}/" + userId;

        // Tampilkan modal dengan animasi
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    // Tutup modal dengan animasi
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');

        // Tunggu animasi selesai baru sembunyikan modal
        setTimeout(() => {
            modal.style.display = 'none';
            // Enable kembali scrolling pada body
            document.body.style.overflow = '';
        }, 300); // Match dengan waktu transisi di CSS
    }

    // Tutup modal ketika klik di luar modal content dengan animasi
    window.addEventListener('click', function(event) {
        const detailModal = document.getElementById('detailModal');
        const deleteModal = document.getElementById('deleteModal');
        const editModal = document.getElementById('editModal');
        const addModal = document.getElementById('addModal');

        if (event.target === detailModal) {
            closeModal('detailModal');
        }

        if (event.target === deleteModal) {
            closeModal('deleteModal');
        }

        if (event.target === editModal) {
            closeModal('editModal');
        }

        if (event.target === addModal) {
            closeModal('addModal');
        }
    });

    // Handle escape key untuk menutup modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal-backdrop.show');
            if (modals.length > 0) {
                const modalId = modals[0].id;
                closeModal(modalId);
            }
        }
    });

    // Animasi saat alert success muncul
    document.addEventListener('DOMContentLoaded', function() {
        const alertSuccess = document.querySelector('.alert-success');
        if (alertSuccess) {
            // Tambahkan animasi fadeIn
            alertSuccess.style.opacity = '0';
            alertSuccess.style.transform = 'translateY(-10px)';

            // Trigger animasi fadeIn
            setTimeout(() => {
                alertSuccess.style.opacity = '1';
                alertSuccess.style.transform = 'translateY(0)';
                alertSuccess.style.transition = 'all 0.5s ease';
            }, 100);

            // Setelah beberapa detik, hilangkan notifikasi
            setTimeout(function() {
                alertSuccess.style.opacity = '0';
                alertSuccess.style.height = '0';
                alertSuccess.style.margin = '0';
                alertSuccess.style.padding = '0';
                alertSuccess.style.transition = 'all 0.5s ease';
            }, 4000);
        }

        // Setup modal untuk form tambah
        const addModalBackdrop = document.getElementById('addModal');
        if (addModalBackdrop) {
            addModalBackdrop.style.display = 'none';
        }

        // Tampilkan modal tambah jika ada error validasi
        @if($errors->any())
            showAddModal();
        @endif

        // Initialize modals
        const modals = document.querySelectorAll('.modal-backdrop');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    });
</script>
@endsection
