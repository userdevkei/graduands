<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Support\ActionLabel;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        return view('admin.logs.index');
    }

    /** Server-side DataTables endpoint. */
    public function data(Request $request)
    {
        $total = Log::count();

        $search = trim((string) $request->input('search.value', ''));
        $query = Log::with('user')->when($search, function ($q, $s) {
            $q->where(function ($w) use ($s) {
                $w->where('action', 'like', "%{$s}%")
                    ->orWhere('model_id', 'like', "%{$s}%")
                    ->orWhere('ip_address', 'like', "%{$s}%");
            });
        });
        $filtered = (clone $query)->count();

        $dir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $rows = $query->orderBy('created_at', $dir)
            ->skip(max((int) $request->input('start', 0), 0))
            ->take(min(max((int) $request->input('length', 25), 1), 100))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->map(fn (Log $l) => [
                'when' => $l->created_at->format('d M Y, H:i:s'),
                'action' => $l->action,
                'meta' => ActionLabel::for($l->action),
                'who' => $l->user?->name ?? 'Student / system',
                'record' => $l->model_type ? class_basename($l->model_type).' '.$l->model_id : '',
                'ip' => $l->ip_address,
                'old' => $l->old_values,
                'new' => $l->new_values,
            ])->all(),
        ]);
    }
}
