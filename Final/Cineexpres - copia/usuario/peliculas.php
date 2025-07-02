<?php
header('Content-Type: application/json');
require_once "../Conexion/classConnectionMySQL.php";

try {
    $conexion = new ConnectionMySQL();
    $conexion->CreateConnection();
    
    $query = "SELECT * FROM CPeliculas";
    $result = $conexion->getConn()->query($query);
    
    $peliculas = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $peliculas[] = $row;
        }
    }
    
    echo json_encode($peliculas);
    
    $conexion->CloseConnection();
} catch (Exception $e) {
    echo json_encode(array(
        'error' => 'Error al obtener películas: ' . $e->getMessage()
    ));
}
?>