<?php
require("../../Conexion/classConnectionMySQL.php");

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idPelicula = $_POST['idPelicula'];
    $idSala = $_POST['idSala'];
    $informacion = trim($_POST['informacion']);
    $hora = $_POST['hora'];
    $precioEntrada = $_POST['precioEntrada'];
    $promocion = $_POST['promocion'];

    $query = "INSERT INTO CFunciones (idPelicula, idSala, informacion, hora, precioEntrada, promocion)
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $NewConn->getConn()->prepare($query);
    $stmt->bind_param("iissdd", $idPelicula, $idSala, $informacion, $hora, $precioEntrada, $promocion);

    if ($stmt->execute()) {
        header("Location: funciones.php");
        exit;
    } else {
        $error_message = "Error al registrar la función: " . $stmt->error;
    }
}

$peliculas = $NewConn->ExecuteQuery("SELECT idP, NombrePelicula FROM CPeliculas");
$salas = $NewConn->ExecuteQuery("SELECT idS, numeroSala FROM CSalas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Función - CineExpress</title>
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
    
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(rgba(245, 247, 255, 0.9), rgba(245, 247, 255, 0.9)),
                  url('https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center;
      background-size: cover;
      background-attachment: fixed;
      display: flex;
      flex-direction: column;
    }
    
    .wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .admin-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      max-width: 800px;
      margin: 2rem auto;
      width: 90%;
      border: none;
    }
    
    .admin-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1.5rem;
      text-align: center;
      position: relative;
    }
    
    .admin-header h2 {
      position: relative;
      z-index: 2;
    }
    
    .function-icon {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.2);
      font-size: 3rem;
      z-index: 1;
    }
    
    .form-container {
      padding: 2rem;
      background-color: white;
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
      color: white;
    }
    
    .btn-submit:hover {
      background: #d91a6d;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(247, 37, 133, 0.3);
    }
    
    .btn-cancel {
      background: #6c757d;
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }
    
    .btn-cancel:hover {
      background: #5a6268;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }
    
    .form-control, .form-select, .form-textarea {
      border-radius: 8px;
      padding: 0.75rem;
      border: 1px solid #ced4da;
      transition: all 0.3s ease;
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
  <div class="wrapper">
    <div class="admin-card">
      <div class="admin-header">
        <h2><i class="fas fa-calendar-alt me-2"></i>Agregar Nueva Función</h2>
        <i class="fas fa-ticket-alt function-icon"></i>
      </div>
      
      <div class="form-container">
        <?php if(isset($error_message)): ?>
          <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="idPelicula" class="form-label">
                <i class="fas fa-film"></i> Película
              </label>
              <select id="idPelicula" name="idPelicula" class="form-select" required>
                <option value="">Selecciona una película</option>
                <?php while ($fila = $peliculas->fetch_assoc()): ?>
                  <option value="<?= $fila['idP'] ?>"><?= htmlspecialchars($fila['NombrePelicula']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            
            <div class="col-md-6">
              <label for="idSala" class="form-label">
                <i class="fas fa-door-open"></i> Sala
              </label>
              <select id="idSala" name="idSala" class="form-select" required>
                <option value="">Selecciona una sala</option>
                <?php while ($fila = $salas->fetch_assoc()): ?>
                  <option value="<?= $fila['idS'] ?>">Sala <?= htmlspecialchars($fila['numeroSala']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            
            <div class="col-12">
              <label for="informacion" class="form-label">
                <i class="fas fa-info-circle"></i> Información adicional
              </label>
              <textarea id="informacion" name="informacion" class="form-control form-textarea" required></textarea>
            </div>
            
            <div class="col-md-6">
              <label for="hora" class="form-label">
                <i class="fas fa-clock"></i> Hora de la función
              </label>
              <input type="time" id="hora" name="hora" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label for="precioEntrada" class="form-label">
                <i class="fas fa-ticket-alt"></i> Precio de entrada
              </label>
              <div class="currency-input">
                <input type="number" id="precioEntrada" name="precioEntrada" class="form-control" step="0.01" min="0" required>
              </div>
            </div>
            
            <div class="col-md-6">
              <label for="promocion" class="form-label">
                <i class="fas fa-percent"></i> Promoción (Nuevo precio)
              </label>
              <input type="number" id="promocion" name="promocion" class="form-control" step="0.01" min="0" max="100" required>
            </div>
            
            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-submit w-100">
                <i class="fas fa-save me-2"></i>Guardar Función
              </button>
              <a href="funciones.php" class="btn btn-cancel w-100 mt-2">
                <i class="fas fa-times me-2"></i>Cancelar
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>