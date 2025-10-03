<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- IMPORTANTE
use App\Models\Tareas;

class TareasController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);

        $tareas = Tareas::with('keywords')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($tareas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => ['required', 'string', 'max:255'],
            'descripcion'   => ['nullable', 'string'],
            'keyword_id'    => ['nullable', 'integer', Rule::exists('keywords', 'id')],
            'keyword_ids'   => ['nullable', 'array'],
            'keyword_ids.*' => ['integer', Rule::exists('keywords', 'id')],
        ]);

        $tarea = Tareas::create([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'is_done'     => false,
        ]);

        $ids = collect($data['keyword_ids'] ?? []);
        if (!empty($data['keyword_id'])) {
            $ids->push((int) $data['keyword_id']);
        }
        $ids = $ids->unique()->values();

        if ($ids->isNotEmpty()) {
            $tarea->keywords()->syncWithoutDetaching($ids->all());
        }

        return response()->json($tarea->load('keywords'), 201);
    }

    // ✅ update RESTful (JSON)
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre'      => ['sometimes','string','max:255'],
            'descripcion' => ['sometimes','nullable','string'],
            'keyword_ids' => ['sometimes','array'],
            'keyword_ids.*' => ['integer', Rule::exists('keywords','id')],
        ]);

        $tarea = Tareas::findOrFail($id);
        $tarea->fill($request->only('nombre','descripcion'))->save();

        if ($request->has('keyword_ids')) {
            $tarea->keywords()->sync($data['keyword_ids'] ?? []);
        }

        return response()->json($tarea->load('keywords'));
    }

    public function toggle($id)
    {
        $tarea = Tareas::findOrFail($id);
        $tarea->is_done = !$tarea->is_done;
        $tarea->save();

        return response()->json($tarea);
    }

    public function delete($id)
    {
        $tarea = Tareas::find($id);
        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }
        $tarea->delete();
        return response()->json(['message' => 'Tarea eliminada']);
    }
}
