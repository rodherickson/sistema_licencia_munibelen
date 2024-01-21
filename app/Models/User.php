<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use SebastianBergmann\CodeUnit\FunctionUnit;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'dni',
        'email',
        'password',
        'type_user',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public static function listUser()
    {
        try  {
            $users = DB::table('users')
            ->where('status', '=', 1 )
            ->orderBy('id', 'desc')
            ->select('id', 'name','last_name', 'dni', 'email', 'type_user')
            ->get();
            return  $users;
        } catch(ModelNotFoundException){
            throw new \Exception('No se pudo listar a los usuarios');
        }
    }

    public static function changePass($idUser, $newPass)
    {
        DB::table('users')
        ->where('id', '=', $idUser)
        ->update(['password' => Hash::make($newPass)]);
    }

    public static function saveUser($name, $lastName, $dni, $email, $typeUser, $password)
    {
        DB::table('users')->insert([
            'name' => $name,
            'last_name' => $lastName, 
            'dni' => $dni,
            'email' => $email,
            'type_user' => $typeUser,
            'status' => true,
            'password' => Hash::make($password),
        ]);
    }

    public static function invalidate($idUser)
    {
        DB::table('users')
        ->where('id', '=', $idUser )
        ->update(
            ['status' => false ]
        );
    }


}
