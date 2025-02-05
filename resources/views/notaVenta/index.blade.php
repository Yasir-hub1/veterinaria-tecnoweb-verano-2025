{{-- resources/views/notaVentas/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de notaVentas | Sistema Veterinario')

@section('content')
<!-- index.blade.php -->
<div class="notaVenta-container">
    <!-- Header Section -->
    <div class="notaVenta-header">
        <h1>Gestión de Ventas</h1>
        @if(auth()->user()->hasPermission('guardar_venta'))

        <button type="button" class="btn-add" onclick="window.ordenServicioController.openModal()">
            <i class="fas fa-plus"></i> Nueva Venta
        </button>
        @endif
    </div>

    <!-- Table Section -->
    <div class="notaVenta-card">
        <div class="table-container">
            <table class="notaVenta-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Cliente</th>
                        <th>fecha</th>
                        <th>tipo de pago</th>
                        <th>estado</th>
                        @if(auth()->user()->hasAnyPermission(['editar_venta', 'eliminar_venta']))
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($notaVentas as $notaVenta)
                        <tr>
                            <td>{{ $notaVenta->usuario->name }}</td>
                            <td>{{ $notaVenta->cliente->nombre }}</td>
                            <td>{{ $notaVenta->fecha }}</td>
                            <td>{{ $notaVenta->tipopago == 1 ? 'Pago con QR' : ($notaVenta->tipopago == 2 ? 'Pago con Tigo Money' : 'Método desconocido') }}</td>
                            <td style="color: {{ $notaVenta->estado == 1 ? 'green' : ($notaVenta->estado == 2 ? 'red' : 'black') }}">
                                {{ $notaVenta->estado == 1 ? 'Activo' : ($notaVenta->estado == 2 ? 'Anulado' : 'Error') }}
                            </td>
                            @if(auth()->user()->hasAnyPermission(['editar_venta', 'eliminar_venta']))

                            <td>
                                <div class="action-buttons">
                                    @if(auth()->user()->hasPermission('editar_venta'))

                                    @if ($notaVenta->estado == 1)
                                        <button type="button"
                                            class="btn-delete"
                                            onclick="window.ordenServicioController.delete({{ $notaVenta->id }})"
                                            title="Eliminar notaVenta">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-paw"></i>
                                    <p>No hay notaVentas registradas</p>
                                    @if(auth()->user()->hasPermission('guardar_venta'))
                                    <button type="button"
                                            class="btn-add"
                                            onclick="window.ordenServicioController.openModal()">
                                        Agregar Primera Venta de Producto
                                    </button>
                                    @endif
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
<!-- modal.blade.php -->
<div class="modal" id="ordenServicioModal" tabindex="-1">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Nueva Venta de Producto</h2>
            <button type="button" class="btn-close" onclick="window.ordenServicioController.closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="notaVentaServicioForm" class="notaVenta-form" novalidate>
            @csrf
            <input type="hidden" id="orden_id" name="id">

            <div class="modal-body" id="modalBody">
                <!-- Selección de productos -->
                <div class="form-group">
                    <label for="productoSelect">Seleccionar Producto</label>
                    <select id="productoSelect" name="producto_id">
                        <option value="">Seleccione un producto</option>
                        @foreach ($productos as $producto)
                        <option value="{{ $producto->producto_id }}"
                                data-precio="{{ $producto->producto->precio }}"
                                data-stock="{{ $producto->stock }}">
                            {{ $producto->producto->nombre }} - ${{ $producto->producto->precio }} stock {{$producto->stock}}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="quantity-container" style="margin-top: 10px;">
                    <label for="cantidadInput">Cantidad:</label>
                    <input type="number" id="cantidadInput" name="cantidad" min="1" value="1" class="form-control">
                </div>

                <!-- Selección de Clientes -->
                <div class="form-group">
                    <label for="clienteSelect">Seleccionar Cliente</label>
                    <select id="clienteSelect" name="cliente_id">
                        <option value="">Seleccione un cliente</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" data-cliente="{{ $cliente->nombre }}">
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn-add" onclick="window.ordenServicioController.agregarServicio()">
                    Añadir Producto
                </button>

                <!-- Tabla de productos -->
                <div class="table-container">
                    <table class="orden-producto-table" style="width: 100%; border-collapse: separate; border-spacing: 10px;">
                        <thead>
                            <tr>
                                <th style="padding: 10px; text-align: left;">Producto</th>
                                <th style="padding: 10px; text-align: left;">Cantidad</th>
                                <th style="padding: 10px; text-align: left;">Precio Unitario</th>
                                <th style="padding: 10px; text-align: left;">Subtotal</th>
                                <th style="padding: 10px; text-align: left;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="serviciosSeleccionados"></tbody>
                        <tfoot>
                            <tr>
                                <td style="padding: 10px;"><strong>Total Items:</strong></td>
                                <td style="padding: 10px;" id="totalItems">0</td>
                                <td style="padding: 10px;"><strong>Total:</strong></td>
                                <td style="padding: 10px;" id="totalPrecio">$0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Método de pago -->
                <div class="form-group">
                    <label for="metodoPagoSelect">Método de Pago</label>
                    <select id="metodoPagoSelect" name="tnTipoServicio">
                        <option value="1">Pago con QR</option>
                        <option value="2">Pago con Tigo Money</option>
                    </select>
                </div>

                <button type="button" class="btn-pay" onclick="window.ordenServicioController.generarPago()">
                    Generar Pago
                </button>
            </div>

            <div id="qrContainer" style="display: none; text-align: center; margin-top: 10px;">
                <img id="qrImage" src="" alt="Código QR">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="window.ordenServicioController.closeModal()">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>



<x-app.footer />
@endsection

{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

<script>
// notaVentas.js
window.ordenServicioController = {
    modal: null,
    form: null,
    productos: [],
    cliente: 0,
    total: 0,
    totalItems: 0,

    init() {
        // Inicializar referencias a elementos del DOM
        this.modal = document.getElementById('ordenServicioModal');
        this.form = document.getElementById('notaVentaServicioForm');

        // Verificar que los elementos existan antes de configurar eventos
        if (this.form) {
            this.setupEventListeners();
        } else {
            console.error('Elemento del formulario no encontrado');
        }
    },

    setupEventListeners() {
        // Event listener para el formulario
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // Event listener para el input de cantidad
        const cantidadInput = document.getElementById('cantidadInput');
        if (cantidadInput) {
            cantidadInput.addEventListener('change', (e) => {
                const select = document.getElementById('productoSelect');
                if (select && select.selectedIndex >= 0) {
                    const stock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock')) || 0;
                    const cantidad = parseInt(e.target.value) || 0;

                    if (cantidad < 1) {
                        e.target.value = 1;
                    } else if (cantidad > stock) {
                        e.target.value = stock;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cantidad excede el stock',
                            text: `Solo hay ${stock} unidades disponibles.`
                        });
                    }
                }
            });
        }
    },

    openModal() {
        this.resetForm();
        this.modal.classList.add('active');
    },

    closeModal() {
        this.modal.classList.remove('active');
        document.getElementById('modalBody').style.display = 'block';
        document.getElementById('qrContainer').style.display = 'none';
        document.getElementById('qrImage').src = "";
        location.reload();
        this.resetForm();
    },

    resetForm() {
        if (this.form) {
            this.form.reset();
            this.productos = [];
            this.total = 0;
            this.totalItems = 0;
            this.actualizarTabla();

            const cantidadInput = document.getElementById('cantidadInput');
            if (cantidadInput) {
                cantidadInput.value = 1;
            }
        }
    },

  // Modificación del método generarPago y métodos relacionados
generarPago() {
    const metodoPago = document.getElementById('metodoPagoSelect').value;
    const email = "usuario@example.com";

    if (this.productos.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No hay productos',
            text: 'Debe agregar al menos un producto antes de generar el pago.'
        });
        return;
    }

    if (!this.cliente) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un cliente',
            text: 'Debe seleccionar un cliente antes de generar el pago.'
        });
        return;
    }

    // Preparar el detalle de productos con cantidades y totales
    const detalleProductos = this.productos.map(producto => ({
        id: producto.id,
        nombre: producto.nombre,
        precio_unitario: producto.precio,
        cantidad: producto.cantidad,
        subtotal: producto.subtotal
    }));

    // Crear objeto de resumen
    const resumenVenta = {
        total_items: this.totalItems,
        total_general: this.total,
        detalle_productos: detalleProductos
    };

    fetch("{{ route('pagos.generarCobro.venta') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            tnTipoServicio: metodoPago,
            tnMonto: this.total,
            tcCorreo: email,
            taPedidoDetalle: detalleProductos,    // Array con detalle de productos
            clienteId: this.cliente,
            tipoTransaccion: 1,                   // orden de producto
            resumen: resumenVenta                 // Objeto con totales
        })
    })
    .then(response => response.json())
    .then(data => {
        if (metodoPago == 1) {
            document.getElementById('modalBody').style.display = 'none';
            document.getElementById('qrContainer').style.display = 'block';
            document.getElementById('qrImage').src = data.qrImage;
        } else {
            console.log("RESUMEN DE VENTA:", resumenVenta);
            Swal.fire({
                icon: 'success',
                title: 'Pago Generado',
                text: 'Pago con Tigo Money generado correctamente',
                timer: 1000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        console.error("Error al generar pago:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al generar el pago. Por favor, intente nuevamente.'
        });
    });
},

// Actualizar el método agregarServicio para calcular correctamente los totales
agregarServicio() {
    const select = document.getElementById('productoSelect');
    const cantidadInput = document.getElementById('cantidadInput');
    const mascotaselect = document.getElementById('clienteSelect');

    if (!select || !cantidadInput || !mascotaselect) {
        console.error('Elementos necesarios no encontrados');
        return;
    }

    if (!select.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un producto',
            text: 'Debe elegir un producto antes de agregarlo.'
        });
        return;
    }

    if (!mascotaselect.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un cliente',
            text: 'Debe seleccionar un cliente antes de agregar productos.'
        });
        return;
    }

    const servicioId = select.value;
    const mascotasId = mascotaselect.value;
    const servicioNombre = select.options[select.selectedIndex].text;
    const precio = parseFloat(select.options[select.selectedIndex].getAttribute('data-precio')) || 0;
    const cantidad = parseInt(cantidadInput.value) || 1;
    const stock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock')) || 0;

    if (cantidad > stock) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock insuficiente',
            text: `Solo hay ${stock} unidades disponibles.`
        });
        return;
    }

    // Verificar si el producto ya existe en la lista
    const productoExistente = this.productos.findIndex(p => p.id === servicioId);
    if (productoExistente !== -1) {
        const nuevoStock = stock - this.productos[productoExistente].cantidad;
        if (cantidad > nuevoStock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock insuficiente',
                text: `Solo hay ${nuevoStock} unidades disponibles adicionales.`
            });
            return;
        }
        // Actualizar cantidad y subtotal del producto existente
        this.productos[productoExistente].cantidad += cantidad;
        this.productos[productoExistente].subtotal =
            this.productos[productoExistente].cantidad * this.productos[productoExistente].precio;
    } else {
        // Agregar nuevo producto
        this.cliente = mascotasId;
        this.productos.push({
            id: servicioId,
            nombre: servicioNombre,
            precio: precio,
            cantidad: cantidad,
            subtotal: precio * cantidad
        });
    }

    this.actualizarTotales();
    this.actualizarTabla();
    cantidadInput.value = 1;

    Swal.fire({
        icon: 'success',
        title: 'Producto agregado',
        text: 'El producto se ha agregado correctamente.',
        timer: 1000,
        showConfirmButton: false
    });
},



    eliminarServicio(index) {
        this.productos.splice(index, 1);
        this.actualizarTotales();
        this.actualizarTabla();
    },

    actualizarTotales() {
        this.total = this.productos.reduce((sum, producto) => sum + producto.subtotal, 0);
        this.totalItems = this.productos.reduce((sum, producto) => sum + producto.cantidad, 0);
    },

    actualizarTabla() {
        const tbody = document.getElementById('serviciosSeleccionados');
        tbody.innerHTML = '';

        this.productos.forEach((producto, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${producto.nombre}</td>
                <td>${producto.cantidad}</td>
                <td>$${producto.precio.toFixed(2)}</td>
                <td>$${producto.subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn-delete" onclick="window.ordenServicioController.eliminarServicio(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('totalPrecio').textContent = `$${this.total.toFixed(2)}`;
        document.getElementById('totalItems').textContent = this.totalItems;
    },

    async delete(id) {
        try {
            const result = await Swal.fire({
                title: '¿Anular Venta?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, Anular',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;

            const response = await fetch(`/notaVentas/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Error al eliminar');

            Swal.fire({
                icon: 'success',
                title: '¡Anulado!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => window.location.reload(), 1000);

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
        }
    },

    async handleSubmit() {
        try {
            if (this.productos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No hay productos',
                    text: 'Debe agregar al menos un producto antes de guardar.'
                });
                return;
            }

            const formData = new FormData(this.form);
            formData.append('productos', JSON.stringify(this.productos));
            formData.append('total', this.total);

            const token = document.querySelector('meta[name="csrf-token"]').content;

            const response = await fetch('/notaVentas', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al guardar la orden');
            }

            Swal.fire({
                icon: 'success',
                title: '¡Orden Guardada!',
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
        }
    }
};

// Inicializar el controlador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.ordenServicioController.init();
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


    #qrImage {
    width: 350px;  /* Ajusta el tamaño como desees */
    height: auto;
}

    .notaVenta-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .notaVenta-header h1 {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Estilos de la Tabla */
    .notaVenta-card {
        background-color: var(--card-background);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .notaVenta-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .notaVenta-table th {
        background-color: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .notaVenta-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .notaVenta-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Imágenes de notaVentas */
    .notaVenta-image-container {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .notaVenta-image {
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
    .notaVenta-form {
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
        .notaVenta-header {
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
        .modal-header,
        .modal-footer {
            position: sticky;
            top: 0;
            left: 0;
            z-index: 10;  /* Asegura que el encabezado y pie estén por encima del contenido */
            background-color: #fff; /* Fonde de fondo blanco para que el texto no se mezcle */
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
