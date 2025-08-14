<?php
// Inicia la sesión para poder usar variables de sesión
session_start();

// Incluye el archivo que conecta a la base de datos
include 'conexion.php';

// Verifica que el formulario se haya enviado mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recibe los datos enviados desde el formulario de login
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Consulta SQL para buscar al usuario en la base de datos
    $query = "SELECT * FROM Usuarios WHERE usuario = ?";
    
    // Prepara la consulta para evitar inyección SQL
    $stmt = $conexion->prepare($query);
    
    // Vincula el parámetro a la consulta (tipo "s" = string)
    $stmt->bind_param("s", $usuario);
    
    // Ejecuta la consulta
    $stmt->execute();
    
    // Obtiene el resultado de la consulta
    $resultado = $stmt->get_result();

    // Verifica si se encontró exactamente un usuario con ese nombre
    if ($resultado->num_rows === 1) {

        // Obtiene los datos del usuario como un array asociativo
        $usuario_data = $resultado->fetch_assoc();

        // Verifica si la contraseña ingresada coincide con la almacenada (encriptada)
        if (password_verify($clave, $usuario_data['clave'])) {

            // Guarda información del usuario en variables de sesión
            $_SESSION['id_usuario'] = $usuario_data['id_usuario'];
            $_SESSION['usuario'] = $usuario_data['usuario'];
            $_SESSION['rol'] = $usuario_data['rol'];

            // Redirige según el rol del usuario
            switch ($usuario_data['rol']) {
                case 'administrador':
                    header("Location: bienvenida_admin.php");
                    break;
                case 'empleado':
                    header("Location: bienvenida_empleado.php");
                    break;
                case 'cliente':
                    header("Location: index.php");
                    break;
            }
            exit(); // Finaliza el script después de redirigir
        } else {
            // Mensaje si la contraseña no es correcta
            echo "Contraseña incorrecta.";
        }
    } else {
        // Mensaje si no se encontró el usuario en la base de datos
        echo "Usuario no encontrado.";
    }
}
?>
