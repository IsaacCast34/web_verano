<?php
session_start();
require_once "../../Conexion/classConnectionMySQL.php";

// Conexión a la base de datos
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Obtener todas las películas disponibles
$query = "SELECT * FROM CPeliculas WHERE DisponibilidadP = 'Disponible'";
$result = $NewConn->ExecuteQuery($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Películas - Cinexpress</title>
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
    
    /* Estilos para el menú de usuario */
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
      margin-top: 5px;
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
      width: 20px;
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
      border-top: 1px solid var(--gris-claro);
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
    
    /* Estilos específicos para la página de películas */
    .peliculas-container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
    }
    
    .peliculas-container h1 {
      font-size: 2.5rem;
      color: var(--azul-oscuro);
      margin-bottom: 2rem;
      text-align: center;
      border-bottom: 3px solid var(--amarillo-brillante);
      padding-bottom: 10px;
    }
    
    .pelicula-card {
      display: flex;
      background-color: var(--blanco);
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .pelicula-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .pelicula-imagen {
      width: 250px;
      height: 350px;
      object-fit: cover;
    }
    
    .pelicula-info {
      flex: 1;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
    }
    
    .pelicula-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }
    
    .pelicula-titulo {
      font-size: 1.8rem;
      color: var(--azul-oscuro);
      margin: 0;
    }
    
    .pelicula-duracion {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .pelicula-meta {
      display: flex;
      gap: 1rem;
      color: var(--gris-medio);
      margin-bottom: 1rem;
    }
    
    .pelicula-meta span {
      background-color: var(--gris-claro);
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.9rem;
    }
    
    .pelicula-descripcion {
      margin-bottom: 1.5rem;
      color: var(--gris-oscuro);
      line-height: 1.6;
    }
    
    .funciones-container {
      margin-top: auto;
      background-color: var(--gris-claro);
      padding: 1rem;
      border-radius: 8px;
    }
    
    .funciones-titulo {
      font-size: 1.2rem;
      color: var(--azul-oscuro);
      margin-bottom: 1rem;
    }
    
    .funciones-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 0.8rem;
    }
    
    .funcion-btn {
      background-color: var(--azul-oscuro);
      color: white;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 500;
      text-decoration: none;
      display: inline-block;
    }
    
    .funcion-btn:hover {
      background-color: var(--azul-muy-oscuro);
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
        text-align: center;
      }
      
      nav ul {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .search-login {
        width: 100%;
        justify-content: center;
      }
      
      .pelicula-card {
        flex-direction: column;
      }
      
      .pelicula-imagen {
        width: 100%;
        height: auto;
      }
      
      .pelicula-header {
        flex-direction: column;
        gap: 1rem;
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
        <li><a href="Peli.php" class="activo">Películas</a></li>
        <li><a href="perfil.php">Mi Perfil</a></li>
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
            <a href="perfil.php">
              <i class="fas fa-user"></i> Mi Perfil
            </a>
            <a href="../index.php" class="logout">
              <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php" class="btn-login">Iniciar Sesión</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="peliculas-container">
    <h1>Nuestras Películas</h1>
    
    <?php while ($pelicula = $result->fetch_assoc()): ?>
      <div class="pelicula-card">
        <img src="<?php echo htmlspecialchars($pelicula['imagen']); ?>" 
             alt="<?php echo htmlspecialchars($pelicula['NombrePelicula']); ?>" 
             class="pelicula-imagen">
             
        <div class="pelicula-info">
          <div class="pelicula-header">
            <h2 class="pelicula-titulo"><?php echo htmlspecialchars($pelicula['NombrePelicula']); ?></h2>
            <span class="pelicula-duracion"><?php echo htmlspecialchars($pelicula['duracion']); ?> min</span>
          </div>
          
          <div class="pelicula-meta">
            <span><?php echo htmlspecialchars($pelicula['Categoria']); ?></span>
            <span><?php echo htmlspecialchars($pelicula['Idiomas']); ?></span>
          </div>
          
          <p class="pelicula-descripcion"><?php echo htmlspecialchars($pelicula['descripcionP']); ?></p>
          
          <div class="funciones-container">
            <h3 class="funciones-titulo">Funciones disponibles:</h3>
            
            <div class="funciones-grid">
              <?php
              // Obtener funciones para esta película
              $queryFunciones = "SELECT f.*, s.numeroSala 
                               FROM CFunciones f
                               JOIN CSalas s ON f.idSala = s.idS
                               WHERE f.idPelicula = {$pelicula['idP']}";
              $resultFunciones = $NewConn->ExecuteQuery($queryFunciones);
              
              while ($funcion = $resultFunciones->fetch_assoc()):
              ?>
                <a href="seleccion_asientos.php?idF=<?php echo $funcion['idF']; ?>" 
                   class="funcion-btn">
                  <?php echo date("h:i A", strtotime($funcion['hora'])); ?> - Sala <?php echo $funcion['numeroSala']; ?>
                </a>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
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
      <p>&copy; <?php echo date('Y'); ?> Cinexpress. Todos los derechos reservados.</p>
    </div>
  </footer>

  <?php $NewConn->CloseConnection(); ?>
</body>
</html>