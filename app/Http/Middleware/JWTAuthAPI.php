<?php

namespace App\Http\Middleware;
use Closure;
use App\Helpers\JwtAuth;

class JWTAuthAPI
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
        // Verifica q el token sea valido
        $token = $request->header('Authorization');
        $jwt = new JwtAuth();
        $checkToken = $jwt->checktoken($token);
        if($checkToken){ // El token es valido
            return $next($request);
        }else{
            $data = array(
                'status' => 'error',
                'code' => 401,
                'message' => 'Token incorrecto'
            );
            return response()->json($data,$data['code']);
        }
    }
}
