<?php
session_start();
require_once "Conexion/classConnectionMySQL.php";

// Verificar si hay un ID de película
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$idPelicula = intval($_GET['id']);

// Conexión a la base de datos
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Obtener detalles de la película
$queryPelicula = "SELECT * FROM CPeliculas WHERE idP = $idPelicula";
$resultPelicula = $NewConn->ExecuteQuery($queryPelicula);
$pelicula = $resultPelicula->fetch_assoc();

if (!$pelicula) {
    header("Location: index.php");
    exit();
}

// Obtener funciones disponibles para esta película
$queryFunciones = "SELECT f.*, s.numeroSala, s.tipoSala 
                   FROM CFunciones f 
                   JOIN CSalas s ON f.idSala = s.idS 
                   WHERE f.idPelicula = $idPelicula 
                   ORDER BY f.hora";
$resultFunciones = $NewConn->ExecuteQuery($queryFunciones);

$NewConn->CloseConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pelicula['NombrePelicula']); ?> - Cinexpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    
    .movie-detail-container {
      max-width: 1200px;
      margin: 2rem auto;
      background-color: var(--blanco);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .movie-header {
      display: flex;
      padding: 2rem;
      gap: 2rem;
      border-bottom: 1px solid #eee;
    }
    
    .movie-poster {
      width: 300px;
      height: 450px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .movie-info {
      flex: 1;
    }
    
    .movie-title {
      font-size: 2.5rem;
      color: var(--azul-oscuro);
      margin-bottom: 1rem;
    }
    
    .movie-meta {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      color: var(--gris-medio);
    }
    
    .movie-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .movie-description {
      margin-bottom: 2rem;
      line-height: 1.6;
    }
    
    .section-title {
      font-size: 1.5rem;
      color: var(--azul-oscuro);
      margin: 2rem 0 1rem;
      padding-left: 2rem;
      border-left: 4px solid var(--amarillo-brillante);
    }
    
    .functions-container {
      padding: 0 2rem 2rem;
    }
    
    .function-card {
      background-color: #f8fafc;
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      border-left: 4px solid var(--azul-oscuro);
    }
    
    .function-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    
    .function-time {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--azul-oscuro);
    }
    
    .function-sala {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-weight: bold;
    }
    
    .function-info {
      margin-bottom: 1rem;
    }
    
    .price {
      font-size: 1.3rem;
      font-weight: bold;
      color: var(--azul-oscuro);
    }
    
    .promo-price {
      color: var(--rojo-oscuro);
      text-decoration: line-through;
      margin-right: 10px;
    }
    
    .btn-select {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-select:hover {
      background-color: var(--amarillo-oscuro);
      transform: translateY(-2px);
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
    
    /* Responsive */
    @media (max-width: 768px) {
      .movie-header {
        flex-direction: column;
      }
      
      .movie-poster {
        width: 100%;
        height: auto;
      }
      
      .function-header {
        flex-direction: column;
        gap: 1rem;
      }
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
  </style>
</head>
<body>
  <header>
  <h1>Cinexpress</h1>
  <nav>
    <ul>
      <li><a href="index.php">Inicio</a></li>
    
       <li><a href="peli.php">Películas</a></li>
    </ul>
    
  </nav>
  <div class="search-login">
      
      <a href="login.php"><button>Acceder</button></a>
    </div>
</header>

  <div class="movie-detail-container">
    <div class="movie-header">
      <img src="<?php echo htmlspecialchars($pelicula['imagen']); ?>" alt="<?php echo htmlspecialchars($pelicula['NombrePelicula']); ?>" class="movie-poster">
      <div class="movie-info">
        <h1 class="movie-title"><?php echo htmlspecialchars($pelicula['NombrePelicula']); ?></h1>
        <div class="movie-meta">
          <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($pelicula['duracion']); ?> min</span>
          <span><i class="fas fa-language"></i> <?php echo htmlspecialchars($pelicula['Idiomas']); ?></span>
          <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($pelicula['Categoria']); ?></span>
          <span><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($pelicula['DisponibilidadP']); ?></span>
        </div>
        <p class="movie-description"><?php echo htmlspecialchars($pelicula['descripcionP']); ?></p>
      </div>
    </div>
    
    <h2 class="section-title">Funciones Disponibles</h2>
    
    <div class="functions-container">
      <?php if ($resultFunciones->num_rows > 0): ?>
        <?php while ($funcion = $resultFunciones->fetch_assoc()): ?>
          <div class="function-card">
            <div class="function-header">
              <span class="function-time">
                <i class="far fa-clock"></i> <?php echo date("h:i A", strtotime($funcion['hora'])); ?>
              </span>
              <span class="function-sala">
                Sala <?php echo htmlspecialchars($funcion['numeroSala']); ?> - <?php echo htmlspecialchars($funcion['tipoSala']); ?>
              </span>
            </div>
            <div class="function-info">
              <p><?php echo htmlspecialchars($funcion['informacion']); ?></p>
            </div>
            <div class="function-footer">
              <span class="price">
                <?php if ($funcion['promocion'] < $funcion['precioEntrada']): ?>
                  <span class="promo-price">$<?php echo number_format($funcion['precioEntrada'], 2); ?></span>
                <?php endif; ?>
                $<?php echo number_format($funcion['promocion'], 2); ?>
              </span>
              <a href="login.php?idF=<?php echo $funcion['idF']; ?>" class="btn-select">
                <i class="fas fa-armchair"></i> Seleccionar Asientos
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No hay funciones disponibles para esta película.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
