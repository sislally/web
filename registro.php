<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>

  <!-- Fuentes Inter y Poppins desde Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary-purple: #8b5cf6;
      --secondary-purple: #7c3aed;
      --accent-cyan: #00d4ff;
      --accent-green: #06d6a0;
      --dark-bg: #0f0b27;
      --card-bg: rgba(255, 255, 255, 0.05);
      --input-bg: rgba(255, 255, 255, 0.08);
      --text-primary: #f8fafc;
      --text-secondary: #cbd5e1;
      --glass-border: rgba(139, 92, 246, 0.2);
      --success-color: #10b981;
      --error-color: #ef4444;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: 
        radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(6, 214, 160, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(0, 212, 255, 0.08) 0%, transparent 50%),
        linear-gradient(135deg, #0f0b27 0%, #1e1b4b 25%, #312e81 50%, #1e293b 75%, #0f172a 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
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
        radial-gradient(1px 1px at 90px 40px, rgba(0, 212, 255, 0.2), transparent);
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

    .container {
      background: linear-gradient(145deg, 
        rgba(255, 255, 255, 0.08) 0%, 
        rgba(139, 92, 246, 0.05) 50%,
        rgba(255, 255, 255, 0.02) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      border-radius: 32px;
      box-shadow: 
        0 32px 64px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1),
        0 0 0 1px rgba(139, 92, 246, 0.1);
      padding: 60px;
      max-width: 900px;
      width: 100%;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.8s ease forwards;
    }

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

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-purple) 0%, var(--accent-cyan) 50%, var(--accent-green) 100%);
      border-radius: 32px 32px 0 0;
    }

    .login-box h2 {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 50%, #cbd5e1 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 40px;
      font-size: 2.8rem;
      font-weight: 800;
      text-align: center;
      position: relative;
      text-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
    }

    .login-box h2::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--accent-cyan), var(--accent-green));
      border-radius: 2px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-bottom: 30px;
    }

    .form-col {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .input-group {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--accent-cyan);
      font-size: 1.2rem;
      z-index: 2;
      transition: all 0.3s ease;
    }

    input,
    select {
      width: 100%;
      padding: 18px 18px 18px 55px;
      background: var(--input-bg);
      border: 1px solid rgba(139, 92, 246, 0.2);
      border-radius: 16px;
      color: var(--text-primary);
      font-size: 1rem;
      font-weight: 500;
      font-family: 'Inter', sans-serif;
      backdrop-filter: blur(10px);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    input:focus,
    select:focus {
      outline: none;
      border-color: var(--accent-cyan);
      box-shadow: 
        0 0 0 3px rgba(0, 212, 255, 0.2),
        0 8px 25px rgba(139, 92, 246, 0.15);
      background: rgba(0, 212, 255, 0.05);
      transform: translateY(-2px);
    }

    input:focus + .input-icon,
    select:focus + .input-icon {
      color: var(--accent-cyan);
      transform: translateY(-50%) scale(1.1);
      filter: drop-shadow(0 0 8px rgba(0, 212, 255, 0.5));
    }

    ::placeholder {
      color: var(--text-secondary);
      opacity: 0.8;
      font-weight: 400;
    }

    .submit-btn {
      background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 50%, var(--accent-cyan) 100%);
      border: none;
      color: white;
      font-weight: 700;
      font-size: 1.1rem;
      font-family: 'Poppins', sans-serif;
      border-radius: 20px;
      cursor: pointer;
      padding: 20px 40px;
      margin-top: 20px;
      box-shadow: 
        0 15px 35px rgba(139, 92, 246, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.6s ease;
    }

    .submit-btn:hover::before {
      left: 100%;
    }

    .submit-btn:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 
        0 20px 40px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      background: linear-gradient(135deg, var(--secondary-purple) 0%, var(--primary-purple) 50%, var(--accent-green) 100%);
    }

    .submit-btn:active {
      transform: translateY(-1px) scale(0.98);
    }

    .login-link-container {
      text-align: center;
      margin-top: 30px;
      padding-top: 25px;
      border-top: 1px solid rgba(139, 92, 246, 0.2);
    }

    .login-link-container span {
      color: var(--text-secondary);
      font-size: 1rem;
      font-weight: 400;
    }

    .login-link {
      color: var(--accent-cyan);
      text-decoration: none;
      font-weight: 600;
      margin-left: 8px;
      padding: 8px 16px;
      border-radius: 12px;
      transition: all 0.3s ease;
      position: relative;
    }

    .login-link::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(6, 214, 160, 0.1));
      border-radius: 12px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .login-link:hover::before {
      opacity: 1;
    }

    .login-link:hover {
      color: white;
      text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
      transform: translateY(-2px);
    }

    /* Form validation styles */
    .input-group.success input {
      border-color: var(--success-color);
    }

    .input-group.error input {
      border-color: var(--error-color);
    }

    .input-group.success .input-icon {
      color: var(--success-color);
    }

    .input-group.error .input-icon {
      color: var(--error-color);
    }

    /* Loading animation for submit button */
    .submit-btn.loading {
      pointer-events: none;
      opacity: 0.8;
    }

    .submit-btn.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 20px;
      height: 20px;
      border: 2px solid transparent;
      border-top: 2px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        padding: 40px 30px;
        margin: 10px;
      }

      .form-grid {
        grid-template-columns: 1fr;
        gap: 25px;
      }

      .login-box h2 {
        font-size: 2.2rem;
        margin-bottom: 30px;
      }

      input,
      select {
        padding: 16px 16px 16px 50px;
      }

      .submit-btn {
        padding: 18px 35px;
        font-size: 1rem;
      }
    }

    @media (max-width: 480px) {
      .container {
        padding: 30px 20px;
        border-radius: 24px;
      }

      .login-box h2 {
        font-size: 2rem;
      }

      .form-col {
        gap: 15px;
      }

      input,
      select {
        padding: 15px 15px 15px 45px;
        font-size: 0.95rem;
      }

      .input-icon {
        left: 15px;
        font-size: 1.1rem;
      }

      .submit-btn {
        padding: 16px 30px;
      }
    }

    /* Floating label animation */
    .floating-label {
      position: relative;
    }

    .floating-label input {
      padding-top: 24px;
      padding-bottom: 12px;
    }

    .floating-label label {
      position: absolute;
      left: 55px;
      top: 18px;
      color: var(--text-secondary);
      font-size: 1rem;
      font-weight: 400;
      transition: all 0.3s ease;
      pointer-events: none;
      z-index: 1;
    }

    .floating-label input:focus ~ label,
    .floating-label input:not(:placeholder-shown) ~ label {
      top: 8px;
      font-size: 0.8rem;
      color: var(--accent-cyan);
      font-weight: 500;
    }

    /* Pulse effect for container */
    @keyframes pulse {
      0%, 100% {
        box-shadow: 
          0 32px 64px rgba(0, 0, 0, 0.2),
          inset 0 1px 0 rgba(255, 255, 255, 0.1),
          0 0 0 1px rgba(139, 92, 246, 0.1);
      }
      50% {
        box-shadow: 
          0 32px 64px rgba(139, 92, 246, 0.15),
          inset 0 1px 0 rgba(255, 255, 255, 0.15),
          0 0 0 1px rgba(139, 92, 246, 0.2);
      }
    }

    .container:hover {
      animation: pulse 3s ease-in-out infinite;
    }
  </style>
