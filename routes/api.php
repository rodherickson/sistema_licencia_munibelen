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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::get('/rubro', [Rubro::class, 'listRubro']);
Route::get('/persona/{dni}',[PersonaController::class,'searchDni']);
Route::get('/persona/ruc/{ruc}',[PersonaController::class,'searchRuc']);
Route::post('/propietario', [Propietario::class, 'register']);
Route::get('/propietario/{dni}', [Propietario::class, 'mostrarpropietario']);
Route::post('/carnet', [CarnetController::class, 'register']);
Route::get('/carnet', [CarnetController::class, 'listcarnet']);
Route::get('/carnet/{dni}',[CarnetController::class, 'obtenercarnet']);
Route::get('/carnet/expedir/{dni}',[CarnetController::class, 'expedirCarnet']);
Route::post('/multa', [MultaController::class, 'registerMulta']);
Route::post('/licencia', [LicenciaController::class, 'register']);
Route::get('/licencia/{dni}', [LicenciaController::class, 'obtnerlicencia']);
Route::get('/licencia/expedir/{id}', [LicenciaController::class, 'expedirLicencia']);
Route::post('/constancia/expedir', [ConstanciaController::class, 'expedirConstancia']);

