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
