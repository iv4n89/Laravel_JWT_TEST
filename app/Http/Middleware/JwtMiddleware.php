<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try
        {
            $user = JWTAuth::parseToken()->authenticate();
        } catch(Exception $ex){
            if($ex instanceof  TokenInvalidException){
                return response()->json(['status' => 'Token invalid']);
            } else if ($ex instanceof TokenExpiredException){
                return response()->json(['status' => 'Token is expired']);
            } else {
                return response()->json(['status' => 'Authorization token not found']);
            }
        }

        return $next($request);
    }
}