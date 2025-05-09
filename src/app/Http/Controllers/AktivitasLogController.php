<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AktivitasLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AktivitasLogController extends Controller
{
    /**
     * Constructor untuk menerapkan middleware role
     */
    public function __construct()
    {
        $this->middleware('role:asisten_manager');
    }

    /**
     * Menampilkan daftar log aktivitas
     */
    public function index(Request $request)
    {
        $query = AktivitasLog::with('user');

        // Filter berdasarkan user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan tipe
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir') && $request->tanggal_akhir != '') {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        // Ambil data
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $users = User::orderBy('name')->get();
        $tipes = ['login', 'logout', 'create', 'update', 'delete', 'verify'];

        return view('aktivitasLog.index', compact('logs', 'users', 'tipes'));
    }

    /**
     * Menampilkan detail log aktivitas
     */
    public function show($id)
    {
        $log = AktivitasLog::with('user')->findOrFail($id);
        return view('aktivitasLog.show', compact('log'));
    }

    /**
     * Mengekspor log aktivitas ke CSV
     */
    public function eksporCsv(Request $request)
    {
        $query = AktivitasLog::with('user');

        // Filter berdasarkan user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan tipe
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir') && $request->tanggal_akhir != '') {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Buat filename untuk CSV
        $filename = 'log_aktivitas_' . date('Y-m-d_His') . '.csv';

        // Header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Buat callback untuk menghasilkan konten CSV
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['ID', 'User', 'Tipe', 'Pesan', 'Data', 'IP', 'User Agent', 'Waktu']);

            // Data CSV
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'Tidak Ada',
                    $log->tipe,
                    $log->pesan,
                    $log->data ? json_encode($log->data) : '',
                    $log->ip,
                    $log->user_agent,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }

    /**
     * Menghapus log aktivitas yang lebih lama dari periode tertentu
     */
    public function hapusLogLama(Request $request)
    {
        $request->validate([
            'periode' => 'required|in:1,3,6,12',
        ]);

        $periode = (int) $request->periode;
        $cutoffDate = Carbon::now()->subMonths($periode);

        $count = AktivitasLog::where('created_at', '<', $cutoffDate)->count();
        AktivitasLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('aktivitasLog.index')->with('success', "$count log aktivitas berhasil dihapus.");
    }

    /**
     * Static method untuk mencatat log aktivitas
     */
    public static function log(User $user, string $tipe, string $pesan, array $data = null, string $ip = null, string $userAgent = null)
    {
        AktivitasLog::create([
            'user_id' => $user->id,
            'tipe' => $tipe,
            'pesan' => $pesan,
            'data' => $data,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
