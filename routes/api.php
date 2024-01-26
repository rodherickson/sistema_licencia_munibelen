<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Rubro;
use App\Http\Controllers\Propietario;
use App\Http\Controllers\CarnetController;

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
Route::get('/propietarios', [Propietario::class, 'obtenerTodos']);
Route::get('/propietarios/{id}', [Propietario::class, 'obtenerPorId']);
Route::put('/propietarios/{id}', [Propietario::class, 'actualizarDatos']);
Route::post('/carnet', [CarnetController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refresh']);