<?php
session_start();
require_once "../../Conexion/classConnectionMySQL.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Verificar si hay un ID de función válido
if (!isset($_GET['idF']) || !is_numeric($_GET['idF'])) {
    header("Location: confirmacion_reserva.php");
    exit();
}

$idFuncion = intval($_GET['idF']);

// Conexión a la base de datos
$NewConn = new ConnectionMySQL();
$NewConn->CreateConnection();

// Obtener detalles de la función
$queryFuncion = "SELECT f.*, p.NombrePelicula, p.imagen, s.numeroSala 
                 FROM CFunciones f 
                 JOIN CPeliculas p ON f.idPelicula = p.idP 
                 JOIN CSalas s ON f.idSala = s.idS 
                 WHERE f.idF = $idFuncion";
$resultFuncion = $NewConn->ExecuteQuery($queryFuncion);
$funcion = $resultFuncion->fetch_assoc();

if (!$funcion) {
    header("Location: index.php");
    exit();
}

// Obtener todos los asientos para esta sala (disponibles y ocupados)
$queryAsientos = "SELECT * FROM CAsientos 
                  WHERE idSala = {$funcion['idSala']} 
                  ORDER BY codigoAsiento";
$resultAsientos = $NewConn->ExecuteQuery($queryAsientos);

