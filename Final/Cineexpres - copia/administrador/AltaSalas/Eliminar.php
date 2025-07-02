<?php
require("../../Conexion/classConnectionMySQL.php");

if (!isset($_GET['idS']) || !is_numeric($_GET['idS'])) {
    die("<h1>ID de sala inválido.</h1>");
}

$idS = intval($_GET['idS']); 

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

$consultaExistencia = "SELECT * FROM CSalas WHERE idS = $idS";
$resultExistencia = $NewConn->ExecuteQuery($consultaExistencia);

if ($resultExistencia && $resultExistencia->num_rows > 0) {
    $eliminar = "DELETE FROM CSalas WHERE idS = $idS";
    $resultado = $NewConn->ExecuteQuery($eliminar);

    if ($resultado) {
        header("Location: salas.php");
        exit;
    } else {
        echo "<h1>Error al eliminar la sala. Verifica dependencias como funciones o asientos asociados.</h1>";
    }
} else {
    echo "<h1>Sala no encontrada.</h1>";
}
$NewConn->CloseConnection();
?>
