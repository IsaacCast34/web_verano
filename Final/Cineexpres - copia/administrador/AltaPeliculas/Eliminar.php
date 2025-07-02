<?php
require("../../Conexion/classConnectionMySQL.php");

if (!isset($_GET['idP']) || !is_numeric($_GET['idP'])) {
    die("<h1>ID inválido.</h1>");
}

$idP = intval($_GET['idP']);

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Consultar si la película existe antes de eliminar
$consultaExistencia = "SELECT NombrePelicula FROM CPeliculas WHERE idP = $idP";
$resultExistencia = $NewConn->ExecuteQuery($consultaExistencia);

if ($resultExistencia && $resultExistencia->num_rows > 0) {
    // Proceder con la eliminación
    $query = "DELETE FROM CPeliculas WHERE idP = $idP";
    $result = $NewConn->ExecuteQuery($query);

    if ($result) {
        $RowCount = $NewConn->GetCountAffectedRows();
        if ($RowCount > 0) {
            header("Location: peliculas.php");
            exit;
        } else {
            echo "<h1>No se encontró la película para eliminar.</h1>";
        }
    } else {
        echo "<h1>Error al intentar eliminar la película.</h1>";
    }
} else {
    echo "<h1>Película no encontrada.</h1>";
}

$NewConn->CloseConnection();
?>
