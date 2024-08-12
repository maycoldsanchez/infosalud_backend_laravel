<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\TokenUser\JwtAuthHelper;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = '';
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $infoToken = [
                'user'    => $user->id,
                'user_ip' => $request->ip(),
                'token'   => $request->bearerToken()
            ];
            $request['userInfo'] = $infoToken;

            $objJwtAuthHelper = new JwtAuthHelper();
            $boolValidToken = $objJwtAuthHelper->checkToken($infoToken);

            if ($boolValidToken) {
                $response = $next($request);
            } else {
                $response = response()->json(['codigo' => '999', 'message' => 'Sesión invalida. '], 200);
            }
        } catch (\Throwable $th) {
            $response = response()
                ->json(['codigo' => '999', 'message' => 'catch. '], 200);
        }

        return $response;
    }
}
