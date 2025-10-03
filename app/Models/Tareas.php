<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Keyword;
class Tareas extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    protected $fillable = ['nombre', 'descripcion', 'is_done', 'usuario_id'];

    protected $casts = ['is_done' => 'boolean'];

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_tarea', 'tarea_id', 'keyword_id');
    }
}
