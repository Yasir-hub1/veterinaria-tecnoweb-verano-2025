{{-- resources/views/reportes/ventas/resultados-partial.blade.php --}}

<!-- Resumen de Ventas -->
<div class="mb-8">
    <h2 class="text-xl font-semibold mb-4">Resumen de Ventas</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total de Ventas -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Ventas</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900" id="totalVentas">0</dd>
            </div>
        </div>

        <!-- Monto Total -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Monto Total</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900" id="montoTotal">$0.00</dd>
            </div>
        </div>

        <!-- Promedio por Venta -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Promedio por Venta</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900" id="promedioVenta">$0.00</dd>
            </div>
        </div>

        <!-- Venta Mínima -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Venta Mínima</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900" id="ventaMinima">$0.00</dd>
            </div>
        </div>

        <!-- Venta Máxima -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Venta Máxima</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900" id="ventaMaxima">$0.00</dd>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Ventas -->
<div class="mt-8">
    <h2 class="text-xl font-semibold mb-4">Detalle de Ventas</h2>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="tablaVentas">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo Pago
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Los datos se cargarán dinámicamente aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen por Tipo de Pago -->
<div class="mt-8">
    <h2 class="text-xl font-semibold mb-4">Resumen por Tipo de Pago</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tablaPagos">
                        <!-- Los datos se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gráfico de Tipos de Pago -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <canvas id="graficoTiposPago"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Función para actualizar la tabla de tipos de pago
    function actualizarTablaPagos(pagosPorTipo) {
        const tbody = document.querySelector('#tablaPagos');
        tbody.innerHTML = '';

        Object.entries(pagosPorTipo).forEach(([tipo, datos]) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${tipo}</td>
                <td class="px-6 py-4 whitespace-nowrap">${datos.cantidad}</td>
                <td class="px-6 py-4 whitespace-nowrap">$${parseFloat(datos.total).toFixed(2)}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Función para actualizar el gráfico de tipos de pago
    function actualizarGraficoTiposPago(pagosPorTipo) {
        const ctx = document.getElementById('graficoTiposPago').getContext('2d');

        // Destruir el gráfico anterior si existe
        if (window.graficoTiposPago) {
            window.graficoTiposPago.destroy();
        }

        const labels = Object.keys(pagosPorTipo);
        const datos = Object.values(pagosPorTipo).map(d => d.total);

        window.graficoTiposPago = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: datos,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Ventas por Tipo de Pago'
                    }
                }
            }
        });
    }

    // Actualizar los datos cuando se recibe nueva información
    function actualizarDatosReporte(data) {
        actualizarTablaPagos(data.pagos_por_tipo);
        actualizarGraficoTiposPago(data.pagos_por_tipo);
    }
</script>
@endpush
