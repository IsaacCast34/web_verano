<?php
session_start();
require_once "../Conexion/classConnectionMySQL.php";

header('Content-Type: application/json');

// Verificar datos requeridos
if (!isset($_POST['idFuncion']) || !isset($_POST['asientos']) || !isset($_POST['total'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$idFuncion = intval($_POST['idFuncion']);
$asientos = explode(',', $_POST['asientos']);
$total = floatval($_POST['total']);

// Conexión a la base de datos
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Obtener ID de usuario
$queryUsuario = "SELECT idL FROM Usuario WHERE usuario = ?";
$stmtUsuario = $NewConn->getConn()->prepare($queryUsuario);
$stmtUsuario->bind_param("s", $_SESSION['usuario']);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();
$usuario = $resultUsuario->fetch_assoc();

if (!$usuario) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit();
}

$idUsuario = $usuario['idL'];

// Insertar reserva
$queryReserva = "INSERT INTO Reservas (idUsuario, idFuncion, fechaReserva, total) 
                 VALUES (?, ?, NOW(), ?)";
$stmtReserva = $NewConn->getConn()->prepare($queryReserva);
$stmtReserva->bind_param("iid", $idUsuario, $idFuncion, $total);
$success = $stmtReserva->execute();

if (!$success) {
    echo json_encode(['success' => false, 'message' => 'Error al crear reserva']);
    exit();
}

$idReserva = $stmtReserva->insert_id;

// Asignar asientos
foreach ($asientos as $codigoAsiento) {
    // Obtener ID del asiento
    $queryAsiento = "SELECT idA FROM CAsientos WHERE codigoAsiento = ? AND idSala = (
                      SELECT idSala FROM CFunciones WHERE idF = ?
                    )";
    $stmtAsiento = $NewConn->getConn()->prepare($queryAsiento);
    $stmtAsiento->bind_param("si", $codigoAsiento, $idFuncion);
    $stmtAsiento->execute();
    $resultAsiento = $stmtAsiento->get_result();
    $asiento = $resultAsiento->fetch_assoc();
    
    if ($asiento) {
        // Actualizar estado del asiento
        $queryUpdate = "UPDATE CAsientos SET estado = 'Ocupado' WHERE idA = ?";
        $stmtUpdate = $NewConn->getConn()->prepare($queryUpdate);
        $stmtUpdate->bind_param("i", $asiento['idA']);
        $stmtUpdate->execute();
        
        // Relacionar asiento con reserva
        $queryRelacion = "INSERT INTO ReservaAsientos (idReserva, idAsiento) VALUES (?, ?)";
        $stmtRelacion = $NewConn->getConn()->prepare($queryRelacion);
        $stmtRelacion->bind_param("ii", $idReserva, $asiento['idA']);
        $stmtRelacion->execute();
    }
}

$NewConn->CloseConnection();

echo json_encode(['success' => true, 'reserva_id' => $idReserva]);
?>