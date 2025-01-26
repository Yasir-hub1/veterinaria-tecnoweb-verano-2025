<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Sistema Veterinaria</h2>
        </div>
        <nav class="nav-menu">
            <a href="{{ route('dashboard') }}" class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('usuarios.index') }}" class="nav-item {{ Request::is('usuarios*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('mascotas.index') }}" class="nav-item {{ Request::is('mascotas*') ? 'active' : '' }}">
                <i class="fas fa-paw"></i>
                <span>Mascotas</span>
            </a>
            <a href="{{ route('productos.index') }}" class="nav-item {{ Request::is('productos*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Productos</span>
            </a>
            <a href="{{ route('servicios.index') }}" class="nav-item {{ Request::is('servicios*') ? 'active' : '' }}">
                <i class="fas fa-stethoscope"></i>
                <span>Servicios</span>
            </a>
            <a href="{{ route('inventario.index') }}" class="nav-item {{ Request::is('inventario*') ? 'active' : '' }}">
                <i class="fas fa-warehouse"></i>
                <span>Inventario</span>
            </a>
            <a href="{{ route('almacenes.index') }}" class="nav-item {{ Request::is('almacenes*') ? 'active' : '' }}">
                <i class="fas fa-warehouse"></i>
                <span>Almacenes</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="nav-item {{ Request::is('ventas*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i>
                <span>Ventas</span>
            </a>
            {{-- <a href="{{ route('reportes.index') }}" class="nav-item {{ Request::is('reportes*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Reportes</span>
            </a> --}}
            <a href="{{ route('logout') }}" class="nav-item"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </nav>
    </aside>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<style>
    :root {
        --primary-color: #764ba2;
        --secondary-color: #667eea;
        --sidebar-width: 250px;
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
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
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

    .nav-item:hover, .nav-item.active {
        background: #f0f2f5;
        color: var(--primary-color);
    }

    .nav-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            }
        });
    });
</script>
