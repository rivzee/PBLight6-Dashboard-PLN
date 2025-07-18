<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AktivitasLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    private const VALID_ROLES = [
        'asisten_manager', 'pic_keuangan', 'pic_manajemen_risiko',
        'pic_sekretaris_perusahaan', 'pic_perencanaan_operasi',
        'pic_pengembangan_bisnis', 'pic_human_capital', 'pic_k3l',
        'pic_perencanaan_korporat', 'karyawan'
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(fn ($request, $next) => $this->checkRole($request, $next));
    }

    protected function checkRole($request, $next)
    {
        if (Auth::user()->role !== 'asisten_manager') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }
        return $next($request);
    }

    protected function getAvailableRoles(): array
    {
        return array_combine(self::VALID_ROLES, array_map('ucwords', str_replace('_', ' ', self::VALID_ROLES)));
    }

    protected function logAktivitas($action, $deskripsi, $ringkasan, $target, $data, Request $request)
    {
        AktivitasLog::log(
            Auth::user(), $action, $deskripsi, $ringkasan, $target,
            $data, $request->ip(), $request->userAgent()
        );
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $users = User::orderBy('name')
            ->when($request->input('search'), function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('role', 'like', "%{$search}%");
            })
            ->paginate($perPage);

        $users->getCollection()->transform(function ($user) {
            if (empty($user->name)) {
                $user->name = 'Pengguna #' . $user->id;
            }
            return $user;
        });

        return view('akun.index', compact('users'));
    }

    public function create()
    {
        return view('akun.create', ['availableRoles' => $this->getAvailableRoles()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:' . implode(',', self::VALID_ROLES),
        ]);

        try {
            if (!\DB::connection()->getPdo()) {
                throw new \Exception('Database connection failed');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            $this->logAktivitas('create', 'Membuat akun baru: ' . $user->name . ' (' . $user->role . ')',
                'Menambahkan akun pengguna baru', $user,
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ], $request);

            return redirect()->route('akun.index')->with('success', 'Akun berhasil dibuat.');

        } catch (\Exception $e) {
            \Log::error('Error creating user account: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat akun: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('akun.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('akun.edit', [
            'user' => $user,
            'availableRoles' => $this->getAvailableRoles(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:' . implode(',', self::VALID_ROLES),
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        try {
            $oldRole = $user->role;
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            $this->logAktivitas('update', 'Mengupdate akun: ' . $user->name,
                'Mengubah data akun pengguna', $user,
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role_lama' => $oldRole,
                    'role_baru' => $user->role,
                    'password_diubah' => $request->filled('password'),
                ], $request);

            return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating user account: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        if (Auth::id() == $id) {
            return redirect()->route('akun.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $this->logAktivitas('delete', 'Menghapus akun: ' . $user->name . ' (' . $user->role . ')',
            'Menghapus akun pengguna dari sistem', null,
            [
                'user_id' => $id,
                'role' => $user->role,
            ], $request);

        $user->delete();

        return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}