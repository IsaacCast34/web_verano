<?php
require("../../Conexion/classConnectionMySQL.php");

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numeroSala = $_POST['numeroSala'];
    $capacidadSala = $_POST['capacidadSala'];
    $tipoAsiento = $_POST['tipoAsiento'];
    $numeracionSala = $_POST['numeracionSala'];
    $tipoSala = $_POST['tipoSala'];
    $estado = $_POST['estado'];

    $query = "INSERT INTO CSalas (numeroSala, capacidadSala, tipoAsiento, numeracionSala, tipoSala, estado)
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $NewConn->getConn()->prepare($query);
    $stmt->bind_param("iissss", $numeroSala, $capacidadSala, $tipoAsiento, $numeracionSala, $tipoSala, $estado);

    if ($stmt->execute()) {
        header("Location: salas.php");
        exit;
    } else {
        $error_message = "Error al registrar la sala: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Sala - CineExpress</title>
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
    
    .sala-icon {
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
    
    .form-control, .form-select {
      border-radius: 8px;
      padding: 0.75rem;
      border: 1px solid #ced4da;
      transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }
    
    .error-message {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="admin-card">
      <div class="admin-header">
        <h2><i class="fas fa-plus-circle me-2"></i>Registrar Nueva Sala</h2>
        <i class="fas fa-theater-masks sala-icon"></i>
      </div>
      
      <div class="form-container">
        <?php if(isset($error_message)): ?>
          <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="numeroSala" class="form-label">Número de Sala</label>
              <input type="number" id="numeroSala" name="numeroSala" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label for="capacidadSala" class="form-label">Capacidad</label>
              <input type="number" id="capacidadSala" name="capacidadSala" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label for="tipoAsiento" class="form-label">Tipo de Asiento</label>
              <input type="text" id="tipoAsiento" name="tipoAsiento" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label for="numeracionSala" class="form-label">Numeración de Sala</label>
              <input type="text" id="numeracionSala" name="numeracionSala" class="form-control" 
                     placeholder="Ej: A1-A10, B1-B10..." required>
            </div>
            
            <div class="col-md-6">
              <label for="tipoSala" class="form-label">Tipo de Sala</label>
              <select id="tipoSala" name="tipoSala" class="form-select" required>
                <option value="2D">2D</option>
                <option value="3D">3D</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label for="estado" class="form-label">Estado</label>
              <select id="estado" name="estado" class="form-select" required>
                <option value="Activa">Activa</option>
                <option value="Inactiva">Inactiva</option>
              </select>
            </div>
            
            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-submit w-100">
                <i class="fas fa-save me-2"></i>Guardar Sala
              </button>
              <a href="salas.php" class="btn btn-cancel w-100 mt-2">
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