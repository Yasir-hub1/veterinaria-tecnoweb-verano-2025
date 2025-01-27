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


    <x-app.footer />
@endsection
<script>
    // reportes.js
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha en formato 'YYYY-MM-DD'

        const form = document.getElementById('reporteForm');
        const resultadosDiv = document.getElementById('resultados');
        const exportarBtn = document.getElementById('exportarBtn');
         // Establecer el valor de fecha_inicio y fecha_fin a la fecha actual
    document.getElementById('fecha_inicio').value = today;
    document.getElementById('fecha_fin').value = today;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                // Mostrar loader
                resultadosDiv.innerHTML = '<div class="loader">Cargando...</div>';

                const formData = new FormData(form);
                const response = await fetch("{{ route('reportes.generar') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Error en la petición');
                }

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                actualizarResultados(data);

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al generar el reporte: ' + error.message
                });
            }
        });

        exportarBtn.addEventListener('click', function() {
            const form = document.getElementById('reporteForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            window.location.href = `{{ route('reportes.exportar') }}?${params.toString()}`;
        });

        function actualizarResultados(data) {
            const {
                ventas,
                resumen,
                pagos_por_tipo
            } = data;

            // Actualizar resumen
            actualizarResumen(resumen);

            // Actualizar tabla de ventas
            actualizarTablaVentas(ventas);

            // Hacer visible la sección de resultados
            resultadosDiv.style.display = 'block';
        }

        function actualizarTablaVentas(ventas) {
            const tabla = document.createElement('table');
            tabla.classList.add('tabla-ventas');

            // Crear encabezados
            const thead = document.createElement('thead');
            thead.innerHTML = `
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Usuario</th>
                <th>Tipo Pago</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        `;
            tabla.appendChild(thead);

            // Crear cuerpo de la tabla
            const tbody = document.createElement('tbody');
            ventas.forEach(venta => {
                const row = document.createElement('tr');
                const tipoPago = venta.tipopago == 1 ? 'QR' : (venta.tipopago == 2 ? 'Tigo Money' : 'Otro');
                row.innerHTML = `
            <td>${venta.id}</td>
            <td>${new Date(venta.fecha).toLocaleDateString()}</td>
            <td>${venta.cliente.nombre} ${venta.cliente.apellido}</td>
            <td>${venta.usuario.name}</td>
            <td>${tipoPago}</td>
            <td class="text-right">${parseFloat(venta.total).toFixed(2)} Bs</td>
            <td>
                <span class="estado-badge ${Number(venta.estado) === 1 ? 'activo' : 'inactivo'}">
                    ${Number(venta.estado) === 1 ? 'Activo' : 'Inactivo'}
                </span>
            </td>
        `;
                tbody.appendChild(row);
            });
            tabla.appendChild(tbody);

            // Limpiar y actualizar el contenedor de resultados
            const tablaContainer = document.createElement('div');
            tablaContainer.classList.add('tabla-container');
            tablaContainer.appendChild(tabla);

            resultadosDiv.innerHTML = '';
            resultadosDiv.appendChild(tablaContainer);
        }

        function actualizarResumen(resumen) {
            const resumenHtml = `
        <div class="resumen-ventas">
            <div class="resumen-card">
                <span class="resumen-label">Total Ventas</span>
                <span class="resumen-valor">${resumen.total_ventas}</span>
            </div>
            <div class="resumen-card">
                <span class="resumen-label">Monto Total</span>
                <span class="resumen-valor">$${parseFloat(resumen.monto_total).toFixed(2)}</span>
            </div>
            <div class="resumen-card">
                <span class="resumen-label">Promedio</span>
                <span class="resumen-valor">$${parseFloat(resumen.promedio_venta).toFixed(2)}</span>
            </div>
            <div class="resumen-card">
                <span class="resumen-label">Mínimo</span>
                <span class="resumen-valor">$${parseFloat(resumen.venta_minima).toFixed(2)}</span>
            </div>
            <div class="resumen-card">
                <span class="resumen-label">Máximo</span>
                <span class="resumen-valor">$${parseFloat(resumen.venta_maxima).toFixed(2)}</span>
            </div>
        </div>
    `;

            const resumenContainer = document.createElement('div');
            resumenContainer.innerHTML = resumenHtml;
            resultadosDiv.insertBefore(resumenContainer, resultadosDiv.firstChild);
        }
    });
</script>
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
    .space-y-6>*+* {
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
    .loader {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    .tabla-container {
    margin: 2rem 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Estilos para la tabla de ventas */
.tabla-ventas {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
    background: #fff;
}

.tabla-ventas thead {
    background-color: #f8f9fa;
}

.tabla-ventas th {
    padding: 1rem;
    text-align: left;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.tabla-ventas td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
}

.tabla-ventas tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

/* Estilos para los badges de estado */
.estado-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
}

.estado-badge.activo {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.estado-badge.inactivo {
    background-color: #ffebee;
    color: #c62828;
}

/* Estilos para el resumen de ventas */
.resumen-ventas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
    background: #fff;
    margin-bottom: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.resumen-card {
    display: flex;
    flex-direction: column;
    padding: 1.25rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: transform 0.2s ease;
}

.resumen-card:hover {
    transform: translateY(-2px);
}

.resumen-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.resumen-valor {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529;
}

/* Estilos para el loader */
.loader {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

/* Estilos responsive */
@media (max-width: 768px) {
    .tabla-container {
        overflow-x: auto;
    }

    .resumen-ventas {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    .resumen-card {
        padding: 1rem;
    }

    .resumen-valor {
        font-size: 1.25rem;
    }
}
</style>

