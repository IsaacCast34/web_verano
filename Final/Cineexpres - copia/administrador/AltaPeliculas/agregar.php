<?php
require("../../Conexion/classConnectionMySQL.php");

$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombrePelicula = trim($_POST['NombrePelicula']);
    $descripcionP = trim($_POST['DescripcionP']);
    $disponibilidadP = $_POST['DisponibilidadP'];
    $duracion = $_POST['Duracion'];
    $idiomas = trim($_POST['Idiomas']);
    $categoria = trim($_POST['Categoria']);
    $imagen = $_POST['Imagen'];

    $query = "INSERT INTO CPeliculas (NombrePelicula, descripcionP, DisponibilidadP, duracion, Idiomas, Categoria, imagen)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $NewConn->getConn()->prepare($query);
    $stmt->bind_param("sssssss", $nombrePelicula, $descripcionP, $disponibilidadP, $duracion, $idiomas, $categoria, $imagen);

    if ($stmt->execute()) {
        header("Location: peliculas.php");
        exit;
    } else {
        $error_message = "Error al registrar la película: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Película - CineExpress</title>
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
    
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(rgba(245, 247, 255, 0.9), rgba(245, 247, 255, 0.9)),
                  url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') no-repeat center center;
      background-size: cover;
      background-attachment: fixed;
      display: flex;
      flex-direction: column;
    }
    
    .wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .admin-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      max-width: 800px;
      margin: 2rem auto;
      width: 90%;
      border: none;
    }
    
    .admin-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 1.5rem;
      text-align: center;
      position: relative;
    }
    
    .admin-header h2 {
      position: relative;
      z-index: 2;
    }
    
    .movie-icon {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.2);
      font-size: 3rem;
      z-index: 1;
    }
    
    .form-container {
      padding: 2rem;
      background-color: white;
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
      color: white;
    }
    
    .btn-submit:hover {
      background: #d91a6d;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(247, 37, 133, 0.3);
    }
    
    .btn-cancel {
      background: #6c757d;
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }
    
    .btn-cancel:hover {
      background: #5a6268;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }
    
    .form-control, .form-select, .form-textarea {
      border-radius: 8px;
      padding: 0.75rem;
      border: 1px solid #ced4da;
      transition: all 0.3s ease;
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
    
    .form-textarea {
      min-height: 120px;
      resize: vertical;
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
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="admin-card">
      <div class="admin-header">
        <h2><i class="fas fa-film me-2"></i>Agregar Nueva Película</h2>
        <i class="fas fa-film movie-icon"></i>
      </div>
      
      <div class="form-container">
        <?php if(isset($error_message)): ?>
          <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-12">
              <label for="NombrePelicula" class="form-label">
                <i class="fas fa-ticket-alt"></i> Nombre de la Película
              </label>
              <input type="text" id="NombrePelicula" name="NombrePelicula" class="form-control" required>
            </div>
            
            <div class="col-12">
              <label for="DescripcionP" class="form-label">
                <i class="fas fa-align-left"></i> Descripción
              </label>
              <textarea id="DescripcionP" name="DescripcionP" class="form-control form-textarea" required></textarea>
            </div>
            
            <div class="col-md-6">
              <label for="DisponibilidadP" class="form-label">
                <i class="fas fa-calendar-check"></i> Disponibilidad
              </label>
              <select id="DisponibilidadP" name="DisponibilidadP" class="form-select" required>
                <option value="Disponible">Disponible</option>
                <option value="No Disponible">No Disponible</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label for="Duracion" class="form-label">
                <i class="fas fa-clock"></i> Duración (minutos)
              </label>
              <input type="number" id="Duracion" name="Duracion" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label for="Idiomas" class="form-label">
                <i class="fas fa-language"></i> Idiomas
              </label>
              <input type="text" id="Idiomas" name="Idiomas" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label for="Categoria" class="form-label">
                <i class="fas fa-tags"></i> Categoría
              </label>
              <input type="text" id="Categoria" name="Categoria" class="form-control" required>
            </div>
            
            <div class="col-12">
              <label for="Imagen" class="form-label">
                <i class="fas fa-image"></i> Imagen de la Película (URL)
              </label>
              <input type="text" id="Imagen" name="Imagen" class="form-control" placeholder="Ingresa el enlace de la imagen" required>
              <div class="image-preview-container">
                <img id="imagePreview" class="image-preview" src="" alt="Vista previa de la imagen" style="display: none;">
              </div>
            </div>
            
            <div class="col-12 mt-4">
              <button type="submit" class="btn btn-submit w-100">
                <i class="fas fa-save me-2"></i>Guardar Película
              </button>
              <a href="peliculas.php" class="btn btn-cancel w-100 mt-2">
                <i class="fas fa-times me-2"></i>Cancelar
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Vista previa de la imagen
    document.getElementById('Imagen').addEventListener('input', function() {
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