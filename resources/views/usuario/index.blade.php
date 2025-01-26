<!-- resources/views/users/index.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema Veterinaria</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Mantener los estilos base del dashboard */
        :root {
            --primary-color: #764ba2;
            --secondary-color: #667eea;
            --sidebar-width: 250px;
        }

        .content-section {
            padding: 20px;
            margin-left: var(--sidebar-width);
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-user-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .users-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .users-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .users-table tbody tr:hover {
            background: #f5f5f5;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .btn-edit {
            background: #ffc107;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-permissions {
            background: var(--primary-color);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-footer {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: black;
        }

        @media (max-width: 768px) {
            .content-section {
                margin-left: 0;
                padding: 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .users-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>


    <div class="content-section">
        <div class="user-header">
            <h1>Gestión de Usuarios</h1>
            <button class="add-user-btn" onclick="openUserModal()">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </button>
        </div>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @foreach($usuario->roles as $role)
                                <span class="badge badge-success">{{ $role->nombre }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $usuario->estado ? 'badge-success' : 'badge-warning' }}">
                                {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-edit" onclick="editUser({{ $usuario->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-permissions" onclick="managePermissions({{ $usuario->id }})">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button class="btn-icon btn-delete" onclick="deleteUser({{ $usuario->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Usuario -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nuevo Usuario</h2>
                <button class="close-modal" onclick="closeUserModal()">&times;</button>
            </div>
            <form id="userForm" onsubmit="handleUserSubmit(event)">
                @csrf
                <input type="hidden" id="userId" name="id">

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="cedula">Cédula</label>
                    <input type="text" id="cedula" name="cedula" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de Usuario</label>
                    <select id="tipo" name="tipo_id" class="form-control" required>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="genero">Género</label>
                    <select id="genero" name="genero" class="form-control" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="O">Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <small>Dejar en blanco para mantener la contraseña actual al editar</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @include('layouts.sidebar')
    <script>
        let currentUserId = null;

        function openUserModal() {
            document.getElementById('userModal').style.display = 'flex';
            document.getElementById('userForm').reset();
            document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
            currentUserId = null;
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function editUser(userId) {
            currentUserId = userId;
            fetch(`/usuarios/${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('userId').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('cedula').value = data.cedula;
                    document.getElementById('celular').value = data.celular;
                    document.getElementById('tipo').value = data.tipo_id;
                    document.getElementById('genero').value = data.genero;
                    document.getElementById('modalTitle').textContent = 'Editar Usuario';
                    openUserModal();
                });
        }

        function handleUserSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const url = currentUserId ? `/usuarios/${currentUserId}` : '/usuarios';
            const method = currentUserId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeUserModal();
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud');
            });
        }

        function deleteUser(userId) {
            if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
                fetch(`/usuarios/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function managePermissions(userId) {
            window.location.href = `/usuarios/${userId}/permisos`;
        }
    </script>


</body>
</html>
