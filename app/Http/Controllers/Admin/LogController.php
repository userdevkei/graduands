<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        return view('admin.logs.index', [
            'logs' => Log::with('user')
                ->when($search, fn ($q) => $q->where('action', 'like', "%{$search}%"))
                ->latest()->paginate(50)->withQueryString(),
            'search' => $search,
        ]);
    }
}
