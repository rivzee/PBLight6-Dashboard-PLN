<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AktivitasLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    /**
     * Konstruktor - hanya Master Admin yang bisa mengakses
     */
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

    /**
     * Menampilkan daftar semua akun pengguna
     */
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('akun.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat akun baru
     */
    public function create()
    {
        // Array daftar role yang diizinkan
        $availableRoles = [
            'asisten_manager' => 'Asisten Manager',
            'pic_keuangan' => 'PIC Keuangan',
            'pic_manajemen_risiko' => 'PIC Manajemen Risiko',
            'pic_sekretaris_perusahaan' => 'PIC Sekretaris Perusahaan',
            'pic_perencanaan_operasi' => 'PIC Perencanaan Operasi',
            'pic_pengembangan_bisnis' => 'PIC Pengembangan Bisnis',
            'pic_human_capital' => 'PIC Human Capital',
            'pic_k3l' => 'PIC K3L',
            'pic_perencanaan_korporat' => 'PIC Perencanaan Korporat',
            'pic_hukum' => 'PIC Hukum',
            'karyawan' => 'Karyawan'
        ];

        return view('akun.create', compact('availableRoles'));
    }

    /**
     * Menyimpan akun baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:asisten_manager,pic_keuangan,pic_manajemen_risiko,pic_sekretaris_perusahaan,pic_perencanaan_operasi,pic_pengembangan_bisnis,pic_human_capital,pic_k3l,pic_perencanaan_korporat,pic_hukum,karyawan',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Catat log aktivitas
        AktivitasLog::log(
            Auth::user(),
            'create',
            'Membuat akun baru: ' . $user->name . ' (' . $user->role . ')',
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('akun.index')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Menampilkan detail akun pengguna
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('akun.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit akun
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Array daftar role yang diizinkan
        $availableRoles = [
            'asisten_manager' => 'Asisten Manager',
            'pic_keuangan' => 'PIC Keuangan',
            'pic_manajemen_risiko' => 'PIC Manajemen Risiko',
            'pic_sekretaris_perusahaan' => 'PIC Sekretaris Perusahaan',
            'pic_perencanaan_operasi' => 'PIC Perencanaan Operasi',
            'pic_pengembangan_bisnis' => 'PIC Pengembangan Bisnis',
            'pic_human_capital' => 'PIC Human Capital',
            'pic_k3l' => 'PIC K3L',
            'pic_perencanaan_korporat' => 'PIC Perencanaan Korporat',
            'pic_hukum' => 'PIC Hukum',
            'karyawan' => 'Karyawan'
        ];

        return view('akun.edit', compact('user', 'availableRoles'));
    }

    /**
     * Update akun pengguna
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:asisten_manager,pic_keuangan,pic_manajemen_risiko,pic_sekretaris_perusahaan,pic_perencanaan_operasi,pic_pengembangan_bisnis,pic_human_capital,pic_k3l,pic_perencanaan_korporat,pic_hukum,karyawan',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Simpan data lama untuk log
        $oldRole = $user->role;

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Update password jika ada
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Catat log aktivitas
        AktivitasLog::log(
            Auth::user(),
            'update',
            'Mengupdate akun: ' . $user->name,
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_lama' => $oldRole,
                'role_baru' => $user->role,
                'password_diubah' => $request->filled('password'),
            ],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Menghapus akun pengguna
     */
    public function destroy(Request $request, $id)
    {
        // Cek jika user mencoba menghapus dirinya sendiri
        if (Auth::id() == $id) {
            return redirect()->route('akun.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $userName = $user->name;
        $userRole = $user->role;

        // Hapus user
        $user->delete();

        // Catat log aktivitas
        AktivitasLog::log(
            Auth::user(),
            'delete',
            'Menghapus akun: ' . $userName . ' (' . $userRole . ')',
            [
                'user_id' => $id,
                'role' => $userRole,
            ],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}
