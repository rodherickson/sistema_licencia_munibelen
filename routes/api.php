<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Rubro;
use App\Http\Controllers\Propietario;
use App\Http\Controllers\CarnetController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\MultaController;
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
Route::get('/rubro', [Rubro::class, 'listRubro']);
Route::get('/propietario/{dni}',[Propietario::class,'getDatosPorDni']);
Route::get('/propietarios', [Propietario::class, 'obtenerTodos']);
Route::put('/propietarios/{id}', [Propietario::class, 'actualizarDatos']);
Route::post('/carnet', [CarnetController::class, 'register']);
<<<<<<< HEAD
Route::get('/carnet', [CarnetController::class, 'listcarnet']);
Route::post('/multa', [MultaController::class, 'registerMulta']);
Route::post('/licencia', [LicenciaController::class, 'register']);
Route::get('/licencia/{dni}', [LicenciaController::class, 'obtnerlicencia']);
Route::get('/carnet/{dni}',[CarnetController::class, 'obtenercarnet']);

=======
Route::post('/refresh', [AuthController::class, 'refresh']);
>>>>>>> 81c481cc1c815c46bce2c08622903c5433bbf141
