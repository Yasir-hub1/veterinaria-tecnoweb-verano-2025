<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Veterinaria</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>.main-content {
        margin-left: var(--sidebar-width); /* Matches the sidebar width */
        min-height: 100vh;
        background-color: #f5f7fb;
        transition: margin-left var(--transition-speed);
    }

    .header {
        background-color: #ffffff;
        padding: 1rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header h1 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--primary-color);
        font-weight: 500;
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--primary-color);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--primary-color);
        font-weight: 500;
    }

    .user-info i {
        font-size: 1.5rem;
    }

    /* Dashboard Container for Centering */
    .dashboard-container {
        padding: 2rem;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: calc(100vh - 4rem - 64px); /* Subtract header height and padding */
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        width: 100%;
        max-width: 1200px; /* Maximum width for large screens */
        margin: 0 auto;
    }

    .card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .card-icon {
        background: rgba(255, 255, 255, 0.2);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-icon i {
        font-size: 1.5rem;
    }

    .card-body {
        padding: 2rem;
        text-align: center;
    }

    .card-body h2 {
        margin: 0;
        font-size: 2.5rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }

        .menu-toggle {
            display: block;
        }

        .dashboard-cards {
            grid-template-columns: 1fr;
            padding: 1rem;
        }

        .header {
            padding: 1rem;
        }

        .card {
            margin: 0 auto;
            max-width: 400px;
        }
    }
    </style>
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
                <!-- Módulo 1: Gestión de Usuario -->
                <div class="nav-module">
                    <div class="module-header">
                        <i class="fas fa-users-cog"></i>
                        <h3>Gestión de Usuario</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <nav class="nav-menu">
                        <a href="{{ route('usuarios.index') }}" class="nav-item">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </a>
                    </nav>
                </div>

                <!-- Módulo 2: Gestión de Mascotas -->
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
                        <a href="{{ route('servicios.index') }}" class="nav-item">
                            <i class="fas fa-stethoscope"></i>
                            <span>Servicios</span>
                        </a>
                    </nav>
                </div>

                <!-- Módulo 3: Gestión de Inventario -->
                <div class="nav-module">
                    <div class="module-header">
                        <i class="fas fa-boxes"></i>
                        <h3>Gestión de Inventario</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <nav class="nav-menu">
                        <a href="{{ route('productos.index') }}" class="nav-item">
                            <i class="fas fa-box"></i>
                            <span>Productos</span>
                        </a>
                        <a href="{{ route('inventarios.index') }}" class="nav-item">
                            <i class="fas fa-warehouse"></i>
                            <span>Ingreso de Inventario</span>
                        </a>
                        <a href="{{ route('inventarios.index') }}" class="nav-item">
                            <i class="fas fa-warehouse"></i>
                            <span>Egreso de Inventario</span>
                        </a>
                        <a href="{{ route('almacenes.index') }}" class="nav-item">
                            <i class="fas fa-store"></i>
                            <span>Almacenes</span>
                        </a>
                    </nav>
                </div>

                <!-- Módulo 4: Gestión de Ventas -->
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
                        <a href="{{ route('ordenServicios.index') }}" class="nav-item">
                            <i class="fas fa-file-medical"></i>
                            <span>Ordenes de servicios</span>
                        </a>
                    </nav>
                </div>

                <!-- Módulo 5: Reportes y Estadísticas -->
                <div class="nav-module">
                    <div class="module-header">
                        <i class="fas fa-chart-line"></i>
                        <h3>Reportes y Estadísticas</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <nav class="nav-menu">
                        <a href="{{ route('reportes.index') }}" class="nav-item">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reportes</span>
                        </a>
                    </nav>
                </div>

                <!-- Cerrar Sesión at the bottom -->
                <div class="nav-bottom">
                    <a href="{{ route('logout') }}" class="nav-item logout"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="header">
                <button class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard</h1>
                <div class="user-info">
                    {{ Auth::user()->name }}
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <h3>Total Mascotas</h3>
                        <div class="card-icon">
                            <i class="fas fa-paw"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <h2>{{ $totalMascotas ?? 0 }}</h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Ventas del Día</h3>
                        <div class="card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <h2>{{ $ventasHoy ?? 0 }}</h2>
                    </div>
                </div>


            </div>
        </main>
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
