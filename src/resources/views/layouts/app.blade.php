@php
use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Dashboard PLN')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="/css/style.css">
  <style>
    :root {
      /* Common variables for both themes */
      --pln-blue: #0a4d85;
      --pln-light-blue: #009cde;

      /* Dark theme variables (default) */
      --pln-bg: #0f172a;
      --pln-surface: #1e293b;
      --pln-surface-2: #334155;
      --pln-text: #f8fafc;
      --pln-text-secondary: rgba(248, 250, 252, 0.7);
      --pln-border: rgba(248, 250, 252, 0.1);
      --pln-shadow: rgba(0, 0, 0, 0.25);
      --pln-accent-bg: rgba(10, 77, 133, 0.15);
      --pln-header-bg: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
      --sidebar-width: 70px;
      --sidebar-expanded: 260px;
      --sidebar-bg: #0a0f1e;
      --transition-speed: 0.35s;
    }

    /* Light theme variables */
    [data-theme="light"] {
      --pln-bg: #f5f7fa;
      --pln-surface: #ffffff;
      --pln-surface-2: #f0f2f5;
      --pln-text: #333333;
      --pln-text-secondary: rgba(0, 0, 0, 0.6);
      --pln-border: rgba(0, 0, 0, 0.1);
      --pln-shadow: rgba(0, 0, 0, 0.1);
      --pln-accent-bg: rgba(10, 77, 133, 0.05);
      --pln-header-bg: linear-gradient(90deg, var(--pln-blue), var(--pln-light-blue));
      --sidebar-bg: #0a4d85;
    }

    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      background: var(--pln-bg);
      color: var(--pln-text);
      transition: background-color var(--transition-speed) ease,
                  color var(--transition-speed) ease;
    }

    .container-fluid {
      display: flex;
      min-height: 100vh;
      padding: 0;
      width: 100%;
    }

    .dashboard-header {
      width: 100%;
      padding: 15px 25px;
      background: var(--pln-header-bg);
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      height: 70px;
      z-index: 10;
      width: calc(100% - var(--sidebar-width));
      box-shadow: 0 2px 15px var(--pln-shadow);
      transition: left var(--transition-speed) ease,
                  width var(--transition-speed) ease;
    }

    /* Sidebar yang lebih modern */
    .sidebar {
      width: var(--sidebar-width);
      background: var(--sidebar-bg);
      position: fixed;
      height: 100%;
      left: 0;
      top: 0;
      z-index: 100;
      transition: all var(--transition-speed) ease;
      overflow: hidden;
      box-shadow: 2px 0 20px var(--pln-shadow);
    }

    .sidebar:hover {
      width: var(--sidebar-expanded);
    }

    .sidebar-logo {
      padding: 15px;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      height: 70px;
      background: rgba(0,0,0,0.2);
      overflow: hidden;
      white-space: nowrap;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo img {
      height: 40px;
      min-width: 40px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      margin-right: 15px;
      transition: transform 0.3s ease;
    }

    .sidebar:hover .sidebar-logo img {
      transform: scale(1.05);
    }

    .logo-text {
      opacity: 0;
      transform: translateX(-20px);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .sidebar:hover .logo-text {
      opacity: 1;
      transform: translateX(0);
    }

    .logo-title {
      font-size: 18px;
      font-weight: 600;
      margin: 0;
      color: #fff;
      letter-spacing: 1px;
    }

    .logo-subtitle {
      font-size: 11px;
      margin: 0;
      color: rgba(255,255,255,0.7);
      line-height: 1.2;
    }

    /* Sidebar menu yang lebih baik */
    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 1rem 0;
    }

    .sidebar-menu li {
      width: 100%;
      margin-bottom: 0.5rem;
    }

    .sidebar-menu a {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      text-decoration: none;
      color: rgba(255, 255, 255, 0.7);
      transition: all 0.3s ease;
      border-radius: 8px;
      margin: 0 8px;
      position: relative;
    }

    .sidebar-menu a:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .sidebar-menu a.active {
      background: rgba(var(--accent-color-rgb), 0.2);
      color: rgb(var(--accent-color-rgb));
      font-weight: 600;
    }

    .sidebar-menu a.active::before {
      content: '';
      position: absolute;
      left: -8px;
      top: 50%;
      transform: translateY(-50%);
      height: 70%;
      width: 4px;
      background: rgb(var(--accent-color-rgb));
      border-radius: 0 4px 4px 0;
    }

    .sidebar-menu .icon {
      min-width: 24px;
      margin-right: 10px;
      font-size: 1.1rem;
    }

    .sidebar-menu .menu-text {
      display: none;
      white-space: nowrap;
    }

    .sidebar:hover .menu-text {
      display: inline-block;
    }

    /* Date display yang lebih modern */
    .date-display {
      color: white;
      font-size: 14px;
      background: rgba(0,0,0,0.25);
      padding: 8px 15px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(5px);
      border: 1px solid rgba(255,255,255,0.1);
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
    }

    .date-display:hover {
      background: rgba(0,0,0,0.35);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .date-display:active {
      transform: translateY(0);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }

    .date-display i {
      margin-right: 8px;
      color: var(--pln-light-blue);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { opacity: 0.7; }
      50% { opacity: 1; }
      100% { opacity: 0.7; }
    }

    .date-info {
      margin-right: 8px;
    }

    .time-display {
      display: block;
      font-weight: 600;
      letter-spacing: 0.5px;
      color: var(--pln-light-blue);
      position: relative;
    }

    .time-colon {
      display: inline-block;
      animation: blink 1s infinite;
    }

    .time-seconds {
      font-size: 0.9em;
      opacity: 0.8;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    .header-text {
      line-height: 1.2;
    }

    .header-title {
      font-size: 18px;
      font-weight: 600;
      margin: 0;
      color: white;
      letter-spacing: 0.5px;
    }

    .header-subtitle {
      font-size: 12px;
      margin: 0;
      opacity: 0.9;
      color: white;
    }

    .main {
      margin-top: 70px;
      margin-left: var(--sidebar-width);
      padding: 25px;
      width: calc(100% - var(--sidebar-width));
      transition: margin-left var(--transition-speed) ease,
                  width var(--transition-speed) ease;
    }

    /* Logout button yang lebih modern */
    .logout-btn {
      position: absolute;
      bottom: 20px;
      left: 0;
      width: 100%;
      background: none;
      border: none;
      display: flex;
      align-items: center;
      color: white;
      padding: 12px 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      opacity: 0.8;
    }

    .logout-btn:hover {
      background: linear-gradient(to right, rgba(220, 53, 69, 0.2), rgba(220, 53, 69, 0.3));
      opacity: 1;
    }

    .logout-icon {
      margin-right: 15px;
      width: 20px;
      text-align: center;
    }

    .logout-text {
      opacity: 0;
      transform: translateX(-10px);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .sidebar:hover .logout-text {
      opacity: 1;
      transform: translateX(0);
    }

    @media (max-width: 1200px) {
      .pillar-container {
        justify-content: center;
      }
    }

    @media (max-width: 992px) {
      :root {
        --sidebar-width: 0px;
      }

      .sidebar {
        width: 0;
      }

      .sidebar:hover {
        width: var(--sidebar-expanded);
      }

      .main, .dashboard-header {
        margin-left: 0;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .date-display {
        display: none;
      }
    }

    /* Toggle switch untuk tema */
    .theme-switch-wrapper {
      display: flex;
      align-items: center;
      margin-right: 15px;
    }

    .theme-switch {
      display: inline-block;
      height: 24px;
      position: relative;
      width: 50px;
    }

    .theme-switch input {
      display: none;
    }

    .slider {
      background-color: #111;
      bottom: 0;
      cursor: pointer;
      left: 0;
      position: absolute;
      right: 0;
      top: 0;
      transition: .4s;
      border-radius: 34px;
      box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255,255,255,0.1);
    }

    .slider:before {
      background-color: #fff;
      bottom: 3px;
      content: "";
      height: 16px;
      left: 4px;
      position: absolute;
      transition: .4s;
      width: 16px;
      border-radius: 50%;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    input:checked + .slider {
      background-color: var(--pln-light-blue);
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    .theme-icon {
      color: white;
      margin: 0 8px;
      font-size: 16px;
    }

    /* Smooth transition untuk semua elemen */
    * {
      transition-property: background-color, color, border-color, box-shadow;
      transition-duration: var(--transition-speed);
      transition-timing-function: ease;
    }

    /* Styling untuk tanggal dan hari libur */
    .holiday-text {
      color: #ff6b6b !important;
      font-weight: 600;
    }

    .weekend-text {
      color: #ff9f43 !important;
      font-weight: 600;
    }

    .holiday-badge {
      display: inline-block;
      font-size: 10px;
      background: rgba(255, 107, 107, 0.2);
      color: #ff6b6b;
      padding: 2px 6px;
      border-radius: 8px;
      margin-left: 8px;
      font-weight: 600;
      border: 1px solid rgba(255, 107, 107, 0.3);
    }

    .weekend-badge {
      display: inline-block;
      font-size: 10px;
      background: rgba(255, 159, 67, 0.2);
      color: #ff9f43;
      padding: 2px 6px;
      border-radius: 8px;
      margin-left: 8px;
      font-weight: 600;
      border: 1px solid rgba(255, 159, 67, 0.3);
    }

    .date-tooltip {
      position: absolute;
      top: calc(100% + 10px);
      right: 0;
      background: var(--pln-surface);
      border-radius: 8px;
      padding: 12px;
      min-width: 200px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--pln-border);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .date-display.show-tooltip .date-tooltip {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .date-tooltip:before {
      content: '';
      position: absolute;
      top: -6px;
      right: 20px;
      width: 12px;
      height: 12px;
      background: var(--pln-surface);
      transform: rotate(45deg);
      border-top: 1px solid var(--pln-border);
      border-left: 1px solid var(--pln-border);
    }

    .tooltip-title {
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--pln-light-blue);
      font-size: 14px;
    }

    .tooltip-info {
      font-size: 12px;
      color: var(--pln-text-secondary);
      margin-bottom: 5px;
    }

    /* Badge notifikasi */
    .notification-badge {
      display: none;
      position: absolute;
      top: 8px;
      right: 8px;
      background-color: #e74c3c;
      color: white;
      font-size: 0.7rem;
      min-width: 20px;
      height: 20px;
      border-radius: 10px;
      text-align: center;
      justify-content: center;
      align-items: center;
      font-weight: bold;
      padding: 0 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* Animasi badge */
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }

    .notification-badge:not(:empty) {
      animation: pulse 2s infinite;
    }
  </style>
  @yield('styles')
</head>
<body data-theme="dark">
  <div class="container-fluid">
    <!-- Sidebar yang lebih modern -->
    <div class="sidebar">
      <div class="sidebar-logo">
        <img src="/images/logoPLN.jpg" alt="Logo PLN" class="logo-pln">
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
          <a href="{{ route('akun.index') }}" class="{{ request()->routeIs('akun.*') ? 'active' : '' }}">
            <i class="fas fa-users icon"></i>
            <span class="menu-text">Data Akun</span>
          </a>
        </li>
        <li>
          <a href="{{ route('verifikasi.index') }}" class="{{ request()->routeIs('verifikasi.*') ? 'active' : '' }}">
            <i class="fas fa-check-circle icon"></i>
            <span class="menu-text">Verifikasi</span>
          </a>
        </li>
        <li>
          <a href="{{ route('tahunPenilaian.index') }}" class="{{ request()->routeIs('tahunPenilaian.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt icon"></i>
            <span class="menu-text">Tahun Penilaian</span>
          </a>
        </li>
        <li>
          <a href="{{ route('eksporPdf.index') }}" class="{{ request()->routeIs('eksporPdf.*') ? 'active' : '' }}">
            <i class="fas fa-file-pdf icon"></i>
            <span class="menu-text">Ekspor PDF</span>
          </a>
        </li>
        <li>
          <a href="{{ route('aktivitasLog.index') }}" class="{{ request()->routeIs('aktivitasLog.*') ? 'active' : '' }}">
            <i class="fas fa-history icon"></i>
            <span class="menu-text">Log Aktivitas</span>
          </a>
        </li>
        @endif

        {{-- Menu untuk Admin (PIC Bidang) --}}
        @if(Str::startsWith(Auth::user()->role, 'pic_'))
        <li>
          <a href="{{ route('dataKinerja.index') }}" class="{{ request()->routeIs('dataKinerja.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar icon"></i>
            <span class="menu-text">Data Kinerja</span>
          </a>
        </li>
        <li>
          <a href="{{ route('realisasi.index') }}" class="{{ request()->routeIs('realisasi.*') ? 'active' : '' }}">
            <i class="fas fa-tasks icon"></i>
            <span class="menu-text">Realisasi KPI</span>
          </a>
        </li>
        <li>
          <a href="{{ route('kpi.index') }}" class="{{ request()->routeIs('kpi.index') ? 'active' : '' }}">
            <i class="fas fa-chart-line icon"></i>
            <span class="menu-text">Laporan KPI</span>
          </a>
        </li>
        @endif

        {{-- Menu untuk semua user --}}
        <li>
          <a href="{{ route('kpi.history') }}" class="{{ request()->routeIs('kpi.history') ? 'active' : '' }}">
            <i class="fas fa-history icon"></i>
            <span class="menu-text">Riwayat KPI</span>
          </a>
        </li>
        <li>
          <a href="{{ route('notifikasi.index') }}" class="{{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
            <i class="fas fa-bell icon"></i>
            <span class="menu-text">Notifikasi</span>
            <span class="notification-badge" id="notification-count"></span>
          </a>
        </li>
      </ul>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <i class="fas fa-sign-out-alt logout-icon"></i>
          <span class="logout-text">Log Out</span>
        </button>
      </form>
    </div>

    <!-- Header yang lebih modern -->
    <div class="dashboard-header">
      <div class="header-text">
        <h1 class="header-title">@yield('page_title', 'Dashboard PLN')</h1>
        <p class="header-subtitle">PT PLN MANDAU CIPTA TENAGA NUSANTARA</p>
      </div>

      <div style="display: flex; align-items: center;">
        <div class="theme-switch-wrapper">
          <i class="fas fa-moon theme-icon"></i>
          <label class="theme-switch">
            <input type="checkbox" id="theme-toggle">
            <span class="slider"></span>
          </label>
          <i class="fas fa-sun theme-icon"></i>
        </div>

        @if(!request()->routeIs('akun.*'))
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
            <span class="time-display" id="live-time"></span>
          </span>

          <div class="date-tooltip">
            <div class="tooltip-title">Informasi Tanggal</div>
            <div class="tooltip-info">Hari ke-{{ date('z')+1 }} dari {{ date('L') ? '366' : '365' }} hari</div>
            <div class="tooltip-info">Minggu ke-{{ date('W') }} tahun {{ $tahun }}</div>
            <div class="tooltip-info">{{ date('t') }} hari dalam bulan ini</div>
            <div class="tooltip-info">{{ date('L') ? 'Tahun Kabisat' : 'Bukan Tahun Kabisat' }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>

    <main class="main">
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Live clock script yang lebih baik -->
  <script>
    function updateClock() {
      const now = new Date();
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');

      // Update jam secara real-time dengan detik
      const timeDisplay = document.getElementById('live-time');
      if (timeDisplay) {
        // Animasi transisi saat detik berubah
        timeDisplay.style.transition = 'opacity 0.2s';
        timeDisplay.style.opacity = '0.5';

        setTimeout(() => {
          timeDisplay.innerHTML = `${hours}<span class="time-colon">:</span>${minutes} <span class="time-seconds">${seconds}</span> WIB`;
          timeDisplay.style.opacity = '1';
        }, 100);

        // Update every second
        setTimeout(updateClock, 1000);
      }
    }

    // Start the clock when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Jalankan updateClock jika tidak berada di halaman Data Akun
      @if(!request()->routeIs('akun.*'))
      updateClock();

      // Toggle tooltip saat date display diklik
      const dateDisplay = document.getElementById('date-display');
      if (dateDisplay) {
        dateDisplay.addEventListener('click', function() {
          this.classList.toggle('show-tooltip');
        });

        // Tutup tooltip saat klik di luar area
        document.addEventListener('click', function(event) {
          if (!dateDisplay.contains(event.target)) {
            dateDisplay.classList.remove('show-tooltip');
          }
        });
      }
      @endif

      // Theme Switcher dengan animasi transisi
      const themeToggle = document.getElementById('theme-toggle');
      const body = document.body;

      // Check for saved theme preference or use default (dark)
      const currentTheme = localStorage.getItem('theme') || 'dark';

      // Set the body's data-theme attribute and adjust the toggle state
      body.setAttribute('data-theme', currentTheme);

      // If the current theme is light, check the toggle
      if (currentTheme === 'light') {
        themeToggle.checked = true;
      }

      // Listen for toggle changes with smooth transition
      themeToggle.addEventListener('change', function() {
        if (this.checked) {
          body.setAttribute('data-theme', 'light');
          localStorage.setItem('theme', 'light');
        } else {
          body.setAttribute('data-theme', 'dark');
          localStorage.setItem('theme', 'dark');
        }
      });

      // Fungsi untuk mendapatkan jumlah notifikasi yang belum dibaca
      function fetchUnreadNotifications() {
        fetch('{{ route("notifikasi.getJumlahBelumDibaca") }}')
          .then(response => response.json())
          .then(data => {
            const count = data.count;
            const badge = document.getElementById('notification-count');

            if (badge) {
              if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-flex';
              } else {
                badge.style.display = 'none';
              }
            }
          })
          .catch(error => console.error('Error fetching notifications:', error));
      }

      // Panggil fungsi saat halaman dimuat
      fetchUnreadNotifications();

      // Perbarui jumlah notifikasi setiap 1 menit
      setInterval(fetchUnreadNotifications, 60000);

      // Mobile sidebar toggle
      const sidebarToggle = document.createElement('button');
      sidebarToggle.className = 'sidebar-toggle';
      sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
      sidebarToggle.style.display = 'none';

      // Add to DOM for mobile
      document.querySelector('.header-text').before(sidebarToggle);

      // Show toggle button on mobile
      if (window.innerWidth < 992) {
        sidebarToggle.style.display = 'block';
        sidebarToggle.style.background = 'transparent';
        sidebarToggle.style.border = 'none';
        sidebarToggle.style.color = 'white';
        sidebarToggle.style.fontSize = '1.5rem';
        sidebarToggle.style.cursor = 'pointer';
        sidebarToggle.style.marginRight = '15px';
      }

      // Toggle sidebar on mobile
      sidebarToggle.addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar.style.width === '260px') {
          sidebar.style.width = '0';
        } else {
          sidebar.style.width = '260px';
        }
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');

        if (window.innerWidth < 992 &&
            sidebar.style.width === '260px' &&
            !sidebar.contains(event.target) &&
            event.target !== sidebarToggle) {
          sidebar.style.width = '0';
        }
      });

      // Adjust on resize
      window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
          sidebarToggle.style.display = 'block';
        } else {
          sidebarToggle.style.display = 'none';
          document.querySelector('.sidebar').style.width = '';
        }
      });
    });
  </script>
  @yield('scripts')
</body>
</html>
