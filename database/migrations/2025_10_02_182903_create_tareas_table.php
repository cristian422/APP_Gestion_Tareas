<?php

// database/migrations/2025_10_02_000001_create_tareas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
   Schema::create('tareas', function (Blueprint $t) {
    $t->id();
    $t->string('nombre');
    $t->text('descripcion')->nullable();
    $t->boolean('is_done')->default(false);
    $t->timestamps();
    });
    }
    public function down(): void {
        Schema::dropIfExists('tareas');
    }
};
