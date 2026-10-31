<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBranchMatch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $request->filled('_branch')) {
            if ((int) $request->_branch !== (int) session('branch_id')) {
                return back()->withErrors(['error' => 'تم تغيير الفرع في نافذة اخرى، يرجى اعادة فتح الصفحة.']);
            }
        }

        return $next($request);
    }
}
