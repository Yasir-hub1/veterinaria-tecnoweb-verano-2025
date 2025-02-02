{{-- resources/views/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de roles | Sistema')

@section('content')
    <div class="servicio-container">
        <div class="servicio-header">
            <h1>Gestión de Roles y Permisos</h1>
            <button type="button" class="btn-add" onclick="roleController.openModal()">
                <i class="fas fa-plus"></i> Nuevo Rol
            </button>
        </div>

        <div class="servicio-card">
            <div class="table-container">
                <table class="servicio-table">
                    <thead>
                        <tr>
                            <th>Nombre del Rol</th>
                            <th>Permisos Asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $rol)
                            <tr>
                                <td>{{ $rol->nombre }}</td>
                                <td>
                                    <div class="permissions-tags">
                                        @foreach ($rol->permisos as $permiso)
                                            <span class="permission-tag">{{ $permiso->nombre }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" onclick="roleController.openModal({{ $rol->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn-delete"
                                            onclick="roleController.delete({{ $rol->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="fas fa-user-shield"></i>
                                        <p>No hay roles registrados</p>
                                        <button type="button" class="btn-add" onclick="roleController.openModal()">
                                            Agregar Primer Rol
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal" id="roleModal" tabindex="-1">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Nuevo Rol</h2>
                <button type="button" class="btn-close" onclick="roleController.closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="roleForm" class="servicio-form" novalidate>
                @csrf
                <input type="hidden" id="role_id" name="id">
                <div class="modal-body">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="nombre">Nombre del Rol</label>
                            <input type="text" id="nombre" name="nombre" required maxlength="255">
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Permisos por Sección</h3>
                        <div class="permissions-grid">
                            @foreach ($seccionesPermisos as $seccion => $permisos)
                                <div class="permission-section">
                                    <h4>{{ $seccion }}</h4>
                                    <div class="permission-options">
                                        @foreach ($permisos as $permiso)
                                            <label class="permission-checkbox">
                                                <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                                    data-seccion="{{ $seccion }}" class="permission-input">
                                                {{ ucfirst(str_replace('_', ' ', $permiso->nombre)) }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="roleController.closeModal()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-save">
                        <span class="button-text">Guardar</span>
                        <span class="button-loader"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const roleController = {
        modal: null,
        form: null,
        currentId: null,
        submitButton: null,
        isSubmitting: false,

        init() {
            this.modal = document.getElementById('roleModal');
            this.form = document.getElementById('roleForm');
            this.submitButton = this.form.querySelector('button[type="submit"]');

            this.setupEventListeners();
        },

        setupEventListeners() {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                if (!this.isSubmitting) {
                    this.handleSubmit(e);
                }
            });
        },

        openModal(id = null) {
            this.currentId = id;
            this.resetForm();

            const title = this.modal.querySelector('.modal-title');
            title.textContent = id ? 'Editar Rol' : 'Nuevo Rol';

            if (id) {
                this.loadRoleData(id);
            }

            this.modal.classList.add('active');
        },

        closeModal() {
            this.modal.classList.remove('active');
            this.resetForm();
        },

        resetForm() {
            this.form.reset();
            this.clearErrors();
            this.enableSubmitButton();
            this.currentId = null;

            // Desmarcar todos los checkboxes de permisos
            this.form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        },

        async loadRoleData(id) {
            try {
                const response = await fetch(`/roles/${id}`);
                const data = await response.json();

                if (!response.ok) throw new Error(data.message || 'Error al cargar datos');

                this.fillFormData(data);
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
                this.closeModal();
            }
        },



        async handleSubmit(event) {
            event.preventDefault();

            try {
                if (!this.validateForm()) {
                    return;
                }

                this.disableSubmitButton();
                const formData = new FormData(this.form);
                const token = document.querySelector('meta[name="csrf-token"]').content;

                const roleId = document.getElementById('role_id').value;

                let url = '/roles';
                let method = 'POST';

                if (roleId) {
                    url = `/roles/${roleId}`;
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                console.log("RESP SAVE ROL ", result);

                if (!response.ok) {
                    if (response.status === 422) {
                        Object.keys(result.errors).forEach(field => {
                            this.showFieldError(field, result.errors[field][0]);
                        });
                        throw new Error('Por favor, revise los campos del formulario.');
                    }
                    throw new Error(result.message || 'Error al procesar la solicitud');
                }

                await Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                this.closeModal();
                setTimeout(() => window.location.reload(), 500);

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            } finally {
                this.enableSubmitButton();
            }
        },



        async delete(id) {
            try {
                const result = await Swal.fire({
                    title: '¿Eliminar rol?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (!result.isConfirmed) return;

                const response = await fetch(`/roles/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) throw new Error(data.message || 'Error al eliminar');

                await Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => window.location.reload(), 1500);

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        },

        showFieldError(field, message) {
            const input = document.getElementById(field);
            if (input) {
                input.classList.add('is-invalid');
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.textContent = message;
                }
            }
        },

        clearErrors() {
            this.form.querySelectorAll('.is-invalid').forEach(input => {
                input.classList.remove('is-invalid');
            });
            this.form.querySelectorAll('.error-message').forEach(span => {
                span.textContent = '';
            });
        },

        disableSubmitButton() {
            this.isSubmitting = true;
            this.submitButton.disabled = true;
            this.submitButton.innerHTML = `
                <span class="button-loader"></span>
                <span class="button-text">Guardando...</span>
            `;
        },

        enableSubmitButton() {
            this.isSubmitting = false;
            this.submitButton.disabled = false;
            this.submitButton.innerHTML = '<span class="button-text">Guardar</span>';
        },
        fillFormData(data) {
            document.getElementById('role_id').value = data.id;
            document.getElementById('nombre').value = data.nombre;

            // Limpiar todos los checkboxes primero
            this.form.querySelectorAll('input[name="permisos[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Marcar los permisos que tiene asignados el rol
            if (data.permisos && Array.isArray(data.permisos)) {
                data.permisos.forEach(permiso => {
                    const checkbox = this.form.querySelector(
                        `input[name="permisos[]"][value="${permiso.id}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        },

        validateForm() {
            let isValid = true;
            const requiredFields = {
                'nombre': 'Ingrese el nombre del rol'
            };

            // Validar campos requeridos
            Object.entries(requiredFields).forEach(([field, message]) => {
                const input = document.getElementById(field);
                const value = input.value.trim();

                if (!value) {
                    this.showFieldError(field, message);
                    isValid = false;
                }
            });

            // Validar que al menos un permiso esté seleccionado por sección
            const secciones = [...new Set(Array.from(this.form.querySelectorAll('input[name="permisos[]"]')).map(
                checkbox => checkbox.dataset.seccion))];

            const seccionesConPermisos = secciones.filter(seccion => {
                const permisosSeccion = this.form.querySelectorAll(
                    `input[name="permisos[]"][data-seccion="${seccion}"]:checked`);
                return permisosSeccion.length > 0;
            });

            if (seccionesConPermisos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Debe seleccionar al menos un permiso en alguna sección'
                });
                isValid = false;
            }

            return isValid;
        },
    };

    // Inicializar el controlador cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        roleController.init();
    });
</script>
<style>
    /* Variables Globales */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --danger-color: #dc2626;
        --danger-hover: #b91c1c;
        --success-color: #059669;
        --success-hover: #047857;
        --background-color: #f9fafb;
        --card-background: #ffffff;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
    }

    /* Reset y Estilos Base */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--background-color);
        color: var(--text-primary);
        line-height: 1.5;
    }

    /* Layout Principal */
    .servicio-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .servicio-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .servicio-header h1 {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Estilos de la Tabla */
    .servicio-card {
        background-color: var(--card-background);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .servicio-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .servicio-table th {
        background-color: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .servicio-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .servicio-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Estilos de Permisos */
    .permissions-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .permission-tag {
        background-color: #eef2ff;
        color: var(--primary-color);
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-full);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding: 1rem;
        background-color: #f8fafc;
        border-radius: var(--radius-md);
    }

    .permission-section {
        background-color: white;
        padding: 1.25rem;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .permission-section h4 {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .permission-options {
        display: grid;
        gap: 0.75rem;
    }

    .permission-checkbox {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        transition: background-color 0.2s;
        cursor: pointer;
    }

    .permission-checkbox:hover {
        background-color: #f3f4f6;
    }

    .permission-input {
        appearance: none;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #d1d5db;
        border-radius: 0.25rem;
        margin-right: 0.75rem;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
    }

    .permission-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .permission-input:checked::after {
        content: '✓';
        position: absolute;
        color: white;
        font-size: 0.875rem;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Botones de Acción */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--primary-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-add:hover {
        background-color: var(--primary-hover);
    }

    .btn-edit,
    .btn-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background-color: #eef2ff;
        color: var(--primary-color);
    }

    .btn-edit:hover {
        background-color: #e0e7ff;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: var(--danger-color);
    }

    .btn-delete:hover {
        background-color: #fecaca;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1001;
    }

    .modal-content {
        position: relative;
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        background-color: var(--card-background);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        z-index: 1002;
        margin: 1rem;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color);
        background-color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .btn-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 0.5rem;
        transition: color 0.2s ease;
    }

    .btn-close:hover {
        color: var(--text-primary);
    }

    /* Formulario */
    .servicio-form {
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .form-section h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-group input[type="text"] {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-group input[type="text"]:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Modal Footer */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.25rem;
        border-top: 1px solid var(--border-color);
        background-color: white;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .btn-cancel {
        padding: 0.75rem 1.5rem;
        background-color: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background-color: #f3f4f6;
    }

    .btn-save {
        padding: 0.75rem 1.5rem;
        background-color: var(--primary-color);
        border: none;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-save:hover {
        background-color: var(--primary-hover);
    }

    /* Estado Vacío */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        padding: 4rem 1rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--text-secondary);
    }

    .empty-state p {
        font-size: 1.125rem;
    }

    /* Mensajes de Error */
    .error-message {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        min-height: 1.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .servicio-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .permissions-grid {
            grid-template-columns: 1fr;
        }

        .modal-content {
            margin: 0;
            max-height: 100vh;
            border-radius: 0;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal.active .modal-content {
        animation: slideIn 0.3s ease forwards;
    }

    .modal.active .modal-backdrop {
        animation: fadeIn 0.3s ease forwards;
    }

    /* Loading State */
    .button-loader {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ffffff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .is-loading .button-loader {
        display: inline-block;
    }

    .is-loading .button-text {
        display: none;
    }
</style>
