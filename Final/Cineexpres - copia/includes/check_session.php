<?php
session_start();

// Lista blanca de páginas accesibles sin login
$paginas_publicas = ['index.php', 'login.php', 'registro.php', 'buscar.php'];

// Obtener el nombre de la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Si no está en una página pública y no está logueado, redirigir
if (!in_array($pagina_actual, $paginas_publicas) && !isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

// Si está logueado pero intenta acceder a login/registro, redirigir
if (in_array($pagina_actual, ['login.php', 'registro.php']) && isset($_SESSION['usuario'])) {
    $destino = "index.php";
    if ($_SESSION['tipo'] === 'administrador') {
        $destino = "administrador/indexAdministrador.php";
    } elseif ($_SESSION['tipo'] === 'empleado') {
        $destino = "empleado/indexEmpleado.php";
    }
    header("Location: $destino");
    exit();
}

// Verificar que el tipo de usuario coincida con la ubicación
if (isset($_SESSION['tipo'])) {
    $ruta_actual = $_SERVER['PHP_SELF'];
    
    // Administrador debe estar en directorio administrador/
    if ($_SESSION['tipo'] === 'administrador' && !strpos($ruta_actual, 'administrador/')) {
        header("Location: administrador/indexAdministrador.php");
        exit();
    }
    
    // Empleado debe estar en directorio empleado/
    if ($_SESSION['tipo'] === 'empleado' && !strpos($ruta_actual, 'empleado/')) {
        header("Location: empleado/indexEmpleado.php");
        exit();
    }
    
    // Usuario normal no debe estar en directorios admin/empleado
    if ($_SESSION['tipo'] === 'usuario' && 
        (strpos($ruta_actual, 'administrador/') || strpos($ruta_actual, 'empleado/'))) {
        header("Location: ../index.php");
        exit();
    }
}
?>