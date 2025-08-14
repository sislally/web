<?php
session_start();
if ($_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

// Obtener información adicional del usuario si está disponible
$usuario_nombre = $_SESSION['usuario'] ?? 'Usuario';
$primer_nombre = explode(' ', $usuario_nombre)[0];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - BlackCat Rent a Car</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4338ca;
            --primary-light: #818cf8;
            --accent-color: #00d4ff;
            --accent-dark: #0284c7;
            --background-primary: #0f0f23;
            --background-secondary: #1a1a3a;
            --background-card: #252545;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.25);
            --shadow-glow: 0 0 30px rgba(99, 102, 241, 0.3);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 212, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(139, 69, 19, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: backgroundMove 20s ease-in-out infinite;
        }

        @keyframes backgroundMove {
            0%, 100% { transform: translateX(0) translateY(0); }
            33% { transform: translateX(-20px) translateY(20px); }
            66% { transform: translateX(20px) translateY(-20px); }
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(26, 26, 58, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1000;
            transition: var(--transition);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: var(--text-primary);
            background: rgba(99, 102, 241, 0.1);
        }

        .nav-link:hover::before {
            width: 80%;
        }

        .nav-link.active {
            color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }

        .user-menu {
            position: relative;
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition);
        }

        .user-button:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: var(--shadow-glow);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
        }

        /* Hero Section */
        .hero-section {
            padding: 4rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px);
        }

        .hero-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            max-width: 1400px;
            width: 100%;
            align-items: center;
        }

        .hero-content {
            animation: slideInLeft 1s ease-out;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--text-primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .hero-description {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.7;
            animation: fadeInUp 1s ease-out 0.8s both;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 1s both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            border: 2px solid transparent;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--text-primary);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--background-card);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            animation: slideInRight 1s ease-out;
        }

        .hero-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            animation: float 6s ease-in-out infinite;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(0, 212, 255, 0.1));
            z-index: 1;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: var(--transition);
        }

        .hero-image:hover img {
            transform: scale(1.05);
        }

        /* Floating elements */
        .floating-element {
            position: absolute;
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
            animation: floatElement 4s ease-in-out infinite;
        }

        .floating-element:nth-child(2) {
            top: 20%;
            right: -10%;
            animation-delay: -2s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: -10%;
            animation-delay: -1s;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Quick Actions */
        .quick-actions {
            padding: 2rem;
            background: var(--background-secondary);
            border-top: 1px solid var(--border-color);
        }

        .quick-actions-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-title h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-title p {
            color: var(--text-muted);
            font-size: 1.125rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .action-card {
            background: var(--background-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            transform: scaleX(0);
            transition: var(--transition);
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .action-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .action-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .action-card p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .action-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .action-link:hover {
            gap: 1rem;
        }

        /* Footer */
        .footer {
            background: var(--background-primary);
            border-top: 1px solid var(--border-color);
            padding: 2rem;
            text-align: center;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .footer-section p,
        .footer-section a {
            color: var(--text-muted);
            text-decoration: none;
            line-height: 1.6;
            transition: var(--transition);
        }

        .footer-section a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes floatElement {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .hero-visual {
                order: -1;
            }

            .floating-element {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .nav-menu {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero-section {
                padding: 2rem 1rem;
            }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                justify-content: center;
                width: 100%;
                max-width: 300px;
            }

            .actions-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .action-card {
                padding: 1.5rem;
            }

            .quick-actions,
            .footer {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1.25rem;
            }

            .hero-description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Encabezado del sitio -->
    <header class="header">
        <div class="header-container">
            <!-- Logo y nombre de la empresa -->
            <a href="#" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-cat"></i> <!-- Ícono de gato -->
                </div>
                BlackCat Rent a Car
            </a>

            <!-- Menú de navegación principal -->
            <nav class="nav-menu">
                <a href="#" class="nav-link active">Inicio</a>
                <a href="servicios.php" class="nav-link">Servicios</a>
                <a href="mis_reservas.php" class="nav-link">Mis Reservas</a>
                <a href="perfil.php" class="nav-link">Perfil</a>
            </nav>

            <!-- Menú del usuario (avatar y nombre) -->
            <div class="user-menu">
                <div class="user-button">
                    <div class="user-avatar">
                        <!-- Primera letra del nombre del usuario en mayúscula -->
                        <?= strtoupper(substr($primer_nombre, 0, 1)) ?>
                    </div>
                    <!-- Nombre del usuario (escapado por seguridad) -->
                    <span><?= htmlspecialchars($primer_nombre) ?></span>
                    <i class="fas fa-chevron-down"></i> <!-- Flecha de menú -->
                </div>
            </div>

            <!-- Botón para menú en dispositivos móviles -->
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="main-content">
        <!-- Sección hero o presentación principal -->
        <section class="hero-section">
            <div class="hero-container">
                <div class="hero-content">
                    <!-- Mensaje de bienvenida -->
                    <div class="welcome-badge">
                        <i class="fas fa-hand-wave"></i>
                        ¡Bienvenido de vuelta!
                    </div>
                    
                    <!-- Título principal -->
                    <h1 class="hero-title">
                        Descubre la 
                        <span style="color: var(--accent-color);">Libertad</span>
                        sobre Ruedas
                    </h1>
                    
                    <!-- Saludo personalizado con el nombre de usuario -->
                    <h2 class="hero-subtitle">
                        Hola, <?= htmlspecialchars($usuario_nombre) ?> 👋
                    </h2>
                    
                    <!-- Descripción de la empresa -->
                    <p class="hero-description">
                        Tu aventura perfecta te está esperando. Explora nuestra flota premium, 
                        reserva al instante y vive experiencias inolvidables con la confianza 
                        y calidad que solo BlackCat puede ofrecerte.
                    </p>
                    
                    <!-- Botones de acción principales -->
                    <div class="hero-actions">
                        <a href="servicios.php" class="btn btn-primary">
                            <i class="fas fa-car"></i>
                            Explorar Vehículos
                        </a>
                        <a href="mis_reservas.php" class="btn btn-secondary">
                            <i class="fas fa-calendar-alt"></i>
                            Mis Reservas
                        </a>
                    </div>
                </div>

                <!-- Imagen principal y estadísticas flotantes -->
                <div class="hero-visual">
                    <div class="hero-image">
                        <!-- Imagen de presentación (cargada en modo diferido) -->
                        <img src="img/inicio.png" alt="BlackCat Rent a Car - Vehículos Premium" loading="lazy">
                    </div>
                    
                    <!-- Estadística: cantidad de vehículos -->
                    <div class="floating-element">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Vehículos</span>
                        </div>
                    </div>
                    
                    <!-- Estadística: soporte 24/7 -->
                    <div class="floating-element">
                        <div class="stat-item">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Soporte</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>


<!-- Quick Actions: sección con accesos rápidos a funciones clave del sitio -->
<section class="quick-actions">
    <div class="quick-actions-container">
        
        <!-- Título de la sección -->
        <div class="section-title">
            <h2>¿Qué deseas hacer hoy?</h2>
            <p>Acceso rápido a nuestros servicios más populares</p>
        </div>

        <!-- Cuadrícula de opciones rápidas -->
        <div class="actions-grid">
            
            <!-- Tarjeta: Buscar vehículos -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Buscar Vehículos</h3>
                <p>Explora nuestra amplia flota de vehículos premium disponibles para ti</p>
                <a href="servicios.php" class="action-link">
                    Explorar ahora <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Tarjeta: Nueva reserva -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3>Nueva Reserva</h3>
                <p>Reserva tu vehículo ideal en pocos clics, rápido y seguro</p>
                <a href="servicios.php" class="action-link">
                    Reservar <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Tarjeta: Mis reservas -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Mis Reservas</h3>
                <p>Gestiona tus reservas activas y revisa tu historial completo</p>
                <a href="mis_reservas.php" class="action-link">
                    Ver reservas <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Tarjeta: Soporte 24/7 -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Soporte 24/7</h3>
                <p>¿Necesitas ayuda? Nuestro equipo está disponible las 24 horas</p>
                <a href="soporte.php" class="action-link">
                    Contactar <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

</main>

<!-- Footer: pie de página con información y enlaces -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">

            <!-- Sección de descripción de la empresa -->
            <div class="footer-section">
                <h3>BlackCat Rent a Car</h3>
                <p>Tu socio confiable en movilidad premium. Ofrecemos las mejores experiencias de renta de vehículos con la más alta calidad de servicio.</p>
            </div>
            
            <!-- Sección de enlaces rápidos -->
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <p><a href="servicios.php">Nuestros Servicios</a></p>
                <p><a href="sobre-nosotros.php">Sobre Nosotros</a></p>
                <p><a href="contacto.php">Contacto</a></p>
                <p><a href="terminos.php">Términos y Condiciones</a></p>
            </div>
            
            <!-- Sección de contacto -->
            <div class="footer-section">
                <h3>Contacto</h3>
                <p><i class="fas fa-phone"></i> +503 2XXX-XXXX</p>
                <p><i class="fas fa-envelope"></i> info@blackcatrent.com</p>
                <p><i class="fas fa-map-marker-alt"></i> San Salvador, El Salvador</p>
            </div>
        </div>
        
        <!-- Línea inferior con derechos de autor -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> BlackCat Rent a Car. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>


<!-- Scripts -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Selecciona elementos clave del DOM
    const userButton = document.querySelector(".user-button"); // Botón del usuario (ícono o avatar)
    const mobileToggle = document.querySelector(".mobile-menu-toggle"); // Botón del menú móvil
    const navMenu = document.querySelector(".nav-menu"); // Menú de navegación principal

    // =========================
    // Menú desplegable del usuario
    // =========================
    if (userButton) {
        // Crear el contenedor del menú desplegable
        const dropdown = document.createElement("div");
        dropdown.classList.add("user-dropdown");

        // Estilos básicos del dropdown
        dropdown.style.position = "absolute";
        dropdown.style.top = "50px";
        dropdown.style.right = "0";
        dropdown.style.background = "#252545";
        dropdown.style.border = "1px solid #334155";
        dropdown.style.borderRadius = "8px";
        dropdown.style.padding = "0.5rem";
        dropdown.style.display = "none"; // Oculto por defecto

        // Contenido del menú con enlaces
        dropdown.innerHTML = `
            <a href="perfil.php" style="display:block;padding:0.5rem 1rem;color:white;text-decoration:none;">Perfil</a>
            <a href="reservas.php" style="display:block;padding:0.5rem 1rem;color:white;text-decoration:none;">Mis Reservas</a>
            <a href="logout.php" style="display:block;padding:0.5rem 1rem;color:white;text-decoration:none;">Cerrar Sesión</a>
        `;

        // Insertar el menú desplegable en el DOM justo después del botón de usuario
        userButton.parentElement.appendChild(dropdown);

        // Evento para mostrar/ocultar el dropdown al hacer clic en el botón del usuario
        userButton.addEventListener("click", () => {
            dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
        });

        // Cerrar el dropdown si se hace clic fuera de él
        document.addEventListener("click", (e) => {
            if (!userButton.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = "none";
            }
        });
    }

    // =========================
    // Menú móvil
    // =========================
    if (mobileToggle && navMenu) {
        // Mostrar/ocultar el menú principal al hacer clic en el botón de menú móvil
        mobileToggle.addEventListener("click", () => {
            navMenu.style.display = navMenu.style.display === "flex" ? "none" : "flex";
        });
    }
});
</script>

</body>
</html>
<?php