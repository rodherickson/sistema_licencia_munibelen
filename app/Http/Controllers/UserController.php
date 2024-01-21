<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function listUsers(Request $request)
    {
        try {
            $list = User::listUser();
            return response()->json(['status' => 'success', 'data' => $list], 200);
        } catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'idUser' => 'required|numeric'
            ],
            [
                'idUser.required' => 'ID usuario necesario', 'regex' => 'La contraseña necesita ser una combinación de caracteres alfanumericos y caracteres especiales'
            ]);
            User::changePass($request->idUser, $request->newPass);
            return response()->json(['status' => 'success', 'message' => 'Contraseña cambiada'], 201);
        } catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function invalidateUser(Request $request)
    {
        try {
            $request->validate(['idUser' => 'required|numeric']);
            $user = User::invalidate($request->idUser);
            return response()->json(['status'=> 'success', 'message'=>'Update successfully'], 200);
        } catch(\Exception $e){
            return response()->json(['status' => 'error', 'message'=> $e->getMessage()]);
        }
    }    
}