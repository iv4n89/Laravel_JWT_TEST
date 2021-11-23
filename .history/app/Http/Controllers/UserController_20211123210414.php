<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
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
            return response()->json(['token expired'], 404);
        }catch(TokenInvalidException $ex){
            return response()->json(['token invalid'], 404);
        }catch(JWTException $ex){
            return response()->json(['token absent'], 404);
        }
        return response()->json(compact('user'));
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'));
    }
}