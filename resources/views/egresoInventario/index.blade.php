{{-- resources/views/egresoInventarios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de ingresos | Sistema Veterinario')

@section('content')
    <div class="egreso-container">
        <!-- Header Section -->
        <div class="egreso-header">
            <h1>Egreso de Inventarios</h1>

            <button type="button" class="btn-add" onclick="egresoController.openModal()">
                <i class="fas fa-plus"></i> Nuevo Egreso
            </button>
        </div>

        <!-- Table Section -->
        <div class="egreso-card">
            <div class="table-container">
                <table class="egreso-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Cantidad de egreso</th>
                            <th>Tipo de Egreso</th>
                            <th>Glosa</th>

                            <th>fecha</th>




                        </tr>
                    </thead>
                    <tbody>
                        @forelse($egresos as $egreso)
                            <tr>
                                <td>{{ $egreso->id }}</td>
                                <td>{{ $egreso->usuario->name }}</td>

                                @foreach($egreso->detalles as $detalle)

                                    <!-- Here's how you access the product name -->
                                    <td> {{ $detalle->productoAlmacen->producto->nombre }}</td>
                                    <td>{{ $detalle->cantidad }}</td>

                            @endforeach



                            <td>
                                {{ $egreso->tipo == '1' ? 'Vencimiento' :
                                   ($egreso->tipo == '2' ? 'Renovacion' :
                                   ($egreso->tipo == '3' ? 'Otro' : 'Venta')) }}
                            </td>
                                <td>{{ $egreso->glosa }}</td>
                                <td>{{ $egreso->fecha }}</td>
                                <td>
                                    <div class="action-buttons">
                                        {{-- <button class="btn-edit" onclick="egresoController.openModal({{ $egreso->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button> --}}
                                        {{-- <button type="button"
                                            class="btn-delete"
                                            onclick="egresoController.delete({{ $egreso->id }})"
                                            title="Eliminar egreso">
                                        <i class="fas fa-trash"></i>
                                    </button> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-paw"></i>
                                        <p>No hay ingresos registradas</p>
                                        <button type="button" class="btn-add" onclick="egresoController.openModal()">
                                            Agregar Primera Ingreso
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
    <div class="modal" id="mascotaModal" tabindex="-1">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Nuevo Egreso</h2>
                <button type="button" class="btn-close" onclick="egresoController.closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="mascotaForm" class="egreso-form" novalidate>
                @csrf
                <input type="hidden" id="egreso_id" name="id">
                <div class="modal-body">


                    <!-- Información Principal -->
                    <div class="form-section form-grid">


                        <div class="form-group">
                            <label for="ingreso_id">Producto</label>
                            <select id="ingreso_id" name="ingreso_id" required>
                                <option value="">Seleccionar Producto</option>
                                @foreach ($ingresos as $ingreso)
                                    <option value="{{ $ingreso->id }}">
                                        {{ $ingreso->producto->nombre }} => {{ $ingreso->stock }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo de Egreso</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccionar Tipo de Egreso</option>
                                <option value="1">Vencimiento</option>
                                <option value="2">Renovacion</option>

                                <option value="3">Otro</option>
                            </select>
                            <span class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label for="stock">Cantidad de stock</label>
                            <input type="number" id="stock" name="stock" required min="0" max="100">
                            <span class="error-message"></span>
                        </div>

                        <div class="form-section form-grid">
                            <div class="form-group">
                                <label for="glosa">Glosa</label>
                                <input type="text" id="glosa" name="glosa" required maxlength="255"
                                    autocomplete="off">
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" onclick="egresoController.closeModal()">
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
    <x-app.footer />
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const egresoController = {
        modal: null,
        form: null,
        currentId: null,
        imagePreview: null,
        submitButton: null,
        isSubmitting: false,

        init() {
            this.modal = document.getElementById('mascotaModal');
            this.form = document.getElementById('mascotaForm');
            this.imagePreview = document.getElementById('imagePreview');
            this.submitButton = this.form.querySelector('button[type="submit"]');

            this.setupEventListeners();
        },

        setupEventListeners() {
            // Manejo del formulario
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                console.log('Formulario enviado');

                // Debug: Mostrar valores actuales
                const formData = new FormData(this.form);
                for (let [key, value] of formData.entries()) {
                    console.log(`Campo ${key}:`, value);
                }

                if (!this.isSubmitting) {
                    this.handleSubmit(e);
                }
            });

            // Agregar listeners para los selects
            const selects = this.form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', (e) => {
                    console.log(`Select ${e.target.id} cambió a:`, e.target.value);
                    // Limpiar error si existe
                    select.classList.remove('is-invalid');
                    const errorElement = select.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        errorElement.textContent = '';
                    }
                });
            });
        },

        openModal(id = null) {
            this.currentId = id;
            this.resetForm();

            const title = this.modal.querySelector('.modal-title');
            title.textContent = id ? 'Editar Engreso' : 'Nuevo Egreso';

            if (id) {
                this.loadMascotaData(id);
            }

            this.modal.classList.add('active');
        },

        closeModal() {
            this.modal.classList.remove('active');
            this.resetForm();
        },

        resetForm() {
            this.form.reset();

            // Asegurarnos de que los selects vuelvan a su estado inicial
            const selects = this.form.querySelectorAll('select');
            selects.forEach(select => {
                select.selectedIndex = 0;
            });

            this.clearErrors();
            this.enableSubmitButton();
            this.currentId = null;
        },

        async loadMascotaData(id) {
            try {
                const response = await fetch(`/egresoInventarios/${id}`);
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

        fillFormData(data) {
            document.getElementById('egreso_id').value = data.id;
            document.getElementById('ingreso_id').value = data.ingreso_id; /// id producto de almacen
            document.getElementById('tipo').value = data.tipo; /// id producto de almacen
            document.getElementById('glosa').value = data.glosa; /// id producto de almacen
            document.getElementById('stock').value = data.stock; /// cantidad de stock a egresar


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

                const egresoId = document.getElementById('egreso_id').value;


                let url = '/egresoInventarios';
                let method = 'POST';

                if (egresoId) {
                    url = `/egresoInventarios/${egresoId}`;
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

                if (!response.ok) {
                    if (response.status === 422) {
                        Object.keys(result.errors).forEach(field => {
                            this.showFieldError(field, result.errors[field][0]);
                        });
                        throw new Error('por favor completa los campos.');
                    }
                    throw new Error(result.message || 'Error');
                }

                await Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                this.closeModal();
                setTimeout(() => window.location.reload(), 1500);

            } catch (error) {
                console.log("error ", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:error
                });
            } finally {
                this.enableSubmitButton();
            }
        },

        validateForm() {
            let isValid = true;
            const requiredFields = {
                'ingreso_id': 'Seleccione un producto',
                'glosa': 'escriba una glosa',
                'tipo': 'Seleccione un tipo de egreso',
                'stock': 'Ingrese la cantidad a dar de baja'
            };

            // Primero, vamos a hacer un debug para ver qué valores estamos obteniendo
            Object.entries(requiredFields).forEach(([field, message]) => {
                const input = document.getElementById(field);

                // Obtenemos el valor dependiendo del tipo de input
                let value;
                if (input.tagName.toLowerCase() === 'select') {
                    // Para selects, verificamos el selectedIndex
                    value = input.options[input.selectedIndex]?.value;
                    console.log(`Select ${field} valor:`, value, 'índice seleccionado:', input
                        .selectedIndex);
                } else if (input.type === 'number') {
                    // Para inputs numéricos
                    value = input.value !== '' ? Number(input.value) : '';
                    console.log(`Number ${field} valor:`, value);
                } else {
                    // Para inputs de texto
                    value = input.value.trim();
                    console.log(`Text ${field} valor:`, value);
                }

                // Validación específica por tipo de campo
                let isFieldValid = false;
                if (input.tagName.toLowerCase() === 'select') {
                    isFieldValid = value && value !== '';
                } else if (input.type === 'number') {
                    isFieldValid = value !== '' && !isNaN(value) && value > 0;
                } else {
                    isFieldValid = value !== '';
                }

                if (!isFieldValid) {
                    this.showFieldError(field, message);
                    isValid = false;
                }
            });

            return isValid;
        },

        async delete(id) {
            try {
                const result = await Swal.fire({
                    title: '¿Eliminar egreso?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (!result.isConfirmed) return;

                const response = await fetch(`/egresoInventarios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) throw new Error(data.message || 'Error al eliminar');

                Swal.fire({
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
        }
    };

    // Inicializar el controlador cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        egresoController.init();
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
    .egreso-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .egreso-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .egreso-header h1 {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Estilos de la Tabla */
    .egreso-card {
        background-color: var(--card-background);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .egreso-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .egreso-table th {
        background-color: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .egreso-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .egreso-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Imágenes de ingresos */
    .egreso-image-container {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .egreso-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
        padding: 0.625rem 1.25rem;
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
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background-color: #f3f4f6;
        color: var(--text-primary);
    }

    .btn-edit:hover {
        background-color: #e5e7eb;
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
        z-index: 1001;
    }

    .modal-content {
        position: relative;
        width: 100%;
        max-width: 600px;
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

    /* Estilos del Formulario */
    .egreso-form {
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        padding: 1.25rem;
    }

    .form-section {
        margin-bottom: 1.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-primary);
    }

    .form-group input,
    .form-group select {
        padding: 0.625rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Subida de Imágenes */
    .image-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .preview-container {
        width: 150px;
        height: 150px;
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 2px dashed var(--border-color);
    }

    .preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .file-input-container {
        position: relative;
        width: 100%;
        max-width: 200px;
    }

    .file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem;
        background-color: #f3f4f6;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-label:hover {
        background-color: #e5e7eb;
    }

    /* Footer del Modal */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.25rem;
        border-top: 1px solid var(--border-color);
    }

    .btn-cancel {
        padding: 0.625rem 1.25rem;
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
        padding: 0.625rem 1.25rem;
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

    /* Estado de Carga */
    .button-loader {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Estado Vacío */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 2.5rem;
    }

    /* Mensajes de Error */
    .error-message {
        font-size: 0.875rem;
        color: var(--danger-color);
        min-height: 1.25rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .egreso-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .modal-content {
            margin: 1rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        .action-buttons {
            flex-direction: row;
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
            transform: translateY(-10px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal.active .modal-content {
        animation: slideIn 0.3s ease forwards;
    }

    .modal.active .modal-backdrop {
        animation: fadeIn 0.3s ease forwards;
    }
</style>
