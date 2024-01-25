<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Services\GenerateTokens;
use Generator;

class AuthController extends Controller

{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::verifyCredentials($request->email, $request->password);
        if (!$user) {
            return response()->json([
                'message' => 'Credenciales incorrectas!Vuelva a intentar'
            ], 401);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario Autentificado',
            'tokenOpertation' => GenerateTokens::oprationToken($user),
            'tokenUpdate' => GenerateTokens::updateToken($user)
        ], 200);
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

    public function register(RegisterUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'dni' => $request->dni,
                'email' => $request->email,
                'password' => $request->password,
                'type_user' => $request->type_user,
            ]);
            return response()->json([
                'message' => 'Datos Guardados',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Problemas con el Servidor'
            ], 500);
        }
    }
}
