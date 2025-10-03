<?php
use App\Http\Controllers\TareasController;
use Illuminate\Support\Facades\Route;


Route::prefix('api')->middleware('api')->group(function () {
    require base_path('routes/tareasRoutes.php');
    Route::prefix('usuarios')->group(base_path('routes/usuariosRoutes.php')); // /api/usuarios/...
});

Route::view('/', 'pages.home');