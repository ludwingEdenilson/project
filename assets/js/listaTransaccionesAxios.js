document.addEventListener("DOMContentLoaded", () => {
    const tablaBody = document.querySelector("#tablaTransacciones");

    // ✅ CORREGIDO: Desde views/ subir 4 niveles hasta raíz, luego models/transacciones/
    axios.get("../../../../models/transacciones/getTransaccionModel.php")
        .then(response => {
            const data = response.data;

            console.log("Datos recibidos:", data); // Debug

            tablaBody.innerHTML = "";

            if (data.length === 0) {
                tablaBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="p-4 text-center text-gray-600">
                            No hay transacciones registradas.
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(t => {
                const fila = document.createElement("tr");

                fila.className = "hover:bg-gray-100 transition";

                fila.innerHTML = `
                    <td class="p-3">${t.id_transaccion}</td>
                    <td class="p-3">${t.fecha}</td>
                    <td class="p-3">${t.tipo}</td>
                    <td class="p-3">${t.filial}</td>
                    <td class="p-3">${t.pastor ?? "—"}</td>
                    <td clasmodules/finanzas/transacciones/views/create_view.phps="p-3">$${parseFloat(t.monto).toFixed(2)}</td>
                    <td class="p-3">${t.descripcion}</td>
                    <td class="p-3">${t.categoria}</td>
                `;

                tablaBody.appendChild(fila);
            });
        })
        .catch(error => {
            console.error("Error al obtener transacciones ●", error);
            console.error("Detalles:", error.response); // Debug adicional

            tablaBody.innerHTML = `
                <tr>
                    <td colspan="8" class="p-4 text-center text-red-600 font-semibold">
                        Error al cargar datos. Revisa la consola para más detalles.
                    </td>
                </tr>
            `;
        });
});