<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Veterinaria</title>
    {{-- <link rel="stylesheet" href="{{asset("css/all.min.css")}}"> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <i class="fas fa-clinic-medical"></i>
                    <h2>Sistema Veterinaria</h2>
                </div>
            </div>

            <div class="nav-sections">
                <!-- Módulo 1: Gestión de User -->
                @if (auth()->user()->hasAnyPermission([
                            'guardar_usuario',
                            'editar_usuario',
                            'eliminar_usuario',
                            'guardar_rol',
                            'editar_rol',
                            'eliminar_rol',
                        ]))
                    <div class="nav-module">
                        <div class="module-header">
                            <i class="fas fa-users-cog"></i>
                            <h3>Gestión de Usuario</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <nav class="nav-menu">
                            @if (auth()->user()->hasAnyPermission(['guardar_usuario', 'editar_usuario', 'eliminar_usuario']))
                                <a href="{{ route('usuarios.index') }}" class="nav-item">
                                    <i class="fas fa-users"></i>
                                    <span>Usuarios</span>
                                </a>
                            @endif

                            @if (auth()->user()->hasAnyPermission(['guardar_rol', 'editar_rol', 'eliminar_rol']))
                                <a href="{{ route('roles.index') }}" class="nav-item">
                                    <i class="fas fa-users"></i>
                                    <span>Roles y Permisos</span>
                                </a>
                                <a href="{{ route('asignacion-roles.index') }}" class="nav-item">
                                    <i class="fas fa-users"></i>
                                    <span>Asignación de roles</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                @endif

                <!-- Módulo 2: Gestión de Mascotas -->
                @if (auth()->user()->hasAnyPermission(['guardar_mascota', 'editar_mascota', 'eliminar_mascota']))
                    <div class="nav-module">
                        <div class="module-header">
                            <i class="fas fa-paw"></i>
                            <h3>Gestión de Mascotas</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <nav class="nav-menu">
                            <a href="{{ route('mascotas.index') }}" class="nav-item">
                                <i class="fas fa-paw"></i>
                                <span>Mascotas</span>
                            </a>
                            <a href="{{ route('clientes.index') }}" class="nav-item">
                                <i class="fas fa-users"></i>
                                <span>Clientes</span>
                            </a>
                            <a href="{{ route('servicios.index') }}" class="nav-item">
                                <i class="fas fa-stethoscope"></i>
                                <span>Servicios</span>
                            </a>
                        </nav>
                    </div>
                @endif

                <!-- Módulo 3: Gestión de Inventario -->
                @if (auth()->user()->hasAnyPermission([
                            'ver_ajuste_inventario',
                            'editar_almacen',
                            'eliminar_almacen',
                            'guardar_almacen',
                            'editar_producto',
                            'eliminar_producto',
                            'guardar_producto',
                        ]))
                    <div class="nav-module">
                        <div class="module-header">
                            <i class="fas fa-boxes"></i>
                            <h3>Gestión de Inventario</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <nav class="nav-menu">
                            @if (auth()->user()->hasAnyPermission(['editar_producto', 'eliminar_producto', 'guardar_producto']))
                                <a href="{{ route('productos.index') }}" class="nav-item">
                                    <i class="fas fa-box"></i>
                                    <span>Productos</span>
                                </a>
                            @endif
                            {{-- <a href="{{ route('inventarios.index') }}" class="nav-item">
                            <i class="fas fa-warehouse"></i>
                            <span>Ingreso de Inventario</span>
                        </a> --}}
                            @if (auth()->user()->hasAnyPermission(['ver_ajuste_inventario']))
                                <a href="{{ route('ajusteInventarios.index') }}" class="nav-item">
                                    <i class="fas fa-warehouse"></i>
                                    <span>Ajuste de Inventario</span>
                                </a>
                            @endif
                            <a href="{{ route('almacenes.index') }}" class="nav-item">
                                <i class="fas fa-store"></i>
                                <span>Almacenes</span>
                            </a>
                        </nav>
                    </div>
                @endif

                <!-- Módulo 4: Gestión de Ventas -->
                @if (auth()->user()->hasAnyPermission(['guardar_venta', 'editar_venta', 'eliminar_venta']))
                    <div class="nav-module">
                        <div class="module-header">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Gestión de Ventas</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <nav class="nav-menu">
                            <a href="{{ route('notaVentas.index') }}" class="nav-item">
                                <i class="fas fa-cash-register"></i>
                                <span>Ventas</span>
                            </a>
                            <a href="{{ route('pagos.index') }}" class="nav-item">
                                <i class="fas fa-cash-register"></i>
                                <span>Pagos</span>
                            </a>
                            <a href="{{ route('ordenServicios.index') }}" class="nav-item">
                                <i class="fas fa-file-medical"></i>
                                <span>Ordenes de servicios</span>
                            </a>
                        </nav>
                    </div>
                @endif

                <!-- Módulo 5: Reportes y Estadísticas -->
                @if (auth()->user()->hasPermission('ver_reporte_venta'))
                    <div class="nav-module">
                        <div class="module-header">
                            <i class="fas fa-chart-line"></i>
                            <h3>Reportes y Estadísticas</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <nav class="nav-menu">
                            <a href="{{ route('reportes.index') }}" class="nav-item">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reporte de venta</span>
                            </a>
                        </nav>
                    </div>
                @endif

                <div class="theme-selector">
                    <button onclick="setTheme('kids')">Niños</button>
                    <button onclick="setTheme('youth')">Jóvenes</button>
                    <button onclick="setTheme('adults')">Adultos</button>
                    <button onclick="setTheme('auto')">Modo Automático</button>
                </div>

                <!-- Cerrar Sesión siempre visible -->
                <div class="nav-bottom">
                    <a href="{{ route('logout') }}" class="nav-item logout"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </aside>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Module accordion functionality
            const moduleHeaders = document.querySelectorAll('.module-header');

            moduleHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const module = this.parentElement;
                    const menu = module.querySelector('.nav-menu');
                    const chevron = this.querySelector('.fa-chevron-down');

                    // Toggle active state
                    module.classList.toggle('active');

                    // Animate menu
                    if (menu.style.display === 'block') {
                        menu.style.display = 'none';
                        chevron.style.transform = 'rotate(0deg)';
                    } else {
                        menu.style.display = 'block';
                        chevron.style.transform = 'rotate(180deg)';
                    }
                });
            });

            // Mark current page as active
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-item').forEach(item => {
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('active');
                    // Expand parent module
                    const parentModule = item.closest('.nav-module');
                    if (parentModule) {
                        parentModule.classList.add('active');
                        parentModule.querySelector('.nav-menu').style.display = 'block';
                        parentModule.querySelector('.fa-chevron-down').style.transform = 'rotate(180deg)';
                    }
                }
            });
        });
    </script>
