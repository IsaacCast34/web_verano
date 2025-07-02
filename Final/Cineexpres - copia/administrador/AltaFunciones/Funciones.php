<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("../../Conexion/classConnectionMySQL.php");
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Consulta para obtener funciones con nombres de película y sala
$query = "
SELECT F.idF, P.NombrePelicula, S.numeroSala, F.informacion, F.hora, F.precioEntrada, F.promocion
FROM CFunciones F
INNER JOIN CPeliculas P ON F.idPelicula = P.idP
INNER JOIN CSalas S ON F.idSala = S.idS
";

$result = $NewConn->ExecuteQuery($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo de Funciones - CineExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #3a0ca3;
      --secondary-color: #4361ee;
      --accent-color: #f72585;
      --light-bg: #f8f9fa;
      --dark-bg: #212529;
      --success-color: #28a745;
      --danger-color: #dc3545;
      --warning-color: #ffc107;
    }

    body {
      background-color: #f5f7ff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    /* Layout */
    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 280px;
      background: white;
      box-shadow: 5px 0 15px rgba(0, 0, 0, 0.05);
      padding: 2rem 1.5rem;
      position: sticky;
      top: 0;
      height: 100vh;
    }

    .sidebar-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .sidebar-header img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 1rem;
    }

    .sidebar-header h3 {
      color: var(--primary-color);
      font-weight: 600;
      margin: 0;
    }

    /* Botones */
    .btn-action {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 0.75rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .btn-action i {
      margin-right: 0.5rem;
    }

    .btn-add {
      background: var(--accent-color);
      color: white;
    }

    .btn-add:hover {
      background: #d91a6d;
      transform: translateY(-2px);
    }

    .btn-edit {
      background: var(--secondary-color);
      color: white;
    }

    .btn-edit:hover {
      background: #3a56e8;
      transform: translateY(-2px);
    }

    .btn-delete {
      background: var(--danger-color);
      color: white;
    }

    .btn-delete:hover {
      background: #c82333;
      transform: translateY(-2px);
    }

    .btn-back {
      background: #6c757d;
      color: white;
      margin-top: 1.5rem;
    }

    .btn-back:hover {
      background: #5a6268;
      transform: translateY(-2px);
    }

    /* Contenido principal */
    .main-content {
      flex: 1;
      padding: 2rem;
      overflow-x: auto;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .card-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1.25rem;
    }

    /* Tabla */
    .table-container {
      background: white;
      border-radius: 0 0 10px 10px;
      padding: 1.5rem;
      overflow-x: auto;
    }

    .table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .table thead th {
      background-color: #f8f9fa;
      color: var(--primary-color);
      font-weight: 600;
      padding: 1rem;
      border-bottom: 2px solid #dee2e6;
      position: sticky;
      top: 0;
    }

    .table tbody tr {
      transition: all 0.2s ease;
    }

    .table tbody tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }

    .table tbody tr.selected {
      background-color: rgba(67, 97, 238, 0.1);
    }

    .table tbody td {
      padding: 1rem;
      border-bottom: 1px solid #dee2e6;
      vertical-align: middle;
    }

    /* Badges */
    .badge {
      padding: 0.5em 0.75em;
      font-weight: 600;
      border-radius: 8px;
      display: inline-block;
    }

    .badge-promo {
      background-color: #fff3cd;
      color: #664d03;
    }

    /* Estilos específicos para funciones */
    .price-cell {
      font-weight: 600;
      color: var(--primary-color);
    }

    .promo-cell {
      font-weight: 600;
      color: var(--success-color);
    }

    /* Mensaje sin datos */
    .no-data {
      text-align: center;
      padding: 2rem;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">
        <img src="https://cdn-icons-png.flaticon.com/512/4341/4341139.png" alt="Funciones Icon">
        <h3>Gestión de Funciones</h3>
      </div>
      
      <button class="btn-action btn-add" onclick="agregar()">
        <i class="fas fa-plus-circle"></i> Agregar Función
      </button>
      
      <button class="btn-action btn-edit" onclick="editar()">
        <i class="fas fa-edit"></i> Editar Función
      </button>
      
      <button class="btn-action btn-delete" onclick="eliminar()">
        <i class="fas fa-times-circle"></i> Cancelar Función
      </button>
      
      <button class="btn-action btn-back" onclick="location.href='../../administrador/indexAdministrador.php'">
        <i class="fas fa-arrow-left"></i> Regresar al Panel
      </button>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Listado de Funciones</h3>
        </div>
        
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Película</th>
                <th>Sala</th>
                <th>Información</th>
                <th>Hora</th>
                <th>Precio</th>
                <th>Promoción</th>
              </tr>
            </thead>
            <tbody id="tablaFunciones">
              <?php
              if ($result) {
                  while ($fila = $result->fetch_assoc()) {
                      $tienePromocion = $fila['promocion'] > 0;
                      
                      echo "<tr data-idf='" . htmlspecialchars($fila['idF']) . "'>
                              <td>" . htmlspecialchars($fila['NombrePelicula']) . "</td>
                              <td>Sala " . htmlspecialchars($fila['numeroSala']) . "</td>
                              <td>" . htmlspecialchars($fila['informacion']) . "</td>
                              <td>" . htmlspecialchars($fila['hora']) . "</td>
                              <td class='price-cell'>$" . number_format($fila['precioEntrada'], 2) . "</td>
                              <td class='promo-cell'>" . 
                                  ($tienePromocion ? 
                                  "<span class='badge badge-promo'>$" . number_format($fila['promocion'], 2) . "</span>" : 
                                  "-") . 
                              "</td>
                            </tr>";
                  }
                  $NewConn->SetFreeResult($result);
              } else {
                  echo "<tr><td colspan='6' class='no-data'>No hay funciones programadas</td></tr>"; 
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    let filaSeleccionada = null;
    let idSeleccionado = null;

    document.querySelectorAll("#tablaFunciones tr").forEach(fila => {
      fila.addEventListener("click", () => {
        if (filaSeleccionada) filaSeleccionada.classList.remove("selected");
        fila.classList.add("selected");
        filaSeleccionada = fila;
        idSeleccionado = fila.getAttribute("data-idf");
      });
    });

    function agregar() {
      window.location.href = "agregar.php";
    }

    function editar() {
      if (!idSeleccionado) {
        alert("Por favor selecciona una función para editar");
        return;
      }
      window.location.href = "editar.php?idF=" + idSeleccionado;
    }

    function eliminar() {
      if (!idSeleccionado) {
        alert("Por favor selecciona una función para cancelar");
        return;
      }
      if (confirm("¿Estás seguro de cancelar esta función?")) {
        window.location.href = "eliminar.php?idF=" + idSeleccionado;
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$NewConn->CloseConnection();
?>