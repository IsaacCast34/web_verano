<?php
session_start();
require_once "../../Conexion/classConnectionMySQL.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario
$conexion = new ConnectionMySQL();
$conexion->CreateConnection();

$query = "SELECT Nombre, usuario, email, Teléfono, FechaNacimiento, Tipo 
          FROM Usuario 
          WHERE usuario = ?";
$stmt = $conexion->getConn()->prepare($query);
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$stmt->close();
$conexion->CloseConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil - Cinexpress</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .user-dropdown {
  position: relative;
  display: inline-block;
  cursor: pointer;
}

.user-name {
  padding: 8px 16px;
  border-radius: 4px;
  background-color: rgba(255, 255, 255, 0.1);
  color: var(--blanco);
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.user-name:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.user-name i {
  font-size: 1.2rem;
}

.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  background-color: var(--blanco);
  min-width: 180px;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  border-radius: 6px;
  overflow: hidden;
  margin-top: 0px;
}

.dropdown-content a {
  color: var(--gris-oscuro);
  padding: 12px 16px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
}

.dropdown-content a i {
  width: 0px;
  text-align: center;
}

.dropdown-content a:hover {
  background-color: var(--gris-claro);
  border-left: 3px solid var(--amarillo-brillante);
  color: var(--azul-oscuro);
}

.user-dropdown:hover .dropdown-content {
  display: block;
}

/* Estilo especial para el botón de cerrar sesión */
.dropdown-content a.logout {
  color: var(--rojo-oscuro);
  border-top: 0px solid var(--gris-claro);
}

.dropdown-content a.logout:hover {
  background-color: #fee2e2;
  border-left: 3px solid var(--rojo-oscuro);
}

.btn-login {
  background-color: var(--amarillo-brillante);
  color: var(--gris-oscuro);
  padding: 8px 16px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  transition: background-color 0.3s;
}

.btn-login:hover {
  background-color: var(--amarillo-oscuro);
}

