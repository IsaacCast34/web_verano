<?php
session_start();
require_once "Conexion/classConnectionMySQL.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y limpiar entradas
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $fechaNacimiento = filter_input(INPUT_POST, 'fechaNacimiento', FILTER_SANITIZE_STRING);

    // Validaciones adicionales
    $errors = [];
    
    if (empty($nombre) || strlen($nombre) < 3) {
        $errors[] = "El nombre debe tener al menos 3 caracteres";
    }
    
    if (empty($usuario) || strlen($usuario) < 4) {
        $errors[] = "El usuario debe tener al menos 4 caracteres";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email no es válido";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if (empty($fechaNacimiento)) {
        $errors[] = "La fecha de nacimiento es obligatoria";
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: registro.php");
        exit();
    }

    // Verificar si el usuario o email ya existen
    $conexion = new ConnectionMySQL();
    $conexion->CreateConnection();

    $stmt = $conexion->getConn()->prepare("SELECT idL FROM Usuario WHERE usuario = ? OR email = ?");
    $stmt->bind_param("ss", $usuario, $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['error'] = "El usuario o email ya están registrados";
        header("Location: registro.php");
        exit();
    }

    // Insertar nuevo usuario (tipo 'usuario' por defecto)
    $tipo = 'usuario';
    $stmt = $conexion->getConn()->prepare("INSERT INTO Usuario (Nombre, usuario, email, Contraseña, Teléfono, FechaNacimiento, Tipo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nombre, $usuario, $email, $password, $telefono, $fechaNacimiento, $tipo);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registro exitoso. Ahora puedes iniciar sesión.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al registrar. Intenta de nuevo.";
        header("Location: registro.php");
        exit();
    }

    $stmt->close();
    $conexion->CloseConnection();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Cinexpress</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #3a0ca3;
      --primary-dark: #1a065a;
      --secondary: #f72585;
      --accent: #4cc9f0;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #4ade80;
      --error: #f87171;
      --warning: #fbbf24;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    
    .register-container {
      width: 100%;
      max-width: 1000px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    
    .register-header {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      padding: 30px;
      text-align: center;
    }
    
    .register-header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      font-weight: 700;
    }
    
    .register-header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }
    
    .register-content {
      display: flex;
      flex-direction: column;
    }
    
    .register-image {
      height: 300px;
      background: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat;
    }
    
    .register-form {
      padding: 30px;
    }
    
    .form-group {
      margin-bottom: 20px;
      position: relative;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--dark);
    }
    
    .input-with-icon {
      position: relative;
    }
    
    .form-control {
      width: 100%;
      padding: 15px 20px 15px 45px;
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      font-size: 16px;
      transition: all 0.3s;
      background-color: #f8fafc;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
      background-color: white;
    }
    
    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
    }
    
    .btn {
      display: inline-block;
      padding: 15px 30px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      text-align: center;
      text-decoration: none;
      width: 100%;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      margin-top: 10px;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .login-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }
    
    .login-link:hover {
      text-decoration: underline;
    }
    
    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    
    .alert-error {
      background-color: #fee2e2;
      color: #b91c1c;
      border-left: 4px solid #ef4444;
    }
    
    .alert-success {
      background-color: #dcfce7;
      color: #166534;
      border-left: 4px solid #22c55e;
    }
    
    .error-list {
      list-style-type: none;
      background-color: #fee2e2;
      color: #b91c1c;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      border-left: 4px solid #ef4444;
    }
    
    .error-list li {
      margin-bottom: 5px;
    }
    
    .error-list li:last-child {
      margin-bottom: 0;
    }
    
    @media (min-width: 768px) {
      .register-content {
        flex-direction: row;
      }
      
      .register-image {
        flex: 1;
        height: auto;
      }
      
      .register-form {
        flex: 1;
        padding: 40px;
      }
    }
    
    /* Animaciones */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .register-container {
      animation: fadeIn 0.5s ease-out;
    }
    
    .form-group {
      animation: fadeIn 0.5s ease-out forwards;
    }
    
    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }
    .form-group:nth-child(5) { animation-delay: 0.5s; }
    .form-group:nth-child(6) { animation-delay: 0.6s; }
  </style>
</head>
<body>
  <div class="register-container">
    <div class="register-header">
      <h1>Únete a Cinexpress</h1>
      <p>Regístrate para disfrutar de la mejor experiencia cinematográfica</p>
    </div>
    
    <div class="register-content">
      <div class="register-image"></div>
      
      <div class="register-form">
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['errors'])): ?>
          <ul class="error-list">
            <?php foreach ($_SESSION['errors'] as $error): ?>
              <li><?php echo $error; ?></li>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
          </ul>
        <?php endif; ?>
        
        <form action="registro.php" method="POST">
          <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <div class="input-with-icon">
              <i class="fas fa-user input-icon"></i>
              <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="usuario">Nombre de usuario</label>
            <div class="input-with-icon">
              <i class="fas fa-at input-icon"></i>
              <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ej: juan123" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <div class="input-with-icon">
              <i class="fas fa-envelope input-icon"></i>
              <input type="email" id="email" name="email" class="form-control" placeholder="Ej: juan@example.com" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-with-icon">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="telefono">Teléfono (opcional)</label>
            <div class="input-with-icon">
              <i class="fas fa-phone input-icon"></i>
              <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Ej: 5551234567">
            </div>
          </div>
          
          <div class="form-group">
            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <div class="input-with-icon">
              <i class="fas fa-calendar input-icon"></i>
              <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" required>
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Crear cuenta
          </button>
        </form>
        
        <a href="login.php" class="login-link">
          ¿Ya tienes una cuenta? Inicia sesión aquí
        </a>
      </div>
    </div>
  </div>
</body>
</html>