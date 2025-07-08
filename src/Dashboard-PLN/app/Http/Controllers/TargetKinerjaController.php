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
            'target_tahunan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
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

        $targetTahunan = $request->target_tahunan;

        // Ambil target bulanan dari input user (dalam format kumulatif) atau bagi rata jika tidak ada
        $targetBulananInput = $request->target_bulanan ?? [];
        $targetBulanan = [];

        // Jika ada input target bulanan dari user, konversi dari kumulatif ke bulanan
        if (!empty($targetBulananInput) && array_sum($targetBulananInput) > 0) {
            // Konversi dari kumulatif ke bulanan (nilai per bulan)
            $targetBulanan[0] = round(floatval($targetBulananInput[0] ?? 0), 2);
            for ($i = 1; $i < 12; $i++) {
                $nilaiKumulatif = floatval($targetBulananInput[$i] ?? 0);
                $nilaiKumulatifSebelum = floatval($targetBulananInput[$i-1] ?? 0);
                $targetBulanan[$i] = round($nilaiKumulatif - $nilaiKumulatifSebelum, 2);
            }
        } else {
            // Jika tidak ada input, bagi rata target tahunan ke 12 bulan
            $perBulan = $targetTahunan / 12;
            for ($i = 0; $i < 12; $i++) {
                $targetBulanan[$i] = round($perBulan, 2);
            }
        }

        TargetKPI::create([
            'indikator_id' => $request->indikator_id,
            'tahun_penilaian_id' => $request->tahun_penilaian_id,
            'user_id' => $user->id,
            'target_tahunan' => $targetTahunan,
            'target_bulanan' => $targetBulanan,
            'keterangan' => $request->keterangan,
            'disetujui' => true, // Langsung disetujui tanpa perlu approval
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);

        $indikator->update(['target' => $targetTahunan]);

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
            'target_tahunan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $targetTahunan = $request->target_tahunan;

        // Ambil target bulanan dari input user (dalam format kumulatif) atau bagi rata jika tidak ada
        $targetBulananInput = $request->target_bulanan ?? [];
        $targetBulanan = [];

        // Jika ada input target bulanan dari user, konversi dari kumulatif ke bulanan
        if (!empty($targetBulananInput) && array_sum($targetBulananInput) > 0) {
            // Konversi dari kumulatif ke bulanan (nilai per bulan)
            $targetBulanan[0] = round(floatval($targetBulananInput[0] ?? 0), 2);
            for ($i = 1; $i < 12; $i++) {
                $nilaiKumulatif = floatval($targetBulananInput[$i] ?? 0);
                $nilaiKumulatifSebelum = floatval($targetBulananInput[$i-1] ?? 0);
                $targetBulanan[$i] = round($nilaiKumulatif - $nilaiKumulatifSebelum, 2);
            }
        } else {
            // Jika tidak ada input, bagi rata target tahunan ke 12 bulan
            $perBulan = $targetTahunan / 12;
            for ($i = 0; $i < 12; $i++) {
                $targetBulanan[$i] = round($perBulan, 2);
            }
        }

        $target->update([
            'target_tahunan' => $targetTahunan,
            'target_bulanan' => $targetBulanan,
            'keterangan' => $request->keterangan,
            'user_id' => $user->id,
            'disetujui' => true, // Langsung disetujui tanpa perlu approval
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);

        $target->indikator->update(['target' => $targetTahunan]);

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
}
