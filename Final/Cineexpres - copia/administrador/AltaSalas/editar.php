<?php
require("../../Conexion/classConnectionMySQL.php");

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idS = $_POST['idS'];
    $numeroSala = $_POST['numeroSala'];
    $capacidadSala = $_POST['capacidadSala'];
    $tipoAsiento = $_POST['tipoAsiento'];
    $numeracionSala = $_POST['numeracionSala'];
    $tipoSala = $_POST['tipoSala'];
    $estado = $_POST['estado'];

    $query = "UPDATE CSalas SET 
                numeroSala = ?, 
                capacidadSala = ?, 
                tipoAsiento = ?, 
                numeracionSala = ?, 
                tipoSala = ?, 
                estado = ?
              WHERE idS = ?";
    $stmt = $NewConn->getConn()->prepare($query);
    $stmt->bind_param("iissssi", $numeroSala, $capacidadSala, $tipoAsiento, $numeracionSala, $tipoSala, $estado, $idS);

    if ($stmt->execute()) {
        header("Location: salas.php");
        exit;
    } else {
        $error_message = "Error al actualizar la sala: " . $stmt->error;
    }
} else {
    if (!isset($_GET['idS']) || !is_numeric($_GET['idS'])) {
        die("<h1>ID inválido.</h1>");
    }

    $idS = intval($_GET['idS']);
    $query = "SELECT * FROM CSalas WHERE idS = $idS";
    $result = $NewConn->ExecuteQuery($query);
    $row = $result->fetch_assoc();

    if (!$row) {
        die("<h1>Sala no encontrada.</h1>");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Sala - CineExpress</title>
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
    
    .form-control, .form-select {
      border-radius: 8px;
      padding: 0.75rem;
      border: 1px solid #ced4da;
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
  <div class="admin-card">
    <div class="admin-header">
      <h2><i class="fas fa-edit me-2"></i>Editar Sala #<?php echo $row['numeroSala']; ?></h2>
    </div>
    
    <div class="form-container">
      <?php if(isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <input type="hidden" name="idS" value="<?= $row['idS'] ?>">
        
        <div class="row g-3">
          <div class="col-md-6">
            <label for="numeroSala" class="form-label">Número de Sala</label>
            <input type="number" id="numeroSala" name="numeroSala" class="form-control" 
                   value="<?= $row['numeroSala'] ?>" required>
          </div>
          
          <div class="col-md-6">
            <label for="capacidadSala" class="form-label">Capacidad</label>
            <input type="number" id="capacidadSala" name="capacidadSala" class="form-control" 
                   value="<?= $row['capacidadSala'] ?>" required>
          </div>
          
          <div class="col-md-6">
            <label for="tipoAsiento" class="form-label">Tipo de Asiento</label>
            <input type="text" id="tipoAsiento" name="tipoAsiento" class="form-control" 
                   value="<?= $row['tipoAsiento'] ?>" required>
          </div>
          
          <div class="col-md-6">
            <label for="numeracionSala" class="form-label">Numeración de Sala</label>
            <input type="text" id="numeracionSala" name="numeracionSala" class="form-control" 
                   value="<?= $row['numeracionSala'] ?>" required>
          </div>
          
          <div class="col-md-6">
            <label for="tipoSala" class="form-label">Tipo de Sala</label>
            <select id="tipoSala" name="tipoSala" class="form-select" required>
              <option value="2D" <?= $row['tipoSala'] == '2D' ? 'selected' : '' ?>>2D</option>
              <option value="3D" <?= $row['tipoSala'] == '3D' ? 'selected' : '' ?>>3D</option>
              <option value="IMAX" <?= $row['tipoSala'] == 'IMAX' ? 'selected' : '' ?>>IMAX</option>
            </select>
          </div>
          
          <div class="col-md-6">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select" required>
              <option value="Activa" <?= $row['estado'] == 'Activa' ? 'selected' : '' ?>>Activa</option>
              <option value="Inactiva" <?= $row['estado'] == 'Inactiva' ? 'selected' : '' ?>>Inactiva</option>
              <option value="Mantenimiento" <?= $row['estado'] == 'Mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
            </select>
          </div>
          
          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-submit text-white w-100">
              <i class="fas fa-save me-2"></i>Actualizar Sala
            </button>
            <a href="salas.php" class="btn btn-cancel text-white w-100 mt-2">
              <i class="fas fa-times me-2"></i>Cancelar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>