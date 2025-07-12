@php
use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Dashboard PLN')</title>
  <link rel="stylesheet" href="{{ asset('css/layouts.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <style>

  </style>
  @yield('styles')
</head>
<script>
  function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
      sidebar.classList.toggle('expanded');
    }
  }
</script>

<body data-theme="dark">
  <div class="container-fluid">
    <!-- Sidebar yang lebih modern -->
    <div class="sidebar">
      <div class="sidebar-logo">
        <img src="/images/logo_pln.png" alt="Logo PLN" class="logo-pln">
        <div class="logo-text">
          <h1 class="logo-title">PLN</h1>
          <p class="logo-subtitle">Mandau Cipta Tenaga Nusantara</p>
        </div>
      </div>

      <ul class="sidebar-menu">
        <li>
          <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt icon"></i>
            <span class="menu-text">Dashboard</span>
          </a>
        </li>

  {{-- Menu untuk Master Admin (Asisten Manajer) --}}
        @if(Auth::user()->role == 'asisten_manager')
        <li>
          <a href="{{route('dataKinerja.index')}}" class="{{ request()->routeIs('dataKinerja.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar icon"></i>
            <span class="menu-text">Data Kinerja</span>
          </a>
        </li>
        <li>
          <a href="{{route('akun.index')}}" class="{{ request()->routeIs('akun.*') ? 'active' : '' }}">
            <i class="fas fa-users icon"></i>
            <span class="menu-text">Data Akun</span>
          </a>
        </li>

         <li>
          <a href="{{ route('tahunPenilaian.index') }}" class="{{ request()->routeIs('tahunPenilaian.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt icon"></i>
            <span class="menu-text">Tahun Penilaian</span>
          </a>
        </li>

        <li>
          <a href="{{route('targetKinerja.index')}}" class="{{ request()->routeIs('targetKinerja.*') ? 'active' : '' }}">
            <i class="fas fa-bullseye icon"></i>
            <span class="menu-text">Target Kinerja</span>
          </a>
        </li>
        <li>
          <a href="{{route('realisasi.index')}}" class="{{ request()->routeIs('realisasi.*') ? 'active' : '' }}">
            <i class="fas fa-tasks icon"></i>
            <span class="menu-text">Data Realisasi</span>
          </a>
        </li>
        <li>
          <a href="{{ route('verifikasi.index') }}" class="{{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
            <i class="fas fa-check-circle icon"></i>
            <span class="menu-text">Verifikasi</span>
          </a>
        </li>
        <li>
          <a href="{{route('aktivitasLog.index')}}" class="{{ request()->routeIs('aktivitasLog.*') ? 'active' : '' }}">
            <i class="fas fa-history icon"></i>
            <span class="menu-text">Log Aktivitas</span>
          </a>
        </li>
        @endif

     {{-- Menu untuk Admin (PIC Bidang) --}}
        @if(Str::startsWith(Auth::user()->role, 'pic_'))
        <li>
          <a href="{{route('dataKinerja.index')}}" class="{{ request()->routeIs('dataKinerja.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar icon"></i>
            <span class="menu-text">Data Kinerja</span>
          </a>
        </li>
        <li>
          <a href="{{route('realisasi.index')}}" class="{{ request()->routeIs('realisasi.*') ? 'active' : '' }}">
            <i class="fas fa-tasks icon"></i>
            <span class="menu-text">Data Realisasi</span>
          </a>
        </li>

        @endif

        {{-- Menu untuk semua user --}}
        <li>
          <a href="{{ route('eksporPdf.index') }}" class="{{ request()->routeIs('eksporPdf.*') ? 'active' : '' }}">
            <i class="fas fa-file-pdf icon"></i>
            <span class="menu-text">Ekspor PDF</span>
          </a>
        </li>
        <li>
          <a href="{{ route('lokasi.index') }}" class="{{ request()->routeIs('lokasi.*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt icon"></i>
            <span class="menu-text">Lokasi</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Header yang lebih modern -->
    <div class="dashboard-header">
      <!-- Tombol hamburger -->
      <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </button>

      <div class="header-text">
        <h1 class="header-title">@yield('page_title', 'Dashboard PLN')</h1>
        <p class="header-subtitle">PT PLN MANDAU CIPTA TENAGA NUSANTARA</p>
      </div>

      <div class="header-right">

        <!-- Tombol notifikasi -->
        <div class="topbar-icon-btn notification-btn" id="notificationBtn" style="position: relative;">
          <i class="fas fa-bell"></i>
          @if(Auth::check() && Auth::user()->role === 'asisten_manager')
            @php
              // Hitung SEMUA notifikasi yang perlu ditangani
              $unverifiedCount = App\Models\Realisasi::where('diverifikasi', false)->count();
              $unapprovedCount = App\Models\TargetKPI::where('disetujui', false)->count();
              $totalNotifications = $unverifiedCount + $unapprovedCount;
            @endphp

            {{-- SATU badge untuk SEMUA notifikasi --}}
            @if($totalNotifications > 0)
              <span class="notification-badge
                @if($unverifiedCount > 0 && $unapprovedCount > 0)
                  mixed-notifications
                @elseif($unverifiedCount > 0)
                  realisasi-notifications
                @else
                  target-notifications
                @endif"
                id="main-notification-badge">
                {{ $totalNotifications }}
              </span>
            @endif
          @endif

          <!-- Dropdown notifikasi - DIPERBAIKI -->
          <div class="notification-dropdown" id="notificationDropdown">
              <div class="notification-header">
                  <h5><i class="fas fa-bell"></i> Notifikasi</h5>
                  <button class="close-btn" id="closeNotification"><i class="fas fa-times"></i></button>
              </div>
            <div class="notification-body">
            @if(Auth::check() && Auth::user()->role === 'asisten_manager')
                @php
                // Dapatkan data realisasi dan target yang perlu ditangani dengan error handling
                try {
                    $unverifiedRealisasi = App\Models\Realisasi::with(['indikator', 'user'])
                        ->where('diverifikasi', false)
                        ->latest()
                        ->get();
                } catch (\Exception $e) {
                    \Log::error('Error fetching unverified realisasi: ' . $e->getMessage());
                    $unverifiedRealisasi = collect([]);
                }

                try {
                    $unapprovedTargets = App\Models\TargetKPI::with(['indikator', 'user'])
                        ->where('disetujui', false)
                        ->latest()
                        ->get();
                } catch (\Exception $e) {
                    \Log::error('Error fetching unapproved targets: ' . $e->getMessage());
                    $unapprovedTargets = collect([]);
                }

                // Hitung total untuk statistik
                $totalUnverified = $unverifiedRealisasi->count();
                $totalUnapproved = $unapprovedTargets->count();
                $totalNotifications = $totalUnverified + $totalUnapproved;

                // Ambil 5 item pertama untuk ditampilkan
                $unverifiedItems = $unverifiedRealisasi->take(5);
                $unapprovedItems = $unapprovedTargets->take(5);

                // Debug: Log data types untuk troubleshooting
                if ($unverifiedItems->count() > 0) {
                    $firstItem = $unverifiedItems->first();
                    \Log::info('Debug Realisasi Item:', [
                        'nilai_type' => gettype($firstItem->nilai),
                        'nilai_value' => $firstItem->nilai,
                        'indikator_exists' => isset($firstItem->indikator),
                        'user_exists' => isset($firstItem->user)
                    ]);
                }

                if ($unapprovedItems->count() > 0) {
                    $firstTarget = $unapprovedItems->first();
                    \Log::info('Debug Target Item:', [
                        'target_type' => gettype($firstTarget->target_bulanan),
                        'target_value' => $firstTarget->target_bulanan,
                        'indikator_exists' => isset($firstTarget->indikator),
                        'user_exists' => isset($firstTarget->user)
                    ]);
                }
                @endphp

                <!-- Header Statistik -->
                @if($totalNotifications > 0)
                <div class="notification-stats">
                    <div class="stats-item">
                        <span class="stats-number">{{ $totalUnverified }}</span>
                        <span class="stats-label">Realisasi</span>
                    </div>
                    <div class="stats-divider"></div>
                    <div class="stats-item">
                        <span class="stats-number">{{ $totalUnapproved }}</span>
                        <span class="stats-label">Target</span>
                    </div>
                    <div class="stats-divider"></div>
                    <div class="stats-item">
                        <span class="stats-number total">{{ $totalNotifications }}</span>
                        <span class="stats-label">Total</span>
                    </div>
                </div>
                @endif

                <!-- Section: Realisasi yang Perlu Diverifikasi -->
                @if($unverifiedItems->count() > 0)
                <div class="notification-section urgent-section">
                    <div class="section-header">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        <h6>Realisasi Perlu Diverifikasi</h6>
                        <span class="section-badge urgent">{{ $totalUnverified }}</span>
                    </div>

                    @foreach($unverifiedItems as $item)
                    @php
                        try {
                            $verifikasiUrl = route('verifikasi.show', $item->id);
                        } catch (\Exception $e) {
                            // Fallback ke halaman verifikasi index jika route show tidak ada
                            $verifikasiUrl = route('verifikasi.index') . '?item=' . $item->id;
                            \Log::warning('Verifikasi.show route not found, using fallback:', ['id' => $item->id, 'fallback_url' => $verifikasiUrl]);
                        }
                        \Log::info('Verifikasi URL generated:', ['id' => $item->id, 'url' => $verifikasiUrl]);
                    @endphp
                    <a href="{{ $verifikasiUrl }}" class="notification-item urgent-item"
                       data-id="{{ $item->id }}"
                       data-type="realisasi"
                       title="Klik untuk verifikasi realisasi ID: {{ $item->id }}">
                        <div class="notification-icon bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-title">
                                {{ $item->indikator->kode ?? 'N/A' }} - {{ Str::limit($item->indikator->nama ?? 'Indikator tidak ditemukan', 40) }}
                            </p>
                            <p class="notification-info">
                                <i class="fas fa-user"></i> {{ $item->user->name ?? 'User tidak ditemukan' }}
                                <span class="notification-value">
                                    <i class="fas fa-chart-line"></i>
                                    @php
                                        try {
                                            $nilai = $item->nilai ?? 0;
                                            // Handle jika nilai adalah array atau object
                                            if (is_array($nilai) || is_object($nilai)) {
                                                $nilai = 0;
                                            }
                                            // Handle jika nilai adalah string yang tidak valid
                                            if (!is_numeric($nilai)) {
                                                $nilai = 0;
                                            }
                                            $formattedNilai = number_format((float)$nilai, 0, ',', '.');
                                        } catch (\Exception $e) {
                                            $formattedNilai = '0';
                                            \Log::error('Error formatting nilai: ' . $e->getMessage());
                                        }
                                    @endphp
                                    {{ $formattedNilai }}
                                </span>
                            </p>
                            <p class="notification-time">
                                <i class="fas fa-clock"></i> {{ $item->created_at->diffForHumans() }}
                                @if($item->created_at->diffInHours() > 24)
                                    <span class="priority-urgent">URGENT</span>
                                @elseif($item->created_at->diffInHours() > 8)
                                    <span class="priority-high">TINGGI</span>
                                @endif
                            </p>
                        </div>
                        <div class="notification-action">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    @endforeach

                    @if($totalUnverified > 5)
                    <a href="{{ route('verifikasi.index') }}" class="notification-more urgent">
                        <i class="fas fa-tasks"></i> Verifikasi {{ $totalUnverified - 5 }} lainnya
                    </a>
                    @endif
                </div>
                @endif


                <!-- Empty State -->
                @if($totalNotifications === 0)
                <div class="notification-empty">
                    <div class="empty-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="empty-content">
                        <h6>Semua Sudah Terverifikasi</h6>
                        <p>Tidak ada realisasi atau target yang perlu ditinjau</p>
                    </div>
                </div>
                @endif

            @else
                <!-- Non-admin users -->
                <div class="notification-empty">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <div class="empty-content">
                        <h6>Tidak Ada Notifikasi</h6>
                        <p>Anda tidak memiliki akses notifikasi</p>
                    </div>
                </div>
            @endif
            </div>
          </div>
        </div>

        <!-- Tanggal & jam -->
        <div class="date-display" id="date-display">
          <i class="far fa-calendar-alt"></i>
          <span>
            @php
              // Set lokasi waktu ke Indonesia/Jakarta
              date_default_timezone_set('Asia/Jakarta');

              // Array untuk konversi nama hari dan bulan ke Bahasa Indonesia
              $hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
              $bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

              // Format waktu dalam Bahasa Indonesia
              $nama_hari = $hari[date('w')];
              $nama_bulan = $bulan[date('n')-1];
              $tanggal = date('j');
              $tahun = date('Y');

              // Deteksi hari libur nasional (contoh sederhana)
              $hari_libur = [
                // Hari Libur Nasional 2023
                '01-01' => 'Tahun Baru',
                '22-01' => 'Tahun Baru Imlek',
                '18-02' => 'Isra Miraj',
                '22-03' => 'Nyepi',
                '07-04' => 'Wafat Isa Almasih',
                '22-04' => 'Idul Fitri',
                '23-04' => 'Idul Fitri',
                '01-05' => 'Hari Buruh',
                '18-05' => 'Kenaikan Isa Almasih',
                '01-06' => 'Hari Lahir Pancasila',
                '04-06' => 'Waisak',
                '29-06' => 'Idul Adha',
                '19-07' => 'Tahun Baru Hijriah',
                '17-08' => 'HUT RI',
                '28-09' => 'Maulid Nabi',
                '25-12' => 'Hari Natal',

                // Hari Libur Nasional 2024
                '01-01-2024' => 'Tahun Baru 2024',
                '10-02-2024' => 'Tahun Baru Imlek',
                '07-03-2024' => 'Isra Miraj',
                '11-03-2024' => 'Nyepi',
                '29-03-2024' => 'Wafat Isa Almasih',
                '10-04-2024' => 'Idul Fitri',
                '11-04-2024' => 'Idul Fitri',
                '01-05-2024' => 'Hari Buruh',
                '09-05-2024' => 'Kenaikan Isa Almasih',
                '01-06-2024' => 'Hari Lahir Pancasila',
                '15-06-2024' => 'Waisak',
                '17-06-2024' => 'Idul Adha',
                '07-07-2024' => 'Tahun Baru Hijriah',
                '17-08-2024' => 'HUT RI',
                '16-09-2024' => 'Maulid Nabi',
                '25-12-2024' => 'Hari Natal',
              ];

              $tanggal_sekarang = date('d-m');
              $tanggal_sekarang_tahun = date('d-m-Y');
              $day_num = date('w'); // 0 = Minggu, 6 = Sabtu

              // Cek hari libur baik dengan atau tanpa tahun
              $is_libur = isset($hari_libur[$tanggal_sekarang]) ||
                         isset($hari_libur[$tanggal_sekarang_tahun]) ||
                         $day_num == 0; // Minggu

              $is_weekend = $day_num == 0 || $day_num == 6; // Sabtu atau Minggu

              $nama_libur = isset($hari_libur[$tanggal_sekarang]) ? $hari_libur[$tanggal_sekarang] :
                           (isset($hari_libur[$tanggal_sekarang_tahun]) ? $hari_libur[$tanggal_sekarang_tahun] : '');

              $text_color = $is_libur ? 'holiday-text' : ($is_weekend ? 'weekend-text' : '');
            @endphp

            <span class="date-info {{ $text_color }}">{{ $nama_hari }}, {{ $tanggal }} {{ $nama_bulan }} {{ $tahun }}
              @if($nama_libur)
                <span class="holiday-badge">{{ $nama_libur }}</span>
              @elseif($day_num == 0)
                <span class="holiday-badge">Hari Minggu</span>
              @elseif($day_num == 6)
                <span class="weekend-badge">Akhir Pekan</span>
              @endif
            </span>
          </span>

          <div class="date-tooltip">
            <div class="tooltip-title">Informasi Tanggal</div>
            <div class="tooltip-info">Hari ke-{{ date('z')+1 }} dari {{ date('L') ? '366' : '365' }} hari</div>
            <div class="tooltip-info">Minggu ke-{{ date('W') }} tahun {{ $tahun }}</div>
            <div class="tooltip-info">{{ date('t') }} hari dalam bulan ini</div>
            <div class="tooltip-info">{{ date('L') ? 'Tahun Kabisat' : 'Bukan Tahun Kabisat' }}</div>
          </div>
        </div>

        <!-- Menu profil -->
        <div class="user-profile-menu">
          <div class="profile-trigger" onclick="toggleProfileMenu()">
            @if(Auth::user()->profile_photo)
              <img src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}" class="profile-img">
            @else
              <div class="profile-icon">
                <i class="fas fa-user"></i>
              </div>
            @endif
            <span class="profile-name">{{ Auth::user()->name }}</span>
            <i class="fas fa-chevron-down"></i>
          </div>
          <div class="profile-menu" id="profile-menu">
            <div class="profile-menu-header">
              <div class="profile-info">
                <h6 class="profile-title">{{ Auth::user()->name }}</h6>
                <p class="profile-subtitle">{{ Auth::user()->email }}</p>
              </div>
            </div>
            <div class="profile-menu-items">
              <a href="#" class="profile-menu-item" onclick="openProfileModal(event)">
                <i class="fas fa-user-edit"></i> Edit Profil
              </a>
              <div class="divider"></div>
              <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="profile-menu-item logout-btn-menu">
                  <i class="fas fa-sign-out-alt"></i> Logout
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <main class="main">
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Overlay untuk animasi logout -->
  <div class="logout-overlay" id="logoutOverlay">
    <div class="logout-spinner"></div>
    <div class="logout-message">Keluar Dari Sistem...</div>
  </div>

  <!-- Modal Edit Profil -->
  <div class="profile-modal" id="profileModal">
    <div class="profile-modal-content">
      <div class="profile-modal-header">
        <h5 class="profile-modal-title">Edit Profil</h5>
        <button type="button" class="profile-modal-close" id="closeProfileModal" onclick="closeProfileModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="profile-modal-body">
        <div class="profile-tabs">
          <button class="profile-tab active" data-tab="info">Informasi Dasar</button>
          <button class="profile-tab" data-tab="photo">Foto Profil</button>
          <button class="profile-tab" data-tab="password">Ubah Password</button>
        </div>

        <div class="profile-tab-content active" id="tab-info">
          <form id="updateProfileForm" action="/profile" method="POST">
            @csrf
            <input type="hidden" name="update_type" value="profile">

            <div class="form-group">
              <label for="name" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}">
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
              </button>
            </div>
          </form>
        </div>

        <div class="profile-tab-content" id="tab-photo">
          <form id="updatePhotoForm" action="/profile/photo" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="profile-photo-preview">
            @if(Auth::user()->profile_photo && Storage::disk('public')->exists(Auth::user()->profile_photo))
                <img src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}" id="photoPreview" style="max-width: 200px;">
            @else
                <div class="profile-photo-placeholder">
                <i class="fas fa-user"></i>
                </div>
            @endif
            </div>


            <div class="profile-photo-upload-container">
              <label for="profile_photo" class="photo-upload-btn">
                <i class="fas fa-camera"></i> Pilih Foto
              </label>
              <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="photo-input">
              <span class="photo-filename" id="photoFilename">Tidak ada file yang dipilih</span>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary" id="uploadPhotoBtn" disabled>
                <i class="fas fa-upload"></i> Upload Foto
              </button>
            </div>
          </form>
        </div>

        <div class="profile-tab-content" id="tab-password">
          <form id="updatePasswordForm" action="/profile" method="POST">
            @csrf
            <input type="hidden" name="update_type" value="password">

            <!-- Tampilkan pesan sukses yang lebih menonjol -->
            @if(session('success'))
            <div class="alert alert-success mb-4" style="background: linear-gradient(135deg, #28a745, #5cb85c); color: white; border-radius: 10px; padding: 15px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
              <div class="d-flex align-items-center mb-2">
                <i class="fas fa-check-circle mr-2" style="font-size: 20px;"></i>
                <strong>{{ session('success') }}</strong>
              </div>
              <p class="mb-0" style="font-size: 14px;">
                Untuk menggunakan password baru Anda, silakan:
                <ol class="mt-2 mb-0" style="padding-left: 20px;">
                  <li>Klik Logout di menu profil</li>
                  <li>Login kembali dengan password baru Anda</li>
                </ol>
              </p>
            </div>
            @endif

            <!-- Success message yang akan ditampilkan via JavaScript -->
            <div id="manual-success-message" style="display:none;" class="alert alert-success mb-4" style="background: linear-gradient(135deg, #28a745, #5cb85c); color: white; border-radius: 10px; padding: 15px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
              <div class="d-flex align-items-center mb-2">
                <i class="fas fa-check-circle mr-2" style="font-size: 20px;"></i>
                <strong>Password berhasil diperbarui!</strong>
              </div>
              <p class="mb-0" style="font-size: 14px;">
                Untuk menggunakan password baru Anda, silakan:
                <ol class="mt-2 mb-0" style="padding-left: 20px;">
                  <li>Klik Logout di menu profil</li>
                  <li>Login kembali dengan password baru Anda</li>
                </ol>
              </p>
            </div>

            <!-- Tampilkan error jika ada -->
            @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
              <label for="password" class="form-label">Password Baru</label>
              <div class="password-input-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <button type="button" class="password-toggle">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="form-group">
              <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
              <div class="password-input-group">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                <button type="button" class="password-toggle">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="password-strength">
              <div class="strength-bar">
                <div class="strength-progress" id="passwordStrength"></div>
              </div>
              <span class="strength-text" id="strengthText">Belum diisi</span>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <i class="fas fa-lock"></i> Perbarui Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Live clock script yang lebih baik -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded');

    const editProfileBtn = document.getElementById('editProfileBtn');
    const profileModal = document.getElementById('profileModal');

    if (editProfileBtn) {
      editProfileBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (profileModal) {
          profileModal.style.display = 'flex';
          profileModal.style.justifyContent = 'center';
          profileModal.style.alignItems = 'center';
          profileModal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      });
    }

    const dateDisplay = document.getElementById('date-display');
    if (dateDisplay) {
      dateDisplay.addEventListener('click', function () {
        this.classList.toggle('show-tooltip');
      });

      document.addEventListener('click', function (event) {
        if (!dateDisplay.contains(event.target)) {
          dateDisplay.classList.remove('show-tooltip');
        }
      });
    }

    const body = document.body;
    body.setAttribute('data-theme', 'light');
    localStorage.setItem('theme', 'light');





    const logoutForm = document.querySelector('.logout-form');
    const logoutButton = document.querySelector('.logout-btn-menu');
    const logoutOverlay = document.getElementById('logoutOverlay');

    if (logoutForm && logoutButton) {
      logoutForm.addEventListener('submit', function (e) {
        e.preventDefault();
        let submitted = false;
        const logoutIcon = logoutButton.querySelector('i');
        if (logoutIcon) logoutIcon.classList.add('logout-icon-animation');
        logoutButton.classList.add('logout-animation');

        const profileMenu = document.getElementById('profile-menu');
        if (profileMenu) {
          profileMenu.style.opacity = '0';
          profileMenu.style.visibility = 'hidden';
        }

        // Animasi overlay muncul
        setTimeout(function () {
          if (logoutOverlay) logoutOverlay.classList.add('active');
          // Setelah overlay muncul, submit form setelah 1.5 detik
          setTimeout(function () {
            if (!submitted) {
              submitted = true;
              logoutForm.submit();
            }
          }, 1500);
        }, 300);

        // Fallback: submit form jika overlay gagal tampil dalam 2 detik
        setTimeout(function () {
          if (!submitted) {
            submitted = true;
            logoutForm.submit();
          }
        }, 2000);
      });
    }

    const closeProfileModal = document.getElementById('closeProfileModal');
    if (closeProfileModal && profileModal) {
      closeProfileModal.addEventListener('click', function () {
        profileModal.classList.remove('active');
        setTimeout(() => profileModal.style.display = '', 300);
        document.body.style.overflow = '';
      });

      profileModal.addEventListener('click', function (e) {
        if (e.target === profileModal) {
          profileModal.classList.remove('active');
          setTimeout(() => profileModal.style.display = '', 300);
          document.body.style.overflow = '';
        }
      });
    }

    const profileTabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.profile-tab-content');

    if (profileTabs.length && tabContents.length) {
      profileTabs.forEach(tab => {
        tab.addEventListener('click', function () {
          profileTabs.forEach(t => t.classList.remove('active'));
          this.classList.add('active');

          tabContents.forEach(content => content.classList.remove('active'));

          const tabId = 'tab-' + this.getAttribute('data-tab');
          document.getElementById(tabId).classList.add('active');
        });
      });
    }

    const photoInput = document.getElementById('profile_photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoFilename = document.getElementById('photoFilename');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');

    if (photoInput) {
      photoInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
          const file = this.files[0];
          const reader = new FileReader();

          photoFilename.textContent = file.name;
          if (uploadPhotoBtn) uploadPhotoBtn.disabled = false;

          reader.onload = function (e) {
            if (photoPreview) {
              if (photoPreview.tagName === 'IMG') {
                photoPreview.src = e.target.result;
              } else {
                const photoPlaceholder = document.querySelector('.profile-photo-placeholder');
                if (photoPlaceholder) {
                  const photoContainer = photoPlaceholder.parentElement;
                  photoPlaceholder.remove();

                  const img = document.createElement('img');
                  img.src = e.target.result;
                  img.id = 'photoPreview';
                  img.alt = 'Preview';
                  photoContainer.appendChild(img);
                }
              }
            }
          };

          reader.readAsDataURL(file);
        }
      });
    }

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');

    if (passwordInput && passwordStrength && strengthText) {
      passwordInput.addEventListener('input', function () {
        const password = this.value;
        let strength = 0;
        let status = '';

        if (password.length > 0) {
          if (password.length >= 8) strength += 25;
          if (password.match(/[a-z]/)) strength += 25;
          if (password.match(/[A-Z]/)) strength += 25;
          if (password.match(/[0-9]/) || password.match(/[^a-zA-Z0-9]/)) strength += 25;

          if (strength <= 25) {
            status = 'Lemah';
            passwordStrength.style.width = '25%';
            passwordStrength.style.backgroundPosition = '0% 0%';
          } else if (strength <= 50) {
            status = 'Sedang';
            passwordStrength.style.width = '50%';
            passwordStrength.style.backgroundPosition = '50% 0%';
          } else if (strength <= 75) {
            status = 'Kuat';
            passwordStrength.style.width = '75%';
            passwordStrength.style.backgroundPosition = '75% 0%';
          } else {
            status = 'Sangat Kuat';
            passwordStrength.style.width = '100%';
            passwordStrength.style.backgroundPosition = '100% 0%';
          }
        } else {
          status = 'Belum diisi';
          passwordStrength.style.width = '0%';
        }

        strengthText.textContent = status;
      });
    }

    const passwordToggles = document.querySelectorAll('.password-toggle');

    if (passwordToggles.length) {
      passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
          const input = this.parentElement.querySelector('input');
          const icon = this.querySelector('i');

          if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
          } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
          }
        });
      });
    }
  });
