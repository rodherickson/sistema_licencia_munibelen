<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class PersonaController extends Controller
{
    public static function searchDni($dni)
    {
        if(strlen($dni) != 8 || !is_numeric($dni)){
            return response()->json(['succes'=>false, 'message'=>'El número ingresado no es valido.']);
        }
        
        $apiUrl = 'https://dniruc.apisperu.com/api/v1/dni/'.$dni;
        $tokens = json_decode(file_get_contents(base_path('app/VisitasToken.json')), true);
        $response = self::curlOperations($tokens, $apiUrl);
        
        if(!$response){
            return response()->json(['success' =>false, 'message'=>'Servicio no disponible en este momento.']);  
        }

        return response()->json([json_decode($response, true)]);
    }   

    protected static function curlOperations($arrayTokens, $datum)
    {
        $firstValue = 0;
        $keyValueChange = 0;
        $valueChange = 0;

        foreach($arrayTokens as $indice => $token){
            $ch = curl_init($datum);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '. $token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCodeStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if($httpCodeStatus == '200'){
                if($indice === 0){
                    return $response;
                }
                $firstValue = $arrayTokens[0];
                $keyValueChange = $indice - 1;
                $valueChange = $token;
                array_shift($arrayTokens);
                $arrayTokens[4] = $firstValue;
                $arrayTokens[$keyValueChange] = $arrayTokens[0];
                $arrayTokens[0] = $valueChange;
                self::makeJsonTokens($arrayTokens);
                return $response;
            }
        }
        return false;
    }
    
    private static function makeJsonTokens($array)
    {   
        $pathFile = base_path('app/VisitasToken.json');
        file_put_contents($pathFile, json_encode($array));
        chmod($pathFile, 0644);
    }
    

    public static function searchRuc($ruc)
    {
        if(strlen($ruc) != 11 || !is_numeric($ruc)){
            return response()->json(['success'=>false, 'message'=>'El número ingresado no es válido.']);
        }
        
        $apiUrl = 'https://dniruc.apisperu.com/api/v1/ruc/'.$ruc;
        $tokens = json_decode(file_get_contents(base_path('app/VisitasToken.json')), true);
        $response = self::curlOperations($tokens, $apiUrl);
        
        if(!$response){
            return response()->json(['success'=>false, 'message'=>'Servicio no disponible en este momento.']);  
        }
    
        $responseData = json_decode($response, true);
    
        // Verificar si la solicitud fue exitosa
        if(isset($responseData['ruc'])) {
            $ruc = $responseData['ruc'];
            $razonSocial = $responseData['razonSocial'];
            $direccion = $responseData['direccion'];
    
            // Aquí puedes hacer lo que desees con los datos obtenidos
            return response()->json([
                'success' => true,
                'ruc' => $ruc,
                'razonSocial' => $razonSocial,
                'direccion' => $direccion
            ]);
        } else {
            return response()->json(['status'=>'error', 'message'=>'No se pudo obtener la información del RUC.']);  
        }
    }  
    

    

}
