<!-- resources/views/users/permissions.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos - Sistema Veterinaria</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .permissions-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .module-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .module-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .permission-checkbox {
            margin-right: 10px;
        }

        .permission-label {
            font-size: 14px;
            color: #333;
        }

        .form-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="permissions-container">
        <h2>Gestión de Permisos para {{ $usuario->name }}</h2>
        <form id="permissionsForm">
            @csrf
            <div class="modules-container">
                <!-- Módulo de Usuarios -->
                <div class="module-card">
                    <div class="module-header">
                        <span class="module-title">
                            <i class="fas fa-users"></i> Usuarios
                        </span>
                    </div>
                    <div class="permissions-grid">
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="ver_usuarios" name="permissions[]"
                                   value="ver_usuarios"
                                   {{ $usuario->hasPermission('ver_usuarios') ? 'checked' : '' }}>
                            <label class="permission-label" for="ver_usuarios">Ver Usuarios</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="crear_usuarios" name="permissions[]"
                                   value="crear_usuarios"
                                   {{ $usuario->hasPermission('crear_usuarios') ? 'checked' : '' }}>
                            <label class="permission-label" for="crear_usuarios">Crear Usuarios</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="editar_usuarios" name="permissions[]"
                                   value="editar_usuarios"
                                   {{ $usuario->hasPermission('editar_usuarios') ? 'checked' : '' }}>
                            <label class="permission-label" for="editar_usuarios">Editar Usuarios</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="eliminar_usuarios" name="permissions[]"
                                   value="eliminar_usuarios"
                                   {{ $usuario->hasPermission('eliminar_usuarios') ? 'checked' : '' }}>
                            <label class="permission-label" for="eliminar_usuarios">Eliminar Usuarios</label>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Mascotas -->
                <div class="module-card">
                    <div class="module-header">
                        <span class="module-title">
                            <i class="fas fa-paw"></i> Mascotas
                        </span>
                    </div>
                    <div class="permissions-grid">
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="ver_mascotas" name="permissions[]"
                                   value="ver_mascotas"
                                   {{ $usuario->hasPermission('ver_mascotas') ? 'checked' : '' }}>
                            <label class="permission-label" for="ver_mascotas">Ver Mascotas</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="crear_mascotas" name="permissions[]"
                                   value="crear_mascotas"
                                   {{ $usuario->hasPermission('crear_mascotas') ? 'checked' : '' }}>
                            <label class="permission-label" for="crear_mascotas">Crear Mascotas</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="editar_mascotas" name="permissions[]"
                                   value="editar_mascotas"
                                   {{ $usuario->hasPermission('editar_mascotas') ? 'checked' : '' }}>
                            <label class="permission-label" for="editar_mascotas">Editar Mascotas</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="permission-checkbox"
                                   id="eliminar_mascotas" name="permissions[]"
                                   value="eliminar_mascotas"
                                   {{ $usuario->hasPermission('eliminar_mascotas') ? 'checked' : '' }}>
                            <label class="permission-label" for="eliminar_mascotas">Eliminar Mascotas</label>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Servicios -->
                <div class="module-card">
                    <div class="module-header">
                        <span class="module-title">
                            <i class="fas fa-stethoscope"></i> Servicios
                        </span>
                    </div>
                    <div class="permissions-grid">
                        <!-- Similar structure for servicios permissions -->
                    </div>
                </div>

                <!-- Continúa con otros módulos... -->

            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Permisos</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('permissionsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(`/usuarios/${userId}/permisos`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Permisos actualizados correctamente');
                    window.location.href = '/usuarios';
                } else {
                    alert('Error al actualizar los permisos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar los permisos');
            });
        });
    </script>
</body>
</html>
