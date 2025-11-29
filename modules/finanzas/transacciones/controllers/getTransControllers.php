<?php
include __DIR__ . '/../../../../config/database.php';

function getConnection(){
    $class = new Database();
    $conn = $class->sendConecction();

    return $conn;
}

function getPastores(){
    $conn = getConnection();

    $query = "SELECT nombre, id_pastor FROM miembros AS m INNER JOIN pastores AS p ON p.id_miembro = m.id_miembro";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        $filas =[];
        while($resultado = $result->fetch_assoc()){
            $filas[] = $resultado;
        }

        return $filas;
    }
    return $row=["array vacio"];
}

function getCategorias(){
    $conn = getConnection();
    $query = "SELECT * FROM categorias_ingreso";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        $row = [];

        while($filas = $result->fetch_assoc()){
            $row[] = $filas;
        }

        return $row;
    }

    return $row=["array vacio"];
}

function getFiliales(){
    $conn = getConnection();
    $query = "SELECT * FROM filiales";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        $row = [];

        while($resultado = $result->fetch_assoc()){
            $row[] = $resultado;
        }

        return $row;
    }

    return $row=["array vacio"];
}