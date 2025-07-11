<?php

namespace App\Http\Controllers;

use App\Models\Realisasi;
use App\Models\Indikator;
use App\Models\TahunPenilaian;
use App\Models\TargetKPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class RealisasiController extends Controller
{


public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', Carbon::today()->year);
        $bulan = $request->input('bulan', Carbon::today()->month);

        $indikatorsQuery = Indikator::with([
            'pilar',
            'bidang',
            'targetKPI' => function ($query) use ($tahun) {
                $query->whereHas('tahunPenilaian', fn($q) => $q->where('tahun', $tahun));
            },
            'realisasis' => function ($query) use ($tahun, $bulan) {
                $query->where('tahun', $tahun)->where('bulan', $bulan)->where('periode_tipe', 'bulanan');
            }
        ]);

        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang) {
                return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan.');
            }
            $indikatorsQuery->where('bidang_id', $bidang->id);
        } elseif (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $indikators = $indikatorsQuery->orderBy('kode')->get();

        $grouped = [];

        foreach ($indikators as $indikator) {
            $realisasi = $indikator->realisasis->first();

            $targetKPI = $indikator->targetKPI
                ->where('tahunPenilaian.tahun', $tahun)
                ->first();

            $target_bulanan = 0;
            if ($targetKPI && is_array($targetKPI->target_bulanan)) {
                $target_bulanan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
            }

            $persentase = 0;
            if ($realisasi && $target_bulanan > 0) {
                $persentase = ($realisasi->nilai / $target_bulanan) * 100;
            }

            $indikator->firstRealisasi = $realisasi;
            $indikator->persentase = min($persentase, 110);
            $indikator->target_nilai = $target_bulanan;

            if ($realisasi && $target_bulanan > 0) {
                $indikator->jenis_polaritas = Realisasi::getJenisPolaritas($indikator->kode);
                $indikator->nilai_polaritas = round($realisasi->hitungPolaritas($target_bulanan), 2);
            } else {
                $indikator->jenis_polaritas = '-';
                $indikator->nilai_polaritas = 0;
            }

            if ($user->isMasterAdmin()) {
                $key = $indikator->pilar->kode ?? 'Tanpa Pilar';
                $grouped[$key]['nama'] = $indikator->pilar->nama ?? 'Tanpa Nama';
            } else {
                $key = $indikator->bidang->kode ?? 'Tanpa Bidang';
                $grouped[$key]['nama'] = $indikator->bidang->nama ?? 'Tanpa Nama';
            }

            $grouped[$key]['indikators'][] = $indikator;
        }

        return view('realisasi.index', [
            'grouped' => $grouped,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'isMaster' => $user->isMasterAdmin(),
        ]);
    }




public function create($indikatorId)
{
    $indikator = Indikator::with(['pilar', 'bidang'])->findOrFail($indikatorId);
    $user = Auth::user();

    // Master Admin (asisten_manager) boleh input semua indikator
    if ($user->isMasterAdmin()) {
        // Akses penuh
    }
    // Admin bidang (pic_*) hanya boleh input jika indikator sesuai bidang mereka
    elseif ($user->isAdmin()) {
        $bidang = \App\Models\Bidang::where('role_pic', $user->role)->first();

        if (!$bidang || $bidang->id !== $indikator->bidang_id) {
            abort(403, 'Anda tidak memiliki akses untuk indikator ini.');
        }
    } else {
        abort(403, 'Anda tidak memiliki akses ke fitur ini.');
    }

    // Ambil tahun dan bulan dari parameter GET (yang dipilih di halaman index)
    $tahun = request('tahun');
    $bulan = request('bulan');

    if (!$tahun || !$bulan) {
        return redirect()->route('realisasi.index')->with('error', 'Tahun dan bulan harus dipilih terlebih dahulu.');
    }

    // Validasi tahun dan bulan
    if (!is_numeric($tahun) || !is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
        return redirect()->route('realisasi.index')->with('error', 'Tahun atau bulan tidak valid.');
    }

    // Cek apakah sudah ada realisasi untuk bulan dan tahun tersebut
    $existingRealisasi = Realisasi::where('indikator_id', $indikator->id)
        ->where('tahun', $tahun)
        ->where('bulan', $bulan)
        ->where('periode_tipe', 'bulanan')
        ->first();

    if ($existingRealisasi) {
        return redirect()->route('realisasi.index', ['tahun' => $tahun, 'bulan' => $bulan])
            ->with('warning', 'Realisasi untuk bulan ini sudah ada. Gunakan fitur edit untuk mengubah data.');
    }

    // Ambil target KPI untuk tahun ini
    $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
        ->whereHas('tahunPenilaian', fn($q) => $q->where('tahun', $tahun))
        ->first();

    // Ambil target bulanan spesifik untuk bulan yang dipilih
    $targetBulanan = 0;
    if ($targetKPI && is_array($targetKPI->target_bulanan)) {
        $targetBulanan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
    }

    // Nama bulan dalam bahasa Indonesia
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    return view('realisasi.create', compact('indikator', 'tahun', 'bulan', 'targetBulanan', 'namaBulan'));
}




