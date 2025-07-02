<?php
session_start();
require_once "../../Conexion/classConnectionMySQL.php";

header('Content-Type: application/json');

// Validar datos del formulario
if (!isset($_POST['idFuncion']) || !isset($_POST['asientos']) || !isset($_POST['total'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

// Obtener datos del formulario
$idFuncion = intval($_POST['idFuncion']);
$asientos = explode(',', $_POST['asientos']);
$total = floatval($_POST['total']);

// Detectar método de pago (por defecto tarjeta)
$metodoPago = isset($_POST['pago']) && $_POST['pago'] === 'efectivo' ? 'Efectivo' : 'Tarjeta';

// Conexión a la base de datos
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Obtener ID del usuario desde la sesión
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

// Insertar la reserva
$queryReserva = "INSERT INTO Reservas (idUsuario, idFuncion, fechaReserva, total, metodoPago) 
                 VALUES (?, ?, NOW(), ?, ?)";
$stmtReserva = $NewConn->getConn()->prepare($queryReserva);
$stmtReserva->bind_param("iids", $idUsuario, $idFuncion, $total, $metodoPago);

if (!$stmtReserva->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al crear la reserva']);
    exit();
}

$idReserva = $stmtReserva->insert_id;

// Asignar asientos a la reserva
foreach ($asientos as $codigoAsiento) {
    // Obtener ID del asiento específico por sala
    $queryAsiento = "SELECT idA FROM CAsientos WHERE codigoAsiento = ? AND idSala = (
                        SELECT idSala FROM CFunciones WHERE idF = ?
                    )";
    $stmtAsiento = $NewConn->getConn()->prepare($queryAsiento);
    $stmtAsiento->bind_param("si", $codigoAsiento, $idFuncion);
    $stmtAsiento->execute();
    $resultAsiento = $stmtAsiento->get_result();
    $asiento = $resultAsiento->fetch_assoc();

    if ($asiento) {
        $idAsiento = $asiento['idA'];

        // Marcar asiento como ocupado
        $queryUpdate = "UPDATE CAsientos SET estado = 'Ocupado' WHERE idA = ?";
        $stmtUpdate = $NewConn->getConn()->prepare($queryUpdate);
        $stmtUpdate->bind_param("i", $idAsiento);
        $stmtUpdate->execute();

        // Insertar relación reserva-asiento
        $queryRelacion = "INSERT INTO ReservaAsientos (idReserva, idAsiento) VALUES (?, ?)";
        $stmtRelacion = $NewConn->getConn()->prepare($queryRelacion);
        $stmtRelacion->bind_param("ii", $idReserva, $idAsiento);
        $stmtRelacion->execute();
    }
}

$NewConn->CloseConnection();

// Respuesta exitosa
echo json_encode(['success' => true, 'reserva_id' => $idReserva]);
exit();
?>
