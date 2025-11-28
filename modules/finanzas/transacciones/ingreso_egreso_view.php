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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* ==================================== */
        /* Animaciones para Nuevos Registros */
        /* ==================================== */
        @keyframes resaltarNuevo {
            0% {
                background-color: #d4edda;
                transform: scale(1.01); /* Ligeramente menos agresivo */
            }
            100% {
                background-color: transparent;
                transform: scale(1);
            }
        }

        .registro-nuevo {
            animation: resaltarNuevo 2s ease-in-out forwards; /* Añadir forwards para mantener el estado final */
            border-left: 5px solid #198754; /* Usar un color de éxito de Bootstrap */
        }

        .badge-nuevo {
            animation: pulse 1.5s infinite ease-in-out;
            font-weight: 700;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Mejorar la legibilidad de la tabla en dispositivos pequeños */
        @media (max-width: 768px) {
            .table-responsive {
                border: 1px solid #dee2e6;
                border-radius: .25rem;
            }
        }
        
        /* Asegurar que la tarjeta de registro se vea bien */
        .card-registro {
            max-width: 800px;
            margin: 0 auto; /* Centrar la forma si es más pequeña */
        }
    </style>
</head>
<body>
    <div class="container py-5">
        
        <header class="text-center mb-5">
            <h1 class="display-5 text-dark fw-bold">Gestión de Tesorería</h1>
            <p class="lead text-muted">Registro y seguimiento de Ingresos y Egresos Filiales.</p>
        </header>

        <main class="row justify-content-center mb-5">
            <div class="col-lg-10 col-xl-8">
                 <form id="formTransaccion" class="card shadow-lg p-4 card-registro"> 
                    <h3 class="mb-4 text-primary d-flex align-items-center">
                        <i class="bi bi-wallet2 me-2"></i> Registrar Transacción
                    </h3>

                    <div class="mb-3">
                        <label for="fecha_hoy" class="form-label fw-semibold text-muted">Fecha de la Transacción</label>
                        <input type="date" id="fecha_hoy" name="fecha" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="tipo" class="form-label fw-semibold text-muted">Tipo de Transacción</label>
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="" disabled selected hidden>Seleccione un Tipo</option>
                                <option value="INGRESO">Ingreso (Entrada)</option>
                                <option value="EGRESO">Egreso (Salida)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="filial" class="form-label fw-semibold text-muted">Filial</label>
                            <select name="filial" id="filial" class="form-select" required>
                                <option value="" disabled selected hidden>Seleccione una Filial</option>
                                <?php
                                foreach($getFiliales as $filial){
                                ?>
                                <option value="<?= $filial['id_filial']?>"><?= htmlspecialchars($filial['nombre'])?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label fw-semibold text-muted">Monto (USD)</label>
                        <input type="number" id="monto" name="monto" class="form-control form-control-lg text-end" placeholder="0.00" step="0.01" required min="0.01">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="categoria" class="form-label fw-semibold text-muted">Categoría</label>
                            <select name="categoria" id="categoria" class="form-select" required>
                                <option value="" disabled selected hidden>Seleccione una Categoría</option>
                                <?php
                                foreach($getCategoria as $categoria){
                                ?>
                                <option value="<?= $categoria['id_categoria']?>"><?= htmlspecialchars($categoria['nombre'])?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="pastor" class="form-label fw-semibold text-muted">Pastor</label>
                            <select name="pastor" id="pastor" class="form-select" required>
                                <option value="" disabled selected hidden>Seleccione un Pastor</option>
                                <?php
                                foreach($getPastor as $pastor){
                                ?>
                                <option value="<?= $pastor['id_pastor']?>"><?= htmlspecialchars($pastor['nombre'])?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-semibold text-muted">Descripción / Notas</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="2" required placeholder="Breve descripción del motivo..."></textarea>
                    </div>

                    <div id="mensaje" class="alert d-none mt-3" role="alert"></div>

                    <div class="text-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg w-100 w-md-auto" id="btnGuardar">
                            <span id="btnTexto"><i class="bi bi-save me-1"></i> Guardar Transacción</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <section class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center"><i class="bi bi-table me-2"></i> Lista de Transacciones Recientes</h5>
                        <button class="btn btn-outline-light btn-sm" id="btnRecargar" title="Recargar tabla">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                            </svg>
                            <span class="ms-1 d-none d-sm-inline">Recargar</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="tablaSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando transacciones...</p>
                        </div>

                        <div id="tablaContainer" class="d-none table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th><i class="bi bi-calendar-date"></i> Fecha</th>
                                        <th><i class="bi bi-arrow-left-right"></i> Tipo</th>
                                        <th><i class="bi bi-building"></i> Filial</th>
                                        <th><i class="bi bi-person"></i> Pastor</th>
                                        <th><i class="bi bi-tags"></i> Categoría</th>
                                        <th><i class="bi bi-cash-stack"></i> Monto</th>
                                        <th><i class="bi bi-file-text"></i> Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaBody">
                                    </tbody>
                            </table>
                        </div>

                        <div id="sinDatos" class="alert alert-info d-none text-center" role="alert">
                            <h4 class="alert-heading"><i class="bi bi-info-circle"></i> Sin Datos</h4>
                            <p class="mb-0">No hay transacciones registradas o no se encontraron datos.</p>
                        </div>

                        <div id="errorTabla" class="alert alert-danger d-none" role="alert"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // *** SE ELIMINA: document.getElementById('fecha_hoy').valueAsDate = new Date(); para que el usuario elija la fecha ***

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
                // Ajustar ruta si es necesario, se mantiene la original
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
                            ? '<span class="badge bg-success"><i class="bi bi-arrow-up-circle-fill"></i> INGRESO</span>'
                            : '<span class="badge bg-danger"><i class="bi bi-arrow-down-circle-fill"></i> EGRESO</span>';
                        
                        // Formatear monto
                        const montoFormateado = parseFloat(transaccion.monto).toLocaleString('es-SV', {
                            style: 'currency',
                            currency: 'USD'
                        });

                        // Badge de "NUEVO" para registros recientes
                        const badgeNuevo = esNuevo 
                            ? '<span class="badge bg-warning text-dark badge-nuevo ms-2"><i class="bi bi-star-fill"></i> NUEVO</span>'
                            : '';

                        row.innerHTML = `
                            <td>${transaccion.fecha} ${badgeNuevo}</td>
                            <td>${tipoBadge}</td>
                            <td>${transaccion.filial}</td>
                            <td>${transaccion.pastor || 'N/A'}</td>
                            <td>${transaccion.categoria || 'N/A'}</td>
                            <td class="fw-bold text-end">${montoFormateado}</td> <td>${transaccion.descripcion || 'N/A'}</td>
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
                
                // Limpiar formulario y restablecer selects a la opción disabled/selected
                this.reset(); 
                
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