</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const savedTheme = localStorage.getItem("theme") || "auto";
        setTheme(savedTheme);
    });

    function setTheme(theme) {
        if (theme === "auto") {
            const hour = new Date().getHours();
            theme = hour >= 18 || hour < 6 ? "night" : "adults";
        }

        document.body.className = theme;
        localStorage.setItem("theme", theme);
    }
</script>
<style>
    :root {
        --primary-color: #1a237e;
        --secondary-color: #283593;
        --hover-color: #3949ab;
        --text-color: #ffffff;
        --background-color: #f5f5f5;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .sidebar {
        background: var(--primary-color);
    }

    /* Tema Niños */
    .kids {
        --primary-color: #ffcc00;
        --secondary-color: #ff9900;
        --hover-color: #ff6600;
        --background-color: #fff8e1;
        --text-color: #000;
    }

    /* Tema Jóvenes */
    .youth {
        --primary-color: #009688;
        --secondary-color: #00796b;
        --hover-color: #004d40;
        --background-color: #e0f2f1;
        --text-color: #000;
    }

    /* Tema Adultos */
    .adults {
        --primary-color: #37474f;
        --secondary-color: #263238;
        --hover-color: #455a64;
        --background-color: #eceff1;
        --text-color: #000;
    }

    /* Modo Automático (Día/Noche) */
    .night {
        --background-color: #121212;
        --text-color: #fff;
    }
    .theme-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            justify-content: center;
            align-items: center;
            max-width: 200px;
            margin: 20px auto;
        }
        .theme-selector button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            text-align: center;
        }
        .theme-selector button:hover {
            transform: scale(1.05);
        }
        .theme-selector button:nth-child(1) {
            background-color: #ffcc00;
            color: #000;
        }
        .theme-selector button:nth-child(2) {
            background-color: #009688;
            color: #fff;
        }
        .theme-selector button:nth-child(3) {
            background-color: #37474f;
            color: #fff;
        }
        .theme-selector button:nth-child(4) {
            background-color: #607d8b;
            color: #fff;
        }
</style>
<style>
    :root {
        --primary-color: #1a237e;
        --secondary-color: #283593;
        --hover-color: #3949ab;
        --text-color: #ffffff;
        --text-muted: #b0bec5;
        --sidebar-width: 280px;
        --transition-speed: 0.3s;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', 'Arial', sans-serif;
        background-color: #f5f5f5;
    }

    .dashboard {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: var(--sidebar-width);
        background: var(--primary-color);
        color: var(--text-color);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
        transition: all var(--transition-speed) ease;
    }

    .sidebar-header {
        padding: 1.5rem;
        background: var(--secondary-color);
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .logo-container i {
        font-size: 2rem;
    }

    .logo-container h2 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 500;
    }

    .nav-sections {
        padding: 1rem 0;
    }

    .module-header {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        transition: background-color var(--transition-speed);
    }

    .module-header:hover {
        background-color: var(--hover-color);
    }

    .module-header i {
        font-size: 1.1rem;
        margin-right: 0.75rem;
    }

    .module-header h3 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 500;
        flex-grow: 1;
    }

    .module-header .fa-chevron-down {
        font-size: 0.8rem;
        transition: transform var(--transition-speed);
    }

    .nav-menu {
        display: none;
        padding: 0.5rem 0;
    }

    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem 0.75rem 3rem;
        color: var(--text-muted);
        text-decoration: none;
        transition: all var(--transition-speed);
    }

    .nav-item:hover {
        background-color: var(--hover-color);
        color: var(--text-color);
    }

    .nav-item i {
        font-size: 1rem;
        margin-right: 1rem;
        width: 20px;
        text-align: center;
    }

    .nav-item span {
        font-size: 0.9rem;
    }

    .nav-bottom {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 1rem 0;
        background: var(--secondary-color);
    }

    .logout {
        color: #ff5252;
    }

    .logout:hover {
        background-color: #ff5252;
        color: var(--text-color);
    }

    /* Active states */
    .nav-module.active .module-header {
        background-color: var(--hover-color);
    }

    .nav-module.active .fa-chevron-down {
        transform: rotate(180deg);
    }

    .nav-item.active {
        background-color: var(--hover-color);
        color: var(--text-color);
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            z-index: 1000;
        }

        .sidebar.active {
            transform: translateX(0);
        }
    }
</style>
