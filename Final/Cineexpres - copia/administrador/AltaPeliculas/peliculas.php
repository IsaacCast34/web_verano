<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("../../Conexion/classConnectionMySQL.php");
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

$query = "SELECT idP, NombrePelicula, descripcionP, DisponibilidadP, duracion, Idiomas, Categoria, imagen FROM CPeliculas";
$result = $NewConn->ExecuteQuery($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo de Películas - CineExpress</title>
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

    .badge-active {
      background-color: #d1e7dd;
      color: #0f5132;
    }

    .badge-inactive {
      background-color: #f8d7da;
      color: #842029;
    }

    /* Estilos específicos para películas */
    .movie-poster {
      width: 80px;
      height: 120px;
      object-fit: cover;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .description-cell {
      max-width: 300px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .description-cell:hover {
      white-space: normal;
      overflow: visible;
      position: relative;
      z-index: 100;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        <img src="https://cdn-icons-png.flaticon.com/512/3917/3917032.png" alt="Películas Icon">
        <h3>Gestión de Películas</h3>
      </div>
      
      <button class="btn-action btn-add" onclick="agregar()">
        <i class="fas fa-plus-circle"></i> Agregar Película
      </button>
      
      <button class="btn-action btn-edit" onclick="editar()">
        <i class="fas fa-edit"></i> Editar Película
      </button>
      
      <button class="btn-action btn-delete" onclick="eliminar()">
        <i class="fas fa-trash-alt"></i> Eliminar Película
      </button>
      
      <button class="btn-action btn-back" onclick="location.href='../../administrador/indexAdministrador.php'">
        <i class="fas fa-arrow-left"></i> Regresar al Panel
      </button>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0"><i class="fas fa-film me-2"></i>Listado de Películas</h3>
        </div>
        
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Poster</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Disponibilidad</th>
                <th>Duración</th>
                <th>Idiomas</th>
                <th>Categoría</th>
              </tr>
            </thead>
            <tbody id="tablaPeliculas">
              <?php
              if ($result) {
                  while ($fila = $result->fetch_assoc()) {
                      $disponibilidad_class = $fila['DisponibilidadP'] === 'Sí' ? 'badge-active' : 'badge-inactive';
                      
                      echo "<tr data-idp='" . htmlspecialchars($fila['idP']) . "'>
                              <td><img src='" . htmlspecialchars($fila['imagen']) . "' alt='Poster' class='movie-poster'></td>
                              <td>" . htmlspecialchars($fila['NombrePelicula']) . "</td>
                              <td class='description-cell' title='" . htmlspecialchars($fila['descripcionP']) . "'>" . 
                                  substr(htmlspecialchars($fila['descripcionP']), 0, 50) . "...</td>
                              <td><span class='badge $disponibilidad_class'>" . htmlspecialchars($fila['DisponibilidadP']) . "</span></td>
                              <td>" . htmlspecialchars($fila['duracion']) . " min</td>
                              <td>" . htmlspecialchars($fila['Idiomas']) . "</td>
                              <td>" . htmlspecialchars($fila['Categoria']) . "</td>
                            </tr>";
                  }
                  $NewConn->SetFreeResult($result);
              } else {
                  echo "<tr><td colspan='7' class='no-data'>No hay películas registradas</td></tr>"; 
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

    document.querySelectorAll("#tablaPeliculas tr").forEach(fila => {
      fila.addEventListener("click", () => {
        if (filaSeleccionada) filaSeleccionada.classList.remove("selected");
        fila.classList.add("selected");
        filaSeleccionada = fila;
        idSeleccionado = fila.getAttribute("data-idp");
      });
    });

    function agregar() {
      window.location.href = "agregar.php";
    }

    function editar() {
      if (!idSeleccionado) {
        alert("Por favor selecciona una película para editar");
        return;
      }
      window.location.href = "editar.php?idP=" + idSeleccionado;
    }

    function eliminar() {
      if (!idSeleccionado) {
        alert("Por favor selecciona una película para eliminar");
        return;
      }
      if (confirm("¿Estás seguro de eliminar esta película?")) {
        window.location.href = "eliminar.php?idP=" + idSeleccionado;
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$NewConn->CloseConnection();
?>