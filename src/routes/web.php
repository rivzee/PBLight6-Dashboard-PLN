<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\TahunPenilaianController;
use App\Http\Controllers\DataKinerjaController;
use App\Http\Controllers\EksporPdfController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\AktivitasLogController;
use Illuminate\Support\Facades\Auth;
use App\Models\Bidang;
use App\Models\Indikator;
use App\Models\NilaiKPI;

    // Redirect ke halaman utama
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Halaman login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

    // Proses login
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route dashboard yang membutuhkan autentikasi
    Route::middleware(['auth'])->group(function () {

        // Dashboard utama, setelah login diarahkan berdasarkan role
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route lama tidak digunakan lagi karena sudah digabung di index()
        // Berikut ini menjadi alias saja untuk kompatibilitas
        Route::get('/dashboard/master', [DashboardController::class, 'master'])->name('dashboard.master');
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
        Route::get('/dashboard/user', [DashboardController::class, 'user'])->name('dashboard.user');

        // Dashboard lama untuk PIC admin per bidang sudah tidak digunakan lagi
        // (semua diarahkan ke dashboard.admin)

        // Dashboard untuk PIC admin per bidang
        Route::get('/dashboard/admin/keuangan', function () {
            return view('dashboard.admin_keuangan');
        })->name('dashboard.admin.keuangan');

        Route::get('/dashboard/admin/risiko', function () {
            return view('dashboard.admin_risiko');
        })->name('dashboard.admin.risiko');

        Route::get('/dashboard/admin/skreperusahaan', function () {
            return view('dashboard.admin_skreperusahaan');
        })->name('dashboard.admin.skreperusahaan');

        Route::get('/dashboard/admin/perencanaan-operasi', function () {
            return view('dashboard.admin_perencanaan_operasi');
        })->name('dashboard.admin.perencanaan_operasi');

        Route::get('/dashboard/admin/pengembangan-bisnis', function () {
            return view('dashboard.admin_pengembangan_bisnis');
        })->name('dashboard.admin.pengembangan_bisnis');

        Route::get('/dashboard/admin/human-capital', function () {
            return view('dashboard.admin_human_capital');
        })->name('dashboard.admin.human_capital');

        Route::get('/dashboard/admin/k3l', function () {
            return view('dashboard.admin_k3l');
        })->name('dashboard.admin.k3l');

        Route::get('/dashboard/admin/perencanaan-korporat', function () {
            return view('dashboard.admin_perencanaan_korporat');
        })->name('dashboard.admin.perencanaan_korporat');

        Route::get('/dashboard/admin/sekretaris-perusahaan', function () {
            return view('dashboard.admin_sekretaris');
        })->name('dashboard.admin.sekretaris_perusahaan');

        Route::get('/dashboard/admin/hukum', function () {
            // Menampilkan dashboard hukum khusus
            $user = Auth::user();
            $tahun = request('tahun', date('Y'));
            $bulan = request('bulan', date('m'));
            $periodeTipe = request('periode_tipe', 'bulanan');

            // Dapatkan bidang hukum
            $bidang = Bidang::where('role_pic', 'pic_hukum')->first();

            if (!$bidang) {
                return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
            }

            // Dapatkan indikator untuk bidang hukum
            $indikators = Indikator::where('bidang_id', $bidang->id)
                ->where('aktif', true)
                ->orderBy('kode')
                ->get();

            // Dapatkan nilai KPI untuk indikator-indikator tersebut
            foreach ($indikators as $indikator) {
                $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', $periodeTipe)
                    ->first();

                $indikator->nilai_persentase = $nilaiKPI ? $nilaiKPI->persentase : 0;
                $indikator->nilai_absolut = $nilaiKPI ? $nilaiKPI->nilai : 0;
                $indikator->diverifikasi = $nilaiKPI ? $nilaiKPI->diverifikasi : false;
            }

            // Hitung rata-rata nilai KPI untuk bidang ini
            $totalNilai = 0;
            foreach ($indikators as $indikator) {
                $totalNilai += $indikator->nilai_persentase;
            }

            $rataRata = $indikators->count() > 0 ? round($totalNilai / $indikators->count(), 2) : 0;

            // Tampilkan view khusus admin_hukum
            return view('dashboard.admin_hukum', compact('bidang', 'indikators', 'rataRata', 'tahun', 'bulan'));
        })->name('dashboard.admin.hukum');

        // Route untuk KPI
        Route::resource('kpi', KPIController::class);
        Route::get('/kpi/verifikasi', [KPIController::class, 'verifikasi'])->name('kpi.verifikasi');
        Route::post('/kpi/verifikasi', [KPIController::class, 'prosesVerifikasi'])->name('kpi.proses-verifikasi');
        Route::get('/kpi/history', [KPIController::class, 'history'])->name('kpi.history');
        Route::get('/kpi/laporan', [KPIController::class, 'laporan'])->name('kpi.laporan');

        // Resource controllers untuk fitur CRUD
        Route::resource('verifikasi', VerifikasiController::class);
        Route::resource('tahunPenilaian', TahunPenilaianController::class);
        Route::get('/tahunPenilaian/{id}/activate', [TahunPenilaianController::class, 'activate'])->name('tahunPenilaian.activate');
        Route::resource('dataKinerja', DataKinerjaController::class);
        Route::resource('realisasi', RealisasiController::class);

        // Ekspor PDF
        Route::get('/ekspor-pdf', [EksporPdfController::class, 'index'])->name('eksporPdf.index');
        Route::post('/ekspor-pdf/bidang', [EksporPdfController::class, 'eksporBidang'])->name('eksporPdf.bidang');
        Route::post('/ekspor-pdf/pilar', [EksporPdfController::class, 'eksporPilar'])->name('eksporPdf.pilar');
        Route::post('/ekspor-pdf/keseluruhan', [EksporPdfController::class, 'eksporKeseluruhan'])->name('eksporPdf.keseluruhan');

        // CRUD Akun - sudah dikonfigurasi di controller
        Route::resource('akun', AkunController::class);

        // Notifikasi
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/{id}/tandai-dibaca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.tandaiDibaca');
        Route::post('/notifikasi/tandai-semua-dibaca', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.tandaiSemuaDibaca');
        Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
        Route::post('/notifikasi/hapus-sudah-dibaca', [NotifikasiController::class, 'hapusSudahDibaca'])->name('notifikasi.hapusSudahDibaca');
        Route::get('/notifikasi/jumlah-belum-dibaca', [NotifikasiController::class, 'getJumlahBelumDibaca'])->name('notifikasi.getJumlahBelumDibaca');
        Route::get('/notifikasi/terbaru', [NotifikasiController::class, 'getNotifikasiTerbaru'])->name('notifikasi.getNotifikasiTerbaru');

        // Aktivitas Log hanya untuk asisten_manager - menggunakan route group biasa tanpa middleware closure
        Route::prefix('aktivitas-log')->name('aktivitasLog.')->middleware('auth')->group(function () {
            // Buat middleware check role di controller
            Route::get('/', [AktivitasLogController::class, 'index'])->name('index');
            Route::get('/ekspor-csv', [AktivitasLogController::class, 'eksporCsv'])->name('eksporCsv');
            Route::post('/hapus-log-lama', [AktivitasLogController::class, 'hapusLogLama'])->name('hapusLogLama');
            // Parameter route harus selalu di paling bawah untuk menghindari konflik dengan route lain
            Route::get('/{id}', [AktivitasLogController::class, 'show'])->name('show')->where('id', '[0-9]+');
        });

        // ========== ROUTES UNTUK MASTER ADMIN (ASISTEN MANAJER) ==========
        Route::middleware(['auth'])->group(function () {
            // Middleware role sudah dihapus, karena pengecekan role dilakukan di controller

            // Verifikasi KPI
            Route::resource('verifikasi', VerifikasiController::class)->except(['create', 'edit', 'store']);
            Route::post('/verifikasi/massal', [VerifikasiController::class, 'verifikasiMassal'])->name('verifikasi.massal');

            // Tahun Penilaian
            Route::resource('tahunPenilaian', TahunPenilaianController::class);
            Route::get('/tahunPenilaian/{id}/activate', [TahunPenilaianController::class, 'activate'])->name('tahunPenilaian.activate');

            // Ekspor PDF
            Route::get('/ekspor-pdf', [EksporPdfController::class, 'index'])->name('eksporPdf.index');
            Route::post('/ekspor-pdf/bidang', [EksporPdfController::class, 'eksporBidang'])->name('eksporPdf.bidang');
            Route::post('/ekspor-pdf/pilar', [EksporPdfController::class, 'eksporPilar'])->name('eksporPdf.pilar');
            Route::post('/ekspor-pdf/keseluruhan', [EksporPdfController::class, 'eksporKeseluruhan'])->name('eksporPdf.keseluruhan');

            // Log Aktivitas
            Route::prefix('aktivitas-log')->name('aktivitasLog.')->group(function () {
                Route::get('/', [AktivitasLogController::class, 'index'])->name('index');
                Route::get('/ekspor-csv', [AktivitasLogController::class, 'eksporCsv'])->name('eksporCsv');
                Route::post('/hapus-log-lama', [AktivitasLogController::class, 'hapusLogLama'])->name('hapusLogLama');
                Route::get('/{id}', [AktivitasLogController::class, 'show'])->name('show')->where('id', '[0-9]+');
            });
        });

        // ========== ROUTES UNTUK ADMIN (PIC BIDANG) ==========
        Route::middleware(['auth'])->group(function () {
            // Manajemen KPI Bidang - tambahkan pengecekan role di controller sebagai ganti middleware
            Route::resource('realisasi', RealisasiController::class)->except(['destroy']);

            // Data Kinerja Bidang - tambahkan pengecekan role di controller sebagai ganti middleware
            Route::resource('dataKinerja', DataKinerjaController::class)->except(['destroy']);
        });

        // ========== ROUTES UNTUK SEMUA (ADMIN & KARYAWAN) ==========
        // KPI Resource routes untuk melihat & membaca data
        Route::get('/kpi', [KPIController::class, 'index'])->name('kpi.index');
        Route::get('/kpi/history', [KPIController::class, 'history'])->name('kpi.history');
        Route::get('/kpi/laporan', [KPIController::class, 'laporan'])->name('kpi.laporan');
        Route::get('/kpi/{id}', [KPIController::class, 'show'])->name('kpi.show');

        // Routes khusus admin & master admin
        Route::middleware(['auth'])->group(function () {
            Route::get('/kpi/create', [KPIController::class, 'create'])->name('kpi.create');
            Route::post('/kpi', [KPIController::class, 'store'])->name('kpi.store');
        });
    });

    // ========== DASHBOARD LEGACY (DEPRECATED) ==========
    // Dashboard admin lama untuk backward compatibility
    Route::get('/dashboard/admin/keuangan', function () {
        return view('dashboard.admin_keuangan');
    })->name('dashboard.admin.keuangan');

    Route::get('/dashboard/admin/risiko', function () {
        return view('dashboard.admin_risiko');
    })->name('dashboard.admin.risiko');

    Route::get('/dashboard/admin/skreperusahaan', function () {
        return view('dashboard.admin_skreperusahaan');
    })->name('dashboard.admin.skreperusahaan');

    Route::get('/dashboard/admin/perencanaan-operasi', function () {
        return view('dashboard.admin_perencanaan_operasi');
    })->name('dashboard.admin.perencanaan_operasi');

    Route::get('/dashboard/admin/pengembangan-bisnis', function () {
        return view('dashboard.admin_pengembangan_bisnis');
    })->name('dashboard.admin.pengembangan_bisnis');

    Route::get('/dashboard/admin/human-capital', function () {
        return view('dashboard.admin_human_capital');
    })->name('dashboard.admin.human_capital');

    Route::get('/dashboard/admin/k3l', function () {
        return view('dashboard.admin_k3l');
    })->name('dashboard.admin.k3l');

    Route::get('/dashboard/admin/perencanaan-korporat', function () {
        return view('dashboard.admin_perencanaan_korporat');
    })->name('dashboard.admin.perencanaan_korporat');

    Route::get('/dashboard/admin/sekretaris-perusahaan', function () {
        return view('dashboard.admin_sekretaris');
    })->name('dashboard.admin.sekretaris_perusahaan');

    Route::get('/dashboard/admin/hukum', function () {
        // Menampilkan dashboard hukum khusus
        $user = Auth::user();
        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));
        $periodeTipe = request('periode_tipe', 'bulanan');

        // Dapatkan bidang hukum
        $bidang = Bidang::where('role_pic', 'pic_hukum')->first();

        if (!$bidang) {
            return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
        }

        // Dapatkan indikator untuk bidang hukum
        $indikators = Indikator::where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->orderBy('kode')
            ->get();

        // Dapatkan nilai KPI untuk indikator-indikator tersebut
        foreach ($indikators as $indikator) {
            $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', $periodeTipe)
                ->first();

            $indikator->nilai_persentase = $nilaiKPI ? $nilaiKPI->persentase : 0;
            $indikator->nilai_absolut = $nilaiKPI ? $nilaiKPI->nilai : 0;
            $indikator->diverifikasi = $nilaiKPI ? $nilaiKPI->diverifikasi : false;
        }

        // Hitung rata-rata nilai KPI untuk bidang ini
        $totalNilai = 0;
        foreach ($indikators as $indikator) {
            $totalNilai += $indikator->nilai_persentase;
        }

        $rataRata = $indikators->count() > 0 ? round($totalNilai / $indikators->count(), 2) : 0;

        // Tampilkan view khusus admin_hukum
        return view('dashboard.admin_hukum', compact('bidang', 'indikators', 'rataRata', 'tahun', 'bulan'));
    })->name('dashboard.admin.hukum');