public function store(Request $request, Indikator $indikator)
{
    $request->validate([
        'tahun' => 'required|integer|min:2020|max:2030',
        'bulan' => 'required|integer|min:1|max:12',
        'nilai' => 'required|numeric|min:0',
        'keterangan' => 'nullable|string|max:1000',
            'polaritas' => 'required|in:Positif,Negatif,Netral',
    ]);

    $tahun = $request->tahun;
    $bulan = $request->bulan;
    $user = auth()->user();

    // Cek apakah user berwenang menginput indikator ini
    if (!$user->isMasterAdmin() && !$user->isAdmin()) {
        abort(403, 'Anda tidak memiliki hak untuk input realisasi.');
    }

    // Jika admin/pic, cek apakah indikator milik bidang yang sesuai
    if ($user->isAdmin() && $indikator->bidang->role_pic !== $user->role) {
        abort(403, 'Indikator ini tidak termasuk dalam bidang Anda.');
    }

    // Cek apakah sudah ada realisasi untuk bulan dan tahun tersebut
    $existingRealisasi = Realisasi::where('indikator_id', $indikator->id)
        ->where('tahun', $tahun)
        ->where('bulan', $bulan)
        ->where('periode_tipe', 'bulanan')
        ->first();

    if ($existingRealisasi) {
        return redirect()->route('realisasi.index', ['tahun' => $tahun, 'bulan' => $bulan])
            ->with('error', 'Realisasi untuk bulan ini sudah ada.');
    }

    // Ambil target KPI untuk tahun ini
    $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
        ->whereHas('tahunPenilaian', function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        })->first();

    // Ambil target bulanan spesifik untuk bulan yang dipilih
    $targetBulanan = 0;
    if ($targetKPI && is_array($targetKPI->target_bulanan)) {
        $targetBulanan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
    }

    // Buat tanggal untuk akhir bulan (untuk keperluan kompatibilitas)
    $tanggalAkhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();
    
   // Ambil bobot indikator, default 1 jika null
$bobot = $indikator->bobot ?? 1;

// Hitung persentase pencapaian
$persentase = $targetBulanan > 0 ? min(($request->nilai / $targetBulanan) * 100, 110) : 0;

// Hitung nilai akhir sesuai rumus
$nilai_akhir = $bobot * ($persentase / 100);

