<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller

{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login( Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make( $credentials,[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>'error', 'message'=>$validator->messages()], 400);
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['status'=>'error', 'message' => 'Unauthorized'], 401);
        }
    
        $user = Auth::guard('api')->user();

        $token = JWTAuth::claims(['type' => $user->type_user, 'email' => $request->email])->fromUser($user);
        
        try {
            $dataUser = $this->dataUser($request->email);
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => 'Estado ']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User Authenticated',
            'user' =>  $dataUser,  
            'token' => $token
        ]);


        // if (! $token = auth()->attempt($credentials)) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        // return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try{
            Auth::guard('api')->logout();
            return response()->json(['message' => 'Successfully logged out']);

        } catch(\Exception $e){
            return response()->json(['status'=> 'error', 'message'=> 'Error'], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $authHeader = $request->header('Authorization');
    
        if (!$authHeader) {
            return response()->json(['status' => 'error', 'message' => 'No token provided'], 401);
        }
    
        try {
            $oldToken = JWTAuth::parseToken();
          
            /** Verify if the user is still active */
            $email = $oldToken->getPayload()->get('email');
            $user = User::where('email', $email)->first();
            if (!$user || !$user->status) {
              return response()->json([
                'status' => 'error',
                'message' => "Ti-ling, you are inactive",
              ], 401);
            }
          
            // Generate a new token without refreshing (blacklist the old one)
            $newToken = JWTAuth::fromUser($user, [], true);
          
            $dataUser = [
              'uid' => $user->id,
              'name' => $user->name,
              'typeUser' => $user->type_user,
            ];
          
            return response()->json([
              'status' => 'success',
              'message' => 'User Authenticated',
              'user' => $dataUser,
              'token' => $newToken,
            ]);
          } catch (\Exception $e) {
            return response()->json([
              'status' => 'error',
              'message' => $e->getMessage(),
            ], 401);
          }

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request){
        $data = $request->only('name', 'last_name', 'dni', 'email', 'password', 'typeUser');       
        $validator  = Validator::make($data, ['name' => 'required|string|max:255',   
                  'last_name' => 'required|string|max:255',             
                  'dni' => 'required|string|max:8',             
                  'email' => 'required|string|email|max:255|unique:users',             
                  'password' => 'required|string|min:6',             
                  'typeUser' => 'required|string|max:255'         
                ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);

        }
        User::saveUser($request->name, $request->last_name, $request->dni, $request->email, $request->typeUser, $request->password);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully'
        ], 200);
    }
        // $user=User::create(array_merge(
        //     $validator->validate(),
        //     ['password'=>bcrypt($request->password)]
        // ));

        // return response()->json(
        //     [
        //         'message'=>'Usuario registrado exitosamente',
        //         'user'=>$user
           //     ],201);

           private  function dataUser($credentials) 
           {
               $data = User::where('email', $credentials)->first();
               if(!$data){
                   throw new Exception('Usuario no encontrado');
               }
               return [
                   'uid' => $data->id, 
                   'name' =>  $data->name,
                   'typeUser' => $data->type_user,
               ];
    }
}
