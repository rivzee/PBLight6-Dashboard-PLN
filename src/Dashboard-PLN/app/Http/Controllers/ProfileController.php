<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Update data profil pengguna (info dasar & password)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Jika update password
        if ($request->input('update_type') === 'password') {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return back()->with('success', 'Password berhasil diperbarui. Silakan logout dan login kembali dengan password baru Anda.');
        }
        // Jika update info dasar
        else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return back()->with('success', 'Profil berhasil diperbarui');
        }
    }

    /**
     * Update foto profil pengguna
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = Auth::user();
        // Hapus foto lama jika ada
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        // Upload foto baru
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo = $path;
        $user->save();
        return back()->with('success', 'Foto profil berhasil diperbarui');
    }
}
