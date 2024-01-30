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
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    

    public function login(LoginRequest $request)


    
    {
        $user = User::verifyCredentials($request->email, $request->password);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas!Vuelva a intentar'
            ], 401);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // Agrega más campos según sea necesario
        ];
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario Autentificado',
            'token' => GenerateTokens::token($user),
            'user' =>[
                'id' =>$user->id,
                'nombre'=>$user->name,
                'email'=>$user->email
            ]

        ], 200);
    }

    public function refresh()
    {
        try {
            list($idUser, $newToken) = GenerateTokens::refreshTokens();

            return response()->json([
                'status' => 'success',
                'message' => 'User Authenticated',
                'user' => $idUser,
                'token' => $newToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
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
