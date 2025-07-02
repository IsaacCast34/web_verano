<?php
require("../../Conexion/classConnectionMySQL.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h1>ID inválido.</h1>");
}

$id = intval($_GET['id']);

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Consultar el tipo de usuario antes de eliminar
$consultaTipo = "SELECT Tipo FROM Usuario WHERE idL = $id";
$resultTipo = $NewConn->ExecuteQuery($consultaTipo);

if ($resultTipo && $resultTipo->num_rows > 0) {
    $fila = $resultTipo->fetch_assoc();
    $tipoUsuario = $fila['Tipo'];

    if (strtolower($tipoUsuario) === 'administrador') {
        echo "<script>alert('No se puede eliminar un administrador.'); window.location.href = 'usuarios.php';</script>";
        exit;
    }

    // Proceder con la eliminación
    $query = "DELETE FROM Usuario WHERE idL = $id";
    $result = $NewConn->ExecuteQuery($query);

    if ($result) {
        $RowCount = $NewConn->GetCountAffectedRows();
        if ($RowCount > 0) {
            header("Location: usuarios.php");
            exit;
        } else {
            echo "<h1>No se encontró el usuario para eliminar.</h1>";
        }
    } else {
        echo "<h1>Error al intentar eliminar el usuario.</h1>";
    }

} else {
    echo "<h1>Usuario no encontrado.</h1>";
}

$NewConn->CloseConnection();
?>
