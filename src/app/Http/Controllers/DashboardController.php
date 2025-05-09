<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pilar;
use App\Models\Indikator;
use App\Models\NilaiKPI;
use App\Models\Bidang;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard untuk master_admin (asisten_manager)
     */
    public function master()
    {
        // Pastikan hanya asisten_manager yang bisa mengakses
        if (Auth::user()->role !== 'asisten_manager') {
            return redirect()->route('dashboard');
        }

        // Dapatkan tahun dan bulan saat ini atau dari parameter request
        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));
        $periodeTipe = request('periode_tipe', 'bulanan');

        // Dapatkan data pilar dari database
        $pilars = Pilar::with('indikators')->orderBy('urutan')->get();

        // Hitung nilai untuk setiap pilar dan indikator
        $data = [
            'nko' => 0,
            'pilar' => []
        ];

        $totalNilai = 0;
        $jumlahPilar = $pilars->count();

        foreach ($pilars as $pilar) {
            // Inisialisasi data pilar
            $pilarData = [
                'nama' => $pilar->nama,
                'nilai' => 0,
                'indikator' => []
            ];

            // Hitung nilai rata-rata indikator dalam pilar
            $totalNilaiIndikator = 0;
            $jumlahIndikator = 0;

            foreach ($pilar->indikators as $indikator) {
                // Dapatkan nilai indikator untuk bulan yang dipilih
                $nilaiKPI = NilaiKPI::where('indikator_id', $indikator->id)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('periode_tipe', $periodeTipe)
                    ->first();

                $nilai = $nilaiKPI ? $nilaiKPI->persentase : 0;

                // Tambahkan ke total untuk kalkulasi rata-rata
                $totalNilaiIndikator += $nilai;
                $jumlahIndikator++;

                // Tambahkan ke array indikator
                $pilarData['indikator'][] = [
                    'nama' => $indikator->nama,
                    'nilai' => $nilai
                ];
            }

            // Hitung nilai rata-rata pilar
            $pilarData['nilai'] = $jumlahIndikator > 0 ? round($totalNilaiIndikator / $jumlahIndikator) : 0;

            // Tambahkan ke total untuk NKO
            $totalNilai += $pilarData['nilai'];

            // Tambahkan ke array pilar
            $data['pilar'][] = $pilarData;
        }

        // Hitung NKO total (rata-rata semua pilar)
        $data['nko'] = $jumlahPilar > 0 ? round($totalNilai / $jumlahPilar, 2) : 0;

        // Render dengan template dashboard/master
        return view('dashboard.master', compact('data', 'tahun', 'bulan', 'periodeTipe'));
    }

    /**
     * Dashboard untuk admin berdasarkan peran mereka
     */
    public function admin()
    {
        $user = Auth::user();

        // Dapatkan tahun dan bulan saat ini atau dari parameter request
        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));
        $periodeTipe = request('periode_tipe', 'bulanan');

        // Dapatkan bidang yang dikelola oleh PIC ini
        $bidang = Bidang::where('role_pic', $user->role)->first();

        if (!$bidang) {
            return redirect()->route('dashboard')->with('error', 'Bidang tidak ditemukan untuk PIC ini.');
        }

        // Dapatkan indikator yang dikelola oleh PIC ini
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

        // Dapatkan data historis untuk perbandingan bulan-bulan sebelumnya
        $historiData = $this->getHistoriData($bidang->id, $tahun);

        // Pastikan $indikators adalah collection (seharusnya sudah, karena hasil dari query Eloquent)
        // tapi lebih baik memastikan untuk keamanan
        if (!is_a($indikators, 'Illuminate\Support\Collection')) {
            $indikators = collect($indikators);
        }

        return view('dashboard.admin', compact('bidang', 'indikators', 'rataRata', 'historiData', 'tahun', 'bulan', 'periodeTipe'));
    }

    /**
     * Dashboard untuk user (karyawan)
     */
    public function user()
    {
        // Pastikan hanya karyawan yang bisa mengakses
        if (Auth::user()->role !== 'karyawan') {
            return redirect()->route('dashboard');
        }

        // Dapatkan tahun dan bulan saat ini atau dari parameter request
        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));

        // Dapatkan data ringkasan dari seluruh bidang
        $bidangs = Bidang::all();
        $bidangData = collect([]);

        foreach ($bidangs as $bidang) {
            $indikators = Indikator::where('bidang_id', $bidang->id)->where('aktif', true)->get();

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

            $bidangData->push([
                'nama' => $bidang->nama,
                'nilai' => $rataRata
            ]);
        }

        return view('dashboard.user', compact('bidangData', 'tahun', 'bulan'));
    }

    /**
     * Dapatkan data historis untuk perbandingan bulan-bulan sebelumnya
     */
    private function getHistoriData($bidangId, $tahun)
    {
        $data = [];
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        // Dapatkan semua indikator untuk bidang ini
        $indikatorIds = Indikator::where('bidang_id', $bidangId)->pluck('id');

        // Data untuk 12 bulan
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $nilaiKPIs = NilaiKPI::whereIn('indikator_id', $indikatorIds)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('periode_tipe', 'bulanan')
                ->get();

            $totalNilai = 0;
            foreach ($nilaiKPIs as $nilaiKPI) {
                $totalNilai += $nilaiKPI->persentase;
            }

            $rataRata = $nilaiKPIs->count() > 0 ? round($totalNilai / $nilaiKPIs->count(), 2) : 0;

            $data[] = [
                'bulan' => $namaBulan[$bulan],
                'nilai' => $rataRata
            ];
        }

        return $data;
    }

    /**
     * Halaman utama dashboard yang mengarahkan sesuai role
     */
    public function index()
    {
        // Mendapatkan role user
        $role = Auth::user()->role;

        // Redirect berdasarkan role
        switch ($role) {
            case 'asisten_manager':
                return $this->master();

            case 'pic_keuangan':
            case 'pic_manajemen_risiko':
            case 'pic_sekretaris_perusahaan':
            case 'pic_perencanaan_operasi':
            case 'pic_pengembangan_bisnis':
            case 'pic_human_capital':
            case 'pic_k3l':
            case 'pic_perencanaan_korporat':
            case 'pic_hukum':
                return $this->admin();

            case 'karyawan':
                return $this->user();

            default:
                return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }
    }
}
