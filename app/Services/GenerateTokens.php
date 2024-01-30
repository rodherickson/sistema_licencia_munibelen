<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class GenerateTokens
{

    protected static $timeToken=109560;

    protected static $iss = 'sistemacarnet.munibelen.gob.pe';
    protected static $aud = 'sistemacarnet.munibelen.gob.pe';

    public static function token($user){

        $customClaims=[
            'iss'=> static::$iss,
            'aud'=> static::$aud,
            'exp'=> Carbon::now()->addMinutes(static::$timeToken)->timestamp,
        ];

        $token=JWTAuth::customClaims($customClaims)->fromUser($user);
        return $token;
    }

    public static function refreshTokens(): array
    {
        try {
            
            $currentToken = JWTAuth::getToken();

            JWTAuth::checkOrFail();

            $payload = JWTAuth::getPayload($currentToken)->toArray();

            $aud = $payload['aud'];
            $iss = $payload['iss'];
            $sub = $payload['sub'];
    
            if (static::$iss != $iss || static::$aud != $aud[0]) {
                throw new JWTException('Los claims esperados no coinciden con los proporcionados');
            }
    
            $user = User::getUserIfActive($sub[0]);
    
            if (!$user) {
                throw new JWTException('El usuario está temporalmente restringido');
            }
            return [$sub[0], self::token($user)];
        } catch (JWTException $e) {
            throw $e; 
        } catch (\Exception $e) {
            throw new JWTException('Error al refrescar el token: ' . $e->getMessage());
        }
    }
}