<?php
require_once "../verificar_sesion.php";

// Opcional: verificar el tipo de cuenta
if ($_SESSION['tipo'] !== 'administrador') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel de Administrador - Cinexpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        
        .admin-panel {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .stat-card {
            border-radius: 10px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .feature-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card .card-img-top {
            height: 120px;
            object-fit: cover;
            object-position: center;
        }
        
        .feature-card .card-body {
            padding: 1.5rem;
        }
        
        .feature-card .card-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .recent-activity {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .logout-btn {
            background: var(--accent-color);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #d91a6d;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="admin-panel">
            <!-- Encabezado -->
            <div class="admin-header">
                <h1><i class="fas fa-user-shield me-2"></i>Panel de Administración</h1>
                <p class="mb-0">Bienvenido, Administrador | Último acceso: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
            
            <!-- Contenido principal -->
            <div class="p-4">
                <!-- Estadísticas rápidas -->
                <h4 class="mb-4"><i class="fas fa-chart-line me-2"></i>Resumen del Sistema</h4>
                <div class="row g-4 mb-5">
                    <?php
                    // Conexión a la base de datos
                    $servername = "localhost";
                    $username = "root"; // Cambiar por tus credenciales
                    $password = "123456"; // Cambiar por tus credenciales
                    $dbname = "CineExpress";
                    
                    // Crear conexión
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    
                    // Verificar conexión
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    
                    // Consulta para contar usuarios
                    $sql_usuarios = "SELECT COUNT(*) as total FROM Usuario";
                    $result_usuarios = $conn->query($sql_usuarios);
                    $row_usuarios = $result_usuarios->fetch_assoc();
                    $total_usuarios = $row_usuarios['total'];
                    
                    // Consulta para contar películas
                    $sql_peliculas = "SELECT COUNT(*) as total FROM CPeliculas WHERE DisponibilidadP = 'Sí'";
                    $result_peliculas = $conn->query($sql_peliculas);
                    $row_peliculas = $result_peliculas->fetch_assoc();
                    $total_peliculas = $row_peliculas['total'];
                    
                    // Consulta para contar funciones de hoy
                    $sql_funciones = "SELECT COUNT(*) as total FROM CFunciones";
                    $result_funciones = $conn->query($sql_funciones);
                    $row_funciones = $result_funciones->fetch_assoc();
                    $total_funciones = $row_funciones['total'];
                    
                   // Consulta para calcular ventas reales por salas ocupadas
$sql_ventas = "SELECT SUM(f.precioEntrada) as total_ventas
               FROM Pagos p
               JOIN Reservas r ON p.idReserva = r.idReserva
               JOIN CFunciones f ON r.idFuncion = f.idF
               JOIN CSalas s ON f.idSala = s.idS
               WHERE p.estadoPago = 'Completado'";

$result_ventas = $conn->query($sql_ventas);
$row_ventas = $result_ventas->fetch_assoc();
$total_ventas = $row_ventas['total_ventas'];
                    
                    // Consulta para película más popular (simulada)
                    $sql_popular = "SELECT NombrePelicula FROM CPeliculas LIMIT 1";
                    $result_popular = $conn->query($sql_popular);
                    $row_popular = $result_popular->fetch_assoc();
                    $pelicula_popular = $row_popular['NombrePelicula'];
                    
                    // Consulta para actividad reciente
                    $sql_actividad = "SELECT * FROM (
                        (SELECT 'Nueva película agregada' as tipo, NombrePelicula as detalle, 'Hoy' as tiempo FROM CPeliculas ORDER BY idP DESC LIMIT 1)
                        UNION
                        (SELECT 'Usuario registrado' as tipo, CONCAT(Nombre, ' (', Tipo, ')') as detalle, 'Hoy' as tiempo FROM Usuario ORDER BY idL DESC LIMIT 1)
                        UNION
                        (SELECT 'Función modificada' as tipo, CONCAT('Sala ', idSala, ' a las ', hora) as detalle, 'Hoy' as tiempo FROM CFunciones ORDER BY idF DESC LIMIT 1)
                    ) as actividades ORDER BY tiempo DESC LIMIT 4";
                    $result_actividad = $conn->query($sql_actividad);
                    ?>
                    
                    <div class="col-md-3">
                        <div class="stat-card card text-center p-4">
                            <i class="fas fa-users card-icon"></i>
                            <div class="stat-value"><?php echo $total_usuarios; ?></div>
                            <div class="stat-label">Usuarios Registrados</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> 3 nuevos este mes</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card card text-center p-4">
                            <i class="fas fa-film card-icon"></i>
                            <div class="stat-value"><?php echo $total_peliculas; ?></div>
                            <div class="stat-label">Películas en Cartelera</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> 1 nueva esta semana</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card card text-center p-4">
                            <i class="fas fa-ticket-alt card-icon"></i>
                            <div class="stat-value"><?php echo $total_funciones; ?></div>
                            <div class="stat-label">Funciones Programadas</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> 2 nuevas hoy</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card card text-center p-4">
                            <i class="fas fa-dollar-sign card-icon"></i>
                            <div class="stat-value">$<?php echo number_format($total_ventas, 2); ?></div>
                            <div class="stat-label">Ventas Estimadas</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> 10% más que ayer</small>
                        </div>
                    </div>
                </div>
                
                <!-- Módulos principales -->
                <h4 class="mb-4"><i class="fas fa-cogs me-2"></i>Módulos de Administración</h4>
                <div class="row g-4 mb-5">
                    <div class="col-md-3">
                        <a href="AltaEmpleados/usuarios.php" class="text-decoration-none">
                            <div class="feature-card card">
                                <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Usuarios">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Gestión de Usuarios</h5>
                                    <p class="card-text text-muted small">Administra empleados y clientes</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="AltaPeliculas/peliculas.php" class="text-decoration-none">
                            <div class="feature-card card">
                                <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Películas">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Catálogo de Películas</h5>
                                    <p class="card-text text-muted small">Administra el contenido cinematográfico</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="AltaSalas/salas.php" class="text-decoration-none">
                            <div class="feature-card card">
                                <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Salas">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Gestión de Salas</h5>
                                    <p class="card-text text-muted small">Configura las salas de proyección</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="AltaFunciones/Funciones.php" class="text-decoration-none">
                            <div class="feature-card card">
                                <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Funciones">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Programación de Funciones</h5>
                                    <p class="card-text text-muted small">Crea y gestiona horarios de películas</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Actividad reciente y otros elementos -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="recent-activity">
                            <h5><i class="fas fa-history me-2"></i>Actividad Reciente</h5>
                            <?php
                            if ($result_actividad->num_rows > 0) {
                                while($row = $result_actividad->fetch_assoc()) {
                                    echo '<div class="activity-item">
                                        <div class="d-flex justify-content-between">
                                            <span><strong>' . $row["tipo"] . ':</strong> ' . $row["detalle"] . '</span>
                                            <span class="activity-time">' . $row["tiempo"] . '</span>
                                        </div>
                                    </div>';
                                }
                            } else {
                                echo '<div class="activity-item">No hay actividad reciente</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-star card-icon"></i>
                                <h5>Película más popular</h5>
                                <h4 class="text-primary"><?php echo $pelicula_popular; ?></h4>
                                <p class="text-muted">85% de ocupación</p>
                                <div class="progress mt-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón de cierre de sesión -->
                <div class="text-center mt-5">
                    <a href="../login.php" class="btn logout-btn">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php $conn->close(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>