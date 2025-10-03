<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keyword;

class KeywordController extends Controller
{
    /**
     * GET /api/keywords
     * Lista de palabras clave (con búsqueda y paginación opcional)
     */
    public function index(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $perPage = (int) $request->get('per_page', 50); // ajusta si quieres

        $query = Keyword::select('id','name')
            ->when($q !== '', fn($qk) =>
                $qk->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name');

        // Si quieres simple array (sin paginar), envía per_page=0
        if ($perPage === 0) {
            return response()->json($query->get());
        }

        return response()->json($query->paginate($perPage));
    }
}
