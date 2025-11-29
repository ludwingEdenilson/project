<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transacciones | Sistema Financiero</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#3b82f6',
                        'primary-dark': '#2563eb',
                        'success': '#10b981',
                        'danger': '#ef4444',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .skeleton {
            animation: shimmer 2s infinite linear;
            background: linear-gradient(to right, #f3f4f6 4%, #e5e7eb 25%, #f3f4f6 36%);
            background-size: 1000px 100%;
        }
        
        .badge-ingreso {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .badge-egreso {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .table-row-hover:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

    <div class="container mx-auto px-4 py-8 md:py-12 max-w-7xl">
        
        <!-- Header con gradiente -->
        <header class="mb-10 animate-fade-in">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-2xl p-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold mb-2 flex items-center">
                            <i class="bi bi-currency-exchange mr-3 text-4xl"></i>
                            Listado de Transacciones
                        </h1>
                        <p class="text-blue-100 text-lg">
                            Historial completo de ingresos y egresos del sistema
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button id="btnRecargar" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg flex items-center space-x-2">
                            <i class="bi bi-arrow-clockwise text-xl"></i>
                            <span>Actualizar</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Estadísticas rápidas -->
        <div id="statsContainer" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-slide-up hidden">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Ingresos</p>
                        <p id="totalIngresos" class="text-3xl font-bold text-green-600">$0.00</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-full">
                        <i class="bi bi-arrow-up-circle-fill text-green-600 text-3xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Egresos</p>
                        <p id="totalEgresos" class="text-3xl font-bold text-red-600">$0.00</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-full">
                        <i class="bi bi-arrow-down-circle-fill text-red-600 text-3xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Balance</p>
                        <p id="balance" class="text-3xl font-bold text-blue-600">$0.00</p>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full">
                        <i class="bi bi-wallet2 text-blue-600 text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de transacciones -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-slide-up">
            
            <!-- Loading skeleton -->
            <div id="loadingContainer" class="p-8">
                <div class="space-y-4">
                    <div class="skeleton h-12 rounded"></div>
                    <div class="skeleton h-16 rounded"></div>
                    <div class="skeleton h-16 rounded"></div>
                    <div class="skeleton h-16 rounded"></div>
                    <div class="skeleton h-16 rounded"></div>
                </div>
            </div>

            <!-- Tabla real -->
            <div id="tableContainer" class="hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-hash mr-1"></i> ID
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-calendar-date mr-1"></i> Fecha
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-arrow-left-right mr-1"></i> Tipo
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-building mr-1"></i> Filial
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-person mr-1"></i> Pastor
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider text-right">
                                    <i class="bi bi-cash-stack mr-1"></i> Monto
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-tags mr-1"></i> Categoría
                                </th>
                                <th class="p-4 font-bold text-sm uppercase tracking-wider">
                                    <i class="bi bi-file-text mr-1"></i> Descripción
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tablaTransacciones" class="divide-y divide-gray-200 bg-white">
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación (placeholder) -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Mostrando <span id="recordCount" class="font-semibold">0</span> transacciones
                        </p>
                        <div class="flex space-x-2">
                            <!-- Aquí podrías agregar botones de paginación si es necesario -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sin datos -->
            <div id="sinDatos" class="hidden p-12 text-center">
                <div class="bg-blue-50 rounded-xl p-8 inline-block">
                    <i class="bi bi-inbox text-6xl text-blue-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No hay transacciones</h3>
                    <p class="text-gray-500">No se encontraron registros en el sistema</p>
                </div>
            </div>

            <!-- Error -->
            <div id="errorContainer" class="hidden p-12 text-center">
                <div class="bg-red-50 rounded-xl p-8 inline-block">
                    <i class="bi bi-exclamation-triangle text-6xl text-red-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Error al cargar datos</h3>
                    <p class="text-gray-500" id="errorMessage">Ocurrió un error al cargar las transacciones</p>
                    <button onclick="location.reload()" class="mt-4 bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Script mejorado -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            cargarTransacciones();
            
            document.getElementById('btnRecargar').addEventListener('click', () => {
                cargarTransacciones();
            });
        });

        function cargarTransacciones() {
            const loadingContainer = document.getElementById('loadingContainer');
            const tableContainer = document.getElementById('tableContainer');
            const sinDatos = document.getElementById('sinDatos');
            const errorContainer = document.getElementById('errorContainer');
            const tablaBody = document.getElementById('tablaTransacciones');
            const statsContainer = document.getElementById('statsContainer');

            // Mostrar loading
            loadingContainer.classList.remove('hidden');
            tableContainer.classList.add('hidden');
            sinDatos.classList.add('hidden');
            errorContainer.classList.add('hidden');
            statsContainer.classList.add('hidden');

            axios.get("../../../../models/transacciones/getTransaccionModel.php")
                .then(response => {
                    const data = response.data;

                    console.log("Datos recibidos:", data);

                    if (data.length === 0) {
                        loadingContainer.classList.add('hidden');
                        sinDatos.classList.remove('hidden');
                        return;
                    }

                    // Calcular estadísticas
                    let totalIngresos = 0;
                    let totalEgresos = 0;
                    
                    data.forEach(t => {
                        const monto = parseFloat(t.monto);
                        if (t.tipo === 'INGRESO') {
                            totalIngresos += monto;
                        } else {
                            totalEgresos += monto;
                        }
                    });

                    const balance = totalIngresos - totalEgresos;

                    // Actualizar estadísticas
                    document.getElementById('totalIngresos').textContent = formatCurrency(totalIngresos);
                    document.getElementById('totalEgresos').textContent = formatCurrency(totalEgresos);
                    document.getElementById('balance').textContent = formatCurrency(balance);
                    document.getElementById('balance').classList.toggle('text-green-600', balance >= 0);
                    document.getElementById('balance').classList.toggle('text-red-600', balance < 0);
                    
                    statsContainer.classList.remove('hidden');

                    // Actualizar contador
                    document.getElementById('recordCount').textContent = data.length;

                    // Limpiar tabla
                    tablaBody.innerHTML = "";

                    // Llenar tabla
                    data.forEach((t, index) => {
                        const fila = document.createElement("tr");
                        fila.className = "table-row-hover transition-all duration-200 " + 
                                        (index % 2 === 0 ? 'bg-white' : 'bg-gray-50');

                        const tipoBadge = t.tipo === 'INGRESO' 
                            ? `<span class="badge-ingreso text-white px-3 py-1 rounded-full text-xs font-bold inline-flex items-center shadow-md">
                                <i class="bi bi-arrow-up-circle-fill mr-1"></i> INGRESO
                               </span>`
                            : `<span class="badge-egreso text-white px-3 py-1 rounded-full text-xs font-bold inline-flex items-center shadow-md">
                                <i class="bi bi-arrow-down-circle-fill mr-1"></i> EGRESO
                               </span>`;

                        fila.innerHTML = `
                            <td class="p-4 font-semibold text-gray-700">#${t.id_transaccion}</td>
                            <td class="p-4 text-gray-600">
                                <div class="flex items-center">
                                    <i class="bi bi-calendar3 text-blue-500 mr-2"></i>
                                    ${formatDate(t.fecha)}
                                </div>
                            </td>
                            <td class="p-4">${tipoBadge}</td>
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span class="font-medium text-gray-700">${t.filial}</span>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600">${t.pastor || '<span class="text-gray-400 italic">N/A</span>'}</td>
                            <td class="p-4 text-right">
                                <span class="font-bold ${t.tipo === 'INGRESO' ? 'text-green-600' : 'text-red-600'}">
                                    ${formatCurrency(parseFloat(t.monto))}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">
                                    ${t.categoria || 'Sin categoría'}
                                </span>
                            </td>
                            <td class="p-4 text-gray-600 max-w-xs truncate" title="${t.descripcion}">
                                ${t.descripcion}
                            </td>
                        `;

                        tablaBody.appendChild(fila);
                    });

                    loadingContainer.classList.add('hidden');
                    tableContainer.classList.remove('hidden');
                })
                .catch(error => {
                    console.error("Error al obtener transacciones:", error);
                    
                    loadingContainer.classList.add('hidden');
                    errorContainer.classList.remove('hidden');
                    
                    if (error.response) {
                        document.getElementById('errorMessage').textContent = 
                            error.response.data.error || 'Error del servidor';
                    } else if (error.request) {
                        document.getElementById('errorMessage').textContent = 
                            'No se pudo conectar con el servidor';
                    }
                });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-SV', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(amount);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-SV', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    </script>

</body>
</html>