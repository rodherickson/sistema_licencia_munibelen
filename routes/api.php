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
Route::get('/propietarios', [Propietario::class, 'obtenerTodos']);
Route::put('/propietarios/{id}', [Propietario::class, 'actualizarDatos']);
Route::post('/carnet', [CarnetController::class, 'register']);
Route::get('/carnet', [CarnetController::class, 'listcarnet']);
Route::get('/carnet/{dni}',[CarnetController::class, 'obtenercarnet']);
Route::post('/multa', [MultaController::class, 'registerMulta']);
Route::post('/licencia', [LicenciaController::class, 'register']);
Route::get('/licencia/{dni}', [LicenciaController::class, 'obtnerlicencia']);
Route::get('/licencia-expedir/{id}', [LicenciaController::class, 'expedirLicencia']);


