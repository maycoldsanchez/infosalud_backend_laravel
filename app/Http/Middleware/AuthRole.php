<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user->hasRole(explode("|", $roles))) {
            return response()
                ->json(['status' => '990', 'message' => 'Permisos insuficientes. '], 200);
        }

        return $next($request);
    }
}
