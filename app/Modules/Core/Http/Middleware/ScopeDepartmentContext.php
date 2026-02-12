<?php

namespace App\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ScopeDepartmentContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $contextDeptId = session('department_context_id');

            // 1. Auto-detect if user only has one department and no context set
            if (!$contextDeptId && !$user->hasRole('super-admin') && !$user->hasRole('admin')) {
                $myDeptIds = $user->getManageableDepartmentIds();
                if (count($myDeptIds) === 1) {
                    $contextDeptId = $myDeptIds[0];
                    session(['department_context_id' => $contextDeptId]);
                }
            }

            // 2. Share context with views
            // We can also bind a service here if needed.
            View::share('contextDepartmentId', $contextDeptId);
            
            // Optional: If context is set, we could enforce it on Route Model Binding or Global Scopes
            // But for now, just sharing it is enough for "Smart Awareness" in UI.
        }

        return $next($request);
    }
}
