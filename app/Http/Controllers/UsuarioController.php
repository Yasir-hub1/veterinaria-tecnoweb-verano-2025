<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\Tipo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with(['roles', 'tipo'])->get();
        $tipos = Tipo::all();
        return view('usuario.index', compact('usuarios', 'tipos'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:usuarios',

                'cedula' => 'required|string|unique:usuarios',
                'celular' => 'required|string',
                'tipo_id' => 'required|exists:tipos,id',
                'genero' => 'required'
            ]);

            $usuario = Usuario::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['cedula']),
                'cedula' => $validated['cedula'],
                'celular' => $validated['celular'],
                'tipo_id' => $validated['tipo_id'],
                'genero' => $validated['genero'],
                'estado' => 1
            ]);

            // Asignar rol por defecto según el tipo


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user' => $usuario
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $usuario = Usuario::find($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',

                'cedula' => 'required|string',
                'celular' => 'required|string',
                'tipo_id' => 'required|exists:tipos,id',
                'genero' => 'required'
            ]);



                $request['password'] = Hash::make($request->cedula);


            $usuario->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',

            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

             $usuario=Usuario::find($id);
            // En lugar de eliminar, desactivamos el usuario
            $usuario->update(['estado' => 2]);



            return response()->json([
                'success' => true,
                'message' => 'Usuario desactivado exitosamente'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Usuario $usuario)
    {
        try {
            $usuario->update(['estado' => !$usuario->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del usuario actualizado exitosamente',
                'estado' => $usuario->estado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