$realisasi = new Realisasi([
    'indikator_id' => $indikator->id,
    'user_id' => $user->id,
    'tanggal' => $tanggalAkhirBulan->toDateString(),
    'tahun' => $tahun,
    'bulan' => $bulan,
    'periode_tipe' => 'bulanan',
    'nilai' => $request->nilai,
    'persentase' => $persentase,
    'nilai_akhir' => $nilai_akhir,  // Tambahkan nilai_akhir di sini
    'keterangan' => $request->keterangan,
    'diverifikasi' => false,
]);


    // Hitung dan set polaritas
    if ($targetBulanan > 0) {
        $jenisPolaritas = Realisasi::getJenisPolaritas($indikator->kode);
        $nilaiPolaritas = $realisasi->hitungPolaritas($targetBulanan);

        $realisasi->jenis_polaritas = $jenisPolaritas;
        $realisasi->nilai_polaritas = round($nilaiPolaritas, 2);
    }

    $realisasi->save();

    return redirect()->route('realisasi.index', ['tahun' => $tahun, 'bulan' => $bulan])
        ->with('success', 'Realisasi berhasil disimpan.');
}




    public function edit(Request $request, $indikatorId)
    {
        $indikator = Indikator::with(['pilar', 'bidang'])->findOrFail($indikatorId);
        $user = Auth::user();

        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $indikator->bidang_id !== $bidang->id) {
                abort(403, 'Anda tidak memiliki akses untuk indikator ini.');
            }
        } elseif (!$user->isMasterAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke fitur ini.');
        }

        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);

        $realisasi = Realisasi::where('indikator_id', $indikator->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('periode_tipe', 'bulanan')
            ->first();

        if (!$realisasi) {
            return redirect()->back()->with('error', 'Data realisasi tidak ditemukan.');
        }

        // Nama bulan dalam bahasa Indonesia
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Ambil target bulanan
        $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
            ->whereHas('tahunPenilaian', function($q) use ($tahun) {
                $q->where('tahun', $tahun);
            })->first();

        $targetBulanan = 0;
        if ($targetKPI) {
            $targetBulanan = $targetKPI->target_bulanan[$bulan - 1] ?? 0;
        }

        return view('realisasi.edit', compact('indikator', 'realisasi', 'tahun', 'bulan', 'namaBulan', 'targetBulanan'));
    }


    public function update(Request $request, $id)
    {
        $realisasi = Realisasi::with('indikator')->findOrFail($id);
        $indikator = $realisasi->indikator;
        $user = Auth::user();

        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $indikator->bidang_id !== $bidang->id) {
                abort(403, 'Anda tidak diizinkan mengedit realisasi ini.');
            }
        } elseif (!$user->isMasterAdmin()) {
            abort(403, 'Anda tidak diizinkan mengedit realisasi ini.');
        }

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        // Ambil target KPI untuk tahun ini
        $targetKPI = TargetKPI::where('indikator_id', $indikator->id)
            ->whereHas('tahunPenilaian', function($q) use ($realisasi) {
                $q->where('tahun', $realisasi->tahun);
            })->first();

        // Ambil target bulanan spesifik untuk bulan realisasi
        $targetBulanan = 0;
        if ($targetKPI && is_array($targetKPI->target_bulanan)) {
            $targetBulanan = $targetKPI->target_bulanan[$realisasi->bulan - 1] ?? 0;
        }

        // Update data realisasi
       $bobot = $indikator->bobot ?? 1;

// Hitung persentase pencapaian
$persentase = $targetBulanan > 0 ? min(($request->nilai / $targetBulanan) * 100, 110) : 0;

// Hitung nilai akhir sesuai rumus
$nilai_akhir = $bobot * ($persentase / 100);

$realisasi->update([
    'nilai' => $validated['nilai'],
    'keterangan' => $validated['keterangan'] ?? $realisasi->keterangan,
    'user_id' => $user->id,
    'persentase' => $persentase,
    'nilai_akhir' => $nilai_akhir,  // Update nilai_akhir juga
]);


        // Hitung ulang dan update polaritas
        if ($targetBulanan > 0) {
            $jenisPolaritas = Realisasi::getJenisPolaritas($indikator->kode);
            $nilaiPolaritas = $realisasi->hitungPolaritas($targetBulanan);

            $realisasi->update([
                'jenis_polaritas' => $jenisPolaritas,
                'nilai_polaritas' => round($nilaiPolaritas, 2)
            ]);
        }

        return redirect()->route('realisasi.index', ['tahun' => $realisasi->tahun, 'bulan' => $realisasi->bulan])
            ->with('success', 'Realisasi berhasil diperbarui.');
    }


    /**
     * Delete realisasi bulanan
     */
    public function destroy($id)
    {
        $realisasi = Realisasi::with('indikator')->findOrFail($id);
        $indikator = $realisasi->indikator;
        $user = Auth::user();

        // Cek akses user
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $indikator->bidang_id !== $bidang->id) {
                abort(403, 'Anda tidak diizinkan menghapus realisasi ini.');
            }
        } elseif (!$user->isMasterAdmin()) {
            abort(403, 'Anda tidak diizinkan menghapus realisasi ini.');
        }

        $tahun = $realisasi->tahun;
        $bulan = $realisasi->bulan;

        $realisasi->delete();

        return redirect()->route('realisasi.index', ['tahun' => $tahun, 'bulan' => $bulan])
            ->with('success', 'Realisasi berhasil dihapus.');
    }

    /**
     * Get daftar bulan untuk dropdown
     */
    public static function getDaftarBulan()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }

    /**
     * Get daftar tahun untuk dropdown
     */
    public static function getDaftarTahun()
    {
        $currentYear = Carbon::now()->year;
        $years = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 2; $i++) {
            $years[$i] = $i;
        }
        return $years;
    }
}
