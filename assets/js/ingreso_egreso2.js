// Variable global para rastrear IDs recién agregados
let idsRecientes = [];
// Variable global para almacenar todas las transacciones
let todasTransacciones = [];
let registroActual = 0;

// Función para mostrar un registro específico en vista DBeaver
function mostrarRegistro(index) {
    const tablaBody = document.getElementById('tablaBody');
    const recordCounter = document.getElementById('recordCounter');
    
    if (todasTransacciones.length === 0) return;
    
    // Asegurar que el índice esté en rango
    registroActual = Math.max(0, Math.min(index, todasTransacciones.length - 1));
    const transaccion = todasTransacciones[registroActual];
    
    // Actualizar contador
    recordCounter.textContent = `${registroActual + 1}/${todasTransacciones.length}`;
    
    // Limpiar tabla
    tablaBody.innerHTML = '';
    
    // Determinar si es un registro nuevo
    const esNuevo = registroActual < 3 && idsRecientes.includes(transaccion.id); 
    
    // Formatear tipo con badge
    const tipoClase = transaccion.tipo === 'INGRESO' ? 'bg-success text-white' : 'bg-danger text-white';
    const tipoIcono = transaccion.tipo === 'INGRESO' ? 'bi-arrow-up-circle-fill' : 'bi-arrow-down-circle-fill';
    const tipoBadge = `<span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium ${tipoClase}"><i class="bi ${tipoIcono} mr-1"></i> ${transaccion.tipo}</span>`;
    
    // Formatear monto
    const montoFormateado = parseFloat(transaccion.monto).toLocaleString('es-SV', {
        style: 'currency',
        currency: 'USD'
    });

    // Badge de "NUEVO" para registros recientes
    const badgeNuevo = esNuevo 
        ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-200 text-amber-800 badge-nuevo ml-2"><i class="bi bi-star-fill mr-1"></i> NUEVO</span>'
        : '';
    
    // Crear filas para cada campo (estilo DBeaver)
    const campos = [
        { nombre: '<i class="bi bi-calendar-date"></i> Fecha', valor: transaccion.fecha + ' ' + badgeNuevo },
        { nombre: '<i class="bi bi-arrow-left-right"></i> Tipo', valor: tipoBadge },
        { nombre: '<i class="bi bi-building"></i> Filial', valor: transaccion.filial },
        { nombre: '<i class="bi bi-person"></i> Pastor', valor: transaccion.pastor || 'N/A' },
        { nombre: '<i class="bi bi-tags"></i> Categoría', valor: transaccion.categoria || 'N/A' },
        { nombre: '<i class="bi bi-cash-stack"></i> Monto', valor: montoFormateado },
        { nombre: '<i class="bi bi-file-text"></i> Descripción', valor: transaccion.descripcion || 'N/A' }
    ];
    
    campos.forEach((campo, idx) => {
        const row = document.createElement('tr');
        const rowClasses = idx % 2 === 0 ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 hover:bg-gray-200';
        row.className = rowClasses + ' transition duration-150 ease-in-out';
        
        // Si el ID del registro actual es uno de los recientes, añade la clase de animación
        if (idsRecientes.includes(transaccion.id)) {
            row.classList.add('registro-nuevo');
        }

        row.innerHTML = `
            <td class="px-4 py-3 text-sm font-semibold text-gray-700 sticky left-12 bg-gray-50">${campo.nombre}</td> 
            <td class="px-4 py-3 text-sm text-gray-900">${campo.valor}</td>
        `;
        
        tablaBody.appendChild(row);
    });
}

