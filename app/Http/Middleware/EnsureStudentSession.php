<?php

namespace App\Http\Middleware;

use App\Models\Graduand;
use Closure;
use Illuminate\Http\Request;

class EnsureStudentSession
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->session()->get('graduand_id');
        $graduand = $id ? Graduand::with('cohort')->find($id) : null;

        if (! $graduand) {
            $request->session()->forget('graduand_id');

            return redirect()->route('student.lookup');
        }

        $request->attributes->set('graduand', $graduand);

        return $next($request);
    }
}
