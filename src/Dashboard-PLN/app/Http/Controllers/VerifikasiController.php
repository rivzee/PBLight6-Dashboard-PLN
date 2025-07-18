<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Realisasi;
use App\Models\Bidang;
use App\Models\AktivitasLog;
use App\Models\TahunPenilaian;
use App\Models\TargetKPI;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VerifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'asisten_manager') {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $bidangId = $request->bidang_id;

        $query = Realisasi::with(['indikator.bidang', 'indikator.pilar', 'user'])
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('diverifikasi', false)
            ->orderBy('created_at', 'desc');

        if ($bidangId) {
            $query->whereHas('indikator', function ($q) use ($bidangId) {
                $q->where('bidang_id', $bidangId);
            });
        }

        $realisasis = $query->paginate(20);
        $bidangs = Bidang::orderBy('nama')->get();

        $tahunPenilaian = TahunPenilaian::where('tahun', $tahun)
            ->where('is_aktif', true)
            ->first();

        $isPeriodeLocked = $tahunPenilaian ? $tahunPenilaian->is_locked : false;

        return view('verifikasi.index', compact('realisasis', 'bidangs', 'tahun', 'bulan', 'bidangId', 'isPeriodeLocked'));
    }

    public function show($id)
    {
        $realisasi = Realisasi::with(['indikator.bidang', 'indikator.pilar', 'user'])->findOrFail($id);

        $tahunPenilaian = TahunPenilaian::where('tahun', $realisasi->tahun)
            ->where('is_aktif', true)
            ->first();

        $isPeriodeLocked = $tahunPenilaian ? $tahunPenilaian->is_locked : false;

        // Ambil target KPI untuk tahun ini
        $targetKPI = TargetKPI::where('indikator_id', $realisasi->indikator_id)
            ->whereHas('tahunPenilaian', function($q) use ($realisasi) {
                $q->where('tahun', $realisasi->tahun);
            })->first();

        return view('verifikasi.show', compact('realisasi', 'isPeriodeLocked', 'targetKPI'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $realisasi = Realisasi::with('indikator')->findOrFail($id);

        if ($realisasi->diverifikasi) {
            return redirect()->route('verifikasi.index')->with('info', 'Realisasi ini sudah diverifikasi sebelumnya.');
        }

        $tahunPenilaian = TahunPenilaian::where('tahun', $realisasi->tahun)
            ->where('is_aktif', true)
            ->first();

        if ($tahunPenilaian && $tahunPenilaian->is_locked) {
            return redirect()->route('verifikasi.index')
                ->with('error', 'Periode penilaian tahun ' . $realisasi->tahun . ' telah dikunci. Verifikasi tidak dapat dilakukan.');
        }

        $realisasi->update([
            'diverifikasi' => true,
            'verifikasi_oleh' => $user->id,
            'verifikasi_pada' => Carbon::now(),
        ]);

        AktivitasLog::log(
            $user,
            'verify',
            'Memverifikasi nilai KPI ' . $realisasi->indikator->kode . ' - ' . $realisasi->indikator->nama,
            '-', // deskripsi
            json_encode([
                'indikator_id' => $realisasi->indikator_id,
                'nilai' => $realisasi->nilai,
                'tahun' => $realisasi->tahun,
                'bulan' => $realisasi->bulan,
            ]),
            $request->ip(),
            $request->userAgent()
        );


        return redirect()->route('verifikasi.index')->with('success', 'Realisasi berhasil diverifikasi.');
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $realisasi = Realisasi::with('indikator')->findOrFail($id);

        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $tahunPenilaian = TahunPenilaian::where('tahun', $realisasi->tahun)
            ->where('is_aktif', true)
            ->first();

        if ($tahunPenilaian && $tahunPenilaian->is_locked) {
            return redirect()->route('verifikasi.index')
                ->with('error', 'Periode penilaian tahun ' . $realisasi->tahun . ' telah dikunci. Penolakan tidak dapat dilakukan.');
        }

        $indikatorId = $realisasi->indikator_id;
        $indikatorKode = $realisasi->indikator->kode;
        $indikatorNama = $realisasi->indikator->nama;
        $userId = $realisasi->user_id;
        $nilai = $realisasi->nilai;
        $tahun = $realisasi->tahun;
        $bulan = $realisasi->bulan;

        AktivitasLog::log(
            $user,
            'delete',
            'Menolak nilai KPI ' . $indikatorKode . ' - ' . $indikatorNama,
            json_encode([
                'indikator_id' => $indikatorId,
                'nilai' => $nilai,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'alasan' => $request->alasan_penolakan,
            ]),
            $request->ip(),
            $request->userAgent()
        );


        return redirect()->route('verifikasi.index')->with('success', 'Realisasi berhasil ditolak.');
    }

    public function verifikasiMassal(Request $request)
    {
        $request->validate([
            'nilai_ids' => 'required|array',
            'nilai_ids.*' => 'exists:realisasis,id',
        ]);

        $user = Auth::user();
        $realisasis = Realisasi::with('indikator')->whereIn('id', $request->nilai_ids)->get();

        $tahunList = $realisasis->pluck('tahun')->unique();
        $lockedPeriods = TahunPenilaian::whereIn('tahun', $tahunList)
            ->where('is_locked', true)
            ->get();

        if ($lockedPeriods->count() > 0) {
            $lockedYears = $lockedPeriods->pluck('tahun')->implode(', ');
            return redirect()->route('verifikasi.index')
                ->with('error', 'Periode penilaian tahun ' . $lockedYears . ' telah dikunci. Verifikasi massal tidak dapat dilakukan.');
        }

        $count = 0;

        foreach ($realisasis as $realisasi) {
            if ($realisasi->diverifikasi) continue;

            $realisasi->update([
                'diverifikasi' => true,
                'verifikasi_oleh' => $user->id,
                'verifikasi_pada' => Carbon::now(),
            ]);

            AktivitasLog::log(
                $user,
                'verify',
                'Memverifikasi nilai KPI ' . $realisasi->indikator->kode . ' - ' . $realisasi->indikator->nama,
                json_encode([
                    'indikator_id' => $realisasi->indikator_id,
                    'nilai' => $realisasi->nilai,
                    'tahun' => $realisasi->tahun,
                    'bulan' => $realisasi->bulan,
                ]),
                $request->ip(),
                $request->userAgent()
            );


            $count++;
        }

        return redirect()->route('verifikasi.index')
            ->with('success', 'Berhasil memverifikasi ' . $count . ' realisasi.');
    }
}
