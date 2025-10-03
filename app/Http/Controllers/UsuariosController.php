<?php

namespace App\Http\Controllers;
use App\Models\Usuarios;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{

    public function show($id){
        $usuario = Usuarios::find($id);
        if($usuario){
            return response()->json($usuario);
        }else{
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
    }
    public function update(Request $request){
        $usuario = new Usuarios();
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->telefono = $request->telefono;
        $usuario->save();
        return response()->json($usuario, 201);
        
    }
    public function delete($id){
        $usuario = Usuarios::find($id);
        if($usuario){
            $usuario->delete();
            return response()->json(['message' => 'Usuario eliminado']);
        }else{
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
    }
    
}
