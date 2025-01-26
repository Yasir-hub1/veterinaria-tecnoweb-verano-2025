<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Veterinaria</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #764ba2;
            --secondary-color: #667eea;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f6fa;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            background: var(--primary-color);
            color: white;
            text-align: center;
        }

        .nav-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .nav-item {
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            color: #333;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .nav-item:hover {
            background: #f0f2f5;
            color: var(--primary-color);
        }

        .nav-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f5f6fa;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            background: none;
            border: none;
            color: #333;
            cursor: pointer;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .menu-toggle {
                display: block;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Sistema Veterinaria</h2>
            </div>
            <nav class="nav-menu">
                <a href="#" class="nav-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('usuarios.index') }}" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <a href="{{ route('mascotas.index') }}" class="nav-item {{ Request::is('mascotas*') ? 'active' : '' }}">
                    <i class="fas fa-paw"></i>
                    <span>Mascotas</span>
                </a>
                <a href="{{ route('productos.index') }}" class="nav-item">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
                <a href="{{ route('servicios.index') }}" class="nav-item {{ Request::is('servicios*') ? 'active' : '' }}">
                    <i class="fas fa-stethoscope"></i>
                    <span>Servicios</span>
                </a>
                <a href="{{ route('ordenServicios.index') }}" class="nav-item {{ Request::is('ordenServicios*') ? 'active' : '' }}">
                    <i class="fas fa-stethoscope"></i>
                    <span>Ordenes de servicios</span>
                </a>
                <a href="{{ route('inventarios.index') }}" class="nav-item {{ Request::is('inventarios*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventario</span>
                </a>

                <a href="{{ route('almacenes.index') }}" class="nav-item {{ Request::is('almacenes*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Almacenes</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-cash-register"></i>
                    <span>Ventas</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
                <a href="{{ route('logout') }}" class="nav-item"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </nav>
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

                <div class="card">
                    <div class="card-header">
                        <h3>Citas Pendientes</h3>
                        <div class="card-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <h2>{{ $citasPendientes ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Cerrar el menú al hacer clic fuera en dispositivos móviles
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.querySelector('.menu-toggle');

            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
