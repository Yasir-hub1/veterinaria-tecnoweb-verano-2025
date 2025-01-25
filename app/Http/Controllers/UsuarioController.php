<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\Tipo;
use App\Models\Usuario;
use Illuminate\Http\Request;

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
                'password' => 'required|string|min:8',
                'cedula' => 'required|string|unique:usuarios',
                'celular' => 'required|string',
                'tipo_id' => 'required|exists:tipos,id',
                'genero' => 'required|in:M,F,O'
            ]);

            $usuario = Usuario::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'cedula' => $validated['cedula'],
                'celular' => $validated['celular'],
                'tipo_id' => $validated['tipo_id'],
                'genero' => $validated['genero'],
                'estado' => true
            ]);

            // Asignar rol por defecto según el tipo
            $rolPorDefecto = Role::where('nombre', 'usuario')->first();
            if ($rolPorDefecto) {
                $usuario->roles()->attach($rolPorDefecto->id);
            }

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
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario->load(['roles', 'tipo']));
    }

    public function update(Request $request, Usuario $usuario)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('usuarios')->ignore($usuario->id)],
                'cedula' => ['required', Rule::unique('usuarios')->ignore($usuario->id)],
                'celular' => 'required|string',
                'tipo_id' => 'required|exists:tipos,id',
                'genero' => 'required|in:M,F,O',
                'password' => 'nullable|string|min:8'
            ]);

            $updateData = collect($validated)->except('password')->toArray();

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $usuario->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => $usuario->fresh()->load(['roles', 'tipo'])
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

    public function destroy(Usuario $usuario)
    {
        try {
            DB::beginTransaction();

            // En lugar de eliminar, desactivamos el usuario
            $usuario->update(['estado' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario desactivado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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

    public function asignarRoles(Request $request, Usuario $usuario)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $usuario->roles()->sync($validated['roles']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Roles asignados exitosamente',
                'user' => $usuario->fresh()->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoles(Usuario $usuario)
    {
        return response()->json([
            'success' => true,
            'roles' => $usuario->roles
        ]);
    }

    public function getAllRoles()
    {
        $roles = Role::all();
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    }
}
