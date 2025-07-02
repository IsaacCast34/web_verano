<?php
session_start();
require_once "Conexion/classConnectionMySQL.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $tipo = trim($_POST['tipo']);

    $conexion = new ConnectionMySQL();
    $conexion->CreateConnection();

    $stmt = $conexion->getConn()->prepare("SELECT * FROM Usuario WHERE usuario = ? AND Tipo = ?");
    $stmt->bind_param("ss", $usuario, $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        // Compara contraseñas sin encriptar (deberías considerar usar password_verify si las contraseñas están hasheadas)
        if ($password === $fila['Contraseña']) {
            // Configurar todos los datos necesarios en la sesión
            $_SESSION['usuario'] = $usuario;
            $_SESSION['tipo'] = $tipo;
            $_SESSION['id_usuario'] = $fila['idL']; // Esto es lo que faltaba
            $_SESSION['nombre'] = $fila['Nombre']; // Opcional: guardar el nombre para mostrarlo
            $_SESSION['email'] = $fila['email']; // Opcional: guardar email

            // Redirigir según el tipo
            switch ($tipo) {
                case 'administrador':
                    header("Location: administrador/indexAdministrador.php");
                    break;
                case 'empleado':
                    header("Location: empleado/indexEmpleado.php");
                    break;
                case 'usuario':
                    header("Location: usuario/index.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = 'Contraseña incorrecta';
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'Usuario o tipo de cuenta incorrectos';
        header("Location: login.php");
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
  <title>Iniciar Sesión - Cinexpress</title>
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
    
    .login-container {
      width: 100%;
      max-width: 500px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }
    
    .login-header {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      padding: 30px;
      text-align: center;
    }
    
    .login-header h1 {
      font-size: 2rem;
      margin-bottom: 10px;
      font-weight: 700;
    }
    
    .login-header p {
      font-size: 1rem;
      opacity: 0.9;
    }
    
    .login-form {
      padding: 30px;
    }
    
    .form-group {
      margin-bottom: 20px;
      position: relative;
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
    
    select.form-control {
      appearance: none;
      padding-left: 45px;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 20px;
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
    
    .btn-login {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      margin-bottom: 15px;
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-register {
      background: white;
      color: var(--primary);
      border: 2px solid var(--primary);
    }
    
    .btn-register:hover {
      background-color: #f8fafc;
      transform: translateY(-2px);
    }
    
    .register-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }
    
    .register-link:hover {
      text-decoration: underline;
    }
    
    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .alert-error {
      background-color: #fee2e2;
      color: #b91c1c;
      border-left: 4px solid #ef4444;
    }
    
    .alert i {
      font-size: 1.2rem;
    }
    
    /* Animaciones */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .login-container {
      animation: fadeIn 0.5s ease-out;
    }
    
    .form-group {
      animation: fadeIn 0.5s ease-out forwards;
    }
    
    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h1>Bienvenido a Cinexpress</h1>
      <p>Inicia sesión para disfrutar de la mejor experiencia cinematográfica</p>
    </div>
    
    <div class="login-form">
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      
      <form action="login.php" method="POST">
        <div class="form-group">
          <div class="input-with-icon">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
          </div>
        </div>
        
        <div class="form-group">
          <div class="input-with-icon">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
          </div>
        </div>
        
        <div class="form-group">
          <div class="input-with-icon">
            <i class="fas fa-user-tag input-icon"></i>
            <select name="tipo" class="form-control" required>
              <option value="">Seleccionar tipo de cuenta</option>
              <option value="administrador">Administrador</option>
              <option value="empleado">Empleado</option>
              <option value="usuario">Usuario</option>
            </select>
          </div>
        </div>
        
        <button type="submit" class="btn btn-login">
          <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>
        
        <a href="registro.php" class="btn btn-register">
          <i class="fas fa-user-plus"></i> Crear Cuenta
        </a>
      </form>
      
      
    </div>
  </div>
</body>
</html>