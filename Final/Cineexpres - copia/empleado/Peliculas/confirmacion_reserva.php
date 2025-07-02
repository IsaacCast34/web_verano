<?php
session_start();
require_once "../../Conexion/classConnectionMySQL.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Verificar parámetros de la reserva
if (!isset($_GET['idF']) || !isset($_GET['asientos']) || !isset($_GET['total'])) {
    header("Location: index.php");
    exit();
}

$idFuncion = intval($_GET['idF']);
$asientos = explode(',', $_GET['asientos']);
$total = floatval($_GET['total']);

// Conexión a la base de datos
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Obtener detalles de la función (incluyendo DATE y TIME separados)
$queryFuncion = "SELECT f.*, p.NombrePelicula, p.imagen, s.numeroSala, 
                DATE(f.hora) as fecha_funcion, TIME(f.hora) as hora_funcion
                FROM CFunciones f 
                JOIN CPeliculas p ON f.idPelicula = p.idP 
                JOIN CSalas s ON f.idSala = s.idS 
                WHERE f.idF = $idFuncion";
$resultFuncion = $NewConn->ExecuteQuery($queryFuncion);
$funcion = $resultFuncion->fetch_assoc();

if (!$funcion) {
    header("Location: index.php");
    exit();
}

// Verificar que el usuario esté en sesión
if (!isset($_SESSION['id_usuario'])) {
    die("Error: No se encontró el ID de usuario en la sesión. Asegúrate de guardar el ID del usuario al hacer login.");
}

// Generar un código de reserva único
$codigoReserva = strtoupper(uniqid('CINEX'));

// Insertar la reserva en la base de datos
$idUsuario = $_SESSION['id_usuario'];
$fechaReserva = date('Y-m-d H:i:s');

// Iniciar transacción para asegurar integridad de datos
$NewConn->ExecuteQuery("START TRANSACTION");

try {
    // Insertar encabezado de reserva
    $queryInsert = "INSERT INTO Reservas (idUsuario, idFuncion, fechaReserva) 
                    VALUES ($idUsuario, $idFuncion, '$fechaReserva')";
    $NewConn->ExecuteQuery($queryInsert);
    $idReserva = $NewConn->getConn()->insert_id;

    // Insertar detalles de asientos reservados y actualizar estado
    foreach ($asientos as $codigoAsiento) {
        // Obtener el ID del asiento basado en el código y la sala
        $queryAsiento = "SELECT idA FROM CAsientos 
                        WHERE codigoAsiento = '$codigoAsiento' 
                        AND idSala = {$funcion['idSala']}";
        $resultAsiento = $NewConn->ExecuteQuery($queryAsiento);
        $asientoData = $resultAsiento->fetch_assoc();
        
        if (!$asientoData) {
            throw new Exception("Asiento $codigoAsiento no encontrado en la sala");
        }
        
        $idAsiento = $asientoData['idA'];
        
        // Verificar que el asiento esté disponible
        $queryEstado = "SELECT estado FROM CAsientos WHERE idA = $idAsiento";
        $resultEstado = $NewConn->ExecuteQuery($queryEstado);
        $estadoAsiento = $resultEstado->fetch_assoc()['estado'];
        
        if ($estadoAsiento != 'Disponible') {
            throw new Exception("El asiento $codigoAsiento no está disponible");
        }
        
        // Actualizar el estado del asiento
        $queryUpdateAsiento = "UPDATE CAsientos SET estado = 'Ocupado' 
                             WHERE idA = $idAsiento";
        $NewConn->ExecuteQuery($queryUpdateAsiento);
    }
    
    // Insertar en la tabla de pagos
    $queryPago = "INSERT INTO Pagos (idReserva, metodoPago, monto, estadoPago)
                 VALUES ($idReserva, 'Tarjeta', $total, 'Completado')";
    $NewConn->ExecuteQuery($queryPago);
    
    // Confirmar la transacción si todo salió bien
    $NewConn->ExecuteQuery("COMMIT");
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $NewConn->ExecuteQuery("ROLLBACK");
    die("Error al procesar la reserva: " . $e->getMessage());
}

$NewConn->CloseConnection();

