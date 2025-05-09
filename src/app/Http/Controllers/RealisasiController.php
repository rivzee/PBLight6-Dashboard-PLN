<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\NilaiKPI;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RealisasiController extends Controller
{
    /**
     * Memastikan user telah login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar realisasi KPI yang menjadi tanggung jawab user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->tahun ?? Carbon::now()->year;
        $bulan = $request->bulan ?? Carbon::now()->month;
        $periodeTipe = $request->periode_tipe ?? 'bulanan';

        if ($user->isMasterAdmin()) {
            // Master admin dapat melihat semua indikator
            $indikators = Indikator::with(['pilar', 'bidang'])->where('aktif', true)->get();
        } elseif ($user->isAdmin()) {
            // Admin (PIC) hanya dapat melihat indikator yang menjadi tanggung jawabnya
            $bidang = $user->getBidang();

            if (!$bidang) {
                return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan.');
            }

            $indikators = Indikator::with(['pilar', 'bidang'])
                ->where('bidang_id', $bidang->id)
                ->where('aktif', true)
                ->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        // Ambil data realisasi untuk setiap indikator
        foreach ($indikators as $indikator) {
            $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', $periodeTipe)
                ->first();

            $indikator->realisasi = $nilaiKPI ? $nilaiKPI->nilai : 0;
            $indikator->persentase = $nilaiKPI ? $nilaiKPI->persentase : 0;
            $indikator->keterangan = $nilaiKPI ? $nilaiKPI->keterangan : '';
            $indikator->diverifikasi = $nilaiKPI ? $nilaiKPI->diverifikasi : false;
            $indikator->nilai_id = $nilaiKPI ? $nilaiKPI->id : null;
        }

        return view('realisasi.index', compact('indikators', 'tahun', 'bulan', 'periodeTipe'));
    }

    /**
     * Menampilkan form input realisasi
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $indikatorId = $request->indikator_id;
        $tahun = $request->tahun ?? Carbon::now()->year;
        $bulan = $request->bulan ?? Carbon::now()->month;
        $periodeTipe = $request->periode_tipe ?? 'bulanan';
        $minggu = $request->minggu;

        $indikator = Indikator::with(['pilar', 'bidang'])->findOrFail($indikatorId);

        // Cek apakah user berhak untuk input indikator ini
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $indikator->bidang_id) {
                return redirect()->route('realisasi.index')->with('error', 'Anda tidak memiliki akses untuk indikator ini.');
            }
        }

        // Cek apakah sudah ada nilai
        $nilaiKPI = NilaiKPI::where('indikator_id', $indikatorId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('periode_tipe', $periodeTipe);

        if ($periodeTipe == 'mingguan' && $minggu) {
            $nilaiKPI->where('minggu', $minggu);
        } else {
            $nilaiKPI->whereNull('minggu');
        }

        $nilai = $nilaiKPI->first();

        return view('realisasi.create', compact('indikator', 'tahun', 'bulan', 'periodeTipe', 'minggu', 'nilai'));
    }

    /**
     * Menyimpan realisasi baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $request->validate([
            'indikator_id' => 'required|exists:indikators,id',
            'tahun' => 'required|integer|min:2020',
            'bulan' => 'required|integer|min:1|max:12',
            'nilai' => 'required|numeric|min:0',
            'periode_tipe' => 'required|in:bulanan,mingguan',
            'minggu' => 'nullable|integer|min:1|max:5',
            'keterangan' => 'nullable|string',
        ]);

        $indikator = Indikator::findOrFail($request->indikator_id);

        // Cek hak akses
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $indikator->bidang_id) {
                return redirect()->route('realisasi.index')->with('error', 'Anda tidak memiliki akses untuk indikator ini.');
            }
        }

        // Hitung persentase pencapaian
        $persentase = min(100, ($request->nilai / $indikator->target) * 100);

        // Cek apakah sudah ada nilai untuk periode ini
        $existing = NilaiKPI::where('indikator_id', $request->indikator_id)
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->where('periode_tipe', $request->periode_tipe);

        if ($request->periode_tipe == 'mingguan' && $request->minggu) {
            $existing->where('minggu', $request->minggu);
        } else {
            $existing->whereNull('minggu');
        }

        $existingData = $existing->first();

        if ($existingData) {
            // Update nilai yang sudah ada
            $existingData->update([
                'nilai' => $request->nilai,
                'persentase' => $persentase,
                'keterangan' => $request->keterangan,
                'user_id' => $user->id,
                'diverifikasi' => false,
                'verifikasi_oleh' => null,
                'verifikasi_pada' => null,
            ]);

            // Catat log aktivitas
            AktivitasLog::log(
                $user,
                'update',
                'Mengupdate nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama,
                [
                    'indikator_id' => $indikator->id,
                    'nilai_lama' => $existingData->getOriginal('nilai'),
                    'nilai_baru' => $request->nilai,
                    'tahun' => $request->tahun,
                    'bulan' => $request->bulan,
                ],
                $request->ip(),
                $request->userAgent()
            );

            // Kirim notifikasi ke Master Admin
            Notifikasi::kirimKeMasterAdmin(
                'Perbarui Nilai KPI',
                'Nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama . ' telah diperbarui oleh ' . $user->name,
                'warning',
                route('kpi.verifikasi')
            );

            return redirect()->route('realisasi.index', [
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'periode_tipe' => $request->periode_tipe
            ])->with('success', 'Nilai KPI berhasil diperbarui.');
        } else {
            // Buat nilai baru
            $nilaiKPI = NilaiKPI::create([
                'indikator_id' => $request->indikator_id,
                'user_id' => $user->id,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'minggu' => $request->minggu,
                'periode_tipe' => $request->periode_tipe,
                'nilai' => $request->nilai,
                'persentase' => $persentase,
                'keterangan' => $request->keterangan,
                'diverifikasi' => false,
            ]);

            // Catat log aktivitas
            AktivitasLog::log(
                $user,
                'create',
                'Menambahkan nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama,
                [
                    'indikator_id' => $indikator->id,
                    'nilai' => $request->nilai,
                    'tahun' => $request->tahun,
                    'bulan' => $request->bulan,
                ],
                $request->ip(),
                $request->userAgent()
            );

            // Kirim notifikasi ke Master Admin
            Notifikasi::kirimKeMasterAdmin(
                'Tambah Nilai KPI Baru',
                'Nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama . ' telah ditambahkan oleh ' . $user->name,
                'info',
                route('kpi.verifikasi')
            );

            return redirect()->route('realisasi.index', [
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'periode_tipe' => $request->periode_tipe
            ])->with('success', 'Nilai KPI berhasil disimpan.');
        }
    }

    /**
     * Menampilkan detail realisasi
     */
    public function show($id)
    {
        $nilaiKPI = NilaiKPI::with(['indikator.pilar', 'indikator.bidang', 'user', 'verifikator'])
            ->findOrFail($id);

        $user = Auth::user();

        // Cek akses
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $nilaiKPI->indikator->bidang_id) {
                return redirect()->route('realisasi.index')->with('error', 'Anda tidak memiliki akses untuk melihat data ini.');
            }
        }

        return view('realisasi.show', compact('nilaiKPI'));
    }

    /**
     * Menampilkan form edit realisasi
     */
    public function edit($id)
    {
        $nilaiKPI = NilaiKPI::with(['indikator.pilar', 'indikator.bidang'])
            ->findOrFail($id);

        $user = Auth::user();

        // Cek akses
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $nilaiKPI->indikator->bidang_id) {
                return redirect()->route('realisasi.index')->with('error', 'Anda tidak memiliki akses untuk mengubah data ini.');
            }
        }

        // Jika sudah diverifikasi, tidak boleh diedit
        if ($nilaiKPI->diverifikasi) {
            return redirect()->route('realisasi.show', $nilaiKPI->id)
                ->with('error', 'Nilai KPI yang sudah diverifikasi tidak dapat diubah.');
        }

        return view('realisasi.edit', compact('nilaiKPI'));
    }

    /**
     * Mengupdate realisasi
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $nilaiKPI = NilaiKPI::with('indikator')->findOrFail($id);

        // Cek akses
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $nilaiKPI->indikator->bidang_id) {
                return redirect()->route('realisasi.index')->with('error', 'Anda tidak memiliki akses untuk mengubah data ini.');
            }
        }

        // Jika sudah diverifikasi, tidak boleh diedit
        if ($nilaiKPI->diverifikasi) {
            return redirect()->route('realisasi.show', $nilaiKPI->id)
                ->with('error', 'Nilai KPI yang sudah diverifikasi tidak dapat diubah.');
        }

        $request->validate([
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $indikator = $nilaiKPI->indikator;
        $persentase = min(100, ($request->nilai / $indikator->target) * 100);

        // Simpan nilai lama untuk log
        $nilaiLama = $nilaiKPI->nilai;

        // Update nilai
        $nilaiKPI->update([
            'nilai' => $request->nilai,
            'persentase' => $persentase,
            'keterangan' => $request->keterangan,
            'user_id' => $user->id,
            'diverifikasi' => false,
            'verifikasi_oleh' => null,
            'verifikasi_pada' => null,
        ]);

        // Catat log aktivitas
        AktivitasLog::log(
            $user,
            'update',
            'Mengupdate nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama,
            [
                'indikator_id' => $indikator->id,
                'nilai_lama' => $nilaiLama,
                'nilai_baru' => $request->nilai,
                'tahun' => $nilaiKPI->tahun,
                'bulan' => $nilaiKPI->bulan,
            ],
            $request->ip(),
            $request->userAgent()
        );

        // Kirim notifikasi ke Master Admin
        Notifikasi::kirimKeMasterAdmin(
            'Perbarui Nilai KPI',
            'Nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama . ' telah diperbarui oleh ' . $user->name,
            'warning',
            route('kpi.verifikasi')
        );

        return redirect()->route('realisasi.index', [
            'tahun' => $nilaiKPI->tahun,
            'bulan' => $nilaiKPI->bulan,
            'periode_tipe' => $nilaiKPI->periode_tipe
        ])->with('success', 'Nilai KPI berhasil diperbarui.');
    }

    /**
     * Menghapus realisasi
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $nilaiKPI = NilaiKPI::with('indikator')->findOrFail($id);

        // Cek akses
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $nilaiKPI->indikator->bidang_id) {
                return redirect()->route('realisasi.index')->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
            }
        }

        // Jika sudah diverifikasi, tidak boleh dihapus
        if ($nilaiKPI->diverifikasi) {
            return redirect()->route('realisasi.show', $nilaiKPI->id)
                ->with('error', 'Nilai KPI yang sudah diverifikasi tidak dapat dihapus.');
        }

        $indikator = $nilaiKPI->indikator;
        $tahun = $nilaiKPI->tahun;
        $bulan = $nilaiKPI->bulan;
        $periodeTipe = $nilaiKPI->periode_tipe;

        // Catat log aktivitas sebelum menghapus
        AktivitasLog::log(
            $user,
            'delete',
            'Menghapus nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama,
            [
                'indikator_id' => $indikator->id,
                'nilai' => $nilaiKPI->nilai,
                'tahun' => $tahun,
                'bulan' => $bulan,
            ],
            request()->ip(),
            request()->userAgent()
        );

        // Hapus nilai
        $nilaiKPI->delete();

        return redirect()->route('realisasi.index', [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'periode_tipe' => $periodeTipe
        ])->with('success', 'Nilai KPI berhasil dihapus.');
    }
}