</script>


  <!-- Skrip khusus untuk fungsi modal profil -->
  <script>
    // Fungsi untuk toggle menu profil
    function toggleProfileMenu() {
      const profileMenu = document.getElementById('profile-menu');
      if (profileMenu.style.opacity === '1') {
        profileMenu.style.opacity = '0';
        profileMenu.style.visibility = 'hidden';
        profileMenu.style.transform = 'translateY(-10px)';
      } else {
        profileMenu.style.opacity = '1';
        profileMenu.style.visibility = 'visible';
        profileMenu.style.transform = 'translateY(0)';
      }
    }

    // Fungsi untuk membuka modal profil
    function openProfileModal(e) {
      e.preventDefault();
      const profileModal = document.getElementById('profileModal');

      // Tutup dropdown profil
      const profileMenu = document.getElementById('profile-menu');
      profileMenu.style.opacity = '0';
      profileMenu.style.visibility = 'hidden';

      // Tampilkan modal
      profileModal.style.display = 'flex';
      profileModal.style.justifyContent = 'center';
      profileModal.style.alignItems = 'center';
      profileModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // Fungsi untuk menutup modal profil
    function closeProfileModal() {
      const profileModal = document.getElementById('profileModal');
      profileModal.classList.remove('active');
      setTimeout(function() {
        profileModal.style.display = 'none';
      }, 300);
      document.body.style.overflow = '';
    }

    // Menambahkan event click untuk area modal
    document.addEventListener('DOMContentLoaded', function() {
      const profileModal = document.getElementById('profileModal');

      if (profileModal) {
        profileModal.addEventListener('click', function(e) {
          if (e.target === this) {
            closeProfileModal();
          }
        });
      }
    });
  </script>

  <!-- Script untuk alert password -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Cari elemen penting
      const passwordForm = document.getElementById('updatePasswordForm');
      const manualSuccessMsg = document.getElementById('manual-success-message');
      const profileTabs = document.querySelectorAll('.profile-tab');
      const passwordTab = document.querySelector('.profile-tab[data-tab="password"]');

      // Fungsi untuk mengaktifkan tab password
      function activatePasswordTab() {
        if (!passwordTab) return;

        // Aktifkan tab button
        profileTabs.forEach(tab => tab.classList.remove('active'));
        passwordTab.classList.add('active');

        // Aktifkan konten tab
        const tabContents = document.querySelectorAll('.profile-tab-content');
        tabContents.forEach(content => content.classList.remove('active'));
        document.getElementById('tab-password').classList.add('active');
      }

      // Cek jika form password disubmit
      if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
          // Simpan state form submission ke localStorage
          localStorage.setItem('password_form_submitted', 'true');
          localStorage.setItem('password_submit_time', Date.now());
        });
      }

      // Cek jika ada flag bahwa form telah disubmit sebelumnya
      const wasSubmitted = localStorage.getItem('password_form_submitted');
      const submitTime = localStorage.getItem('password_submit_time');
      const currentTime = Date.now();

      // Jika form baru saja disubmit (dalam 5 detik terakhir)
      if (wasSubmitted === 'true' && submitTime && (currentTime - submitTime < 5000)) {
        console.log('Form was recently submitted, showing manual success message');

        // Tampilkan pesan sukses manual
        if (manualSuccessMsg) {
          manualSuccessMsg.style.display = 'block';

          // Animasi pesan
          setTimeout(function() {
            manualSuccessMsg.style.transition = 'all 0.3s ease';
            manualSuccessMsg.style.transform = 'scale(1.03)';
            setTimeout(function() {
              manualSuccessMsg.style.transform = 'scale(1)';
            }, 300);
          }, 500);
        }

        // Aktifkan tab password
        activatePasswordTab();

        // Hapus flag agar tidak muncul lagi di refresh berikutnya
        localStorage.removeItem('password_form_submitted');
        localStorage.removeItem('password_submit_time');
      }

      // Cek jika ada success message dari session
      const sessionSuccessMsg = document.querySelector('.profile-tab-content .alert-success:not(#manual-success-message)');
      if (sessionSuccessMsg) {
        console.log('Session success message found, highlighting tab');
        activatePasswordTab();

        // Animasi pesan
        setTimeout(function() {
          sessionSuccessMsg.style.transition = 'all 0.3s ease';
          sessionSuccessMsg.style.transform = 'scale(1.03)';
          setTimeout(function() {
            sessionSuccessMsg.style.transform = 'scale(1)';
          }, 300);
        }, 500);
      }
    });
  </script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

  <script>

    document.addEventListener('DOMContentLoaded', function() {
      // Notification Badge Debug dan Setup
      const notificationBadge = document.querySelector('.notification-badge');

      console.log('🔔 NOTIFICATION BADGE DEBUG:');

      @if(Auth::check() && Auth::user()->role === 'asisten_manager')
        @php
          $debugUnverified = App\Models\Realisasi::where('diverifikasi', false)->count();
          $debugUnapproved = App\Models\TargetKPI::where('disetujui', false)->count();
          $debugTotal = $debugUnverified + $debugUnapproved;
          $allRealisasi = App\Models\Realisasi::count();
        @endphp

        console.log('� Data dari Backend:');
        console.log('- Total realisasi di database:', {{ $allRealisasi }});
        console.log('- Realisasi belum diverifikasi:', {{ $debugUnverified }});
        console.log('- Target belum disetujui:', {{ $debugUnapproved }});
        console.log('- Total notifikasi seharusnya:', {{ $debugTotal }});

        if (notificationBadge) {
          const realisasiCount = parseInt(notificationBadge.getAttribute('data-realisasi')) || 0;
          const targetCount = parseInt(notificationBadge.getAttribute('data-target')) || 0;
          const badgeText = notificationBadge.textContent.trim();

          console.log('🎯 Badge Element Found:');
          console.log('- Badge text:', badgeText);
          console.log('- Data realisasi:', realisasiCount);
          console.log('- Data target:', targetCount);
          console.log('- Badge classes:', notificationBadge.className);
          console.log('- Badge visible:', window.getComputedStyle(notificationBadge).display !== 'none');

          // Verification check
          if ({{ $debugTotal }} > 0 && badgeText === '') {
            console.error('❌ MASALAH: Ada notifikasi tapi badge kosong!');
          } else if ({{ $debugTotal }} === 0 && badgeText !== '') {
            console.error('❌ MASALAH: Tidak ada notifikasi tapi badge terisi!');
          } else if (parseInt(badgeText) === {{ $debugTotal }}) {
            console.log('✅ Badge berfungsi dengan benar!');
          } else {
            console.warn('⚠️ Badge count tidak match dengan data backend');
          }

        } else {
          console.log('❌ Badge element tidak ditemukan');
          if ({{ $debugTotal }} > 0) {
            console.error('❌ MASALAH SERIUS: Ada notifikasi tapi badge tidak ada!');
            console.log('🔧 Kemungkinan penyebab:');
            console.log('   - CSS display:none');
            console.log('   - JavaScript error');
            console.log('   - HTML tidak ter-render');
          }
        }
      @else
        console.log('👤 User bukan asisten_manager, badge tidak ditampilkan');
      @endif

      // Dropdown debugging
      @if(Auth::check() && Auth::user()->role === 'asisten_manager')
        console.log('\n📋 Dropdown Debug:');

        // Cek dropdown elements
        const dropdownSections = document.querySelectorAll('.notification-section');
        console.log('- Dropdown sections ditemukan:', dropdownSections.length);

        // Tampilkan detail setiap section
        dropdownSections.forEach((section, index) => {
          const header = section.querySelector('h6');
          const items = section.querySelectorAll('.notification-item');
          console.log(`  Section ${index + 1}: "${header?.textContent || 'No header'}" (${items.length} items)`);
        });

        // Check specific sections
        const urgentSection = document.querySelector('.urgent-section');
        if ({{ $debugUnverified }} > 0) {
          if (urgentSection) {
            console.log('✅ Urgent section (realisasi) ditemukan');
          } else {
            console.error('❌ MASALAH: Ada realisasi tapi urgent-section tidak ditemukan!');
          }
        }

        console.log('📊 Tips: Silakan input realisasi/target baru untuk test badge');
      @endif

      // Notifikasi dropdown
      const notificationBtn = document.getElementById('notificationBtn');
      const notificationDropdown = document.getElementById('notificationDropdown');
      const closeNotification = document.getElementById('closeNotification');

      if (notificationBtn && notificationDropdown) {
        // Event listener sederhana untuk toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
          // Hanya cegah jika klik pada bell icon, bukan pada dropdown content
          if (e.target.classList.contains('fa-bell') || e.target === this) {
            e.preventDefault();
            e.stopPropagation();

            const isShown = notificationDropdown.classList.contains('show');

            if (isShown) {
              notificationDropdown.classList.remove('show');
              console.log('🔔 Notifikasi ditutup');
            } else {
              notificationDropdown.classList.add('show');
              console.log('🔔 Notifikasi dibuka');
            }
          }
        });

        // Event listener untuk tombol close
        if (closeNotification) {
          closeNotification.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            notificationDropdown.classList.remove('show');
            console.log('🔔 Notifikasi ditutup via tombol close');
          });
        }

        // Klik di luar untuk menutup dropdown
        document.addEventListener('click', function(e) {
          if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.remove('show');
          }
        });

        // SIMPLE: Event listener untuk semua link notifikasi
        setTimeout(() => {
          const notificationLinks = document.querySelectorAll('.notification-item');
          console.log('� Setting up ' + notificationLinks.length + ' notification links');

          notificationLinks.forEach((link, index) => {
            link.addEventListener('click', function(e) {
              console.log('🔗 Link clicked:', this.href);

              // Tutup dropdown
              notificationDropdown.classList.remove('show');

              // Force navigation jika diperlukan
              setTimeout(() => {
                if (this.href && this.href !== '#') {
                  window.location.href = this.href;
                }
              }, 50);
            });
          });
        }, 500);
      } else {
        console.error('🚨 Elemen notifikasi tidak ditemukan:', {
          btn: !!notificationBtn,
          dropdown: !!notificationDropdown
        });
      }

      // DEBUGGING: Test semua link di halaman
      setTimeout(() => {
        const allNotificationLinks = document.querySelectorAll('.notification-item');
        console.log('🔍 DEBUGGING: Total notification links found:', allNotificationLinks.length);

        allNotificationLinks.forEach((link, index) => {
          console.log(`Link ${index}:`, {
            href: link.href,
            id: link.getAttribute('data-id'),
            type: link.getAttribute('data-type'),
            visible: link.offsetParent !== null,
            clickable: getComputedStyle(link).pointerEvents !== 'none'
          });

          // Test manual click
          link.addEventListener('contextmenu', function(e) {
            console.log('🖱️ RIGHT CLICK test pada:', this.href);
          });
        });
      }, 1000);
    });
  </script>

  @yield('scripts')

</body>
</html>
