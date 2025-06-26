<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AktivitasLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class AktivitasLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(fn($request, $next) => $this->checkRole($request, $next));
    }

    private function checkRole($request, $next)
    {
        if (Auth::user()->role !== 'asisten_manager') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }
        return $next($request);
    }

    public function index(Request $request)
    {
        $logs = $this->applyFilters($request)
                     ->orderBy('created_at', 'desc')
                     ->paginate($request->input('per_page', 15));

        return view('aktivitasLog.index', [
            'logs' => $logs,
            'users' => User::orderBy('name')->get(),
            'tipes' => $this->getTipeList(),
            'modelTypes' => $this->getModelTypes(),
            'stats' => $this->getStatistikAktivitas()
        ]);
    }

    public function show($id)
    {
        $log = AktivitasLog::with(['user', 'loggable'])->findOrFail($id);
        return view('aktivitasLog.show', compact('log'));
    }

    public function eksporCsv(Request $request)
    {
        $logs = $this->applyFilters($request)->orderByDesc('created_at')->get();
        $filename = 'log_aktivitas_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Tipe', 'Judul', 'Deskripsi', 'Model', 'Data', 'IP Address', 'User Agent', 'Waktu']);
            foreach ($logs as $log) {
                $modelInfo = $log->loggable_type && $log->loggable_id
                    ? class_basename($log->loggable_type) . ' #' . $log->loggable_id
                    : '';
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'Tidak Ada',
                    $log->tipe,
                    $log->judul,
                    $log->deskripsi,
                    $modelInfo,
                    $log->data ? json_encode($log->data) : '',
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }

    public function hapusLogLama(Request $request)
    {
        $request->validate(['periode' => 'required|in:1,3,6,12']);
        $cutoffDate = now()->subMonths((int)$request->periode);

        $count = AktivitasLog::where('created_at', '<', $cutoffDate)->count();
        AktivitasLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('aktivitasLog.index')->with('success', "$count log aktivitas berhasil dihapus.");
    }

    public function destroy($id)
    {
        AktivitasLog::findOrFail($id)->delete();
        return redirect()->route('aktivitasLog.index')->with('success', 'Log aktivitas berhasil dihapus.');
    }

    public function hapusMultiple(Request $request)
    {
        $request->validate([
            'log_ids' => 'required|array',
            'log_ids.*' => 'required|integer|exists:aktivitas_logs,id',
        ]);

        $count = AktivitasLog::whereIn('id', $request->log_ids)->count();
        AktivitasLog::whereIn('id', $request->log_ids)->delete();

        return redirect()->route('aktivitasLog.index')->with('success', "$count log aktivitas berhasil dihapus.");
    }

    private function applyFilters(Request $request)
    {
        return AktivitasLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('tipe'), fn($q) => $q->where('tipe', $request->tipe))
            ->when($request->filled('tanggal_mulai'), fn($q) => $q->whereDate('created_at', '>=', $request->tanggal_mulai))
            ->when($request->filled('tanggal_akhir'), fn($q) => $q->whereDate('created_at', '<=', $request->tanggal_akhir))
            ->when($request->filled('model_type'), fn($q) => $q->where('loggable_type', $request->model_type))
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = '%' . $request->keyword . '%';
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('judul', 'like', $keyword)
                        ->orWhere('deskripsi', 'like', $keyword)
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', $keyword));
                });
            });
    }

    private function getTipeList()
    {
        return [
            AktivitasLog::TYPE_LOGIN,
            AktivitasLog::TYPE_LOGOUT,
            AktivitasLog::TYPE_CREATE,
            AktivitasLog::TYPE_UPDATE,
            AktivitasLog::TYPE_DELETE,
            AktivitasLog::TYPE_VERIFY,
        ];
    }

    private function getModelTypes()
    {
        return AktivitasLog::select('loggable_type')
            ->distinct()
            ->whereNotNull('loggable_type')
            ->pluck('loggable_type');
    }

    private function getStatistikAktivitas(): array
    {
        $stats = [
            'total' => AktivitasLog::count(),
            'today' => AktivitasLog::whereDate('created_at', now())->count(),
            'week' => AktivitasLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => AktivitasLog::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count(),
            'tipe_counts' => AktivitasLog::select('tipe', DB::raw('count(*) as count'))->groupBy('tipe')->pluck('count', 'tipe')->toArray(),
        ];

        $stats['daily'] = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i);
            return [
                'date' => $date->format('d/m'),
                'count' => AktivitasLog::whereDate('created_at', $date)->count(),
            ];
        })->toArray();

        $stats['user_counts'] = AktivitasLog::select('user_id', DB::raw('count(*) as count'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $user = User::find($item->user_id);
                return $user ? ['name' => $user->name, 'count' => $item->count] : null;
            })->filter()->values()->toArray();

        return $stats;
    }
}