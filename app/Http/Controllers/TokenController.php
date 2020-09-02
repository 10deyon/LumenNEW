<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTAuth;

class TokenController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->middleware ('auth');
        $this->jwt = $jwt;
    }

    public function generateToken(Request $request, $isLogin = false)
    {
       try {
           if (!$token = $this->jwt->attempt($request->only('email', 'password'))) {
               return response()->json(['error' => 'Invalid email and password'], 404);
           }
       } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
           return response()->json(['token_expired'], 500);
       } 
       catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
           return response()->json(['token_invalid'], 500);
       }
       catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
           return response()->json(['token_absent' => $e->getMessage()], 500);
       }

       return $isLogin ?  response()->json([
            'status' => '00',
            'message' => 'successfull',
            'token' => $this->respondWithToken($token)
        ],200) : $this->respondWithToken($token);
   }


   public function refresh(Request $request)
   {
       $token = $this->jwt->getToken();
       if(!$token) {
           return response()->json([
               'status' => '01',
               'message' => 'Token not provided',
           ]);
        } 
        
        if($token = $this->jwt->refresh($token)){
            return response()->json([
                'status' => '200',
                "token" => $this->respondWithToken($token)
            ]);
        } else {
            return response()-> json([
                "status" => 401,
                "message" => "Token has been blacklisted, kindly login"
            ]);
        }
   }

   protected function respondWithToken($token)
   {
       return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->jwt->factory()->getTTL() * 60
       ];
   }
}