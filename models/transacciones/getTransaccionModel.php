<?php
// Archivo: models/transacciones/getTransaccionModel.php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../../config/database.php";

try {
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        
        $class = new Database();
        $conn = $class->sendConecction();
        
        $sql = "SELECT 
                    tf.id_transaccion,
                    tf.fecha, 
                    tf.tipo, 
                    f.nombre AS filial, 
                    CONCAT(m.nombre, ' ', m.apellido) AS pastor, 
                    tf.monto, 
                    tf.descripcion,
                    c.nombre AS categoria
                FROM transacciones_finanzas tf 
                INNER JOIN filiales f ON f.id_filial = tf.id_filial
                LEFT JOIN pastores p ON p.id_pastor = tf.id_pastor
                LEFT JOIN miembros m ON m.id_miembro = p.id_miembro
                LEFT JOIN categorias_ingreso c ON c.id_categoria = tf.id_categoria
                ORDER BY tf.creado_en DESC, tf.id_transaccion DESC
                LIMIT 100";
        
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = [];
            while ($resultado = $result->fetch_assoc()) {
                $row[] = $resultado;
            }
            echo json_encode($row);
        } else {
            // Devolver array vacío si no hay datos
            echo json_encode([]);
        }
        
        $conn->close();
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>