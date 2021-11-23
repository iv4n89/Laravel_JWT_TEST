<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try{
            if(!$token = JWTAuth::attempt($credentials))
            {
                return response()->json(['error' => 'invalid credentials'], 400);
            }
        }catch(JWTException $jwtex){
            return response()->json(['error' => 'could not create token'], 500);
        }
        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
    {
        try{
            if(!$user = JWTAuth::parseToken()->authenticate())
            {
                return response()->json(['user not found'], 404);
            }
        }catch(TokenExpiredException $ex){
            return response()->json(['token expired'], $ex->getStatusCode());
        }
    }
}