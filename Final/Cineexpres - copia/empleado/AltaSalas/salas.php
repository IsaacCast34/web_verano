<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("../../Conexion/classConnectionMySQL.php");
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

$query = "
SELECT S.idS, S.numeroSala, S.capacidadSala, S.tipoAsiento, S.numeracionSala, 
       S.tipoSala, S.estado,
       COUNT(A.idA) AS totalAsientos,
       SUM(CASE WHEN A.estado = 'Ocupado' THEN 1 ELSE 0 END) AS asientosOcupados,
       SUM(CASE WHEN A.estado = 'Disponible' THEN 1 ELSE 0 END) AS asientosDisponibles
FROM CSalas S
LEFT JOIN CAsientos A ON S.idS = A.idSala
GROUP BY S.idS
";
$result = $NewConn->ExecuteQuery($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo de Salas - CineExpress</title>
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
    
    .admin-container {
      display: flex;
      min-height: 100vh;
    }
    
    .sidebar {
      width: 280px;
      background: white;
      box-shadow: 5px 0 15px rgba(0, 0, 0, 0.05);
      padding: 2rem 1.5rem;
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
    }
    
    .btn-action {
      width: 100%;
      margin-bottom: 0.75rem;
      padding: 0.75rem;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .btn-action i {
      margin-right: 0.5rem;
    }
    
    .btn-add {
      background: var(--accent-color);
      border: none;
      color: white;
    }
    
    .btn-add:hover {
      background: #d91a6d;
      transform: translateY(-2px);
    }
    
    .btn-edit {
      background: var(--secondary-color);
      border: none;
      color: white;
    }
    
    .btn-edit:hover {
      background: #3a56e8;
      transform: translateY(-2px);
    }
    
    .btn-delete {
      background: #dc3545;
      border: none;
      color: white;
    }
    
    .btn-delete:hover {
      background: #c82333;
      transform: translateY(-2px);
    }
    
    .btn-back {
      background: #6c757d;
      border: none;
      color: white;
      margin-top: 1.5rem;
    }
    
    .btn-back:hover {
      background: #5a6268;
      transform: translateY(-2px);
    }
    
    .main-content {
      flex: 1;
      padding: 2rem;
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1.25rem;
      border-radius: 10px 10px 0 0 !important;
    }
    
    .table-container {
      background: white;
      border-radius: 0 0 10px 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
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
    
    .badge {
      padding: 0.5em 0.75em;
      font-weight: 600;
      border-radius: 8px;
    }
    
    .badge-active {
      background-color: #d1e7dd;
      color: #0f5132;
    }
    
    .badge-inactive {
      background-color: #f8d7da;
      color: #842029;
    }
    
    .badge-maintenance {
      background-color: #fff3cd;
      color: #664d03;
    }
    
    .progress {
      height: 10px;
      border-radius: 5px;
    }
    
    .progress-bar {
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">
        <img src="https://cdn-icons-png.flaticon.com/512/1153/1153430.png" alt="Salas Icon">
        <h3>Gestión de Salas</h3>
      </div>
      
      <button class="btn btn-action btn-add" onclick="agregar()">
        <i class="fas fa-plus-circle"></i> Agregar Sala
      </button>
      
      <button class="btn btn-action btn-edit" onclick="editar()">
        <i class="fas fa-edit"></i> Editar Sala
      </button>
      
      <button class="btn btn-action btn-delete" onclick="eliminar()">
        <i class="fas fa-trash-alt"></i> Eliminar Sala
      </button>
      
      <button class="btn btn-action btn-back" onclick="location.href='../../empleado/indexEmpleado.php'">
        <i class="fas fa-arrow-left"></i> Regresar al Panel
      </button>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
      <div class="card shadow-sm">
        <div class="card-header">
          <h3 class="mb-0"><i class="fas fa-film me-2"></i>Listado de Salas</h3>
        </div>
        
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>N° Sala</th>
                <th>Capacidad</th>
                <th>Tipo Asiento</th>
                <th>Tipo Sala</th>
                <th>Estado</th>
                <th>Ocupación</th>
                <th>Disponibles</th>
              </tr>
            </thead>
            <tbody id="tablaSalas">
              <?php
              if ($result) {
                  while ($fila = $result->fetch_assoc()) {
                      $estado_class = '';
                      if ($fila['estado'] == 'Activa') $estado_class = 'badge-active';
                      if ($fila['estado'] == 'Inactiva') $estado_class = 'badge-inactive';
                      if ($fila['estado'] == 'Mantenimiento') $estado_class = 'badge-maintenance';
                      
                      $total_asientos = $fila['totalAsientos'];
                      $ocupados = $fila['asientosOcupados'];
                      $disponibles = $fila['asientosDisponibles'];
                      $porcentaje = $total_asientos > 0 ? round(($ocupados / $total_asientos) * 100) : 0;
                      
                      echo "<tr data-ids='" . htmlspecialchars($fila['idS']) . "'>
                              <td>" . htmlspecialchars($fila['numeroSala']) . "</td>
                              <td>" . htmlspecialchars($fila['capacidadSala']) . "</td>
                              <td>" . htmlspecialchars($fila['tipoAsiento']) . "</td>
                              <td>" . htmlspecialchars($fila['tipoSala']) . "</td>
                              <td><span class='badge $estado_class'>" . htmlspecialchars($fila['estado']) . "</span></td>
                              <td>
                                <div class='d-flex align-items-center'>
                                  <div class='progress flex-grow-1 me-2'>
                                    <div class='progress-bar bg-success' role='progressbar' 
                                         style='width: $porcentaje%' aria-valuenow='$porcentaje' 
                                         aria-valuemin='0' aria-valuemax='100'></div>
                                  </div>
                                  <small>$ocupados/$total_asientos</small>
                                </div>
                              </td>
                              <td>" . htmlspecialchars($disponibles) . "</td>
                            </tr>";
                  }
                  $NewConn->SetFreeResult($result);
              } else {
                  echo "<tr><td colspan='7' class='text-center py-4'>No hay salas registradas</td></tr>"; 
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

    document.querySelectorAll("#tablaSalas tr").forEach(fila => {
      fila.addEventListener("click", () => {
        if (filaSeleccionada) filaSeleccionada.classList.remove("selected");
        fila.classList.add("selected");
        filaSeleccionada = fila;
        idSeleccionado = fila.getAttribute("data-ids");
      });
    });

    function agregar() {
      window.location.href = "agregar.php";
    }

    function editar() {
      if (!idSeleccionado) {
        alert("Por favor selecciona una sala para editar");
        return;
      }
      window.location.href = "editar.php?idS=" + idSeleccionado;
    }

    function eliminar() {
      if (!idSeleccionado) {
        alert("Por favor selecciona una sala para eliminar");
        return;
      }
      if (confirm("¿Estás seguro de eliminar esta sala?")) {
        window.location.href = "eliminar.php?idS=" + idSeleccionado;
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$NewConn->CloseConnection();
?>