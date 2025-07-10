<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Bidang;
use App\Models\Indikator;
use App\Models\TargetKPI;
use App\Models\TahunPenilaian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TargetKinerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isMasterAdmin() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk fitur ini.');
        }

        $tahunPenilaianId = $request->tahun_penilaian_id;
        $tahunPenilaian = $tahunPenilaianId
            ? TahunPenilaian::find($tahunPenilaianId)
            : TahunPenilaian::where('is_aktif', true)->first() ?? TahunPenilaian::orderBy('tahun', 'desc')->first();

        if (!$tahunPenilaian) {
            if ($user->isMasterAdmin()) {
                session()->flash('error', 'Tidak ada tahun penilaian. Silakan buat terlebih dahulu.');
                return view('targetKinerja.index', [
                    'pilars' => collect([]),
                    'tahunPenilaian' => null,
                    'totalIndikators' => 0
                ]);
            } else {
                session()->flash('error', 'Tidak ada tahun penilaian. Hubungi administrator.');
                return view('targetKinerja.index_admin', [
                    'indikators' => collect([]),
                    'bidang' => $user->getBidang(),
                    'tahunPenilaian' => null
                ]);
            }
        }

        if ($user->isMasterAdmin()) {
            $pilars = Pilar::with(['indikators' => function ($q) {
                $q->with('bidang')->orderBy('kode');
            }])->orderBy('urutan')->get();

            foreach ($pilars as $pilar) {
                foreach ($pilar->indikators as $indikator) {
                    $indikator->target_data = $indikator->getTarget($tahunPenilaian->id);
                }
            }

            $totalIndikators = $pilars->sum(fn($p) => $p->indikators->count());

            return view('targetKinerja.index', compact('pilars', 'tahunPenilaian', 'totalIndikators'));
        } else {
            $bidang = $user->getBidang();
            if (!$bidang) return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan.');

            $indikators = Indikator::where('bidang_id', $bidang->id)->orderBy('kode')->get();
            foreach ($indikators as $indikator) {
                $indikator->target_data = $indikator->getTarget($tahunPenilaian->id);
            }

            return view('targetKinerja.index_admin', compact('indikators', 'bidang', 'tahunPenilaian'));
        }
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->isMasterAdmin() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Tidak memiliki akses.');
        }

        $indikator = Indikator::findOrFail($request->indikator_id);
        $tahunPenilaian = TahunPenilaian::findOrFail($request->tahun_penilaian_id);

        if ($user->isAdmin() && $indikator->bidang_id !== $user->getBidang()->id) {
            return redirect()->route('targetKinerja.index')->with('error', 'Indikator tidak sesuai bidang.');
        }

        $existingTarget = TargetKPI::where('indikator_id', $indikator->id)
            ->where('tahun_penilaian_id', $tahunPenilaian->id)->first();

        if ($existingTarget) {
            return redirect()->route('targetKinerja.edit', $existingTarget->id)
                ->with('info', 'Target sudah ada. Silakan perbarui.');
        }

        return view('targetKinerja.create', compact('indikator', 'tahunPenilaian'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isMasterAdmin() && !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Tidak memiliki akses.');
        }

        $request->validate([
            'indikator_id' => 'required|exists:indikators,id',
            'tahun_penilaian_id' => 'required|exists:tahun_penilaians,id',
            'target_bulanan' => 'required|array|size:12',
            'target_bulanan.*' => 'required|numeric|min:0|max:10000000000',
        ]);

        $indikator = Indikator::findOrFail($request->indikator_id);

        if ($user->isAdmin() && $indikator->bidang_id !== $user->getBidang()->id) {
            return redirect()->route('targetKinerja.index')->with('error', 'Indikator tidak sesuai bidang.');
        }

        $existingTarget = TargetKPI::where('indikator_id', $request->indikator_id)
            ->where('tahun_penilaian_id', $request->tahun_penilaian_id)->first();

        if ($existingTarget) {
            return redirect()->route('targetKinerja.edit', $existingTarget->id)
                ->with('info', 'Target sudah ada.');
        }

        $targetBulanan = [];
        for ($i = 0; $i < 12; $i++) {
            $targetBulanan[$i] = round(floatval($request->target_bulanan[$i]), 3);
        }

        // Target tahunan diambil dari target bulan Desember (index 11)
        $targetTahunan = $targetBulanan[11];

        TargetKPI::create([
            'indikator_id' => $request->indikator_id,
            'tahun_penilaian_id' => $request->tahun_penilaian_id,
            'user_id' => $user->id,
            'target_tahunan' => $targetTahunan,
            'target_bulanan' => $targetBulanan,
            'disetujui' => true,
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);

        return redirect()->route('targetKinerja.index', ['tahun_penilaian_id' => $request->tahun_penilaian_id])
            ->with('success', 'Target berhasil disimpan.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $target = TargetKPI::with(['indikator.bidang', 'tahunPenilaian'])->findOrFail($id);

        if ($user->isAdmin() && $target->indikator->bidang_id !== $user->getBidang()->id) {
            return redirect()->route('targetKinerja.index')->with('error', 'Tidak memiliki akses.');
        }

        if ($target->disetujui && !$user->isMasterAdmin()) {
            return redirect()->route('targetKinerja.index', ['tahun_penilaian_id' => $target->tahun_penilaian_id])
                ->with('error', 'Target sudah disetujui, tidak bisa diubah.');
        }

        return view('targetKinerja.edit', [
            'target' => $target,
            'indikator' => $target->indikator,
            'tahunPenilaian' => $target->tahunPenilaian
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $target = TargetKPI::with('indikator')->findOrFail($id);

        if ($user->isAdmin() && $target->indikator->bidang_id !== $user->getBidang()->id) {
            return redirect()->route('targetKinerja.index')->with('error', 'Tidak memiliki akses.');
        }

        $request->validate([
            'target_bulanan' => 'required|array|size:12',
            'target_bulanan.*' => 'required|numeric|min:0|max:10000000000',
        ]);

        $targetBulanan = [];
        $totalTahunan = 0;
        for ($i = 0; $i < 12; $i++) {
            $nilai = round(floatval($request->target_bulanan[$i]), 3);
            $targetBulanan[$i] = $nilai;
            $totalTahunan += $nilai;
        }

        // Target tahunan adalah total dari semua bulan (Jan-Des)
        $targetTahunan = $targetBulanan[11]; // gunakan nilai bulan Desember sebagai target tahunan


        $target->update([
            'target_tahunan' => $targetTahunan,
            'target_bulanan' => $targetBulanan,
            'user_id' => $user->id,
            'disetujui' => true,
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);

        return redirect()->route('targetKinerja.index', ['tahun_penilaian_id' => $target->tahun_penilaian_id])
            ->with('success', 'Target berhasil diperbarui.');
    }

    public function approve($id)
    {
        $user = Auth::user();
        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Hanya Master Admin yang dapat menyetujui target.');
        }

        $target = TargetKPI::findOrFail($id);
        $target->update([
            'disetujui' => true,
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);

        return redirect()->route('targetKinerja.index', ['tahun_penilaian_id' => $target->tahun_penilaian_id])
            ->with('success', 'Target berhasil disetujui.');
    }

    public function unapprove($id)
    {
        $user = Auth::user();
        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Hanya Master Admin yang dapat membatalkan persetujuan.');
        }

        $target = TargetKPI::findOrFail($id);
        $target->update([
            'disetujui' => false,
            'disetujui_oleh' => null,
            'disetujui_pada' => null,
        ]);

        return redirect()->route('targetKinerja.index', ['tahun_penilaian_id' => $target->tahun_penilaian_id])
            ->with('success', 'Persetujuan target berhasil dibatalkan.');
    }

    public function verifikasi($id)
    {
        $target = TargetKPI::findOrFail($id);
        $target->update([
            'disetujui' => true,
            'verifikasi_oleh' => Auth::id(),
            'verifikasi_pada' => now(),
        ]);

        return redirect()->back()->with('success', 'Target berhasil diverifikasi.');
    }

    /**
     * Update weights for all indicators
     */
    public function updateWeights()
    {
        $user = Auth::user();
        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Hanya Master Admin yang dapat mengubah bobot indikator.');
        }

        $weights = [
            'A1' => 8, 'A2' => 8, 'A3' => 5, 'A4' => 9, 'A5' => 9, 'A6' => 10, 'A7' => 4, 'A8' => 4, 'A9' => 2,
            'B1' => 12, 'B2' => 6, 'B3' => 2, 'C1' => 10, 'D1' => 5, 'E1' => 2, 'E2' => 2, 'E3' => 2, 'E4' => 2, 'E5' => 2,
            'F1' => 4, 'F2' => 10,
        ];

        try {
            foreach ($weights as $kode => $bobot) {
                $indikator = Indikator::where('kode', $kode)->first();
                if ($indikator) {
                    $indikator->update(['bobot' => $bobot]);
                }
            }

            return redirect()->route('targetKinerja.index')
                ->with('success', 'Bobot indikator berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('targetKinerja.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui bobot indikator: ' . $e->getMessage());
        }
    }

    /**
     * Get target bulanan untuk bulan tertentu
     */
    public function getTargetBulanan($indikatorId, $tahunPenilaianId, $bulan)
    {
        $targetKPI = TargetKPI::where('indikator_id', $indikatorId)
            ->where('tahun_penilaian_id', $tahunPenilaianId)
            ->first();

        if (!$targetKPI || !$targetKPI->target_bulanan) {
            return 0;
        }

        $targetBulanan = $targetKPI->target_bulanan;
        $bulanIndex = $bulan - 1;

        return isset($targetBulanan[$bulanIndex]) ? $targetBulanan[$bulanIndex] : 0;
    }

    /**
     * Get all monthly targets untuk indikator dan tahun tertentu
     */
    public function getAllTargetBulanan($indikatorId, $tahunPenilaianId)
    {
        $targetKPI = TargetKPI::where('indikator_id', $indikatorId)
            ->where('tahun_penilaian_id', $tahunPenilaianId)
            ->first();

        if (!$targetKPI || !$targetKPI->target_bulanan) {
            return array_fill(0, 12, 0);
        }

        return $targetKPI->target_bulanan;
    }
}
