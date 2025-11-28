<?php

class Database {
    private $conn;
    private $server = "localhost";
    private $user = "root";
    private $password = "Ludwing123";
    private $base = "bd_iglesia";
    
    public function __construct(){
        $this->conn = new mysqli($this->server, $this->user, $this->password, $this->base);

        if($this->conn->connect_error){
            echo ("error de conexion");
        }
    }
    
    public function sendConecction(){
        return $this->conn;
    }
}