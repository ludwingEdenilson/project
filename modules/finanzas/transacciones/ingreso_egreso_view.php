<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
include 'ingresoEgresoLogica.php';
$getPastor = getPastores();
$getCategoria = getCategorias();
$getFiliales = getFiliales();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>
    <title>Ingresos/Egresos</title>
    <style>
        /* Animación para registros nuevos */
        @keyframes resaltarNuevo {
            0% {
                background-color: #d4edda;
                transform: scale(1.02);
            }
            100% {
                background-color: transparent;
                transform: scale(1);
            }
        }

        .registro-nuevo {
            animation: resaltarNuevo 2s ease-in-out;
            border-left: 4px solid #28a745;
        }

        .badge-nuevo {
            animation: pulse 1.5s ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <form id="formTransaccion" class="container mt-4">

                <div class="card shadow-sm p-4">
                    <h4 class="mb-4 text-primary">Registrar Transacción</h4>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="fecha" class="form-label fw-semibold">Fecha</label>
                        <input type="date" id="fecha_hoy" name="fecha" class="form-control" required>
                    </div>

                    <!-- Tipo y Filial -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label fw-semibold">Tipo de Transacción</label>
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="INGRESO">Ingreso</option>
                                <option value="EGRESO">Egreso</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="filial" class="form-label fw-semibold">Filial</label>
                            <select name="filial" id="filial" class="form-select" required>
                                <?php
                                foreach($getFiliales as $filial){
                                ?>
                                <option value="<?= $filial['id_filial']?>"><?= $filial['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Monto -->
                    <div class="mb-3">
                        <label for="monto" class="form-label fw-semibold">Monto</label>
                        <input type="number" id="monto" name="monto" class="form-control" placeholder="0.00" step="0.01" required>
                    </div>

                    <!-- Categoria y Pastor -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label fw-semibold">Categoría</label>
                            <select name="categoria" id="categoria" class="form-select" required>
                                <?php
                                foreach($getCategoria as $categoria){
                                ?>
                                <option value="<?= $categoria['id_categoria']?>"><?= $categoria['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pastor" class="form-label fw-semibold">Pastor</label>
                            <select name="pastor" id="pastor" class="form-select" required>
                                <?php
                                foreach($getPastor as $pastor){
                                ?>
                                <option value="<?= $pastor['id_pastor']?>"><?= $pastor['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                    </div>

                    <!-- Mensajes -->
                    <div id="mensaje" class="alert d-none" role="alert"></div>

                    <!-- Botón -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4" id="btnGuardar">
                            <span id="btnTexto">Guardar</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </div>

            </form>

        </div>

        <!-- Tabla de Transacciones -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Transacciones</h5>
                        <button class="btn btn-light btn-sm" id="btnRecargar" title="Recargar tabla">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Spinner de carga -->
                        <div id="tablaSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando transacciones...</p>
                        </div>

                        <!-- Tabla -->
                        <div id="tablaContainer" class="d-none table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Filial</th>
                                        <th>Pastor</th>
                                        <th>Categoría</th>
                                        <th>Monto</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaBody">
                                    <!-- Los datos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Mensaje cuando no hay datos -->
                        <div id="sinDatos" class="alert alert-info d-none" role="alert">
                            <i class="bi bi-info-circle"></i> No hay transacciones registradas
                        </div>

                        <!-- Mensaje de error -->
                        <div id="errorTabla" class="alert alert-danger d-none" role="alert"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Establecer fecha actual
        document.getElementById('fecha_hoy').valueAsDate = new Date();

        // Variable global para rastrear IDs recién agregados
        let idsRecientes = [];

        // Función para cargar la tabla de transacciones
        async function cargarTabla(resaltarNuevos = false) {
            const tablaSpinner = document.getElementById('tablaSpinner');
            const tablaContainer = document.getElementById('tablaContainer');
            const tablaBody = document.getElementById('tablaBody');
            const sinDatos = document.getElementById('sinDatos');
            const errorTabla = document.getElementById('errorTabla');

            // Mostrar spinner
            tablaSpinner.classList.remove('d-none');
            tablaContainer.classList.add('d-none');
            sinDatos.classList.add('d-none');
            errorTabla.classList.add('d-none');

            try {
                const response = await axios.get('../../../models/transacciones/getTransaccionModel.php');
                
                // Limpiar tabla
                tablaBody.innerHTML = '';

                if (response.data && response.data.length > 0) {
                    // Llenar la tabla con los datos
                    response.data.forEach((transaccion, index) => {
                        const row = document.createElement('tr');
                        
                        // Determinar si es un registro nuevo (primeros 3 registros después de guardar)
                        const esNuevo = resaltarNuevos && index < 3;
                        
                        if (esNuevo) {
                            row.classList.add('registro-nuevo');
                        }
                        
                        // Formatear tipo con badge
                        const tipoBadge = transaccion.tipo === 'INGRESO' 
                            ? '<span class="badge bg-success">INGRESO</span>'
                            : '<span class="badge bg-danger">EGRESO</span>';
                        
                        // Formatear monto
                        const montoFormateado = parseFloat(transaccion.monto).toLocaleString('es-SV', {
                            style: 'currency',
                            currency: 'USD'
                        });

                        // Badge de "NUEVO" para registros recientes
                        const badgeNuevo = esNuevo 
                            ? '<span class="badge bg-warning text-dark badge-nuevo ms-2">NUEVO</span>'
                            : '';

                        row.innerHTML = `
                            <td>${transaccion.fecha} ${badgeNuevo}</td>
                            <td>${tipoBadge}</td>
                            <td>${transaccion.filial}</td>
                            <td>${transaccion.pastor || 'N/A'}</td>
                            <td>${transaccion.categoria || 'N/A'}</td>
                            <td class="fw-bold">${montoFormateado}</td>
                            <td>${transaccion.descripcion || 'N/A'}</td>
                        `;
                        
                        tablaBody.appendChild(row);
                    });

                    // Mostrar tabla
                    tablaContainer.classList.remove('d-none');

                    // Scroll suave hacia la tabla si hay registros nuevos
                    if (resaltarNuevos) {
                        setTimeout(() => {
                            tablaContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 100);
                    }
                } else {
                    // No hay datos
                    sinDatos.classList.remove('d-none');
                }

            } catch (error) {
                console.error('Error al cargar transacciones:', error);
                errorTabla.textContent = 'Error al cargar las transacciones. Por favor, intente nuevamente.';
                errorTabla.classList.remove('d-none');
            } finally {
                // Ocultar spinner
                tablaSpinner.classList.add('d-none');
            }
        }

        // Cargar tabla al inicio
        cargarTabla();

        // Botón recargar
        document.getElementById('btnRecargar').addEventListener('click', cargarTabla);

        // Manejar el envío del formulario
        document.getElementById('formTransaccion').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btnGuardar = document.getElementById('btnGuardar');
            const btnTexto = document.getElementById('btnTexto');
            const btnSpinner = document.getElementById('btnSpinner');
            const mensaje = document.getElementById('mensaje');
            
            // Deshabilitar botón y mostrar spinner
            btnGuardar.disabled = true;
            btnTexto.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
            mensaje.classList.add('d-none');
            
            // Obtener datos del formulario usando FormData
            const formData = new FormData(this);
            
            try {
                // Enviar datos con Axios usando FormData (como POST tradicional)
                const response = await axios.post(
                    '../../../models/transacciones/transaccionModel.php',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }
                );
                
                // Mostrar mensaje de éxito
                mensaje.className = 'alert alert-success';
                mensaje.textContent = response.data.mensaje || 'Transacción registrada exitosamente';
                mensaje.classList.remove('d-none');
                
                // Limpiar formulario
                this.reset();
                document.getElementById('fecha_hoy').valueAsDate = new Date();
                
                // Recargar la tabla después de guardar y resaltar nuevos registros
                cargarTabla(true);
                
            } catch (error) {
                // Mostrar mensaje de error detallado
                mensaje.className = 'alert alert-danger';
                
                // Intentar obtener el mensaje de error del servidor
                let errorMsg = 'Error al registrar la transacción';
                
                if (error.response) {
                    // El servidor respondió con un código de error
                    console.error('Error Response:', error.response.data);
                    errorMsg = error.response.data.mensaje || error.response.data || errorMsg;
                } else if (error.request) {
                    // La petición fue hecha pero no hubo respuesta
                    errorMsg = 'No se recibió respuesta del servidor';
                } else {
                    // Algo pasó al configurar la petición
                    errorMsg = error.message;
                }
                
                mensaje.textContent = errorMsg;
                mensaje.classList.remove('d-none');
                console.error('Error completo:', error);
            } finally {
                // Habilitar botón y ocultar spinner
                btnGuardar.disabled = false;
                btnTexto.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
            }
        });
    </script>

</body>
</html>