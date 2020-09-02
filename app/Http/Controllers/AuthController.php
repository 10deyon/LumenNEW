<?php

//declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Resources\User\UserResource;

use App\User;

class AuthController extends Controller
{
    //use TokenController;
    protected $jwt;

    public function __construct(JWTAuth $jwt, TokenController $token)
    {
        $this->middleware ('auth', ['except' => ['register', 'postLogin']]);
        $this->jwt = $jwt;
        $this->token = $token;
    }

    public function getAuthUser()
    {
        $user = $this->jwt->user();
        if (count((array)$user) > 0) {
            return response()->json([
                'code' => "00",
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => 'success', 
                    'getUser' => [
                        'href' => 'api/v1/user',
                        'method' => 'POST',
                        'params' => 'email, password'
                    ]
                ]
            ]);
        } else {
            return response()->json(['status' => 'fail'], 401);
        }
        //return response()->json(auth()->user());
    }
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed',
            'password_confirmation' => 'required|string|min:8|max:255',
        ]);

        //$credentials = $request->only();

        $user = new User;
        $user->fill($request->all());
        $user->password = app('hash')->make($request->password);
        // $user = User::first();
        if ($user->save()) {
            $response = [
                'statusCode' => '01',
                'token' => $this->token->generateToken($request),
                'message' => 'User registered successfully',
                'data' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'registered' => [
                        'href' => 'api/v1/user/register',
                        'method' => 'POST',
                        'params' => 'email, password'
                    ]
                ]
            ];
            return response()->json($response, 200);
        };
        return response()->json(['message' => 'User Registration Failed!'], 409);
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);
            
        $credentials = $request->only('email', 'password');

        return $this->token->generateToken($request, true);
    }

    public function logout() 
    {
        // Pass true to force the token to be blacklisted "forever"
        // $this->jwt->invalidate();
        $this->jwt->invalidate($this->jwt->getToken());
        
        return response()->json([
            'statusCode' => '01',
            'message' => 'User logged out successfully'
        ], 200);
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
                "token" => $this->token->generateToken($request)
            ]);
        } else {
            return response()-> json([
                "status" => 401,
                "message" => "Token has been blacklisted, kindly login"
            ]);
        }
   }

}