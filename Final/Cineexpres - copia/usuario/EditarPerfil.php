
<?php
session_start();
require_once "../Conexion/classConnectionMySQL.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['idL']);
    $nombre = trim($_POST['Nombre']);
    $telefono = trim($_POST['Teléfono']);
    $email = trim($_POST['email']);
    $fechaNacimiento = trim($_POST['FechaNacimiento']);

    $query = "UPDATE Usuario SET Nombre = ?, Teléfono = ?, email = ?, FechaNacimiento = ? WHERE idL = ?";
    $stmt = $NewConn->getConn()->prepare($query);
    $stmt->bind_param("ssssi", $nombre, $telefono, $email, $fechaNacimiento, $id);

    if ($stmt->execute()) {
        header("Location: perfil.php?success=1");
        exit();
    } else {
        $error = "Error al actualizar el perfil";
    }
} else {
    // Obtener datos del usuario actual
    $query = "SELECT * FROM Usuario WHERE usuario = ?";
    $stmt = $NewConn->getConn()->prepare($query);
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Usuario no encontrado.");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil - CineExpress</title>
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
    
    .profile-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      max-width: 800px;
      margin: 2rem auto;
    }
    
    .profile-header {
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
      color: white;
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
      color: white;
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
    
    .user-icon {
      font-size: 1rem;
      color: var(--accent-color);
      margin-right: 8px;
    }
    
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="profile-card">
    <div class="profile-header">
      <h2><i class="fas fa-user-edit me-2"></i>Editar Mi Perfil</h2>
    </div>
    
    <div class="form-container">
      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success-message">
          <i class="fas fa-check-circle me-2"></i>Perfil actualizado correctamente
        </div>
      <?php endif; ?>
      
      <?php if (isset($error)): ?>
        <div class="error-message mb-3">
          <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="EditarPerfil.php">
        <input type="hidden" name="idL" value="<?php echo $row['idL']; ?>">

        <div class="mb-3">
          <label class="form-label">
            <i class="fas fa-user user-icon"></i>Nombre Completo:
          </label>
          <input type="text" name="Nombre" value="<?php echo htmlspecialchars($row['Nombre']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">
            <i class="fas fa-envelope user-icon"></i>Correo Electrónico:
          </label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">
            <i class="fas fa-phone user-icon"></i>Teléfono:
          </label>
          <input type="text" name="Teléfono" value="<?php echo htmlspecialchars($row['Teléfono']); ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">
            <i class="fas fa-birthday-cake user-icon"></i>Fecha de Nacimiento:
          </label>
          <input type="date" name="FechaNacimiento" value="<?php echo htmlspecialchars($row['FechaNacimiento']); ?>" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-submit w-100">
          <i class="fas fa-save me-2"></i>Guardar Cambios
        </button>
        <a href="perfil.php" class="btn btn-cancel w-100 mt-2">
          <i class="fas fa-times me-2"></i>Cancelar
        </a>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>