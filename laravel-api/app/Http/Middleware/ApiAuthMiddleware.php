<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
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
        $token = $request->header('Authorization');
        $jwt = new \JwtAuth();
        //Borro las comillas por defecto del token
        $token = str_replace('"', '', $token);
        //Comprobar si el token es correcto
        $checkToken = $jwt->checkToken($token, false); //el segundo parametro de la funcion nos devolvera el jwt codificado o decodificado
        $data = [
          'code' => 404,
          'status' => 'error',
          'message' => 'El token no es correcto'
        ];
        
        if ($checkToken) {
            return $next($request);
        } else {
            return response()->json($data, $data['code']);
        }
        
    }
}
