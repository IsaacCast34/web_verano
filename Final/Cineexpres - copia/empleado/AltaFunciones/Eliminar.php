<?php
require("../../Conexion/classConnectionMySQL.php");

if (!isset($_GET['idF']) || !is_numeric($_GET['idF'])) {
    die("<h1>ID de función inválido.</h1>");
}

$idF = intval($_GET['idF']); 

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

$consultaExistencia = "SELECT * FROM CFunciones WHERE idF = $idF";
$resultExistencia = $NewConn->ExecuteQuery($consultaExistencia);

if ($resultExistencia && $resultExistencia->num_rows > 0) {
    $eliminarFuncion = "DELETE FROM CFunciones WHERE idF = $idF";
    $result = $NewConn->ExecuteQuery($eliminarFuncion);

    if ($result) {
        $rowCount = $NewConn->GetCountAffectedRows();
        if ($rowCount > 0) {
            header("Location: funciones.php"); 
            exit;
        } else {
            echo "<h1>No se eliminó ninguna función.</h1>";
        }
    } else {
        echo "<h1>Error al intentar eliminar la función.</h1>";
    }
} else {
    echo "<h1>Función no encontrada.</h1>";
}

$NewConn->CloseConnection();
?>
