<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CashierAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if cashier is logged in
        if (!session()->has('cashier_id')) {
            return redirect()->route('cashier.login.form')
                ->with('error', 'Please login to continue.');
        }

        return $next($request);
    }
}