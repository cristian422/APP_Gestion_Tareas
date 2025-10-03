<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TareasController;
use App\Http\Controllers\keywordController;

Route::get('/keywords', [KeywordController::class, 'index']);

Route::prefix('tareas')->group(function () {
    Route::get('/',        [TareasController::class, 'index']);        // GET  /api/tareas
    Route::post('/',       [TareasController::class, 'store']);        // POST /api/tareas
    Route::patch('{id}',   [TareasController::class, 'update']);       // PATCH /api/tareas/{id}
    Route::patch('{id}/toggle', [TareasController::class, 'toggle']);  // PATCH /api/tareas/{id}/toggle
    Route::delete('{id}',  [TareasController::class, 'delete']);       // DELETE /api/tareas/{id}
});
