<?php
session_start();
if ($_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Empleado</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-purple: #8b5cf6;
            --secondary-purple: #7c3aed;
            --accent-cyan: #00d4ff;
            --accent-green: #06d6a0;
            --accent-orange: #ff6b35;
            --dark-bg: #0f0b27;
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --glass-border: rgba(139, 92, 246, 0.2);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: 
                radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(6, 214, 160, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(0, 212, 255, 0.08) 0%, transparent 50%),
                linear-gradient(135deg, #0f0b27 0%, #1e1b4b 25%, #312e81 50%, #1e293b 75%, #0f172a 100%);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(139, 92, 246, 0.3), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(6, 214, 160, 0.2), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(0, 212, 255, 0.2), transparent),
                radial-gradient(1px 1px at 130px 80px, rgba(255, 107, 53, 0.1), transparent);
            background-repeat: repeat;
            background-size: 200px 100px;
            animation: sparkle 20s linear infinite;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes sparkle {
            0% { transform: translateX(0); }
            100% { transform: translateX(-200px); }
        }

        header {
            background: linear-gradient(135deg, 
                rgba(139, 92, 246, 0.9) 0%, 
                rgba(124, 58, 237, 0.85) 30%,
                rgba(99, 102, 241, 0.8) 70%,
                rgba(139, 92, 246, 0.9) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 40px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 8px 32px rgba(139, 92, 246, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(
                from 0deg,
                transparent,
                rgba(255, 255, 255, 0.05),
                transparent,
                rgba(255, 255, 255, 0.08),
                transparent
            );
            animation: rotate 10s linear infinite;
            pointer-events: none;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 50%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
            text-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
            animation: glow 3s ease-in-out infinite alternate;
        }

        @keyframes glow {
            0% { filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3)); }
            100% { filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.5)); }
        }

        header p {
            font-family: 'Inter', sans-serif;
            font-size: 1.2rem;
            font-weight: 400;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin: 80px auto;
            max-width: 1400px;
            padding: 0 30px;
        }

        .card {
            background: linear-gradient(145deg, 
                rgba(255, 255, 255, 0.08) 0%, 
                rgba(139, 92, 246, 0.05) 50%,
                rgba(255, 255, 255, 0.02) 100%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 50px 35px;
            backdrop-filter: blur(20px);
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
            overflow: hidden;
            text-align: center;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 0 0 1px rgba(139, 92, 246, 0.1);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.1), 
                transparent);
            transition: left 0.6s ease;
        }

        .card:hover::before {
            left: 100%;
        }

        .card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-purple) 0%, var(--accent-cyan) 50%, var(--accent-green) 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            border-radius: 24px 24px 0 0;
        }

        .card:hover::after {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-15px) rotateY(5deg) rotateX(5deg);
            box-shadow: 
                0 40px 80px rgba(139, 92, 246, 0.25),
                0 0 0 1px rgba(139, 92, 246, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            background: linear-gradient(145deg, 
                rgba(139, 92, 246, 0.15) 0%, 
                rgba(99, 102, 241, 0.1) 50%,
                rgba(6, 214, 160, 0.05) 100%);
            border-color: rgba(139, 92, 246, 0.4);
        }

        .card-icon {
            font-size: 4rem;
            margin-bottom: 25px;
            position: relative;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.3));
        }

        .card:hover .card-icon {
            transform: scale(1.2) translateY(-10px);
            filter: drop-shadow(0 0 30px rgba(139, 92, 246, 0.6));
        }

        .card h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: all 0.4s ease;
            position: relative;
        }

        .card:hover h2 {
            color: var(--accent-cyan);
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
            transform: scale(1.05);
        }

        .card p {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            color: var(--text-secondary);
            line-height: 1.7;
            font-weight: 400;
            transition: all 0.4s ease;
        }

        .card:hover p {
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        /* Specific card colors and animations */
        .card#pagos .card-icon {
            background: linear-gradient(135deg, var(--accent-green) 0%, var(--accent-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card#reservas .card-icon {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card#multas .card-icon {
            background: linear-gradient(135deg, var(--accent-orange) 0%, #fbbf24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card#autos .card-icon {
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--primary-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating animation for icons */
        @keyframes float {
            0%, 100% { 
                transform: translateY(0) rotate(0deg); 
            }
            25% { 
                transform: translateY(-8px) rotate(1deg); 
            }
            50% { 
                transform: translateY(-4px) rotate(0deg); 
            }
            75% { 
                transform: translateY(-12px) rotate(-1deg); 
            }
        }

        .card-icon {
            animation: float 4s ease-in-out infinite;
        }

        .card:hover .card-icon {
            animation-play-state: paused;
        }

        /* Pulse effect for active states */
        @keyframes pulse {
            0%, 100% { 
                box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4); 
            }
            50% { 
                box-shadow: 0 0 0 20px rgba(139, 92, 246, 0); 
            }
        }

        .card:active {
            animation: pulse 0.6s ease-in-out;
        }

        /* Navigation dots indicator */
        .nav-indicator {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 100;
        }

        .nav-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(139, 92, 246, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-dot.active {
            background: var(--accent-cyan);
            transform: scale(1.5);
            box-shadow: 0 0 10px var(--accent-cyan);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                padding: 0 20px;
            }
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                margin: 50px auto;
                gap: 25px;
            }

            header {
                padding: 30px 20px;
            }

            .card {
                padding: 40px 25px;
                border-radius: 20px;
            }

            .card:hover {
                transform: translateY(-10px) rotateY(0deg) rotateX(0deg);
            }

            .card-icon {
                font-size: 3.5rem;
            }

            .card h2 {
                font-size: 1.7rem;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 25px 15px;
            }

            .container {
                padding: 0 15px;
                margin: 40px auto;
            }

            .card {
                padding: 30px 20px;
            }

            .card-icon {
                font-size: 3rem;
            }

            .card h2 {
                font-size: 1.5rem;
            }

            .card p {
                font-size: 1rem;
            }
        }

        /* Loading animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.8s ease forwards;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }

        /* Scroll indicator */
        .scroll-indicator {
            position: fixed;
            bottom: 50px;
            right: 30px;
            width: 50px;
            height: 50px;
            border: 2px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .scroll-indicator:hover {
            background: var(--primary-purple);
            border-color: var(--accent-cyan);
            transform: scale(1.1);
        }

        .scroll-indicator i {
            color: var(--text-secondary);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .scroll-indicator:hover i {
            color: white;
        }


/* Estilo exclusivo para el botón de cerrar sesión */
.nav-link.logout {
    display: flex;
    align-items: center;
    background-color: #ff4d4d; /* rojo llamativo */
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    width: 200px; /* ajustable */
    font-weight: bold;
    transition: background-color 0.3s, transform 0.2s;
}

.nav-link.logout:hover {
    background-color: #e60000; /* rojo más oscuro al pasar el mouse */
    transform: translateY(-2px); /* efecto sutil de “levantar” */
}

.nav-link.logout .nav-icon {
    margin-right: 10px;
    font-size: 18px;
}

.nav-link.logout .nav-text {
    font-size: 16px;
}

    </style>
</head>
<body>

<header>
    <!-- Saludo al empleado usando el nombre de sesión -->
    <h1>Bienvenido, <?php echo $_SESSION['usuario']; ?></h1>
    <p>Panel de Control del Empleado</p>
    
    <!-- Enlace para cerrar sesión -->
    <a href="logout.php" class="nav-link">
        <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
        <span class="nav-text">Cerrar Sesión</span>
    </a>
</header>

<!-- Contenedor principal con las tarjetas de acciones rápidas -->
<div class="container">
    
    <!-- Tarjeta: Pagos -->
    <a href="nav/pagos.php" class="card" id="pagos">
        <div class="card-icon">
            <i class="fas fa-credit-card"></i>
        </div>
        <h2>Pagos</h2>
        <p>Administrar y procesar pagos de clientes de manera segura y eficiente</p>
    </a>

    <!-- Tarjeta: Reservas -->
    <a href="nav/reservas.php" class="card" id="reservas">
        <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <h2>Reservas</h2>
        <p>Gestionar y organizar todas las reservas de vehículos del sistema</p>
    </a>

    <!-- Tarjeta: Multas -->
    <a href="nav/multas.php" class="card" id="multas">
        <div class="card-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2>Multas</h2>
        <p>Consultar, registrar y administrar multas e infracciones vehiculares</p>
    </a>

    <!-- Tarjeta: Autos -->
    <a href="nav/autos.php" class="card" id="autos">
        <div class="card-icon">
            <i class="fas fa-car"></i>
        </div>
        <h2>Autos</h2>
        <p>Visualizar y administrar toda la flota de vehículos disponibles</p>
    </a>
</div>

<!-- Indicadores de navegación (puntos) sincronizados con las tarjetas -->
<div class="nav-indicator">
    <div class="nav-dot active"></div> <!-- Punto activo inicial -->
    <div class="nav-dot"></div>
    <div class="nav-dot"></div>
    <div class="nav-dot"></div>
</div>

<!-- Indicador de scroll: al hacer clic sube suavemente al inicio de la página -->
<div class="scroll-indicator" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
    <i class="fas fa-chevron-up"></i>
</div>

<script>
    // =========================
    // Cambiar el indicador activo al pasar el mouse sobre cada tarjeta
    // =========================
    document.querySelectorAll('.card').forEach((card, index) => {
        card.addEventListener('mouseenter', () => {
            // Elimina la clase 'active' de todos los puntos de navegación
            document.querySelectorAll('.nav-dot').forEach(dot => dot.classList.remove('active'));
            
            // Agrega la clase 'active' al punto correspondiente a la tarjeta actual
            document.querySelectorAll('.nav-dot')[index].classList.add('active');
        });
    });

    // =========================
    // Scroll suave al hacer clic en los puntos de navegación
    // =========================
    document.querySelectorAll('.nav-dot').forEach((dot, index) => {
        dot.addEventListener('click', () => {
            const cards = document.querySelectorAll('.card');
            
            // Si la tarjeta correspondiente existe, hacer scroll hacia ella de forma suave
            if (cards[index]) {
                cards[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>


</body>
</html>