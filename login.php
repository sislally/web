<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, #0e0b1f, #1a133d); /* igual que index.php */
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.container {
  display: flex;
  background: #1b152e; /* fondo similar al header de index.php */
  border-radius: 15px;

  overflow: hidden;
}
.welcome {
  padding: 60px;
  color: #b3a1ff; /* texto lila */
  width: 350px;
  background-color: #1a133d; /* fondo igual al gradiente de index.php */
}
.welcome h1 {
  font-size: 3em;
  margin-bottom: 20px;
  color: #a58cff; /* color del logo en index.php */
}
.welcome p {
  margin-bottom: 30px;
  color: #d4d4f0; /* texto claro */
}
.welcome .btn {
  padding: 10px 20px;
  background: linear-gradient(90deg, #7f5fff, #5b39aa); /* botón igual que index.php */
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 20px;
  text-decoration: none;
}
.login-box {
  background: rgba(179,161,255,0.08); /* fondo lila translúcido */
  backdrop-filter: blur(10px);
  padding: 60px;
  width: 350px;
}
.login-box h2 {
  color: #a58cff; /* color del logo */
  margin-bottom: 30px;
  font-size: 2em;
}
.login-box input[type="text"],
.login-box input[type="password"],
.login-box input[type="submit"] {
  width: 100%;
  box-sizing: border-box;
  padding: 12px;
  margin: 10px 0;
  background: rgba(127,95,255,0.12); /* fondo input morado claro */
  border: none;
  border-radius: 5px;
  color: #ffffff;
}
.login-box input[type="submit"] {
  width: 100%;
  padding: 12px;
  background: linear-gradient(90deg, #7f5fff, #5b39aa); /* botón igual que index.php */
  border: none;
  color: white;
  font-weight: bold;
  border-radius: 20px;
  cursor: pointer;
}
  </style>
</head>
<body>
  <!-- Contenedor principal que agrupa todo el contenido -->
  <div class="container">
    
    <!-- Sección de bienvenida con un mensaje motivacional -->
    <div class="welcome">
      <h1>Bienvenido</h1>
      <p>
        No sigas un solo camino...
        Con nosotros, cada viaje es una oportunidad nueva.
        Ágil como un gato, elegante como tu estilo.
        ¡Empieza a recorrer tus nueve caminos!
      </p>
      <!-- Botón que redirige a la página con más información -->
      <a href="info.php" class="btn">Más Información</a>
    </div>

    <!-- Caja de inicio de sesión -->
    <div class="login-box">
      <h2>Iniciar Sesión</h2>
      
      <!-- Formulario para enviar credenciales de inicio de sesión -->
      <!-- Usa el método POST para enviar los datos de forma segura -->
      <form action="procesar_login.php" method="POST">
        
        <!-- Campo de entrada para el nombre de usuario -->
        <input type="text" name="usuario" placeholder="Usuario" required>
        
        <!-- Campo de entrada para la contraseña -->
        <input type="password" name="clave" placeholder="Clave" required>
        
        <!-- Botón para enviar el formulario -->
        <input type="submit" value="Submit">

        <!-- Sección de enlace para registrarse si no se tiene cuenta -->
        <div style="text-align:center; margin-top:18px;">
          <span style="color:#d4d4f0;">¿No tienes cuenta?</span>
          <!-- Enlace que lleva al formulario de registro -->
          <a href="registro.php" 
             style="color:#8dbfff; text-decoration:none; font-weight:bold; margin-left:6px;">
             Crea una
          </a>
        </div>

      </form>
    </div>
  </div>
</body>
</html>

