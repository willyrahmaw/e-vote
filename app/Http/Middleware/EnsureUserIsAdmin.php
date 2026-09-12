<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin administrator.');
        }

        if (! $request->user()->is_active) {
            \Illuminate\Support\Facades\Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun administrator Anda telah dinonaktifkan.']);
        }

        return $next($request);
    }
}
