<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>

  <!-- Fuente Poppins desde Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

  <style>
    /* Estilos generales y reseteo de márgenes/paddings */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Estilo del cuerpo de la página */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0e0b1f, #1a133d); /* Fondo degradado */
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh; /* Ocupa toda la altura de la pantalla */
      padding: 20px;
    }

    /* Contenedor principal del formulario */
    .container {
      background: #1b152e; /* Fondo oscuro */
      border-radius: 15px;
      box-shadow: 0 0 30px rgba(127, 95, 255, 0.15); /* Sombra suave */
      padding: 50px;
      max-width: 850px;
      width: 100%;
    }

    /* Título del formulario */
    .login-box h2 {
      color: #a58cff;
      margin-bottom: 30px;
      font-size: 2em;
      font-weight: 700;
      text-align: center;
    }

    /* Distribución de columnas en el formulario */
    .form-grid {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }

    /* Columna del formulario */
    .form-col {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    /* Estilo de los campos de entrada y selectores */
    input,
    select {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      background: #2a2150; /* Fondo de campos */
      border: none;
      border-radius: 8px;
      color: #ffffff;
      font-size: 1em;
      box-shadow: 0 2px 8px rgba(127, 95, 255, 0.08);
      transition: box-shadow 0.2s;
    }

    /* Efecto al enfocar un campo */
    input:focus,
    select:focus {
      outline: none;
      box-shadow: 0 0 0 2px #a58cff;
    }

    /* Botón de enviar */
    input[type="submit"] {
      background: linear-gradient(90deg, #7f5fff, #5b39aa);
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 25px;
      cursor: pointer;
      margin-top: 10px;
      padding: 12px;
      box-shadow: 0 5px 15px rgba(127, 95, 255, 0.15);
      transition: background 0.3s;
    }

    /* Efecto hover en el botón */
    input[type="submit"]:hover {
      background: linear-gradient(90deg, #5b39aa, #7f5fff);
    }

    /* Color del placeholder */
    ::placeholder {
      color: #b3a1ff;
      opacity: 1;
    }

    /* Diseño responsivo para pantallas pequeñas */
    @media (max-width: 768px) {
      .form-grid {
        flex-direction: column;
        gap: 0;
      }
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
          <input type="text" name="nombre_completo" placeholder="Nombre completo" required>
          <input type="email" name="email" placeholder="Correo electrónico" required>
          <input type="text" name="telefono" placeholder="Teléfono" required>
          <input type="text" name="direccion" placeholder="Dirección" required>
        </div>

        <!-- Segunda columna: datos de usuario -->
        <div class="form-col">
          <input type="text" name="dui" placeholder="DUI" required>
          <input type="text" name="usuario" placeholder="Nombre de usuario" required>
          <input type="password" name="clave" placeholder="Contraseña" required>

        </div>
      </div>

      <!-- Botón para enviar el formulario -->
      <input type="submit" value="Registrar">

      <!-- Enlace para ir al login si ya tiene cuenta -->
      <div style="text-align:center; margin-top:18px;">
        <span style="color:#d4d4f0;">¿Ya tienes cuenta?</span>
        <a href="login.php" 
           style="color:#8dbfff; text-decoration:none; font-weight:bold; margin-left:6px;">
           Inicia Sesión
        </a>
      </div>
    </form>
  </div>
</body>
</html>