// Función para cargar la tabla de transacciones
async function cargarTabla(resaltarNuevos = false) {
    const tablaSpinner = document.getElementById('tablaSpinner');
    const tablaContainer = document.getElementById('tablaContainer');
    const tablaBody = document.getElementById('tablaBody');
    const sinDatos = document.getElementById('sinDatos');
    const errorTabla = document.getElementById('errorTabla');

    // Mostrar spinner
    tablaSpinner.classList.remove('hidden');
    tablaContainer.classList.add('hidden');
    sinDatos.classList.add('hidden');
    errorTabla.classList.add('hidden');

    try {
        // ✅ RUTA CORREGIDA - Desde views/ subir 4 niveles hasta raíz
        const response = await axios.get('../../../../models/transacciones/getTransaccionModel.php');
        
        console.log('Respuesta del servidor:', response.data); // Debug
        
        tablaBody.innerHTML = '';

        if (response.data && response.data.length > 0) {
            todasTransacciones = response.data;
            
            if (resaltarNuevos) {
                idsRecientes = todasTransacciones.slice(0, 3).map(t => t.id); 
            } else {
                idsRecientes = []; 
            }
            
            registroActual = 0;
            mostrarRegistro(registroActual);

            tablaContainer.classList.remove('hidden');

            if (resaltarNuevos) {
                setTimeout(() => {
                    tablaContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            }
        } else {
            todasTransacciones = [];
            idsRecientes = [];
            sinDatos.classList.remove('hidden');
        }

    } catch (error) {
        console.error('Error al cargar transacciones:', error);
        console.error('Error completo:', error.response); // Debug adicional
        errorTabla.textContent = 'Error al cargar las transacciones. Por favor, intente nuevamente.';
        errorTabla.classList.remove('hidden');
    } finally {
        tablaSpinner.classList.add('hidden');
    }
}

// Cargar tabla al inicio
cargarTabla();

// Botón recargar
document.getElementById('btnRecargar').addEventListener('click', () => {
    cargarTabla();
});

// Navegación entre registros
document.getElementById('btnPrevRecord').addEventListener('click', () => {
    if (registroActual > 0) mostrarRegistro(registroActual - 1);
});

document.getElementById('btnNextRecord').addEventListener('click', () => {
    if (registroActual < todasTransacciones.length - 1) mostrarRegistro(registroActual + 1);
});

// Manejar el envío del formulario
document.getElementById('formTransaccion').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btnGuardar = document.getElementById('btnGuardar');
    const btnTexto = document.getElementById('btnTexto');
    const btnSpinner = document.getElementById('btnSpinner');
    const mensaje = document.getElementById('mensaje');
    
    btnGuardar.disabled = true;
    btnTexto.classList.add('hidden');
    btnSpinner.classList.remove('hidden');
    mensaje.classList.add('hidden');
    
    const formData = new FormData(this);
    
    try {
        // ✅ RUTA CORREGIDA - Desde views/ subir 4 niveles hasta raíz
        const response = await axios.post(
            '../../../../models/transacciones/transaccionModel.php',
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );
        
        console.log('Respuesta guardado:', response.data); // Debug
        
        mensaje.className = 'p-4 rounded-lg bg-green-100 border border-green-400 text-green-700';
        mensaje.textContent = response.data.mensaje || 'Transacción registrada exitosamente';
        mensaje.classList.remove('hidden');
        
        this.reset(); 
        
        cargarTabla(true);
        
    } catch (error) {
        mensaje.className = 'p-4 rounded-lg bg-red-100 border border-red-400 text-red-700';
        
        let errorMsg = 'Error al registrar la transacción';
        
        if (error.response) {
            console.error('Error response:', error.response); // Debug
            errorMsg = error.response.data.mensaje || error.response.data || errorMsg;
        } else if (error.request) {
            console.error('Error request:', error.request); // Debug
            errorMsg = 'No se recibió respuesta del servidor';
        } else {
            errorMsg = error.message;
        }
        
        mensaje.textContent = errorMsg;
        mensaje.classList.remove('hidden');
        console.error('Error completo:', error);
    } finally {
        btnGuardar.disabled = false;
        btnTexto.classList.remove('hidden');
        btnSpinner.classList.add('hidden');
    }
});