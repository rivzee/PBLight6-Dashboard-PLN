<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Konstruktor - semua method membutuhkan autentikasi
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan semua notifikasi pengguna yang login
     */
    public function index()
    {
        $user = Auth::user();
        $notifikasis = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasis'));
    }

    /**
     * Menandai notifikasi sebagai dibaca
     */
    public function tandaiDibaca($id)
    {
        $user = Auth::user();
        $notifikasi = Notifikasi::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notifikasi->update([
            'dibaca' => true
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Menandai semua notifikasi sebagai dibaca
     */
    public function tandaiSemuaDibaca()
    {
        $user = Auth::user();

        Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Menghapus notifikasi
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notifikasi = Notifikasi::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notifikasi->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifikasi.index')->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Menghapus semua notifikasi yang sudah dibaca
     */
    public function hapusSudahDibaca()
    {
        $user = Auth::user();

        $count = Notifikasi::where('user_id', $user->id)
            ->where('dibaca', true)
            ->count();

        Notifikasi::where('user_id', $user->id)
            ->where('dibaca', true)
            ->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return redirect()->route('notifikasi.index')->with('success', $count . ' notifikasi berhasil dihapus.');
    }

    /**
     * Mendapatkan jumlah notifikasi yang belum dibaca (untuk navbar)
     */
    public function getJumlahBelumDibaca()
    {
        $user = Auth::user();

        $count = Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mendapatkan notifikasi terbaru (untuk dropdown navbar)
     */
    public function getNotifikasiTerbaru()
    {
        $user = Auth::user();

        $notifikasis = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'notifikasis' => $notifikasis,
            'count' => Notifikasi::where('user_id', $user->id)
                ->where('dibaca', false)
                ->count()
        ]);
    }

    /**
     * Static method untuk mengirim notifikasi ke Master Admin
     */
    public static function kirimKeMasterAdmin($judul, $pesan, $jenis = 'info', $url = null)
    {
        // Dapatkan semua user dengan role asisten_manager
        $masterAdmins = User::where('role', 'asisten_manager')->get();

        foreach ($masterAdmins as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => $judul,
                'pesan' => $pesan,
                'jenis' => $jenis, // info, success, warning, danger
                'url' => $url,
                'dibaca' => false
            ]);
        }
    }
}
