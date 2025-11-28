<?php

include "../../config/database.php";
$class = new Database();
$conn = $class->sendConecction();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $fecha = $_POST['fecha'];           
    $tipo = $_POST['tipo'];
    $filial = $_POST['filial'];
    $monto = $_POST['monto'];
    $categoria = $_POST['categoria'];
    $pastor = $_POST['pastor'];
    $descripcion = $_POST['descripcion'];
    $creador = 1;

    $query = "INSERT INTO transacciones_finanzas(fecha,tipo,id_filial,id_categoria,id_pastor,monto,descripcion,creado_por) 
    VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiiidsi", $fecha, $tipo, $filial, $categoria, $pastor, $monto, $descripcion, $creador);
    $result = $stmt->execute();
    echo $result;
}