<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - BlackCat Rent a Car</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4338ca;
            --secondary-color: #0f172a;
            --background-color: #0f0f23;
            --card-bg: #1a1a3a;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --accent-color: #00d4ff;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --border-color: #334155;
            --glass-bg: rgba(26, 26, 58, 0.6);
            --input-bg: rgba(51, 65, 85, 0.3);
            --hover-bg: rgba(99, 102, 241, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 212, 255, 0.3) 0%, transparent 50%),
                linear-gradient(135deg, #0f0f23 0%, #1a133d 100%);
            z-index: -1;
        }

        /* Floating particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 50%;
            opacity: 0.4;
            animation: float 6s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 0.4;
            }
            90% {
                opacity: 0.4;
            }
            100% {
                transform: translateY(-100vh) scale(1);
                opacity: 0;
            }
        }

        .container {
            display: flex;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 90%;
            min-height: 600px;
            position: relative;
            animation: slideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .welcome-section {
            flex: 1;
            padding: 4rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 30%, rgba(0, 212, 255, 0.2) 0%, transparent 50%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            backdrop-filter: blur(10px);
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: white;
            line-height: 1.1;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .welcome-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        .welcome-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            align-self: flex-start;
            animation: fadeInUp 0.8s ease 0.8s both;
        }

        .welcome-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

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

        .login-section {
            flex: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--card-bg);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInUp 0.8s ease 0.3s both;
        }

        .login-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .form-container {
            animation: fadeInUp 0.8s ease 0.5s both;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.875rem;
        }

        .input-container {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.1rem;
            z-index: 1;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: var(--input-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder {
            color: var(--text-secondary);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-input:focus + .input-icon {
            color: var(--primary-color);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            animation: fadeInUp 0.8s ease 0.7s both;
        }

        .register-link {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--primary-color);
        }

        /* Error/Success Messages */
        .message {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            animation: slideDown 0.5s ease;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #86efac;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading State */
        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .submit-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                margin: 1rem;
                max-width: none;
                width: auto;
                min-height: auto;
            }

            .welcome-section,
            .login-section {
                padding: 2rem;
            }

            .welcome-title {
                font-size: 2.5rem;
            }

            .login-title {
                font-size: 2rem;
            }

            .welcome-section {
                order: 2;
                padding: 2rem;
                text-align: center;
            }

            .login-section {
                order: 1;
            }
        }

        @media (max-width: 480px) {
            .welcome-section,
            .login-section {
                padding: 1.5rem;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .login-title {
                font-size: 1.8rem;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- Fondo animado con partículas -->
    <div class="particles" id="particles"></div>

    <div class="container">
        <!-- =============================
             Sección de bienvenida
             Muestra el logo, título y un texto introductorio
        ============================== -->
        <div class="welcome-section">
            <div class="welcome-content">
                
                <!-- Logo con ícono y texto -->
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-cat"></i> <!-- Ícono de gato -->
                    </div>
                    <div class="logo-text">BlackCat</div> <!-- Nombre de la marca -->
                </div>
                
                <!-- Título principal de bienvenida -->
                <h1 class="welcome-title">Bienvenido de vuelta</h1>
                
                <!-- Descripción motivacional -->
                <p class="welcome-description">
                    No sigas un solo camino... Con nosotros, cada viaje es una oportunidad nueva. 
                    Ágil como un gato, elegante como tu estilo. ¡Empieza a recorrer tus nueve caminos!
                </p>
                
                <!-- Botón que lleva a una página con más información -->
                <a href="info.php" class="welcome-btn">
                    <i class="fas fa-info-circle"></i>
                    Más Información
                </a>
            </div>
        </div>

        <!-- =============================
             Sección de inicio de sesión
             Formulario para que el usuario ingrese sus credenciales
        ============================== -->
        <div class="login-section">
            <!-- Encabezado del formulario -->
            <div class="login-header">
                <h2 class="login-title">Iniciar Sesión</h2>
                <p class="login-subtitle">Accede a tu cuenta para continuar</p>
            </div>

            <div class="form-container">
                <!-- Aquí se mostrarían mensajes de error o éxito -->
                <!-- Ejemplo: <div class="message error">Credenciales inválidas</div> -->
                
                <!-- Formulario de login -->
                <form action="procesar_login.php" method="POST" id="loginForm">
                    
                    <!-- Campo de usuario -->
                    <div class="form-group">
                        <label for="usuario" class="form-label">Usuario</label>
                        <div class="input-container">
                            <input 
                                type="text" 
                                id="usuario" 
                                name="usuario" 
                                class="form-input" 
                                placeholder="Ingresa tu usuario" 
                                required
                                autocomplete="username"
                            >
                            <!-- Ícono de usuario -->
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="form-group">
                        <label for="clave" class="form-label">Contraseña</label>
                        <div class="input-container">
                            <input 
                                type="password" 
                                id="clave" 
                                name="clave" 
                                class="form-input" 
                                placeholder="Ingresa tu contraseña" 
                                required
                                autocomplete="current-password"
                            >
                            <!-- Ícono de candado -->
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <!-- Botón para enviar el formulario -->
                    <button type="submit" class="submit-btn" id="submitBtn">
                        Iniciar Sesión
                    </button>
                </form>

                <!-- Enlace para crear una cuenta si el usuario no tiene -->
                <div class="form-footer">
                    <p class="register-link">
                        ¿No tienes cuenta? 
                        <a href="registro.php">Crear una cuenta</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    // Función para crear partículas flotantes en el fondo
    function createParticles() {
        const particlesContainer = document.getElementById('particles'); // Contenedor donde se insertarán las partículas
        const particleCount = 50; // Número total de partículas a generar

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div'); // Crear elemento <div> para la partícula
            particle.className = 'particle'; // Asignar clase para estilos CSS
            particle.style.left = Math.random() * 100 + '%'; // Posición horizontal aleatoria
            particle.style.animationDelay = Math.random() * 6 + 's'; // Retraso aleatorio antes de iniciar animación
            particle.style.animationDuration = (Math.random() * 3 + 3) + 's'; // Duración aleatoria de la animación
            particlesContainer.appendChild(particle); // Insertar partícula en el contenedor
        }
    }

    // Manejo del envío del formulario de login
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn'); // Botón de envío
        
        // Mostrar estado de "cargando"
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Iniciando sesión...';
        
        // Simular espera de respuesta del servidor (esto normalmente lo manejaría el backend)
        setTimeout(() => {
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Iniciar Sesión';
        }, 2000);
    });

    // Animación de enfoque (focus) y desenfoque (blur) en inputs
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)'; // Agrandar ligeramente el contenedor del input
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)'; // Volver a tamaño normal
        });
    });

    // Inicializar partículas y animaciones al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        createParticles(); // Llamar a la función que genera partículas
        
        // Agregar un retraso en la animación de cada elemento del formulario para un efecto escalonado
        const formElements = document.querySelectorAll('.form-group, .submit-btn, .form-footer');
        formElements.forEach((element, index) => {
            element.style.animationDelay = (0.5 + index * 0.1) + 's';
        });
    });

    // Mejorar accesibilidad con el teclado (uso de tecla Enter para cambiar de campo)
    document.addEventListener('keydown', (e) => {
        // Si la tecla presionada es Enter y el elemento activo es un input del formulario
        if (e.key === 'Enter' && e.target.classList.contains('form-input')) {
            const form = e.target.closest('form'); // Buscar el formulario al que pertenece el input
            const inputs = form.querySelectorAll('.form-input'); // Obtener todos los inputs del formulario
            const currentIndex = Array.from(inputs).indexOf(e.target); // Determinar índice del input actual
            
            // Si no es el último input, pasar al siguiente
            if (currentIndex < inputs.length - 1) {
                inputs[currentIndex + 1].focus();
            } else {
                // Si es el último, enviar el formulario
                form.querySelector('.submit-btn').click();
            }
        }
    });
</script>

</body>
</html>