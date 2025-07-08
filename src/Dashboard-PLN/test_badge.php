<?php
/**
 * Test Badge Notifikasi
 * Untuk memeriksa apakah badge notifikasi berfungsi dengan benar
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== TEST BADGE NOTIFIKASI ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Test koneksi database
    echo "1. Test koneksi database...\n";
    $realisasiTotal = \App\Models\Realisasi::count();
    $targetTotal = \App\Models\TargetKPI::count();
    echo "   ✅ Koneksi berhasil - Total realisasi: $realisasiTotal, Total target: $targetTotal\n\n";

    // 2. Hitung notifikasi
    echo "2. Hitung notifikasi aktif...\n";
    $unverifiedCount = \App\Models\Realisasi::where('diverifikasi', false)->count();
    $unapprovedCount = \App\Models\TargetKPI::where('disetujui', false)->count();
    $totalNotifications = $unverifiedCount + $unapprovedCount;

    echo "   - Realisasi belum diverifikasi: $unverifiedCount\n";
    echo "   - Target belum disetujui: $unapprovedCount\n";
    echo "   - Total notifikasi: $totalNotifications\n\n";

    // 3. Test badge logic
    echo "3. Test badge logic...\n";
    if ($totalNotifications > 0) {
        echo "   ✅ Badge HARUS tampil dengan angka: $totalNotifications\n";

        if ($unverifiedCount > 0 && $unapprovedCount > 0) {
            echo "   🟣 Badge type: mixed-notifications (purple)\n";
            echo "   📍 Mini indicator: realisasi ($unverifiedCount) + target ($unapprovedCount)\n";
        } elseif ($unverifiedCount > 0) {
            echo "   🔴 Badge type: realisasi-notifications (red)\n";
        } else {
            echo "   🟢 Badge type: target-notifications (green)\n";
        }
    } else {
        echo "   ❌ Badge TIDAK tampil (tidak ada notifikasi)\n";
    }
    echo "\n";

    // 4. Test data detail
    echo "4. Detail data untuk debugging...\n";

    if ($unverifiedCount > 0) {
        echo "   📋 Realisasi belum diverifikasi:\n";
        $unverifiedItems = \App\Models\Realisasi::with(['indikator', 'user'])
            ->where('diverifikasi', false)
            ->latest()
            ->take(3)
            ->get();

        foreach ($unverifiedItems as $item) {
            echo "      - ID: {$item->id}, Nilai: {$item->nilai}, ";
            echo "Indikator: " . ($item->indikator->nama ?? 'N/A') . "\n";
        }
    }

    if ($unapprovedCount > 0) {
        echo "   🎯 Target belum disetujui:\n";
        $unapprovedItems = \App\Models\TargetKPI::with(['indikator', 'user'])
            ->where('disetujui', false)
            ->latest()
            ->take(3)
            ->get();

        foreach ($unapprovedItems as $item) {
            echo "      - ID: {$item->id}, Target: {$item->target_bulanan}, ";
            echo "Indikator: " . ($item->indikator->nama ?? 'N/A') . "\n";
        }
    }
    echo "\n";

    // 5. Simulasi HTML badge
    echo "5. Simulasi HTML badge yang akan di-render...\n";
    if ($totalNotifications > 0) {
        $badgeClass = '';
        if ($unverifiedCount > 0 && $unapprovedCount > 0) {
            $badgeClass = 'mixed-notifications';
        } elseif ($unverifiedCount > 0) {
            $badgeClass = 'realisasi-notifications';
        } else {
            $badgeClass = 'target-notifications';
        }

        echo "   HTML: <span class=\"notification-badge $badgeClass\" \n";
        echo "                data-realisasi=\"$unverifiedCount\" \n";
        echo "                data-target=\"$unapprovedCount\">$totalNotifications</span>\n";

        if ($unverifiedCount > 0 && $unapprovedCount > 0) {
            echo "   Mini indicator: Ya (kedua jenis ada)\n";
        }
    } else {
        echo "   HTML: Tidak ada badge (totalNotifications = 0)\n";
    }
    echo "\n";

    // 6. Rekomendasi
    echo "6. Rekomendasi troubleshooting...\n";
    if ($totalNotifications > 0) {
        echo "   ✅ Data siap - badge seharusnya muncul\n";
        echo "   🔍 Jika badge tidak muncul, cek:\n";
        echo "      - User role = 'asisten_manager'\n";
        echo "      - CSS tidak ada display: none\n";
        echo "      - JavaScript tidak error\n";
        echo "      - Browser console untuk debug\n";
    } else {
        echo "   💡 Untuk test badge:\n";
        echo "      - Tambah realisasi baru tanpa centang 'diverifikasi'\n";
        echo "      - Atau tambah target baru tanpa centang 'disetujui'\n";
        echo "      - Kemudian refresh halaman\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== END TEST ===\n";
?>
/**
 * Test Badge Notification System
 * File ini digunakan untuk testing badge notifikasi
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap aplikasi Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Realisasi;
use App\Models\TargetKPI;
use App\Models\User;

echo "🔔 TESTING BADGE NOTIFICATION SYSTEM\n";
echo "=====================================\n\n";

try {
    // Test 1: Hitung data aktual
    echo "📊 1. DATA COUNTS:\n";

    $totalRealisasi = Realisasi::count();
    $unverifiedRealisasi = Realisasi::where('diverifikasi', false)->count();
    $unapprovedTargets = TargetKPI::where('disetujui', false)->count();
    $totalNotifications = $unverifiedRealisasi + $unapprovedTargets;

    echo "   - Total Realisasi: {$totalRealisasi}\n";
    echo "   - Realisasi Belum Diverifikasi: {$unverifiedRealisasi}\n";
    echo "   - Target Belum Disetujui: {$unapprovedTargets}\n";
    echo "   - TOTAL NOTIFIKASI: {$totalNotifications}\n\n";

    // Test 2: Analisis kondisi badge
    echo "🎯 2. BADGE CONDITION ANALYSIS:\n";

    if ($totalNotifications === 0) {
        echo "   ✅ STATUS: Tidak ada notifikasi - badge tidak muncul (NORMAL)\n";
    } elseif ($unverifiedRealisasi > 0 && $unapprovedTargets > 0) {
        echo "   🔴 STATUS: Mixed notifications - badge harus MERAH/PINK dengan dots\n";
        echo "   📋 BADGE TEXT: {$totalNotifications}\n";
        echo "   🎨 BADGE CLASS: mixed-notifications\n";
    } elseif ($unverifiedRealisasi > 0) {
        echo "   🔴 STATUS: Realisasi only - badge harus MERAH\n";
        echo "   📋 BADGE TEXT: {$totalNotifications}\n";
        echo "   🎨 BADGE CLASS: realisasi-notifications\n";
    } else {
        echo "   🔵 STATUS: Target only - badge harus BIRU\n";
        echo "   📋 BADGE TEXT: {$totalNotifications}\n";
        echo "   🎨 BADGE CLASS: target-notifications\n";
    }
    echo "\n";

    // Test 3: Sample data detail
    echo "📋 3. SAMPLE DATA FOR DROPDOWN:\n";

    if ($unverifiedRealisasi > 0) {
        echo "   🚨 REALISASI SECTION:\n";
        $sampleRealisasi = Realisasi::with(['indikator', 'user'])
            ->where('diverifikasi', false)
            ->latest()
            ->take(3)
            ->get();

        foreach ($sampleRealisasi as $index => $item) {
            $indikatorNama = $item->indikator->nama ?? 'Unknown';
            $userName = $item->user->name ?? 'Unknown';
            $nilai = is_numeric($item->nilai) ? number_format($item->nilai, 0, ',', '.') : '0';
            $waktu = $item->created_at->diffForHumans();

            echo "     {$index + 1}. {$indikatorNama} - {$userName} ({$nilai}) - {$waktu}\n";
        }
        echo "\n";
    }

    if ($unapprovedTargets > 0) {
        echo "   🎯 TARGET SECTION:\n";
        $sampleTargets = TargetKPI::with(['indikator', 'user'])
            ->where('disetujui', false)
            ->latest()
            ->take(3)
            ->get();

        foreach ($sampleTargets as $index => $item) {
            $indikatorNama = $item->indikator->nama ?? 'Unknown';
            $userName = $item->user->name ?? 'Unknown';
            $target = is_numeric($item->target_bulanan) ? number_format($item->target_bulanan, 0, ',', '.') : '0';
            $waktu = $item->created_at->diffForHumans();

            echo "     {$index + 1}. {$indikatorNama} - {$userName} (Target: {$target}) - {$waktu}\n";
        }
        echo "\n";
    }

    // Test 4: Rekomendasi testing
    echo "🧪 4. TESTING RECOMMENDATIONS:\n";

    if ($totalNotifications === 0) {
        echo "   📝 Untuk test badge:\n";
        echo "      1. Buat realisasi baru tanpa verifikasi\n";
        echo "      2. Buat target KPI baru tanpa persetujuan\n";
        echo "      3. Refresh halaman dashboard\n";
    } else {
        echo "   ✅ Badge seharusnya muncul dengan count: {$totalNotifications}\n";
        echo "   🔍 Cek browser console untuk debug JavaScript\n";
        echo "   🖱️  Klik bell icon untuk test dropdown\n";
    }
    echo "\n";

    // Test 5: Role check
    echo "👤 5. USER ROLE CHECK:\n";
    $asistenManagerUsers = User::where('role', 'asisten_manager')->count();
    echo "   - Asisten Manager users: {$asistenManagerUsers}\n";
    echo "   - Note: Badge hanya muncul untuk role 'asisten_manager'\n\n";

    echo "✅ BADGE TEST COMPLETED\n";
    echo "======================\n";
    echo "💡 TIP: Jalankan di browser dan cek console untuk debug JavaScript\n\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 FILE: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

echo "🔔 END OF BADGE TEST\n";
?>
