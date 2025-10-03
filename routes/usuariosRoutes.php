<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

// /usuarios/{id}
Route::get('{id}', [UsuariosController::class, 'show']);

Route::post('/', [UsuariosController::class, 'update']);

// /usuarios/{id}
Route::delete('{id}', [UsuariosController::class, 'delete']);
