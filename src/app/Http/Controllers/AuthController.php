<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi inputan
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Menyiapkan kredensial
        $credentials = $request->only('email', 'password');

        // Cek kredensial dan login
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk menghindari serangan session fixation
            $request->session()->regenerate();

            // Cek apakah role pengguna diperbolehkan
            $allowedRoles = [
                'asisten_manager',
                'pic_keuangan',
                'pic_manajemen_risiko',
                'pic_sekretaris_perusahaan',
                'pic_perencanaan_operasi',
                'pic_pengembangan_bisnis',
                'pic_human_capital',
                'pic_k3l',
                'pic_perencanaan_korporat',
                'pic_hukum',
                'karyawan',
            ];

            // Jika role valid, arahkan ke dashboard sesuai role
            if (in_array(Auth::user()->role, $allowedRoles)) {
                return $this->redirectBasedOnRole();
            }

            // Jika role tidak diperbolehkan
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role Anda tidak diizinkan untuk login.');
        }

        // Jika login gagal
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email atau password salah. Silakan coba lagi.');
    }

    /**
     * Redirect berdasarkan role user
     */
    private function redirectBasedOnRole()
    {
        // Semua role diarahkan ke dashboard utama yang akan menangani pengarahan berdasarkan role
        return redirect()->route('dashboard');
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
