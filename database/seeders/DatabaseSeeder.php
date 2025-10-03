<?php

namespace Database\Seeders;
use App\Models\Usuarios;
use App\Models\User;
use App\Models\Tareas;
use App\Models\Keyword;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('email', 'test@example.com')->exists()) {
    User::factory()->create([
        'name'  => 'Test User',
        'email' => 'test@example.com',
    ]);
}

        $usuarios = Usuarios::factory(10)->create();

        $tareas   = Tareas::factory(50)->create();
        $keywords = Keyword::factory(100)->create();

      // asigna 1–4 keywords aleatorias a cada tarea (pivote)
      $tareas->each(function (Tareas $t) use ($keywords) {
         $ids = $keywords->random(rand(1, 4))->pluck('id')->all();
         $t->keywords()->syncWithoutDetaching($ids);
    });
    }
}
