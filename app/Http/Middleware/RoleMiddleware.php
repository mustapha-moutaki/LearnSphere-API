<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        if (!Auth::user()->hasRole($role)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        return $next($request);
    }

}
