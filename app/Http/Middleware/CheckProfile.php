<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && (empty($user->email) || empty($user->signature) || empty($user->department_id) || empty($user->image))) {
            // Allow access only to dashboard and profile routes
            if (!$request->is('dashboard') && !$request->is('profile')) {
                return redirect('/dashboard')->with('message', 'Please complete your profile before accessing other pages.');
            }
        }

        return $next($request);
    }
}
