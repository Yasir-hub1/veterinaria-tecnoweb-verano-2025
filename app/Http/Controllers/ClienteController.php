<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();

        return view('clientes.index', compact('clientes'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clientes',
                'razon_social' => 'required',
                'celular' => 'required|string',
                'nit' => 'required',
                'genero' => 'required',
                 'direccion' => 'required',
                 'tipo' => 'required',
            ]);

            $cliente = Cliente::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'razon_social' =>$validated['razon_social'],
                'celular' => $validated['celular'],
                'nit' => $validated['nit'],
                'genero' => $validated['genero'],
                'direccion' => $validated['direccion'],
                'tipo' =>  $validated['tipo'],
            ]);

            // Asignar rol por defecto según el tipo


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'user' => $cliente
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
        $cliente = Cliente::findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $cliente = Cliente::find($id);
            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clientes',
                'razon_social' => 'required|string|unique:clientes',
                'celular' => 'required|string',
                'nit' => 'required',
                'genero' => 'required',
                 'direccion' => 'required',
                 'tipo' => 'required',
            ]);






            $cliente->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',

            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

             $cliente=Cliente::find($id);
            // En lugar de eliminar, desactivamos el cliente
            $cliente->delete();



            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }




}
