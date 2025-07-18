@extends('layouts.app')
{{-- @extends('layouts.master') --}}

@section('title', 'Kelola Akun - PLN')
@section('page_title', 'DATA AKUN')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/akun.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="akun-title">Daftar Akun</h2>

    <div class="action-top">
        <form action="{{ route('akun.index') }}" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Cari nama, email, atau role..." value="{{ request('search') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

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
                    <th style="width: 60px;">Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            @if($user->profile_photo)
                                <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}" class="profile-image-mini">
                            @else
                                <div class="profile-icon-mini">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ $user->name ?: 'Tidak ada nama' }}</strong></td>
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
                            <button type="button" class="btn-action btn-detail"
                                onclick="showDetailModal('{{ $user->name }}', '{{ $user->email }}', '{{ ucwords(str_replace('_', ' ', $user->role)) }}', '{{ $user->profile_photo ? Storage::url($user->profile_photo) : '' }}')">
                                <i class="fas fa-eye"></i> <span>Detail</span>
                            </button>
                            <button type="button" class="btn-action btn-edit"
                                onclick="showEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')">
                                <i class="fas fa-edit"></i> <span>Edit</span>
                            </button>
                            <button type="button" class="btn-action btn-hapus"
                                onclick="showDeleteModal({{ $user->id }}, '{{ $user->name }}')">
                                <i class="fas fa-trash"></i> <span>Hapus</span>
                            </button>
                        </td>
                    </tr>
                @endforeach

                @if ($users->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>Belum ada data akun di sistem</p>
                                <button type="button" class="btn-tambah-akun" onclick="showAddModal()">
                                    <i class="fas fa-plus-circle"></i> Tambah Akun Baru
                                </button>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($users->isNotEmpty())
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} data
        </div>
        <div class="pagination-control">
            <label for="perPage">Tampilkan:</label>
            <select id="perPage" onchange="changePerPage(this.value)">
                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span>data per halaman</span>
        </div>
        <div class="pagination-links">
            {{ $users->appends(['perPage' => request('perPage', 10)])->links('pagination.custom') }}
        </div>
    </div>
    @endif
</div>

<!-- Modal Detail -->
<div class="modal-backdrop" id="detailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Akun</h3>
            <button type="button" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="detailPhoto" class="text-center mb-4">
                <!-- Foto profil akan ditampilkan di sini -->
            </div>
            <p><strong>Nama:</strong> <span id="detailName"></span></p>
            <p><strong>Email:</strong> <span id="detailEmail"></span></p>
            <p><strong>Role:</strong> <span id="detailRole"></span></p>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal-backdrop" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Hapus</h3>
            <button type="button" onclick="closeModal('deleteModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus akun <strong id="deleteUserName"></strong>?</p>
            <p>Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('deleteModal')">Batal</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background-color: #dc3545; color: white;">Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Akun</h3>
            <button type="button" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 15px;">
                    <label for="editName">Nama Lengkap</label>
                    <input type="text" id="editName" name="name" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="password">Password</label>
                    <input type="password" name="password" style="width: 100%; padding: 8px; margin-top: 5px;" placeholder="Biarkan kosong jika tidak diubah">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" style="width: 100%; padding: 8px; margin-top: 5px;" placeholder="Biarkan kosong jika tidak diubah">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="editRole">Peran</label>
                    <select id="editRole" name="role" style="width: 100%; padding: 8px; margin-top: 5px;" required>
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
                    <button type="button" onclick="closeModal('editModal')">Batal</button>
                    <button type="submit" style="background-color: var(--pln-blue); color: white;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal-backdrop" id="addModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Akun Baru</h3>
            <button type="button" onclick="closeModal('addModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addForm" method="POST" action="{{ route('akun.store') }}">
                @csrf
                @if ($errors->any())
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div style="margin-bottom: 15px;">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" style="width: 100%; padding: 8px; margin-top: 5px;" value="{{ old('name') }}" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="email">Email</label>
                    <input type="email" name="email" style="width: 100%; padding: 8px; margin-top: 5px;" value="{{ old('email') }}" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="password">Password</label>
                    <input type="password" name="password" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" style="width: 100%; padding: 8px; margin-top: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="role">Peran</label>
                    <select name="role" style="width: 100%; padding: 8px; margin-top: 5px;" required>
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
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('addModal')">Batal</button>
                    <button type="submit" style="background-color: var(--pln-blue); color: white;">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Function to show modal dengan animasi
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');

            // Tambahkan kelas untuk animasi pada content modal
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.add('animate-in');
            }

            // Mencegah scrolling pada halaman ketika modal terbuka
            document.body.style.overflow = 'hidden';
        }
    }

    // Function to close modal dengan animasi
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Animasikan keluarnya modal
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('animate-in');
                modalContent.classList.add('animate-out');

                // Delay sedikit sebelum benar-benar menutup modal
                setTimeout(() => {
                    modal.classList.remove('show');
                    modalContent.classList.remove('animate-out');
                    document.body.style.overflow = '';
                }, 300);
            } else {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
    }

    // Functions for different modal types
    function showAddModal() {
        showModal('addModal');
    }

    function showDetailModal(name, email, role, profilePhoto) {
        document.getElementById('detailName').innerText = name;
        document.getElementById('detailEmail').innerText = email;
        document.getElementById('detailRole').innerText = role;

        // Tampilkan foto profil jika ada
        const photoContainer = document.getElementById('detailPhoto');
        if (profilePhoto) {
            photoContainer.innerHTML = `<img src="${profilePhoto}" alt="${name}" class="profile-image-modal">`;
        } else {
            photoContainer.innerHTML = `<div class="profile-icon-modal"><i class="fas fa-user"></i></div>`;
        }

        showModal('detailModal');
    }

    function showEditModal(userId, name, email, role) {
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        const form = document.getElementById('editForm');
        form.action = `/akun/${userId}`;
        showModal('editModal');
    }

    function showDeleteModal(userId, userName) {
        document.getElementById('deleteUserName').textContent = userName;
        const form = document.getElementById('deleteForm');
        form.action = `/akun/${userId}`;
        showModal('deleteModal');
    }

    // Function to change items per page
    function changePerPage(value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('perPage', value);

        if (urlParams.has('search')) {
            const searchValue = urlParams.get('search');
            window.location.href = '{{ route("akun.index") }}?perPage=' + value + '&search=' + searchValue;
        } else {
            window.location.href = '{{ route("akun.index") }}?perPage=' + value;
        }
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal-backdrop');
        modals.forEach(modal => {
            if (event.target === modal) {
                const modalId = modal.id;
                closeModal(modalId);
            }
        });
    });

    // Escape key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const visibleModal = document.querySelector('.modal-backdrop.show');
            if (visibleModal) {
                closeModal(visibleModal.id);
            }
        }
    });

    // Show add modal if there are validation errors
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            showAddModal();
        @endif

        // Tambahkan efek ripple pada tombol-tombol
        const buttons = document.querySelectorAll('.btn-action, .btn-tambah-akun');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
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
            });
        });

        // Tambahkan animasi untuk baris tabel saat pertama kali dimuat
        const tableRows = document.querySelectorAll('.akun-table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(20px)';

            setTimeout(() => {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
    });
</script>
@endsection
