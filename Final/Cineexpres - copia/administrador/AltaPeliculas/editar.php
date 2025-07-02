<?php
require("../../Conexion/classConnectionMySQL.php");

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idP = intval($_POST['idP']);
    $nombre = trim($_POST['NombrePelicula']);
    $descripcion = trim($_POST['descripcionP']);
    $disponibilidad = trim($_POST['DisponibilidadP']);
    $duracion = floatval($_POST['duracion']);
    $idiomas = trim($_POST['Idiomas']);
    $categoria = trim($_POST['Categoria']);
    $imagen = trim($_POST['imagen']);

    // Validaciones
    if (empty($nombre) || empty($descripcion) || empty($duracion) || 
        empty($idiomas) || empty($categoria) || empty($imagen)) {
        $error_message = "Todos los campos son obligatorios";
    } elseif ($duracion <= 0) {
        $error_message = "La duración debe ser un número positivo";
    } else {
        $query = "UPDATE CPeliculas SET 
                  NombrePelicula = ?, 
                  descripcionP = ?, 
                  DisponibilidadP = ?, 
                  duracion = ?, 
                  Idiomas = ?, 
                  Categoria = ?, 
                  imagen = ? 
                  WHERE idP = ?";
        $stmt = $NewConn->getConn()->prepare($query);
        $stmt->bind_param("sssssssi", $nombre, $descripcion, $disponibilidad, $duracion, $idiomas, $categoria, $imagen, $idP);

        if ($stmt->execute()) {
            header("Location: peliculas.php");
            exit;
        } else {
            $error_message = "Error al actualizar la película: " . $stmt->error;
        }
    }
} else {
    if (!isset($_GET['idP']) || !is_numeric($_GET['idP'])) {
        die("<h1>ID inválido.</h1>");
    }

    $idP = intval($_GET['idP']);
    $query = "SELECT * FROM CPeliculas WHERE idP = $idP";
    $result = $NewConn->ExecuteQuery($query);
    $row = $result->fetch_assoc();

    if (!$row) {
        die("<h1>Película no encontrada.</h1>");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Película - CineExpress</title>
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
    
    .admin-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      max-width: 800px;
      margin: 2rem auto;
    }
    
    .admin-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1.5rem;
      text-align: center;
    }
    
    .form-container {
      padding: 2rem;
    }
    
    .form-label {
      font-weight: 600;
      color: var(--primary-color);
    }
    
    .btn-submit {
      background: var(--accent-color);
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
      background: #d91a6d;
      transform: translateY(-2px);
    }
    
    .btn-cancel {
      background: #6c757d;
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
      background: #5a6268;
      transform: translateY(-2px);
    }
    
    .form-control, .form-select, .form-textarea {
      border-radius: 8px;
      padding: 0.75rem;
      border: 1px solid #ced4da;
    }
    
    .form-control:focus, .form-select:focus, .form-textarea:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }
    
    .error-message {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    
    .movie-icon {
      font-size: 1rem;
      color: var(--accent-color);
      margin-right: 8px;
    }
    
    .image-preview-container {
      margin-top: 1rem;
      text-align: center;
    }
    
    .image-preview {
      max-width: 100%;
      max-height: 200px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .form-textarea {
      min-height: 120px;
      resize: vertical;
    }
  </style>
</head>
<body>
  <div class="admin-card">
    <div class="admin-header">
      <h2><i class="fas fa-film me-2"></i>Editar Película: <?php echo htmlspecialchars($row['NombrePelicula']); ?></h2>
    </div>
    
    <div class="form-container">
      <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <input type="hidden" name="idP" value="<?php echo $row['idP']; ?>">
        
        <div class="row g-3">
          <div class="col-12">
            <label for="NombrePelicula" class="form-label">
              <i class="fas fa-ticket-alt movie-icon"></i>Nombre de la Película
            </label>
            <input type="text" id="NombrePelicula" name="NombrePelicula" class="form-control" 
                   value="<?php echo htmlspecialchars($row['NombrePelicula']); ?>" required>
          </div>
          
          <div class="col-12">
            <label for="descripcionP" class="form-label">
              <i class="fas fa-align-left movie-icon"></i>Descripción
            </label>
            <textarea id="descripcionP" name="descripcionP" class="form-control form-textarea" required><?php 
              echo htmlspecialchars($row['descripcionP']); 
            ?></textarea>
          </div>
          
          <div class="col-md-6">
            <label for="DisponibilidadP" class="form-label">
              <i class="fas fa-calendar-check movie-icon"></i>Disponibilidad
            </label>
            <select id="DisponibilidadP" name="DisponibilidadP" class="form-select" required>
              <option value="Sí" <?php echo ($row['DisponibilidadP'] === 'Sí') ? 'selected' : ''; ?>>Sí</option>
              <option value="No" <?php echo ($row['DisponibilidadP'] === 'No') ? 'selected' : ''; ?>>No</option>
            </select>
          </div>
          
          <div class="col-md-6">
            <label for="duracion" class="form-label">
              <i class="fas fa-clock movie-icon"></i>Duración (minutos)
            </label>
            <input type="number" id="duracion" name="duracion" class="form-control" 
                   value="<?php echo htmlspecialchars($row['duracion']); ?>" min="1" step="0.1" required>
          </div>
          
          <div class="col-md-6">
            <label for="Idiomas" class="form-label">
              <i class="fas fa-language movie-icon"></i>Idiomas
            </label>
            <input type="text" id="Idiomas" name="Idiomas" class="form-control" 
                   value="<?php echo htmlspecialchars($row['Idiomas']); ?>" 
                   placeholder="Ej: Español, Inglés" required>
          </div>
          
          <div class="col-md-6">
            <label for="Categoria" class="form-label">
              <i class="fas fa-tags movie-icon"></i>Categoría
            </label>
            <input type="text" id="Categoria" name="Categoria" class="form-control" 
                   value="<?php echo htmlspecialchars($row['Categoria']); ?>" 
                   placeholder="Ej: Acción, Comedia" required>
          </div>
          
          <div class="col-12">
            <label for="imagen" class="form-label">
              <i class="fas fa-image movie-icon"></i>URL de la Imagen
            </label>
            <input type="text" id="imagen" name="imagen" class="form-control" 
                   value="<?php echo htmlspecialchars($row['imagen']); ?>" required>
            <div class="image-preview-container">
              <img id="imagePreview" class="image-preview" src="<?php echo htmlspecialchars($row['imagen']); ?>" 
                   alt="Vista previa de la imagen" style="display: block;">
            </div>
          </div>
          
          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-submit text-white w-100">
              <i class="fas fa-save me-2"></i>Actualizar Película
            </button>
            <a href="peliculas.php" class="btn btn-cancel text-white w-100 mt-2">
              <i class="fas fa-times me-2"></i>Cancelar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mostrar vista previa de la imagen
    document.getElementById('imagen').addEventListener('input', function() {
      const preview = document.getElementById('imagePreview');
      if (this.value) {
        preview.src = this.value;
        preview.style.display = 'block';
      } else {
        preview.style.display = 'none';
      }
    });
  </script>
</body>
</html>