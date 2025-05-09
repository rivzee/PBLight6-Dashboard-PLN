<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\Indikator;
use App\Models\NilaiKPI;
use App\Models\Bidang;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KPIController extends Controller
{
    /**
     * Validasi akses berdasarkan role
     */
    private function validateAccessForAdmin($bidangId)
    {
        $user = Auth::user();

        // Jika master admin, selalu diizinkan
        if ($user->isMasterAdmin()) {
            return true;
        }

        // Jika admin, validasi akses ke bidangnya
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $bidangId) {
                return false;
            }
            return true;
        }

        // Karyawan biasa tidak boleh mengakses
        return false;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $periodeTipe = $request->periode_tipe ?? 'bulanan';

        // Jika master admin, dapatkan semua pilar dan indikator
        if ($user->isMasterAdmin()) {
            $pilars = Pilar::with(['indikators' => function($query) {
                $query->where('aktif', true)->orderBy('kode');
            }])->orderBy('urutan')->get();

            // Dapatkan nilai untuk indikator
            foreach ($pilars as $pilar) {
                foreach ($pilar->indikators as $indikator) {
                    $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('periode_tipe', $periodeTipe)
                        ->first();

                    $indikator->nilai_persentase = $nilaiKPI ? $nilaiKPI->persentase : 0;
                    $indikator->nilai_absolut = $nilaiKPI ? $nilaiKPI->nilai : 0;
                    $indikator->diverifikasi = $nilaiKPI ? $nilaiKPI->diverifikasi : false;
                }
            }

            return view('kpi.index', compact('pilars', 'tahun', 'bulan', 'periodeTipe'));
        }

        // Jika admin, dapatkan indikator untuk bidangnya
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();

            if (!$bidang) {
                return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
            }

            $indikators = Indikator::where('bidang_id', $bidang->id)
                ->where('aktif', true)
                ->orderBy('kode')
                ->get();

            // Dapatkan nilai untuk indikator
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

            return view('kpi.admin_index', compact('bidang', 'indikators', 'tahun', 'bulan', 'periodeTipe'));
        }

        // Jika karyawan, dapatkan ringkasan
        $bidangs = Bidang::orderBy('nama')->get();
        $bidangData = [];

        foreach ($bidangs as $bidang) {
            $indikators = Indikator::where('bidang_id', $bidang->id)
                ->where('aktif', true)
                ->get();

            $totalNilai = 0;
            foreach ($indikators as $indikator) {
                $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', $periodeTipe)
                    ->first();

                $totalNilai += $nilaiKPI ? $nilaiKPI->persentase : 0;
            }

            $rataRata = $indikators->count() > 0 ? round($totalNilai / $indikators->count(), 2) : 0;

            $bidangData[] = [
                'nama' => $bidang->nama,
                'nilai' => $rataRata,
            ];
        }

        return view('kpi.user_index', compact('bidangData', 'tahun', 'bulan', 'periodeTipe'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Hanya admin dan master admin yang boleh create
        if (!$user->isAdmin() && !$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        $periodeTipe = $request->periode_tipe ?? 'bulanan';
        $indikatorId = $request->indikator_id;

        // Jika ada indikator yang dipilih
        if ($indikatorId) {
            $indikator = Indikator::findOrFail($indikatorId);

            // Validasi akses admin ke bidang
            if ($user->isAdmin()) {
                $bidang = $user->getBidang();
                if (!$bidang || $bidang->id != $indikator->bidang_id) {
                    return redirect()->route('kpi.index')->with('error', 'Anda tidak memiliki akses untuk mengelola indikator ini.');
                }
            }

            return view('kpi.create', compact('indikator', 'tahun', 'bulan', 'periodeTipe'));
        }

        // Jika master admin
        if ($user->isMasterAdmin()) {
            $indikators = Indikator::where('aktif', true)->orderBy('kode')->get();
            return view('kpi.select_indikator', compact('indikators', 'tahun', 'bulan', 'periodeTipe'));
        }

        // Jika admin
        $bidang = $user->getBidang();
        if (!$bidang) {
            return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
        }

        $indikators = Indikator::where('bidang_id', $bidang->id)
            ->where('aktif', true)
            ->orderBy('kode')
            ->get();

        return view('kpi.select_indikator', compact('indikators', 'tahun', 'bulan', 'periodeTipe'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Hanya admin dan master admin yang boleh store
        if (!$user->isAdmin() && !$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $request->validate([
            'indikator_id' => 'required|exists:indikators,id',
            'nilai' => 'required|numeric|min:0',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bulan' => 'required|integer|min:1|max:12',
            'periode_tipe' => 'required|in:bulanan,mingguan',
            'minggu' => 'nullable|integer|min:1|max:5',
            'keterangan' => 'nullable|string',
        ]);

        $indikator = Indikator::find($request->indikator_id);

        // Validasi akses admin ke bidang
        if ($user->isAdmin()) {
            $bidang = $user->getBidang();
            if (!$bidang || $bidang->id != $indikator->bidang_id) {
                return redirect()->route('kpi.index')->with('error', 'Anda tidak memiliki akses untuk mengelola indikator ini.');
            }
        }

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

            // Log aktivitas
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

            // Notifikasi ke asisten manager
            Notifikasi::kirimKeMasterAdmin(
                'Update Nilai KPI',
                'Nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama . ' telah diperbarui oleh ' . $user->name,
                'warning',
                route('kpi.verifikasi')
            );

            return redirect()->route('kpi.index', [
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

            // Log aktivitas
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

            // Notifikasi ke asisten manager
            Notifikasi::kirimKeMasterAdmin(
                'Tambah Nilai KPI Baru',
                'Nilai KPI ' . $indikator->kode . ' - ' . $indikator->nama . ' telah ditambahkan oleh ' . $user->name,
                'info',
                route('kpi.verifikasi')
            );

            return redirect()->route('kpi.index', [
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'periode_tipe' => $request->periode_tipe
            ])->with('success', 'Nilai KPI berhasil disimpan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $indikator = Indikator::findOrFail($id);
        $tahun = request('tahun', date('Y'));

        // Dapatkan data historis
        $nilaiKPIs = NilaiKPI::where('indikator_id', $id)
            ->where('tahun', $tahun)
            ->orderBy('bulan')
            ->orderBy('minggu')
            ->get();

        // Kelompokkan berdasarkan periode
        $bulananData = $nilaiKPIs->where('periode_tipe', 'bulanan')->keyBy('bulan');
        $mingguanData = $nilaiKPIs->where('periode_tipe', 'mingguan')->groupBy('bulan');

        return view('kpi.show', compact('indikator', 'bulananData', 'mingguanData', 'tahun'));
    }

    /**
     * Menampilkan form untuk verifikasi KPI (untuk master admin)
     */
    public function verifikasi()
    {
        $user = Auth::user();

        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));
        $bidangId = request('bidang_id');

        $query = NilaiKPI::with(['indikator.bidang', 'indikator.pilar', 'user'])
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('diverifikasi', false);

        if ($bidangId) {
            $query->whereHas('indikator', function($q) use ($bidangId) {
                $q->where('bidang_id', $bidangId);
            });
        }

        $nilaiKPIs = $query->get();
        $bidangs = Bidang::all();

        return view('kpi.verifikasi', compact('nilaiKPIs', 'bidangs', 'tahun', 'bulan', 'bidangId'));
    }

    /**
     * Proses verifikasi KPI
     */
    public function prosesVerifikasi(Request $request)
    {
        $user = Auth::user();

        if (!$user->isMasterAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        $request->validate([
            'nilai_ids' => 'required|array',
            'nilai_ids.*' => 'exists:nilai_kpi,id',
        ]);

        $nilaiKPIs = NilaiKPI::with('indikator')->whereIn('id', $request->nilai_ids)->get();

        foreach ($nilaiKPIs as $nilaiKPI) {
            $nilaiKPI->update([
                'diverifikasi' => true,
                'verifikasi_oleh' => $user->id,
                'verifikasi_pada' => Carbon::now(),
            ]);

            // Log aktivitas
            AktivitasLog::log(
                $user,
                'verify',
                'Memverifikasi nilai KPI ' . $nilaiKPI->indikator->kode . ' - ' . $nilaiKPI->indikator->nama,
                [
                    'indikator_id' => $nilaiKPI->indikator_id,
                    'nilai' => $nilaiKPI->nilai,
                    'tahun' => $nilaiKPI->tahun,
                    'bulan' => $nilaiKPI->bulan,
                ],
                $request->ip(),
                $request->userAgent()
            );

            // Kirim notifikasi ke user yang input
            Notifikasi::create([
                'user_id' => $nilaiKPI->user_id,
                'judul' => 'KPI Terverifikasi',
                'pesan' => 'Nilai KPI ' . $nilaiKPI->indikator->kode . ' - ' . $nilaiKPI->indikator->nama . ' telah diverifikasi oleh ' . $user->name,
                'jenis' => 'success',
                'dibaca' => false,
                'url' => route('kpi.show', $nilaiKPI->indikator_id),
            ]);
        }

        return redirect()->back()->with('success', count($nilaiKPIs) . ' Nilai KPI berhasil diverifikasi.');
    }

    /**
     * Menampilkan history KPI
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->tahun ?? date('Y');

        if ($user->isMasterAdmin()) {
            // Master admin bisa melihat semua data
            $pilars = Pilar::with(['indikators' => function($query) {
                $query->where('aktif', true)->orderBy('kode');
            }])->orderBy('urutan')->get();

            foreach ($pilars as $pilar) {
                foreach ($pilar->indikators as $indikator) {
                    $indikator->historiData = $this->getIndikatorHistoriData($indikator->id, $tahun);
                }
            }

            return view('kpi.history', compact('pilars', 'tahun'));
        } elseif ($user->isAdmin()) {
            // Admin hanya bisa melihat data bidangnya
            $bidang = $user->getBidang();

            if (!$bidang) {
                return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
            }

            $indikators = Indikator::where('bidang_id', $bidang->id)
                ->where('aktif', true)
                ->orderBy('kode')
                ->get();

            foreach ($indikators as $indikator) {
                $indikator->historiData = $this->getIndikatorHistoriData($indikator->id, $tahun);
            }

            return view('kpi.history_admin', compact('bidang', 'indikators', 'tahun'));
        } else {
            // Karyawan hanya bisa melihat ringkasan
            $bidangs = Bidang::all();
            $bidangHistoriData = [];

            foreach ($bidangs as $bidang) {
                $bidangHistoriData[$bidang->id] = [
                    'nama' => $bidang->nama,
                    'data' => $this->getBidangHistoriData($bidang->id, $tahun),
                ];
            }

            return view('kpi.history_user', compact('bidangHistoriData', 'tahun'));
        }
    }

    /**
     * Helper method untuk mendapatkan data historis indikator
     */
    private function getIndikatorHistoriData($indikatorId, $tahun)
    {
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $nilaiKPI = NilaiKPI::where('indikator_id', $indikatorId)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', 'bulanan')
                ->first();

            $data[] = [
                'bulan' => $namaBulan[$bulan],
                'nilai' => $nilaiKPI ? $nilaiKPI->persentase : 0,
                'diverifikasi' => $nilaiKPI ? $nilaiKPI->diverifikasi : false,
            ];
        }

        return $data;
    }

    /**
     * Helper method untuk mendapatkan data historis bidang
     */
    private function getBidangHistoriData($bidangId, $tahun)
    {
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $indikators = Indikator::where('bidang_id', $bidangId)
                ->where('aktif', true)
                ->get();

            $totalNilai = 0;
            foreach ($indikators as $indikator) {
                $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', 'bulanan')
                    ->first();

                $totalNilai += $nilaiKPI ? $nilaiKPI->persentase : 0;
            }

            $rataRata = $indikators->count() > 0 ? round($totalNilai / $indikators->count(), 2) : 0;

            $data[] = [
                'bulan' => $namaBulan[$bulan],
                'nilai' => $rataRata,
            ];
        }

        return $data;
    }

    /**
     * Menampilkan laporan KPI
     */
    public function laporan(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');

        if ($user->isMasterAdmin()) {
            // Master admin bisa melihat semua data
            $pilars = Pilar::with(['indikators' => function($query) {
                $query->where('aktif', true)->orderBy('kode');
            }])->orderBy('urutan')->get();

            foreach ($pilars as $pilar) {
                foreach ($pilar->indikators as $indikator) {
                    $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('periode_tipe', 'bulanan')
                        ->first();

                    $indikator->nilai_persentase = $nilaiKPI ? $nilaiKPI->persentase : 0;
                    $indikator->nilai_absolut = $nilaiKPI ? $nilaiKPI->nilai : 0;
                    $indikator->diverifikasi = $nilaiKPI ? $nilaiKPI->diverifikasi : false;
                }
            }

            return view('kpi.laporan', compact('pilars', 'tahun', 'bulan'));
        } elseif ($user->isAdmin()) {
            // Admin hanya bisa melihat data bidangnya
            $bidang = $user->getBidang();

            if (!$bidang) {
                return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
            }

            $indikators = Indikator::where('bidang_id', $bidang->id)
                ->where('aktif', true)
                ->orderBy('kode')
                ->get();

            foreach ($indikators as $indikator) {
                $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', 'bulanan')
                    ->first();

                $indikator->nilai_persentase = $nilaiKPI ? $nilaiKPI->persentase : 0;
                $indikator->nilai_absolut = $nilaiKPI ? $nilaiKPI->nilai : 0;
                $indikator->diverifikasi = $nilaiKPI ? $nilaiKPI->diverifikasi : false;
            }

            return view('kpi.laporan_admin', compact('bidang', 'indikators', 'tahun', 'bulan'));
        } else {
            // Karyawan hanya bisa melihat ringkasan
            $bidangs = Bidang::all();
            $bidangData = [];

            foreach ($bidangs as $bidang) {
                $indikators = Indikator::where('bidang_id', $bidang->id)
                    ->where('aktif', true)
                    ->get();

                $totalNilai = 0;
                foreach ($indikators as $indikator) {
                    $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->where('periode_tipe', 'bulanan')
                        ->first();

                    $totalNilai += $nilaiKPI ? $nilaiKPI->persentase : 0;
                }

                $rataRata = $indikators->count() > 0 ? round($totalNilai / $indikators->count(), 2) : 0;

                $bidangData[] = [
                    'nama' => $bidang->nama,
                    'nilai' => $rataRata,
                ];
            }

            return view('kpi.laporan_user', compact('bidangData', 'tahun', 'bulan'));
        }
    }
}
