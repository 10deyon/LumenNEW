<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController 
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60 // Expiration time
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    } 

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function authenticate(User $user) {
        $this->validate($this->request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        // Find the user by email
        $user = User::where('email', $this->request->input('email'))->first();

        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the 
            // below respose for now.
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }

        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            return response()->json([
                'token' => $this->jwt($user)
            ], 200);
        }

        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }
}

// <?php

// //declare(strict_types=1);

// namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Validator;
// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Tymon\JWTAuth\JWTAuth;
// //use App\Http\Resources\User\UserResource;

// //use Illuminate\Http\Resources\JsonResource;

// use App\User;

// class AuthController extends Controller
// {
//     //$this->middleware('auth:api', ['except' => ['login', 'register']]);
    
//     protected $jwt;

//    public function postLogin(Request $request)
//    {
//        $this->validate($request, [
//            'email'    => 'required|email|max:255',
//            'password' => 'required',
//        ]);

//        return $this->generateToken($request, true);
//    }

// public function postLogin(Request $request)
//     {
//         $this->validate($request, [
//             'email' => 'required|string',
//             'password' => 'required|string',
//         ]);

//         $credentials = $request->only(['email', 'password']);

//         if (! $token = Auth::attempt($credentials)) {
//             return response()->json(['message' => 'Unauthorized'], 401);
//         }

//         return $this->jwt->respondWithToken($token);

//     public function __construct(JWTAuth $jwt)
//     {
//         //$this->userData = new UserResource();
//         $this->jwt = $jwt;
//     }
    
//     // public function __construct()
//     // {
//     //     // $this->middleware('auth:api');
//     // }
    
//     /**
//      * Store a new user.
//      *
//      * @param  Request  $request
//      * @return Response
//      */
//     public function register(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:100',
//             'email' => 'required|email|max:255|unique:users',
//             'password' => 'required|string|min:8|max:255|confirmed',
//             'password_confirmation' => 'required|string|min:8|max:255',
//         ]);

//         if($validator->fails()) {
//             return response()->json([
//                 'status' => 'error',
//                 'messages' => $validator->messages()
//             ], 200);
//         }

//         $user = new User;
//         $user->fill($request->all());
//         $user->password = app('hash')->make($request->password);

//         if ($user->save()) {
//             $user->register = [
//                 'href' => 'api/v1/user/register',
//                 'method' => 'POST',
//                 'params' => 'email, hashedPassword'
//             ];

//             // $this->toArray();
//             $response = [
//                 'message' => 'User registered successfully',
//                 'data' => [
//                     'name' => $request->name,
//                     'email' => $request->email,
//                     'registered' => [
//                         'href' => 'api/v1/user/register',
//                         'method' => 'POST',
//                         'params' => 'email, password'
//                     ]
//                 ]
//             ];
//             // return response()->json($response, 201);
//             return response()->json([
//                 compact('token'), 
//                 $response
//             ], 201);
//         };

//         // return response()->json([
//         //     'status' => 'success',
//         //     'data' => $user
//         // ], 200);
//         return response()->json(['message' => 'User Registration Failed!'], 409);
//         // return "Passwords do not match";       
//     }

//     // public function postLogin(Request $req)
//     // {

//     //     $credentials = $req->only('email', 'password');

//     //     /**
//     //      * Token on success | false on fail
//     //      *
//     //      * @var string | boolean
//     //      */
//     //     $token = Auth::attempt($credentials);

//     //     return ($token !== false)
//     //             ? json_encode(['jwt' => $token])
//     //             : response('Unauthorized.', 401);

//     // }

//     public function postLogin(Request $request)
//     {
//         $this->validate($request, [
//             'email'    => 'required|email|max:255',
//             'password' => 'required',
//         ]);

//         try {
//             if (!$token = $this->jwt->attempt($request->only('email', 'password'))) {
//                 return response()->json(['error' => 'Unauthorized'], 404);
//             }

//         } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

//             return response()->json(['token_expired'], 500);

//         } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

//             return response()->json(['token_invalid'], 500);

//         } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

//             return response()->json(['token_absent' => $e->getMessage()], 500);

//         }

//         return response()->json(compact('token'));
//     }

//     public function logout() {

//         auth()->logout();

// // Pass true to force the token to be blacklisted "forever"
// auth()->logout(true);
//         Auth::guard('api')->logout();
    
//         return response()->json([
//             'status' => 'success',
//             'message' => 'logout'
//         ], 200);
//     }

//     // public function login (){
//     //     $credentials = request(['email', 'password']);
//     //     //$token = auth('api')->attempt($credentials);
//     //     if (! $token = auth()->attempt($credentials)) {
//     //         return response()->json(['error' => 'Unauthorized'], 401);
//     //     }

//     //     return $this->respondWithToken($token);
//     // }

//     // public function logout (){
//     //     auth()->logout();

//     //     return response()->json(['message' => 'Successfully logged out']);
//     // }

//     // public function refresh (){
//     //     return $this->respondWithToken(auth()->refresh());
//     // }

//     // public function me (){
//     //     return response()->json(auth()->user());
//     // }

//     // protected function respondWithToken($token)
//     // {
//     //     return response()->json([
//     //         'access_token' => $token,
//     //         'token_type' => 'bearer',
//     //         'expires_in' => auth()->factory()->getTTL() * 60
//     //     ]);
//     // }

// }