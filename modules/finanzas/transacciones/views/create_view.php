<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>


<?php
include __DIR__ . '/../controllers/getTransControllers.php';
$getPastor = getPastores();
$getCategoria = getCategorias();
$getFiliales = getFiliales();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#3b82f6',
                        'primary-dark': '#2563eb', 
                        'success': '#10b981',
                        'danger': '#ef4444',
                        'warning': '#f59e0b',
                        'gray-dark': '#1f2937',
                        'table-header': '#eff6ff',
                    },
                    boxShadow: {
                        'custom': '0 8px 32px rgba(34, 34, 73, 0.08)',
                    }
                }
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>
    <title>Ingresos/Egresos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* ========================== */
        /* Animaciones Tailwind-Friendly */
        /* ========================== */
        @keyframes resaltarNuevo {
            0% { background-color: #ecfdf5; border-left-color: #10b981; }
            70% { background-color: #ffffff; border-left-color: #10b981; }
            100% { background-color: transparent; border-left-color: transparent; }
        }
        .registro-nuevo {
            animation: resaltarNuevo 2s cubic-bezier(.42,1.9,.47,.98) forwards;
            border-left-width: 6px;
        }

        @keyframes pulse {
            from { opacity: 1; box-shadow: 0 0 10px rgba(253, 230, 138, 0.7); }
            to   { opacity: .79; box-shadow: 0 0 22px rgba(253, 230, 138, 0.9); }
        }
        .badge-nuevo {
            animation: pulse 1s infinite alternate ease-in-out;
            transform: scale(1.09);
        }
        
        /* Estilos base para inputs y selects */
        .form-control, .form-select {
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3);
        }

        /* ========================== */
        /* Estilos para la Tabla Fija (SOLUCIÓN CORREGIDA) */
        /* ========================== */
        
        /* Contenedor de la vista de registro */
        #recordView {
            display: flex; 
            width: 100%;
        }

        #recordView table {
            /* FUERZA a que el ancho de columna sea fijo (IGNORA el contenido) */
            table-layout: fixed;
            width: 100%; /* Ocupa el espacio restante */
        }
        
        /* Panel de navegación de registros (Columna más a la izquierda) */
        #recordView .w-12 {
            position: sticky; 
            left: 0;
            z-index: 30; /* El más alto */
            flex-shrink: 0; /* Asegura que no se encoja */
        }

        /* Definir variables para anchos */
        :root {
            --ancho-campo: 160px; /* Ancho fijo para la celda Campo */
            --ancho-panel: 48px;  /* Ancho de .w-12 */
        }

        /* Definir anchos explícitos para las columnas */
        #recordView thead th:nth-child(1) {
            width: var(--ancho-campo); 
            /* Posición sticky: debe empezar después del Panel (48px) */
            left: var(--ancho-panel);
            z-index: 25; /* Entre el panel (30) y la columna valor (20) */
            background-color: #eff6ff; /* Fondo de encabezado */
        }

        #recordView tbody td:nth-child(1) {
            width: var(--ancho-campo);
            position: sticky;
            /* Posición sticky: debe empezar después del Panel (48px) */
            left: var(--ancho-panel);
            /* Estilos de fondo y borde para que se vea bien pegada */
            background-color: #f9fafb; /* bg-gray-50 */
            border-right: 1px solid #e5e7eb; 
            z-index: 20; /* Flota sobre los valores */
        }

        /* El encabezado general debe tener un z-index alto para flotar sobre las filas */
        #recordView thead tr th {
            position: sticky;
            top: 0;
            z-index: 25;
        }
        
        /* La segunda columna (Valor) toma el resto del espacio y es solo sticky top */
        #recordView thead th:nth-child(2) {
            width: auto;
            left: unset;
            z-index: 15; /* El más bajo, debajo del Campo */
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 md:py-12">
        
        <header class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl text-gray-800 font-extrabold">Gestión de Tesorería</h1>
            <p class="text-lg text-gray-500 mt-2">Registro y seguimiento de Ingresos y Egresos Filiales.</p>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-10">
            
            <main class="flex justify-center">
                <div class="w-full">
                     <form id="formTransaccion" class="bg-white rounded-xl shadow-2xl p-6 md:p-8 shadow-custom"> 
                        <h3 class="mb-6 text-xl md:text-2xl text-primary font-semibold flex items-center border-b pb-4">
                            <i class="bi bi-wallet2 mr-3 text-2xl"></i> Registrar Transacción
                        </h3>

                        <div class="mb-4">
                            <label for="fecha_hoy" class="block text-sm font-medium text-gray-700 mb-1">Fecha de la Transacción</label>
                            <input type="date" id="fecha_hoy" name="fecha" class="form-control w-full p-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Transacción</label>
                                <select name="tipo" id="tipo" class="form-select w-full p-3 border border-gray-300 rounded-lg bg-white appearance-none focus:ring-primary focus:border-primary" required>
                                    <option value="" disabled selected hidden>Seleccione un Tipo</option>
                                    <option value="INGRESO">Ingreso (Entrada)</option>
                                    <option value="EGRESO">Egreso (Salida)</option>
                                </select>
                            </div>

                            <div>
                                <label for="filial" class="block text-sm font-medium text-gray-700 mb-1">Filial</label>
                                <select name="filial" id="filial" class="form-select w-full p-3 border border-gray-300 rounded-lg bg-white appearance-none focus:ring-primary focus:border-primary" required>
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

                        <div class="mb-4">
                            <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto (USD)</label>
                            <input type="number" id="monto" name="monto" class="form-control w-full p-4 text-xl border-2 border-blue-100 rounded-lg text-right bg-blue-50 focus:ring-primary focus:border-primary" placeholder="0.00" step="0.01" required min="0.01">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                <select name="categoria" id="categoria" class="form-select w-full p-3 border border-gray-300 rounded-lg bg-white appearance-none focus:ring-primary focus:border-primary" required>
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

                            <div>
                                <label for="pastor" class="block text-sm font-medium text-gray-700 mb-1">Pastor</label>
                                <select name="pastor" id="pastor" class="form-select w-full p-3 border border-gray-300 rounded-lg bg-white appearance-none focus:ring-primary focus:border-primary" required>
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
                        
                        <div class="mb-6">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción / Notas</label>
                            <textarea id="descripcion" name="descripcion" class="form-control w-full p-3 border border-gray-300 rounded-lg bg-blue-50 focus:ring-primary focus:border-primary" rows="3" required placeholder="Breve descripción del motivo..."></textarea>
                        </div>

                        <div id="mensaje" class="p-4 rounded-lg hidden mt-4" role="alert"></div>

                        <div class="pt-4 border-t border-gray-200 text-right">
                            <button type="submit" class="w-full md:w-auto px-8 py-3 text-lg font-semibold text-white bg-gradient-to-r from-primary to-blue-400 rounded-lg shadow-lg hover:from-primary-dark hover:to-blue-500 transition duration-200 ease-in-out" id="btnGuardar">
                                <span id="btnTexto" class="flex items-center justify-center">
                                    <i class="bi bi-save mr-2"></i> Guardar Transacción
                                </span>
                                <span id="btnSpinner" class="hidden h-5 w-5 border-2 border-white border-t-transparent rounded-full animate-spin" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </main>

            <section class="flex justify-center">
                <div class="w-full">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                        <div class="bg-gray-800 text-white p-4 rounded-t-xl flex justify-between items-center">
                            <h5 class="text-lg font-semibold flex items-center"><i class="bi bi-table mr-2"></i> Lista de Transacciones Recientes</h5>
                            <button class="flex items-center text-sm px-3 py-1 border border-gray-500 rounded-lg hover:bg-gray-700 transition" id="btnRecargar" title="Recargar tabla">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                                </svg>
                                <span class="ml-1 hidden sm:inline">Recargar</span>
                            </button>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-b-xl">
                            
                            <div id="tablaSpinner" class="text-center py-10">
                                <div class="h-10 w-10 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-3 text-gray-500">Cargando transacciones...</p>
                            </div>

                            <div id="tablaContainer" class="hidden overflow-auto rounded-lg shadow-md max-h-[600px]">
                                <div id="recordView"> 
                                    <div class="w-12 bg-gray-700 text-white flex flex-col items-center py-2">
                                        <button id="btnPrevRecord" class="p-2 hover:bg-gray-600 rounded mb-2" title="Registro anterior">
                                            <i class="bi bi-chevron-up"></i>
                                        </button>
                                        <div class="flex-1 flex items-center">
                                            <span id="recordCounter" class="text-xs font-semibold transform -rotate-90 whitespace-nowrap">1/0</span>
                                        </div>
                                        <button id="btnNextRecord" class="p-2 hover:bg-gray-600 rounded mt-2" title="Registro siguiente">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                    
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-table-header text-primary sticky top-0 z-10">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider"> 
                                                    Campo
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">
                                                    Valor
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaBody" class="bg-white divide-y divide-gray-100">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="sinDatos" class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 hidden text-center rounded-md" role="alert">
                                <h4 class="text-lg font-semibold mb-1"><i class="bi bi-info-circle"></i> Sin Datos</h4>
                                <p class="mb-0">No hay transacciones registradas o no se encontraron datos.</p>
                            </div>

                            <div id="errorTabla" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 hidden rounded-md" role="alert"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="/projMisionVerdad/assets/js/ingreso_egreso2.js"></script>
</body>
</html>