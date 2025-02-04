<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            'gestionar_usuarios',
            'gestionar_mascotas',
            'gestionar_productos',
            'gestionar_servicios',
            'gestionar_inventario',
            'gestionar_ventas',
            'gestionar_pagos',
            'ver_reporte_ventas',
        ];

        foreach ($permisos as $permiso) {
            Permiso::create(['nombre' => $permiso]);
        }

        // Crear roles
        $roles = [
            'gerente' => $permisos,
            'veterinario' => ['gestionar_mascotas', 'gestionar_servicios'],
            'recepcionista' => ['gestionar_mascotas', 'gestionar_ventas', 'gestionar_pagos'],
        ];

        foreach ($roles as $nombreRol => $permisosRol) {
            $rol = Role::create(['nombre' => $nombreRol]);
            $permisosIds = Permiso::whereIn('nombre', $permisosRol)->pluck('id');
            $rol->permisos()->attach($permisosIds);
        }

    }
}