</head>
<body>
  <!-- Contenedor del formulario -->
  <div class="container login-box">
    <h2>Formulario de Registro</h2>

    <!-- Formulario que envía los datos a procesar_registro.php -->
    <form action="procesar_registro.php" method="POST">
      <div class="form-grid">
        
        <!-- Primera columna: datos personales -->
        <div class="form-col">
          <div class="input-group">
            <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
            <i class="fas fa-user input-icon"></i>
          </div>
          
          <div class="input-group">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <i class="fas fa-envelope input-icon"></i>
          </div>
          
          <div class="input-group">
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <i class="fas fa-phone input-icon"></i>
          </div>
          
          <div class="input-group">
            <input type="text" name="direccion" placeholder="Dirección" required>
            <i class="fas fa-map-marker-alt input-icon"></i>
          </div>
        </div>

        <!-- Segunda columna: datos de usuario -->
        <div class="form-col">
          <div class="input-group">
            <input type="text" name="dui" placeholder="DUI" required>
            <i class="fas fa-id-card input-icon"></i>
          </div>
          
          <div class="input-group">
            <input type="text" name="usuario" placeholder="Nombre de usuario" required>
            <i class="fas fa-user-circle input-icon"></i>
          </div>
          
          <div class="input-group">
            <input type="password" name="clave" placeholder="Contraseña" required>
            <i class="fas fa-lock input-icon"></i>
          </div>
        </div>
      </div>

      <!-- Botón para enviar el formulario -->
      <input type="submit" value="Registrar Usuario" class="submit-btn">

      <!-- Enlace para ir al login si ya tiene cuenta -->
      <div class="login-link-container">
        <span>¿Ya tienes cuenta?</span>
        <a href="login.php" class="login-link">
          <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </a>
      </div>
    </form>
  </div>

<script>
  // Función para manejar focus/blur sin parpadeo
  document.querySelectorAll('input').forEach(input => {
    const parent = input.parentNode;

    // Al enfocar
    input.addEventListener('focus', () => {
      parent.classList.add('focused');
    });

    // Al perder foco
    input.addEventListener('blur', () => {
      parent.classList.remove('focused');

      // Validación visual
      const value = input.value.trim();
      if (value !== '') {
        if (!parent.classList.contains('success')) {
          parent.classList.add('success');
          parent.classList.remove('error');
        }
      } else {
        if (!parent.classList.contains('error')) {
          parent.classList.add('error');
          parent.classList.remove('success');
        }
      }
    });
  });

  // Form submission: mostrar estado de carga
  const form = document.querySelector('form');
  if(form){
    form.addEventListener('submit', () => {
      const submitBtn = form.querySelector('.submit-btn');
      if(submitBtn){
        submitBtn.classList.add('loading');
        submitBtn.value = 'Procesando...';
      }
    });
  }
</script>

</body>
</html>