// Contenido que llevaría el QR real (para mostrar en el placeholder)
$qrContent = "CINEXPRESS\nCódigo Reserva: $codigoReserva\nPelícula: {$funcion['NombrePelicula']}\nSala: {$funcion['numeroSala']}\nFecha: ".date("d/m/Y", strtotime($funcion['fecha_funcion']))."\nHora: ".date("h:i A", strtotime($funcion['hora_funcion']))."\nAsientos: ".implode(', ', $asientos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmación de Reserva - Cinexpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    
    :root {
      --azul-oscuro: #1e3a8a;
      --azul-muy-oscuro: #031b42;
      --amarillo-brillante: #facc15;
      --amarillo-oscuro: #eab308;
      --blanco: #ffffff;
      --gris-claro: #f5f5f5;
      --gris-medio: #94a3b8;
      --gris-oscuro: #1f2937;
      --verde-oscuro: #166534;
    }
    
    body {
      background-color: var(--gris-claro);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    header {
      background: linear-gradient(90deg, var(--azul-oscuro) 0%, var(--azul-muy-oscuro) 70%);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .confirmation-container {
      max-width: 1000px;
      margin: 2rem auto;
      background-color: var(--blanco);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    
    .confirmation-header {
      text-align: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #eee;
    }
    
    .confirmation-icon {
      font-size: 4rem;
      color: var(--verde-oscuro);
      margin-bottom: 1rem;
    }
    
    .ticket {
      background: linear-gradient(to bottom right, #f8fafc, #e2e8f0);
      border-radius: 10px;
      padding: 2rem;
      margin-bottom: 2rem;
      border: 1px solid #cbd5e1;
      position: relative;
      overflow: hidden;
    }
    
    .ticket:before {
      content: "";
      position: absolute;
      top: 0;
      left: 30px;
      right: 30px;
      height: 10px;
      background: repeating-linear-gradient(
        to right,
        transparent 0,
        transparent 5px,
        #cbd5e1 5px,
        #cbd5e1 10px
      );
    }
    
    .ticket:after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 30px;
      right: 30px;
      height: 10px;
      background: repeating-linear-gradient(
        to right,
        transparent 0,
        transparent 5px,
        #cbd5e1 5px,
        #cbd5e1 10px
      );
    }
    
    .movie-info {
      display: flex;
      gap: 2rem;
      margin-bottom: 2rem;
    }
    
    .movie-poster {
      width: 150px;
      height: 225px;
      object-fit: cover;
      border-radius: 8px;
    }
    
    .movie-details {
      flex: 1;
    }
    
    .movie-title {
      font-size: 1.8rem;
      color: var(--azul-oscuro);
      margin-bottom: 0.5rem;
    }
    
    .reservation-code {
      background-color: var(--azul-oscuro);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      display: inline-block;
      font-weight: bold;
      margin-bottom: 1rem;
    }
    
    .qr-code {
      text-align: center;
      margin: 2rem 0;
    }
    
    .qr-code img {
      max-width: 200px;
      border: 1px solid #ddd;
      padding: 10px;
      background: white;
    }
    
    .btn-print {
      background-color: var(--azul-oscuro);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 1rem;
    }
    
    .btn-print:hover {
      background-color: var(--azul-muy-oscuro);
      transform: translateY(-2px);
    }
    
    .btn-home {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 1rem;
    }
    
    .btn-home:hover {
      background-color: var(--amarillo-oscuro);
      transform: translateY(-2px);
    }
    
    @media print {
      header, .no-print {
        display: none !important;
      }
      
      body {
        background-color: white;
      }
      
      .confirmation-container {
        box-shadow: none;
        padding: 0;
      }
      
      .ticket {
        border: none;
        padding: 0;
      }
      
      .ticket:before, .ticket:after {
        display: none;
      }
    }
    
    @media (max-width: 768px) {
      .movie-info {
        flex-direction: column;
      }
      
      .movie-poster {
        margin: 0 auto;
      }
    }
  </style>
</head>
<body>
 

  <div class="confirmation-container">
    <div class="confirmation-header">
      <div class="confirmation-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h1>¡Reserva Confirmada!</h1>
      <p class="lead">Tu reserva ha sido procesada exitosamente</p>
      <div class="reservation-code">
        Código de Reserva: <?php echo $codigoReserva; ?>
      </div>
    </div>
    
    <div class="ticket">
      <div class="movie-info">
        <img src="<?php echo htmlspecialchars($funcion['imagen']); ?>" alt="<?php echo htmlspecialchars($funcion['NombrePelicula']); ?>" class="movie-poster">
        <div class="movie-details">
          <h1 class="movie-title"><?php echo htmlspecialchars($funcion['NombrePelicula']); ?></h1>
          <div class="function-info">
            <p><strong>Sala:</strong> <?php echo htmlspecialchars($funcion['numeroSala']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($funcion['fecha_funcion'])); ?></p>
            <p><strong>Hora:</strong> <?php echo date("h:i A", strtotime($funcion['hora_funcion'])); ?></p>
            <p><strong>Asientos:</strong> <?php echo implode(', ', $asientos); ?></p>
            <p><strong>Total pagado:</strong> $<?php echo number_format($total, 2); ?></p>
          </div>
        </div>
      </div>
      
      <!-- QR Placeholder -->
      <div class="qr-code">
        <div style="border: 2px dashed #ccc; padding: 15px; display: inline-block; border-radius: 10px; background: #f9f9f9;">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=CINEXPRESS-DEMO-<?php; ?>" 
               alt="QR de ejemplo" 
               style="width: 200px; height: 200px; border: 1px solid #eee;">
          <p style="margin-top: 10px; font-style: italic; color: #7f8c8d;">(Código QR demostrativo)</p>
        </div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px; text-align: left;">
          <h4 style="color: #2c3e50; margin-bottom: 10px;">Contenido del QR real:</h4>
          <pre style="color: #3498db; white-space: pre-wrap;"><?php echo htmlspecialchars($qrContent); ?></pre>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <h4>Instrucciones:</h4>
          <ul>
            <li>Llega al menos 30 minutos antes de la función</li>
            <li>Presenta este comprobante o el código QR</li>
            <li>Muestra una identificación válida</li>
            <li>Los asientos se asignan por orden de llegada</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h4>Políticas:</h4>
          <ul>
            <li>No se permiten cambios ni devoluciones</li>
            <li>Se requiere identificación para recoger los boletos</li>
            <li>Los niños menores de 3 años no pagan entrada</li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="text-center no-print">
      <button class="btn-print me-2" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir Comprobante
      </button>
      <a href="index.php" class="btn-home">
        <i class="fas fa-home"></i> Volver al Inicio
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>