/* Estilos para el mensaje de cierre de sesión */
.logout-message {
  background-color: #dcfce7;
  color: #166534;
  padding: 10px 15px;
  border-radius: 4px;
  margin-bottom: 15px;
  text-align: center;
  font-size: 0.9rem;
}
    :root {
      --azul-oscuro: #1e3a8a;
      --azul-muy-oscuro: #031b42;
      --amarillo-brillante: #facc15;
      --amarillo-oscuro: #eab308;
      --blanco: #ffffff;
      --gris-claro: #f5f5f5;
      --gris-medio: #94a3b8;
      --gris-oscuro: #1f2937;
      --rojo-oscuro: #8b0000;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background-color: var(--gris-claro);
      color: var(--gris-oscuro);
      line-height: 1.6;
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
    
    header h1 {
      font-size: 48px;
      color: var(--amarillo-brillante);
      text-shadow:
        -2px -2px 0 #3b5ea0,
        2px -2px 0 #3b5ea0,
        -2px 2px 0 #3b5ea0,
        2px 2px 0 #3b5ea0;
      margin: 0;
      letter-spacing: 2px;
    }
    
    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
      margin: 0;
      padding: 0;
    }
    
    nav a {
      font-size: 18px;
      font-weight: 600;
      color: #dfe1e9;
      text-transform: uppercase;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 5px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    nav a:hover {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
    }
    
    nav a.activo {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      box-shadow: inset 0 -3px 0 var(--amarillo-oscuro);
    }
    
    .search-login {
      display: flex;
      align-items: center;
      gap: 24px;
    }
    
    .search-login input[type="text"] {
      padding: 8px 12px;
      border-radius: 4px;
      border: 1px solid var(--gris-medio);
      background-color: #0f172a;
      color: var(--gris-claro);
    }
    
    .search-login button {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      border: none;
      border-radius: 4px;
      padding: 6px 12px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }
    
    .search-login button:hover {
      background-color: var(--amarillo-oscuro);
    }
    
    /* Estilos para el perfil */
    .profile-container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: var(--blanco);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .profile-header {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 1rem;
    }
    
    .profile-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background-color: var(--azul-oscuro);
      color: var(--blanco);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      margin-right: 2rem;
    }
    
    .profile-info h2 {
      color: var(--azul-oscuro);
      margin-bottom: 0.5rem;
    }
    
    .profile-info p {
      color: var(--gris-medio);
    }
    
    .profile-details {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
    }
    
    .detail-card {
      background-color: #f8fafc;
      padding: 1.5rem;
      border-radius: 8px;
      border-left: 4px solid var(--azul-oscuro);
    }
    
    .detail-card h3 {
      color: var(--azul-oscuro);
      margin-bottom: 1rem;
      font-size: 1.1rem;
    }
    
    .detail-item {
      margin-bottom: 0.8rem;
      display: flex;
    }
    
    .detail-item i {
      color: var(--azul-oscuro);
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .btn-edit {
      display: inline-block;
      padding: 0.5rem 1rem;
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      text-decoration: none;
      border-radius: 5px;
      font-weight: 500;
      margin-top: 1rem;
      transition: all 0.3s;
    }
    
    .btn-edit:hover {
      background-color: var(--amarillo-oscuro);
      transform: translateY(-2px);
    }
    
    /* Footer */
    footer {
      background-color: var(--azul-muy-oscuro);
      color: var(--blanco);
      padding: 2rem;
      text-align: center;
      margin-top: 3rem;
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      text-align: left;
    }
    
    .footer-column h3 {
      color: var(--amarillo-brillante);
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }
    
    .footer-column ul {
      list-style: none;
    }
    
    .footer-column ul li {
      margin-bottom: 0.5rem;
    }
    
    .footer-column ul li a {
      color: var(--gris-claro);
      text-decoration: none;
      transition: color 0.3s;
    }
    
    .footer-column ul li a:hover {
      color: var(--amarillo-brillante);
    }
    
    .social-links {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .social-links a {
      color: var(--blanco);
      background-color: var(--azul-oscuro);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.3s;
    }
    
    .social-links a:hover {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
    }
    
    .copyright {
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      header {
        flex-direction: column;
        gap: 1.5rem;
      }
      
      nav ul {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .search-login {
        width: 100%;
        justify-content: center;
      }
      
      .profile-header {
        flex-direction: column;
        text-align: center;
      }
      
      .profile-avatar {
        margin-right: 0;
        margin-bottom: 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Cinexpress</h1>
    <nav>
      <ul>
        <li><a href="../indexEmpleado.php">Inicio</a></li>
        
        <li><a href="peli.php">Películas</a></li>
      </ul>
    </nav>
    <div class="search-login">
     
      <?php if (isset($_SESSION['usuario'])): ?>
        <div class="user-dropdown">
          <span class="user-name">
            <i class="fas fa-user-circle"></i>
            <?php echo htmlspecialchars($_SESSION['usuario']); ?>
          </span>
          <div class="dropdown-content">
            <a href="logout.php" class="logout">
              <i  class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="../index.php" class="btn-login">
          <i class="fas fa-sign-in-alt"></i> Acceder
        </a>
      <?php endif; ?>
    </div>
  </header>

  <div class="profile-container">
    <div class="profile-header">
      <div class="profile-avatar">
        <?php echo strtoupper(substr($usuario['Nombre'], 0, 1)); ?>
      </div>
      <div class="profile-info">
        <h2><?php echo htmlspecialchars($usuario['Nombre']); ?></h2>
        <p>@<?php echo htmlspecialchars($usuario['usuario']); ?></p>
      </div>
    </div>
    
    <div class="profile-details">
      <div class="detail-card">
        <h3>Información Personal</h3>
        <div class="detail-item">
          <i class="fas fa-user"></i>
          <span><?php echo htmlspecialchars($usuario['Nombre']); ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-at"></i>
          <span><?php echo htmlspecialchars($usuario['usuario']); ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-envelope"></i>
          <span><?php echo htmlspecialchars($usuario['email']); ?></span>
        </div>
      
      </div>
      
      <div class="detail-card">
        <h3>Detalles Adicionales</h3>
        <div class="detail-item">
          <i class="fas fa-phone"></i>
          <span><?php echo htmlspecialchars($usuario['Teléfono'] ?? 'No especificado'); ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-birthday-cake"></i>
          <span><?php echo htmlspecialchars($usuario['FechaNacimiento'] ?? 'No especificada'); ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-user-tag"></i>
          <span><?php echo htmlspecialchars(ucfirst($usuario['Tipo'])); ?></span>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <div class="footer-column">
        <h3>Horarios</h3>
        <ul>
          <li>Lunes a Viernes: 12pm - 11pm</li>
          <li>Sábados y Domingos: 10am - 12am</li>
          <li>Festivos: 10am - 11pm</li>
        </ul>
      </div>
      <div class="footer-column">
        <h3>Contacto</h3>
        <ul>
          <li><i class="fas fa-map-marker-alt"></i> Av. Cine 123, Ciudad</li>
          <li><i class="fas fa-phone"></i> +123 456 7890</li>
          <li><i class="fas fa-envelope"></i> info@cinexpress.com</li>
        </ul>
      </div>
      <div class="footer-column">
        <h3>Síguenos</h3>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
    </div>
    <div class="copyright">
      <p>&copy; 2025 Cinexpress. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>