$NewConn->CloseConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seleccionar Asientos - Cinexpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      --verde-oscuro: #166534;
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
    
    .seats-container {
      max-width: 1200px;
      margin: 2rem auto;
      background-color: var(--blanco);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    
    .movie-info {
      display: flex;
      gap: 2rem;
      margin-bottom: 2rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid #eee;
    }
    
    .movie-poster {
      width: 150px;
      height: 225px;
      object-fit: cover;
      border-radius: 8px;
    }
    
    .movie-details {
      flex: 1;
    }
    
    .movie-title {
      font-size: 1.8rem;
      color: var(--azul-oscuro);
      margin-bottom: 0.5rem;
    }
    
    .function-info {
      margin-bottom: 1rem;
    }
    
    .screen {
      background: linear-gradient(to bottom, #bdc3c7, #2c3e50);
      color: white;
      text-align: center;
      padding: 1rem;
      margin: 2rem 0;
      border-radius: 5px;
      font-weight: bold;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .seats-grid {
      display: grid;
      grid-template-columns: repeat(10, 1fr);
      gap: 10px;
      margin-bottom: 2rem;
    }
    
    .seat {
      width: 40px;
      height: 40px;
      background-color: var(--gris-medio);
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: bold;
    }
    
    .seat:hover {
      transform: scale(1.1);
    }
    
    .seat.selected {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
    }
    
    .seat.occupied {
      background-color: var(--rojo-oscuro);
      color: white;
      cursor: not-allowed;
    }
    
    .seat-legend {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-bottom: 2rem;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .legend-color {
      width: 20px;
      height: 20px;
      border-radius: 3px;
    }
    
    .selected-seats {
      margin-top: 2rem;
      padding: 1rem;
      background-color: #f8fafc;
      border-radius: 8px;
    }
    
    .btn-continue {
      background-color: var(--amarillo-brillante);
      color: var(--gris-oscuro);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 1rem;
      font-size: 1.1rem;
    }
    
    .btn-continue:hover {
      background-color: var(--amarillo-oscuro);
      transform: translateY(-2px);
    }
    
    .payment-section {
      display: none;
      margin-top: 2rem;
      padding: 1.5rem;
      background-color: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #dee2e6;
    }
    
    .payment-method {
      margin-bottom: 1.5rem;
    }
    
    .payment-option {
      display: flex;
      align-items: center;
      padding: 1rem;
      margin-bottom: 1rem;
      background-color: white;
      border-radius: 8px;
      border: 1px solid #dee2e6;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .payment-option:hover {
      border-color: var(--amarillo-brillante);
    }
    
    .payment-option.selected {
      border-color: var(--amarillo-brillante);
      background-color: rgba(250, 204, 21, 0.1);
    }
    
    .payment-option input {
      margin-right: 1rem;
    }
    
    .payment-icon {
      font-size: 1.5rem;
      margin-right: 1rem;
      color: var(--azul-oscuro);
    }
    
    .card-details {
      margin-top: 1rem;
      padding: 1rem;
      background-color: white;
      border-radius: 5px;
      border: 1px solid #ced4da;
    }
    
    .btn-confirm {
      background-color: var(--verde-oscuro);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 1rem;
      font-size: 1.1rem;
    }
    
    .btn-confirm:hover {
      background-color: #14532d;
      transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
      header {
        flex-direction: column;
        gap: 1.5rem;
      }
      
      nav ul {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .movie-info {
        flex-direction: column;
      }
      
      .seats-grid {
        grid-template-columns: repeat(5, 1fr);
      }
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
        <li><a href="perfil.php">Mi Perfil</a></li>
      </ul>
    </nav>
    <div class="search-login">
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
    </div>
  </header>

  <div class="seats-container">
    <div class="movie-info">
      <img src="<?php echo htmlspecialchars($funcion['imagen']); ?>" alt="<?php echo htmlspecialchars($funcion['NombrePelicula']); ?>" class="movie-poster">
      <div class="movie-details">
        <h1 class="movie-title"><?php echo htmlspecialchars($funcion['NombrePelicula']); ?></h1>
        <div class="function-info">
          <p><strong>Sala:</strong> <?php echo htmlspecialchars($funcion['numeroSala']); ?></p>
          <p><strong>Hora:</strong> <?php echo date("h:i A", strtotime($funcion['hora'])); ?></p>
          <p><strong>Precio por asiento:</strong> $<?php echo number_format($funcion['promocion'], 2); ?></p>
        </div>
      </div>
    </div>
    
    <div class="screen">Pantalla</div>
    
    <div class="seat-legend">
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--gris-medio);"></div>
        <span>Disponible</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--amarillo-brillante);"></div>
        <span>Seleccionado</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--rojo-oscuro);"></div>
        <span>Ocupado</span>
      </div>
    </div>
    
    <div class="seats-grid" id="seatsGrid">
      <?php 
      $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
      $resultAsientos->data_seek(0);
      
      $asientosOcupados = [];
      while ($asiento = $resultAsientos->fetch_assoc()) {
          if ($asiento['estado'] === 'Ocupado') {
              $asientosOcupados[] = $asiento['codigoAsiento'];
          }
      }
      
      foreach ($letras as $letra) {
          for ($i = 1; $i <= 10; $i++) {
              $codigo = $letra . $i;
              $ocupado = in_array($codigo, $asientosOcupados);
              
              echo '<div class="seat ' . ($ocupado ? 'occupied' : 'available') . '" 
                   data-seat="' . $codigo . '" ' . ($ocupado ? 'title="Asiento ocupado"' : '') . '>
                   ' . $codigo . '</div>';
          }
      }
      ?>
    </div>
    
    <div class="selected-seats">
      <h3>Asientos seleccionados:</h3>
      <div id="selectedSeatsList">Ningún asiento seleccionado</div>
      <p>Total: $<span id="totalPrice">0.00</span></p>
    </div>
    
    <button type="button" class="btn-continue" id="continueBtn" disabled>
      <i class="fas fa-arrow-right"></i> Continuar con el Pago
    </button>
    
    <div class="payment-section" id="paymentSection">
      <h2>Método de Pago</h2>
      
      <div class="payment-method">
        <div class="payment-option selected" id="cardOption">
          <input type="radio" name="paymentMethod" id="cardMethod" value="tarjeta" checked>
          <i class="fas fa-credit-card payment-icon"></i>
          <div>
            <h5>Tarjeta de Crédito/Débito</h5>
            <p>Pago seguro con tarjeta</p>
          </div>
        </div>
        
        <div class="card-details" id="cardDetails">
          <div class="mb-3">
            <label for="cardNumber" class="form-label">Número de Tarjeta</label>
            <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="cardExpiry" class="form-label">Fecha de Expiración</label>
              <input type="text" class="form-control" id="cardExpiry" placeholder="MM/AA">
            </div>
            <div class="col-md-6 mb-3">
              <label for="cardCvv" class="form-label">CVV</label>
              <input type="text" class="form-control" id="cardCvv" placeholder="123">
            </div>
          </div>
          <div class="mb-3">
            <label for="cardName" class="form-label">Nombre en la Tarjeta</label>
            <input type="text" class="form-control" id="cardName" placeholder="Nombre Apellido">
          </div>
        </div>
      </div>
      
      <form id="reservationForm" action="procesar_reserva.php" method="POST">
        <input type="hidden" name="idFuncion" value="<?php echo $idFuncion; ?>">
        <input type="hidden" name="asientos" id="selectedSeatsInput">
        <input type="hidden" name="total" id="totalInput">
        
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="confirmTerms" required>
          <label class="form-check-label" for="confirmTerms">
            Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a> de la reserva
          </label>
        </div>
        
        <button type="submit" class="btn-confirm" id="confirmBtn">
          <i class="fas fa-check-circle"></i> Confirmar Reserva
        </button>
      </form>
    </div>
  </div>

  <!-- Modal Términos y Condiciones -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h6>Política de Reservas:</h6>
          <p>1. Las reservas se confirman inmediatamente al realizar el pago con tarjeta.</p>
          <p>2. No se permiten cambios ni devoluciones una vez realizada la reserva.</p>
          <p>3. Debes presentar tu código de reserva y una identificación al recoger tus boletos.</p>
          <p>4. Cinexpress se reserva el derecho de cancelar reservas con información fraudulenta.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>






// Validar formulario antes de enviar
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (selectedSeats.length === 0) {
        alert('Por favor selecciona al menos un asiento');
        return false;
    }
    
    if (!document.getElementById('confirmTerms').checked) {
        alert('Debes aceptar los términos y condiciones');
        return false;
    }
    
    // Validar datos de tarjeta
    const cardNumber = document.getElementById('cardNumber').value;
    const cardExpiry = document.getElementById('cardExpiry').value;
    const cardCvv = document.getElementById('cardCvv').value;
    const cardName = document.getElementById('cardName').value;
    
    if (!cardNumber || !cardExpiry || !cardCvv || !cardName) {
        alert('Por favor completa todos los datos de la tarjeta');
        return false;
    }
    
    // Validación básica de número de tarjeta (16 dígitos)
    if (!/^\d{16}$/.test(cardNumber.replace(/\s/g, ''))) {
        alert('El número de tarjeta debe tener 16 dígitos');
        return false;
    }
    
    // Validación básica de fecha (MM/AA)
    if (!/^(0[1-9]|1[0-2])\/?([0-9]{2})$/.test(cardExpiry)) {
        alert('Formato de fecha inválido (MM/AA)');
        return false;
    }
    
    // Validación básica de CVV (3 o 4 dígitos)
    if (!/^\d{3,4}$/.test(cardCvv)) {
        alert('El CVV debe tener 3 o 4 dígitos');
        return false;
    }
    
    // Mostrar loader
    const loader = document.createElement('div');
    loader.style.position = 'fixed';
    loader.style.top = '0';
    loader.style.left = '0';
    loader.style.width = '100%';
    loader.style.height = '100%';
    loader.style.backgroundColor = 'rgba(0,0,0,0.7)';
    loader.style.display = 'flex';
    loader.style.justifyContent = 'center';
    loader.style.alignItems = 'center';
    loader.style.zIndex = '1000';
    loader.innerHTML = '<div style="color: white; text-align: center;"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Procesando reserva...</p></div>';
    document.body.appendChild(loader);
    
    // Enviar datos al servidor
    const formData = new FormData(this);
    
    fetch('procesar_reserva.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirigir a confirmación con ID de reserva
            window.location.href = 'confirmacion_reserva.php?id=' + data.reserva_id;
        } else {
            alert('Error al procesar la reserva: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error de conexión: ' + error.message);
    })
    .finally(() => {
        document.body.removeChild(loader);
    });
});






    document.addEventListener('DOMContentLoaded', function() {
      const seats = document.querySelectorAll('.seat.available');
      const selectedSeatsList = document.getElementById('selectedSeatsList');
      const selectedSeatsInput = document.getElementById('selectedSeatsInput');
      const totalPriceElement = document.getElementById('totalPrice');
      const totalInput = document.getElementById('totalInput');
      const continueBtn = document.getElementById('continueBtn');
      const paymentSection = document.getElementById('paymentSection');
      const confirmBtn = document.getElementById('confirmBtn');
      
      let selectedSeats = [];
      const pricePerSeat = <?php echo $funcion['promocion']; ?>;
      
      // Manejar clic en asientos disponibles
      seats.forEach(seat => {
        seat.addEventListener('click', function() {
          const seatCode = this.getAttribute('data-seat');
          
          if (this.classList.contains('selected')) {
            // Deseleccionar asiento
            this.classList.remove('selected');
            selectedSeats = selectedSeats.filter(s => s !== seatCode);
          } else {
            // Seleccionar asiento
            this.classList.add('selected');
            selectedSeats.push(seatCode);
          }
          
          updateSelection();
        });
      });
      
      // Actualizar la selección y el total
      function updateSelection() {
        if (selectedSeats.length > 0) {
          selectedSeatsList.textContent = selectedSeats.join(', ');
          selectedSeatsInput.value = selectedSeats.join(',');
          
          // Calcular total
          const total = selectedSeats.length * pricePerSeat;
          totalPriceElement.textContent = total.toFixed(2);
          totalInput.value = total.toFixed(2);
          
          // Habilitar botón continuar
          continueBtn.disabled = false;
        } else {
          selectedSeatsList.textContent = 'Ningún asiento seleccionado';
          selectedSeatsInput.value = '';
          totalPriceElement.textContent = '0.00';
          totalInput.value = '0.00';
          continueBtn.disabled = true;
        }
      }
      
      // Mostrar sección de pago al continuar
      continueBtn.addEventListener('click', function() {
        paymentSection.style.display = 'block';
        this.style.display = 'none';
        window.scrollTo({
          top: paymentSection.offsetTop - 20,
          behavior: 'smooth'
        });
      });
      
      // Validar formulario antes de enviar
      document.getElementById('reservationForm').addEventListener('submit', function(e) {
        if (selectedSeats.length === 0) {
          e.preventDefault();
          alert('Por favor selecciona al menos un asiento');
          return false;
        }
        
        if (!document.getElementById('confirmTerms').checked) {
          e.preventDefault();
          alert('Debes aceptar los términos y condiciones');
          return false;
        }
        
        // Validar datos de tarjeta
        const cardNumber = document.getElementById('cardNumber').value;
        const cardExpiry = document.getElementById('cardExpiry').value;
        const cardCvv = document.getElementById('cardCvv').value;
        const cardName = document.getElementById('cardName').value;
        
        if (!cardNumber || !cardExpiry || !cardCvv || !cardName) {
          e.preventDefault();
          alert('Por favor completa todos los datos de la tarjeta');
          return false;
        }
        
        // Validación básica de número de tarjeta (16 dígitos)
        if (!/^\d{16}$/.test(cardNumber.replace(/\s/g, ''))) {
          e.preventDefault();
          alert('El número de tarjeta debe tener 16 dígitos');
          return false;
        }
        
        // Validación básica de fecha (MM/AA)
        if (!/^(0[1-9]|1[0-2])\/?([0-9]{2})$/.test(cardExpiry)) {
          e.preventDefault();
          alert('Formato de fecha inválido (MM/AA)');
          return false;
        }
        
        // Validación básica de CVV (3 o 4 dígitos)
        if (!/^\d{3,4}$/.test(cardCvv)) {
          e.preventDefault();
          alert('El CVV debe tener 3 o 4 dígitos');
          return false;
        }
        
        // Simular procesamiento de pago
        e.preventDefault();
        if (confirm(`¿Confirmar pago de $${(selectedSeats.length * pricePerSeat).toFixed(2)} con tarjeta terminada en ${cardNumber.slice(-4)}?`)) {
          // Redirigir a confirmación de reserva
          window.location.href = 'confirmacion_reserva.php?idF=' + <?php echo $idFuncion; ?> + 
                                '&asientos=' + encodeURIComponent(selectedSeats.join(',')) + 
                                '&total=' + (selectedSeats.length * pricePerSeat).toFixed(2);
        }
      });
    });
  </script>
</body>
</html>