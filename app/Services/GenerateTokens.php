<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;


class GenerateTokens
{

    protected static $timeLifeUpdateToken=178560;
    protected static $timeLifeOperationToken=30;

    public static function updateToken($user){

        $customClaims=[
            'iss'=>'',
            'aud'=>'',
            'exp'=> Carbon::now()->addMinutes(static::$timeLifeUpdateToken)->timestamp,
        ];

        $token=JWTAuth::customClaims($customClaims)->fromUser($user);
        return $token;
    }


    public static function oprationToken($user){

        $customClaims=[
            'iss'=>'',
            'aud'=>'',
            'exp'=> Carbon::now()->addMinutes(static::$timeLifeUpdateToken)->timestamp,
        ];
        $token=JWTAuth::customClaims($customClaims)->fromUser($user);
        return $token;
    }



}
?>