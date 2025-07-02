<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cinexpress</title>
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
    
    /* Hero Slider */
    .hero-slider {
      width: 100%;
      height: 70vh;
      overflow: hidden;
      position: relative;
    }
    
    .slider-container {
      display: flex;
      width: 100%;
      height: 100%;
      transition: transform 0.5s ease;
    }
    
    .slide {
      min-width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      position: relative;
    }
    
    .slide-content {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
      padding: 2rem;
      color: var(--blanco);
    }
    
    .slide-content h2 {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: var(--amarillo-brillante);
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .slide-content p {
      max-width: 600px;
      margin-bottom: 1rem;
    }
    
    .slide-indicators {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
    }
    
    .indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background-color: rgba(255,255,255,0.5);
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .indicator.active {
      background-color: var(--amarillo-brillante);
    }
    
    /* Secciones */
    section {
      padding: 3rem 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    h2 {
      font-size: 28px;
      color: var(--azul-oscuro);
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.25), -1px -1px 2px rgba(0, 0, 0, 0.15);
      border-bottom: 2px solid var(--amarillo-brillante);
      padding-bottom: 6px;
      margin-bottom: 20px;
      letter-spacing: 1px;
    }
    
    .movies-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 2rem;
    }
    
    .movie-card {
      background-color: var(--blanco);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .movie-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    
    .movie-poster {
      width: 100%;
      height: 350px;
      object-fit: cover;
      border-bottom: 3px solid var(--amarillo-brillante);
    }
    
    .movie-info {
      padding: 1.5rem;
    }
    
    .movie-title {
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
      color: var(--azul-oscuro);
    }
    
    .movie-meta {
      display: flex;
      justify-content: space-between;
      color: var(--gris-medio);
      font-size: 0.9rem;
      margin-bottom: 0.8rem;
    }
    
    .movie-description {
      font-size: 0.9rem;
      margin-bottom: 1rem;
      color: var(--gris-oscuro);
    }
    
    .btn {
      display: inline-block;
      padding: 0.5rem 1rem;
      background-color: var(--azul-oscuro);
      color: var(--blanco);
      text-decoration: none;
      border-radius: 4px;
      font-weight: 500;
      transition: background-color 0.3s;
    }
    
    .btn:hover {
      background-color: var(--azul-muy-oscuro);
    }
    
    /* Sobre nosotros */
    .about-section {
      background-color: var(--blanco);
      padding: 3rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      margin-top: 2rem;
    }
    
    .about-content {
      display: flex;
      align-items: center;
      gap: 2rem;
    }
    
    .about-text {
      flex: 1;
    }
    
    .about-image {
      flex: 1;
      border-radius: 8px;
      overflow: hidden;
    }
    
    .about-image img {
      width: 100%;
      height: auto;
      display: block;
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
      }
      
      nav ul {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .search-login {
        width: 100%;
        justify-content: center;
      }
      
      .hero-slider {
        height: 50vh;
      }
      
      .about-content {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Cinexpress</h1>
    <nav>
      <ul>
        <li><a href="#cartelera">Cartelera</a></li>
        <li><a href="#estrenos">Estrenos</a></li>
        <li><a href="peli.php">Películas</a></li>
      </ul>
    </nav>
    <div class="search-login">
      
      <a href="login.php"><button>Acceder</button></a>
    </div>
  </header>

  <!-- Hero Slider con películas destacadas -->
  <div class="hero-slider" id="heroSlider">
    <div class="slider-container" id="sliderContainer">
      <!-- Las slides se generarán con JavaScript -->
    </div>
    <div class="slide-indicators" id="slideIndicators">
      <!-- Los indicadores se generarán con JavaScript -->
    </div>
  </div>

  <main>
    <section id="cartelera">
      <h2>Cartelera Actual</h2>
      <div class="movies-grid" id="currentMovies">
        <!-- Películas se cargarán aquí con JavaScript -->
      </div>
    </section>

    <section id="estrenos">
      <h2>Próximos Estrenos</h2>
      <div class="movies-grid" id="upcomingMovies">
        <!-- Próximos estrenos se cargarán aquí con JavaScript -->
      </div>
    </section>

    <section class="about-section">
      <h2>Sobre Cinexpress</h2>
      <div class="about-content">
        <div class="about-text">
          <p>Bienvenidos a Cinexpress, el destino cinematográfico más emocionante de la ciudad. Desde 2010, nos hemos dedicado a brindar la mejor experiencia de cine con tecnología de punta, comodidad inigualable y una selección de películas que abarca desde los últimos estrenos hasta clásicos inolvidables.</p>
          <p>Nuestras salas cuentan con sistemas de sonido Dolby Atmos, pantallas 4K y asientos reclinables premium para tu máximo disfrute. Además, ofrecemos funciones especiales, eventos temáticos y promociones exclusivas para nuestros clientes frecuentes.</p>
          <a href="#" class="btn">Conoce más</a>
        </div>
        <div class="about-image">
          <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Cinexpress interior">
        </div>
      </div>
    </section>
  </main>

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
      <p>&copy; 2025 Cinexpress. Todos los derechos reservados.</p>
    </div>
  </footer>

  <script>
    // Función para cargar películas desde la API
    async function cargarPeliculas() {
      try {
        const response = await fetch('api/peliculas.php');
        if (!response.ok) {
          throw new Error('Error al cargar películas');
        }
        const peliculas = await response.json();
        
        // Separar en cartelera y estrenos (simulado)
        const cartelera = peliculas.slice(0, 6);
        const estrenos = peliculas.slice(6, 12);
        
        // Mostrar cartelera
        const carteleraContainer = document.getElementById('currentMovies');
        cartelera.forEach(pelicula => {
          carteleraContainer.innerHTML += crearTarjetaPelicula(pelicula);
        });
        
        // Mostrar estrenos
        const estrenosContainer = document.getElementById('upcomingMovies');
        estrenos.forEach(pelicula => {
          estrenosContainer.innerHTML += crearTarjetaPelicula(pelicula);
        });
        
        // Configurar slider con las primeras 3 películas
        configurarSlider(peliculas.slice(0, 3));
        
      } catch (error) {
        console.error('Error:', error);
        document.getElementById('currentMovies').innerHTML = 
          '<p>No se pudieron cargar las películas. Intente más tarde.</p>';
      }
    }
    
    // Función para crear el HTML de una tarjeta de película
    function crearTarjetaPelicula(pelicula) {
      return `
        <div class="movie-card">
          <img src="${pelicula.imagen || 'https://via.placeholder.com/300x450?text=Poster+no+disponible'}" 
               alt="${pelicula.NombrePelicula}" class="movie-poster">
          <div class="movie-info">
            <h3 class="movie-title">${pelicula.NombrePelicula}</h3>
            <div class="movie-meta">
              <span>${pelicula.duracion} min</span>
              <span>${pelicula.Categoria}</span>
            </div>
            <p class="movie-description">${pelicula.descripcionP}</p>
            <a href="detalle_pelicula.php?id=${pelicula.idP}" class="btn">Ver detalles</a>
          </div>
        </div>
      `;
    }
    
    // Configurar el slider de películas destacadas
    function configurarSlider(peliculasDestacadas) {
      const sliderContainer = document.getElementById('sliderContainer');
      const indicatorsContainer = document.getElementById('slideIndicators');
      
      peliculasDestacadas.forEach((pelicula, index) => {
        // Crear slide
        const slide = document.createElement('div');
        slide.className = 'slide';
        slide.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('${pelicula.imagen}')`;
        
        const slideContent = document.createElement('div');
        slideContent.className = 'slide-content';
        slideContent.innerHTML = `
          <h2>${pelicula.NombrePelicula}</h2>
          <p>${pelicula.descripcionP.substring(0, 150)}...</p>
          <a href="detalle_pelicula.php?id=${pelicula.idP}" class="btn">Ver ahora</a>
        `;
        
        slide.appendChild(slideContent);
        sliderContainer.appendChild(slide);
        
        // Crear indicador
        const indicator = document.createElement('div');
        indicator.className = 'indicator' + (index === 0 ? ' active' : '');
        indicator.dataset.index = index;
        indicator.addEventListener('click', () => {
          goToSlide(index);
        });
        indicatorsContainer.appendChild(indicator);
      });
      
      // Configurar auto-slide
      let currentSlide = 0;
      const slides = document.querySelectorAll('.slide');
      const indicators = document.querySelectorAll('.indicator');
      const totalSlides = slides.length;
      
      function goToSlide(index) {
        currentSlide = index;
        sliderContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Actualizar indicadores
        indicators.forEach((ind, i) => {
          ind.classList.toggle('active', i === currentSlide);
        });
      }
      
      function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        goToSlide(currentSlide);
      }
      
      // Cambiar slide cada 5 segundos
      setInterval(nextSlide, 5000);
    }
    
    // Cargar películas cuando la página esté lista
    document.addEventListener('DOMContentLoaded', cargarPeliculas);
  </script>
</body>
</html>