<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class MustVerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        if (auth()->user()->email_verified_at === null) {
            return redirect('email/verify');
        }
        return $next($request);
    }
}
