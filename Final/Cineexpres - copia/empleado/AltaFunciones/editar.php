<?php
require("../../Conexion/classConnectionMySQL.php");

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idF = intval($_POST['idF']);
    $idPelicula = intval($_POST['idPelicula']);
    $idSala = intval($_POST['idSala']);
    $informacion = trim($_POST['informacion']);
    $hora = $_POST['hora'];
    $precioEntrada = floatval($_POST['precioEntrada']);
    $promocion = floatval($_POST['promocion']);

    // Validaciones
    if (empty($idPelicula) || empty($idSala) || empty($informacion) || empty($hora)) {
        $error_message = "Todos los campos obligatorios deben completarse";
    } elseif ($precioEntrada <= 0) {
        $error_message = "El precio de entrada debe ser mayor que 0";
    } elseif ($promocion < 0) {
        $error_message = "La promoción no puede ser negativa";
    } else {
        $query = "UPDATE CFunciones 
                  SET idPelicula = ?, idSala = ?, informacion = ?, hora = ?, precioEntrada = ?, promocion = ?
                  WHERE idF = ?";
        $stmt = $NewConn->getConn()->prepare($query);
        $stmt->bind_param("iissddi", $idPelicula, $idSala, $informacion, $hora, $precioEntrada, $promocion, $idF);

        if ($stmt->execute()) {
            header("Location: funciones.php");
            exit;
        } else {
            $error_message = "Error al actualizar la función: " . $stmt->error;
        }
    }
} else {
    // Validación del parámetro GET
    if (!isset($_GET['idF']) || !is_numeric($_GET['idF'])) {
        die("<h1>ID de función inválido.</h1>");
    }

    $idF = intval($_GET['idF']);
    $query = "SELECT * FROM CFunciones WHERE idF = $idF";
    $result = $NewConn->ExecuteQuery($query);
    $row = $result->fetch_assoc();

    if (!$row) {
        die("<h1>Función no encontrada.</h1>");
    }

    // Obtener datos para los select
    $peliculas = $NewConn->ExecuteQuery("SELECT idP, NombrePelicula FROM CPeliculas");
    $salas = $NewConn->ExecuteQuery("SELECT idS, numeroSala FROM CSalas");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Función - CineExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #3a0ca3;
      --secondary-color: #4361ee;
      --accent-color: #f72585;
      --light-bg: #f8f9fa;
      --dark-bg: #212529;
    }
    
    body {
      background-color: #f5f7ff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .admin-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      max-width: 800px;
      margin: 2rem auto;
    }
    
    .admin-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1.5rem;
      text-align: center;
    }
    
    .form-container {
      padding: 2rem;
    }
    
    .form-label {
      font-weight: 600;
      color: var(--primary-color);
    }
    
    .btn-submit {
      background: var(--accent-color);
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
      background: #d91a6d;
      transform: translateY(-2px);
    }
    
    .btn-cancel {
      background: #6c757d;
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
      background: #5a6268;
      transform: translateY(-2px);
    }
    
    .form-control, .form-select, .form-textarea {
      border-radius: 8px;
      padding: 0.75rem;
      border: 1px solid #ced4da;
    }
    
    .form-control:focus, .form-select:focus, .form-textarea:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }
    
    .error-message {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    
    .function-icon {
      font-size: 1rem;
      color: var(--accent-color);
      margin-right: 8px;
    }
    
    .form-textarea {
      min-height: 120px;
      resize: vertical;
    }
    
    .currency-input {
      position: relative;
    }
    
    .currency-input::before {
      content: '$';
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #495057;
      font-weight: bold;
    }
    
    .currency-input input {
      padding-left: 25px;
    }
  </style>
</head>
<body>
  <div class="admin-card">
    <div class="admin-header">
      <h2><i class="fas fa-calendar-alt me-2"></i>Editar Función #<?php echo $row['idF']; ?></h2>
    </div>
    
    <div class="form-container">
      <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <input type="hidden" name="idF" value="<?php echo $row['idF']; ?>">
        
        <div class="row g-3">
          <div class="col-md-6">
            <label for="idPelicula" class="form-label">
              <i class="fas fa-film function-icon"></i>Película
            </label>
            <select name="idPelicula" class="form-select" required>
              <option value="">Selecciona una película</option>
              <?php while ($p = $peliculas->fetch_assoc()): ?>
                <option value="<?= $p['idP'] ?>" <?= $p['idP'] == $row['idPelicula'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['NombrePelicula']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          
          <div class="col-md-6">
            <label for="idSala" class="form-label">
              <i class="fas fa-door-open function-icon"></i>Sala
            </label>
            <select name="idSala" class="form-select" required>
              <option value="">Selecciona una sala</option>
              <?php while ($s = $salas->fetch_assoc()): ?>
                <option value="<?= $s['idS'] ?>" <?= $s['idS'] == $row['idSala'] ? 'selected' : '' ?>>
                  Sala <?= htmlspecialchars($s['numeroSala']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          
          <div class="col-12">
            <label for="informacion" class="form-label">
              <i class="fas fa-info-circle function-icon"></i>Información adicional
            </label>
            <textarea name="informacion" class="form-control form-textarea" required><?= htmlspecialchars($row['informacion']) ?></textarea>
          </div>
          
          <div class="col-md-6">
            <label for="hora" class="form-label">
              <i class="fas fa-clock function-icon"></i>Hora de la función
            </label>
            <input type="time" name="hora" class="form-control" value="<?= $row['hora'] ?>" required>
          </div>
          
          <div class="col-md-6">
            <label for="precioEntrada" class="form-label">
              <i class="fas fa-ticket-alt function-icon"></i>Precio de entrada
            </label>
            <div class="currency-input">
              <input type="number" name="precioEntrada" class="form-control" step="0.01" min="0" 
                     value="<?= number_format($row['precioEntrada'], 2, '.', '') ?>" required>
            </div>
          </div>
          
          <div class="col-md-6">
            <label for="promocion" class="form-label">
              <i class="fas fa-percent function-icon"></i>Promoción (% descuento)
            </label>
            <input type="number" name="promocion" class="form-control" step="0.01" min="0" max="100" 
                   value="<?= number_format($row['promocion'], 2, '.', '') ?>" required>
          </div>
          
          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-submit text-white w-100">
              <i class="fas fa-save me-2"></i>Actualizar Función
            </button>
            <a href="funciones.php" class="btn btn-cancel text-white w-100 mt-2">
              <i class="fas fa-times me-2"></i>Cancelar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Validación adicional en el cliente
    document.querySelector('form').addEventListener('submit', function(e) {
      const precio = parseFloat(document.querySelector('input[name="precioEntrada"]').value);
      const promocion = parseFloat(document.querySelector('input[name="promocion"]').value);
      
      if (precio <= 0) {
        alert('El precio de entrada debe ser mayor que 0');
        e.preventDefault();
      }
      
      if (promocion < 0 || promocion > 100) {
        alert('La promoción debe estar entre 0 y 100');
        e.preventDefault();
      }
    });
  </script>
</body>
</html>
<?php
    $NewConn->CloseConnection();
