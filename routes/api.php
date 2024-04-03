<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Rubro;
use App\Http\Controllers\Propietario;
use App\Http\Controllers\CarnetController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ConstanciaController;
use App\Http\Controllers\TipoMultaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
Route::get('/rubro', [Rubro::class, 'listRubro']);
Route::get('/persona/dni/{dni}',[PersonaController::class,'searchDni']);
Route::get('/persona/ruc/{ruc}',[PersonaController::class,'searchRuc']);  
Route::post('/propietario', [Propietario::class, 'register']);
Route::get('/propietario/{dni}', [Propietario::class, 'mostrarpropietario']);
Route::post('/multa', [MultaController::class, 'registerMulta']);
Route::get('/multa/listar', [MultaController::class, 'listarMultasEnProceso']);
Route::post('/multa/editar/{idmulta}', [MultaController::class, 'updateMulta']);
Route::get('/multa/dashboard/consulta', [MultaController::class, 'obtenerDatosTipoMultas']);
Route::get('multa/datos/{idmulta}', [MultaController::class, 'datosMulta']);
Route::get('/multas', [TipoMultaController::class, 'listTipoMulta']);
Route::get('/carnet/{dni}',[CarnetController::class, 'obtenercarnet']);
Route::get('/carnet/expedir/{dni}',[CarnetController::class, 'expedirCarnet']);
Route::get('/carnet', [CarnetController::class, 'listcarnet']);
Route::get('/carnet/listar/caducados', [CarnetController::class, 'listcarnetCaducados']);
Route::post('/carnet', [CarnetController::class, 'register']);
Route::post('/carnet/editar/{idCarnet}', [CarnetController::class, 'updateCarnet']);
Route::get('/carnet/dashboard/conteo', [CarnetController::class, 'contarCarnetsPorMeses']);
Route::get('/carnet/dashboard/estados', [CarnetController::class, 'contarCarnetsPorEstado']);
Route::post('/carnet/padron/vendedores', [CarnetController::class, 'obtenerReportePadronVendedores']);
// Route::get('/carnet/dashboard/conteo/ano', [CarnetController::class, 'contarCarnetsPorAnio']);
Route::post('/licencia', [LicenciaController::class, 'register']);
Route::get('/licencia/{dni}', [LicenciaController::class, 'obtnerlicencia']);
Route::get('/licencia/expedir/{id}', [LicenciaController::class, 'expedirLicencia']);
Route::get('/licencia/dashboard/conteo', [LicenciaController::class, 'contarLicenciasPorMeses']);
Route::post('/constancia/expedir', [ConstanciaController::class, 'expedirConstancia']); 
Route::get('/constancia/listar/caducados', [ConstanciaController::class, 'listconstanciaCaducados']);
Route::post('/constancia/editar/{idConstancia}', [ConstanciaController::class, 'updateConstancia']);      

Route::get('/actualizar-carnets', function (App\Jobs\ActualizarEstadoCarnetsCaducado $job) {
    $job->dispatch();
    return response()->json(['success'=>true,'message' => 'Job despachado correctamente']);
});
Route::get('/actualizar-constancia', function (App\Jobs\ActualizarEstadoConstanciaCaducado $job) {
    $job->dispatch();
    return response()->json(['success'=>true,'message' => 'Job despachado correctamente']);
});
});