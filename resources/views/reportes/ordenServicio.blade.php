@extends('layouts.app')

@section('title', 'Reporte de Servicios')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Reporte de Servicios Veterinarios</h1>

            <!-- Formulario de Filtros -->
            <form id="reporteForm" class="form-container">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="mascota_id">Mascota</label>
                        <select id="mascota_id" name="mascota_id" class="form-select">
                            <option value="">Todas las mascotas</option>
                            @foreach ($mascotas as $mascota)
                                <option value="{{ $mascota['id'] }}">{{ $mascota['nombre_completo'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn-generar">
                            Generar Reporte
                        </button>
                    </div>
                </div>
            </form>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="chart-container">
                    <h3 class="chart-title">Tendencia de Servicios</h3>
                    <div class="chart-wrapper">
                        <canvas id="serviciosChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title">Servicios por Tipo de Mascota</h3>
                    <div class="chart-wrapper">
                        <canvas id="tiposMascotaChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title">Top 5 Mascotas</h3>
                    <div class="chart-wrapper">
                        <canvas id="mascotasChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title">Métricas Clave</h3>
                    <div id="metricas" class="grid grid-cols-2 gap-4">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div id="tablaResultados" class="overflow-x-auto">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>



@endsection
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let serviciosChart, tiposMascotaChart, mascotasChart;

            // Establecer fechas por defecto
            const hoy = new Date();
            const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            document.querySelector('input[name="fecha_inicio"]').value = inicioMes.toISOString().split('T')[0];
            document.querySelector('input[name="fecha_fin"]').value = hoy.toISOString().split('T')[0];

            function inicializarGraficos() {
                // Gráfico de servicios diarios
                const ctxServicios = document.getElementById('serviciosChart').getContext('2d');
                serviciosChart = new Chart(ctxServicios, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Total Servicios',
                            data: [],
                            borderColor: '#4299e1',
                            backgroundColor: 'rgba(66, 153, 225, 0.1)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Gráfico de tipos de mascota
                const ctxTipos = document.getElementById('tiposMascotaChart').getContext('2d');
                tiposMascotaChart = new Chart(ctxTipos, {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [
                                '#4299e1',
                                '#48bb78',
                                '#ecc94b',
                                '#ed64a6',
                                '#9f7aea'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Gráfico de mascotas
                const ctxMascotas = document.getElementById('mascotasChart').getContext('2d');
                mascotasChart = new Chart(ctxMascotas, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Total Servicios',
                            data: [],
                            backgroundColor: '#4299e1'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            inicializarGraficos();

            // Manejar envío del formulario
            document.getElementById('reporteForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                try {
                    const formData = new FormData(this);
                    const response = await fetch("{{ route('reportesOrdenServicio.generar') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (!response.ok) throw new Error('Error en la petición');

                    const data = await response.json();

                    actualizarGraficos(data);
                    actualizarResumen(data.resumen);
                    actualizarTabla(data.servicios);

                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al generar el reporte: ' + error.message);
                }
            });

            function actualizarGraficos(data) {
                // Actualizar gráfico de servicios
                const serviciosPorDia = data.graficos.servicios_por_dia;
                serviciosChart.data.labels = serviciosPorDia.map(s => s.fecha);
                serviciosChart.data.datasets[0].data = serviciosPorDia.map(s => s.total);
                serviciosChart.update();

                // Actualizar gráfico de tipos
                const serviciosPorTipo = data.graficos.servicios_por_tipo;
                tiposMascotaChart.data.labels = serviciosPorTipo.map(s => s.tipo);
                tiposMascotaChart.data.datasets[0].data = serviciosPorTipo.map(s => s.cantidad);
                tiposMascotaChart.update();

                // Actualizar gráfico de mascotas
                const serviciosPorMascota = data.graficos.servicios_por_mascota;
                mascotasChart.data.labels = serviciosPorMascota.map(s => `${s.mascota} (${s.cliente})`);
                mascotasChart.data.datasets[0].data = serviciosPorMascota.map(s => s.total);
                mascotasChart.update();
            }

            function actualizarResumen(resumen) {
                const resumenHtml = `
            <div class="metric-card bg-blue-100">
                <h4>Total Servicios</h4>
                <p class="metric-value">${resumen.total_servicios}</p>
            </div>
            <div class="metric-card bg-green-100">
                <h4>Monto Total</h4>
                <p class="metric-value">Bs. ${resumen.monto_total}</p>
            </div>
            <div class="metric-card bg-yellow-100">
                <h4>Promedio por Servicio</h4>
                <p class="metric-value">Bs. ${resumen.promedio_servicio.toFixed(2)}</p>
            </div>
            <div class="metric-card bg-purple-100">
                <h4>Total Mascotas</h4>
                <p class="metric-value">${resumen.total_mascotas}</p>
            </div>
        `;
                document.getElementById('metricas').innerHTML = resumenHtml;
            }

            function actualizarTabla(servicios) {
                const tablaHtml = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Mascota</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${servicios.map(servicio => `
                            <tr>
                                <td>${new Date(servicio.fecha).toLocaleDateString()}</td>
                                <td>${servicio.mascota.nombre}</td>
                                <td>${servicio.mascota.cliente.nombre} ${servicio.mascota.cliente.apellido}</td>
                                <td>Bs. ${servicio.total}</td>
                                <td>
                                    <span class="badge ${servicio.estado === '1' ? 'badge-success' : 'badge-warning'}">
                                        ${servicio.estado === '1' ? 'Completado' : 'Pendiente'}
                                    </span>
                                </td>
                            </tr>
                        `).join('')}
                </tbody>
            </table>
        `;
                document.getElementById('tablaResultados').innerHTML = tablaHtml;
            }
        });
    </script>

<style>
    /* Estilos para el contenedor principal */
    .container {
        max-width: 1280px;
        margin: 0 auto;
    }

    /* Estilos para el título principal */
    .text-2xl {
        font-size: 1.75rem;
        color: #1a365d;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    /* Estilos mejorados para los inputs y selects */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .form-group input[type="date"],
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: #2d3748;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    .form-group input[type="date"]:focus,
    .form-group select:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.25);
    }

    /* Estilos para los botones */
    .bg-blue-600 {
        background-color: #3182ce;
        transition: all 0.3s ease;
    }

    .bg-blue-600:hover {
        background-color: #2c5282;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Estilos para las tarjetas de resumen */
    #resumen>div {
        padding: 1.25rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    #resumen>div:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #resumen .text-sm {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    #resumen .text-xl {
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* Estilos mejorados para la tabla */
    .min-w-full {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .min-w-full thead th {
        background-color: #f8fafc;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .min-w-full tbody tr {
        transition: all 0.2s ease;
    }

    .min-w-full tbody tr:hover {
        background-color: #f7fafc;
    }

    .min-w-full tbody td {
        padding: 1rem;
        font-size: 0.875rem;
        color: #2d3748;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Estilos para los badges de estado */
    .rounded-full {
        padding: 0.25rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
        display: inline-block;
    }

    .bg-green-100 {
        background-color: #c6f6d5;
        color: #22543d;
    }

    .bg-red-100 {
        background-color: #fed7d7;
        color: #822727;
    }

    /* Estilos para los contenedores de gráficos */
    .chart-container {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .chart-container:hover {
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.1);
    }

    .chart-container h3 {
        color: #2d3748;
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    /* Responsividad para móviles */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }

        #resumen {
            grid-template-columns: repeat(1, 1fr);
        }

        .min-w-full {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .chart-container {
            margin-bottom: 1.5rem;
        }
    }

    /* Estilos para los tooltips de los gráficos */
    .chartjs-tooltip {
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    /* Animaciones para las cards de resumen */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #resumen>div {
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Estilos para el loader */
    .loader {
        width: 100%;
        height: 4px;
        background-color: #f3f3f3;
        position: relative;
        overflow: hidden;
    }

    .loader::after {
        content: '';
        position: absolute;
        width: 40%;
        height: 100%;
        background-color: #3182ce;
        animation: loading 1s infinite;
    }

    @keyframes loading {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(200%);
        }
    }

    /* Mejoras en la accesibilidad */
    .form-group input:focus,
    .form-group select:focus,
    button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
    }

    /* Estilos para mensajes de error */
    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    /* Estilos para el formato de moneda */
    .currency {
        font-family: 'Roboto Mono', monospace;
        font-weight: 500;
    }

    /* Estilos para los inputs de fecha y selects */
    input[type="date"],
    select {
        width: 100%;
        padding: 0.75rem;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: #1a202c;
        transition: all 0.3s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    input[type="date"]:hover,
    select:hover {
        border-color: #cbd5e0;
    }

    input[type="date"]:focus,
    select:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.25);
    }

    /* Estilos para el botón de generar */
    .btn-generar {
        width: 100%;
        background-color: #4299e1;
        color: #ffffff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-generar:hover {
        background-color: #3182ce;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-generar:active {
        transform: translateY(0);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-generar:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.25);
    }

    /* Estilos para las etiquetas de los inputs */
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
    }

    /* Contenedor del formulario */
    .form-container {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    /* Grid para los inputs */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .form-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Grupo de form */
    .form-group {
        margin-bottom: 1rem;
    }

    /* Estilos para el estado disabled */
    input[type="date"]:disabled,
    select:disabled,
    .btn-generar:disabled {
        background-color: #f7fafc;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Estilos para mensajes de validación */
    .input-error {
        border-color: #fc8181 !important;
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Estilos para el estado de carga */
    .loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .loading::after {
        content: '...';
        animation: loading 1s infinite;
    }

    @keyframes loading {
        0% {
            content: '.';
        }

        33% {
            content: '..';
        }

        66% {
            content: '...';
        }
    }
</style>
