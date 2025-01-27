@extends('layouts.app')

@section('title', 'Gestión de servicios | Sistema Veterinario')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Reporte de Ventas</h1>

            <form id="reporteForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Fecha Inicio -->
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                    </div>

                    <!-- Cliente -->
                    <div>
                        <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select id="cliente_id" name="cliente_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los clientes</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Usuario -->
                    <div>
                        <label for="usuario_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select id="usuario_id" name="usuario_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los usuarios</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Generar Reporte
                    </button>
                    <button type="button" id="exportarBtn"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Exportar CSV
                    </button>
                </div>
            </form>

            <!-- Resultados -->
            <div id="resultados" class="mt-8">
                <!-- Aquí se cargarán los resultados dinámicamente -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('reporteForm');
                const resultadosDiv = document.getElementById('resultados');
                const exportarBtn = document.getElementById('exportarBtn');

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    try {
                        const response = await fetch("{{ route('reportes.generar') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Error en la petición');

                        const data = await response.json();
                        actualizarResultados(data);
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al generar el reporte');
                    }
                });

                exportarBtn.addEventListener('click', function() {
                    const params = new URLSearchParams(new FormData(form));
                    window.location.href = `{{ route('reportes.exportar') }}?${params.toString()}`;
                });

                function actualizarResultados(data) {
                    // Cargar la vista parcial con los resultados
                    resultadosDiv.innerHTML = `
            @include('reportes.resultados-partial')
        `;

                    // Actualizar los datos en la tabla y resumen
                    actualizarTablaVentas(data.ventas);
                    actualizarResumen(data.resumen);
                    actualizarGraficos(data);
                }

                function actualizarTablaVentas(ventas) {
                    const tbody = document.querySelector('#tablaVentas tbody');
                    tbody.innerHTML = '';

                    ventas.forEach(venta => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${venta.id}</td>
                <td class="px-6 py-4 whitespace-nowrap">${new Date(venta.fecha).toLocaleDateString()}</td>
                <td class="px-6 py-4">${venta.cliente.nombre} ${venta.cliente.apellido}</td>
                <td class="px-6 py-4">${venta.usuario.name}</td>
                <td class="px-6 py-4">${venta.tipopago}</td>
                <td class="px-6 py-4 text-right">$${parseFloat(venta.total).toFixed(2)}</td>
                <td class="px-6 py-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        ${venta.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${venta.estado}
                    </span>
                </td>
            `;
                        tbody.appendChild(row);
                    });
                }

                function actualizarResumen(resumen) {
                    document.getElementById('totalVentas').textContent = resumen.total_ventas;
                    document.getElementById('montoTotal').textContent =
                    `$${parseFloat(resumen.monto_total).toFixed(2)}`;
                    document.getElementById('promedioVenta').textContent =
                        `$${parseFloat(resumen.promedio_venta).toFixed(2)}`;
                    document.getElementById('ventaMinima').textContent =
                        `$${parseFloat(resumen.venta_minima).toFixed(2)}`;
                    document.getElementById('ventaMaxima').textContent =
                        `$${parseFloat(resumen.venta_maxima).toFixed(2)}`;
                }

                function actualizarGraficos(data) {
                    // Aquí puedes agregar código para actualizar gráficos si los implementas
                    // Por ejemplo, usando Chart.js o cualquier otra biblioteca de gráficos
                }
            });
        </script>
    @endpush
    <x-app.footer />
<style>
    /* styles.css */

/* Contenedor principal */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem 1rem;
}

/* Card principal */
.bg-white {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

/* Título */
h1 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
    color: #333;
}

/* Formulario */
.space-y-6 > * + * {
    margin-top: 1.5rem;
}

/* Grid para inputs */
.grid {
    display: grid;
    grid-gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 768px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Labels */
label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

/* Inputs y Selects */
input[type="date"],
select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

input[type="date"]:focus,
select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Contenedor de botones */
.flex {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Estilos de botones */
button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.375rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

button[type="submit"] {
    background-color: #4f46e5;
    color: white;
}

button[type="submit"]:hover {
    background-color: #4338ca;
}

#exportarBtn {
    background-color: #10b981;
    color: white;
}

#exportarBtn:hover {
    background-color: #059669;
}

button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Sección de resultados */
#resultados {
    margin-top: 2rem;
}

/* Estilos para estados disabled */
button:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Estilos para inputs requeridos */
input:required:invalid {
    border-color: #ef4444;
}

/* Mejoras de accesibilidad */
@media (prefers-reduced-motion: reduce) {
    button {
        transition: none;
    }
}

/* Estilos de hover para dispositivos que lo soportan */
@media (hover: hover) {
    input:hover,
    select:hover {
        border-color: #9ca3af;
    }
}

/* Estilos para modo oscuro si el sistema lo soporta */
@media (prefers-color-scheme: dark) {
    .bg-white {
        background-color: #1f2937;
        color: #f3f4f6;
    }

    label {
        color: #d1d5db;
    }

    input[type="date"],
    select {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }

    input[type="date"]:focus,
    select:focus {
        border-color: #6366f1;
    }
}
</style>
@endsection



