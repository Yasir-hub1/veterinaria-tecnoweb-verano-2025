{{-- resources/views/productos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Productos | Sistema Veterinario')

@section('content')
<div class="producto-container">
    <!-- Header Section -->
    <div class="producto-header">
        <h1>Gestión de Productos</h1>
        <button type="button" class="btn-add" onclick="mascotaController.openModal()">
            <i class="fas fa-plus"></i> Nuevo Producto
        </button>
    </div>

    <!-- Table Section -->
    <div class="producto-card">
        <div class="table-container">
            <table class="producto-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Cliente</th>
                        <th>descripcion</th>
                        {{-- <th>stock</th> --}}
                        <th>precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td>
                                <div class="producto-image-container">
                                    <img src="{{ Storage::url($producto->imagen ?? 'productos/default.png') }}"
                                         alt="Foto de {{ $producto->nombre }}"
                                         class="producto-image">
                                </div>
                            </td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->categoria->nombre }}</td>
                            <td>{{ $producto->descripcion }}</td>
                            {{-- <td>{{ $producto->stock }}</td> --}}
                            <td>{{ $producto->precio }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-edit" onclick="mascotaController.openModal({{ $producto->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                            class="btn-delete"
                                            onclick="mascotaController.delete({{ $producto->id }})"
                                            title="Eliminar producto">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-paw"></i>
                                    <p>No hay productos registradas</p>
                                    <button type="button"
                                            class="btn-add"
                                            onclick="mascotaController.openModal()">
                                        Agregar Primera Producto
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
            <h2 class="modal-title">Nuevo Producto</h2>
            <button type="button" class="btn-close" onclick="mascotaController.closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="mascotaForm" class="producto-form" novalidate>
            @csrf
            <input type="hidden" id="producto_id" name="id">
            <div class="modal-body">
                <!-- Sección de Imagen -->
                <div class="form-section">
                    <div class="image-upload">
                        <div class="preview-container">
                            <img id="imagePreview" src="/storage/productos/default.png"
                                 alt="Vista previa de imagen">
                        </div>
                        <div class="file-input-container">
                            <input type="file"
                                   id="imagen"
                                   name="imagen"
                                   accept="image/*"
                                   class="file-input">
                            <label for="imagen" class="file-label">
                                <i class="fas fa-camera"></i>
                                <span>Seleccionar Foto</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="form-section form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text"
                               id="nombre"
                               name="nombre"
                               required
                               maxlength="255"
                               autocomplete="off">
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="categoria_id">Categorias</label>
                        <select id="categoria_id"
                                name="categoria_id"
                                required>
                            <option value="">Seleccionar Categoria</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <span class="error-message"></span>
                    </div>



                    {{-- <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number"
                               id="stock"
                               name="stock"
                               required
                               maxlength="100"
                               autocomplete="off">
                        <span class="error-message"></span>
                    </div> --}}

                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number"
                               id="precio"
                               name="precio"
                               required
                               min="0"
                               >
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripcion</label>
                        <input type="text"
                               id="descripcion"
                               name="descripcion"
                               required
                               maxlength="255"
                               autocomplete="off">
                        <span class="error-message"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn-cancel"
                        onclick="mascotaController.closeModal()">
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
    const mascotaController = {
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
                if (!this.isSubmitting) {
                    this.handleSubmit(e);
                }
            });

            // Manejo de imagen
            document.getElementById('imagen').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        },

        openModal(id = null) {
            this.currentId = id;
            this.resetForm();

            const title = this.modal.querySelector('.modal-title');
            title.textContent = id ? 'Editar Producto' : 'Nuevo Producto';

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
            this.imagePreview.src = '/storage/productos/default.png';
            this.clearErrors();
            this.enableSubmitButton();
            this.currentId = null;
        },

        async loadMascotaData(id) {
            try {
                const response = await fetch(`/productos/${id}`);
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
            document.getElementById('producto_id').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('categoria_id').value = data.categoria_id;
            document.getElementById('descripcion').value = data.descripcion;
            // document.getElementById('stock').value = data.stock;
            document.getElementById('precio').value = data.precio;

            if (data.imagen) {
                this.imagePreview.src = `/storage/${data.imagen}`;
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

        // Get the current ID from the hidden input
        const mascotaId = document.getElementById('producto_id').value;

        let url = '/productos';
        let method = 'POST';

        if (mascotaId) {
            url = `/productos/${mascotaId}`;
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
                throw new Error('Please check the form fields.');
            }
            throw new Error(result.message || 'Error processing request');
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
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    } finally {
        this.enableSubmitButton();
    }
},

        validateForm() {
            let isValid = true;
            const requiredFields = {
                'categoria_id': 'Seleccione un categoria',
                'nombre': 'Ingrese el nombre',
                'descripcion': 'Ingrese la descripcion',
                // 'stock': 'Ingrese la stock',
                'precio': 'Ingrese la precio'
            };

            Object.entries(requiredFields).forEach(([field, message]) => {
                const input = document.getElementById(field);
                const value = input.value.trim();

                if (!value) {
                    this.showFieldError(field, message);
                    isValid = false;
                }
            });



            return isValid;
        },

        async delete(id) {
            try {
                const result = await Swal.fire({
                    title: '¿Eliminar producto?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (!result.isConfirmed) return;

                const response = await fetch(`/productos/${id}`, {
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
    mascotaController.init();
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
    .producto-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .producto-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .producto-header h1 {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Estilos de la Tabla */
    .producto-card {
        background-color: var(--card-background);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .producto-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .producto-table th {
        background-color: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .producto-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .producto-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Imágenes de Productos */
    .producto-image-container {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .producto-image {
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

    .btn-edit, .btn-delete {
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
    .producto-form {
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
        to { transform: rotate(360deg); }
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
        .producto-header {
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
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal.active .modal-content {
        animation: slideIn 0.3s ease forwards;
    }

    .modal.active .modal-backdrop {
        animation: fadeIn 0.3s ease forwards;
    }
    </style>
