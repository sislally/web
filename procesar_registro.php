<?php
// Incluye el archivo que establece la conexión con la base de datos
include 'conexion.php';

// Verifica que el formulario se haya enviado mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtiene los datos enviados desde el formulario
    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $usuario = $_POST['usuario'];
    $clave_plana = $_POST['clave']; // Contraseña sin encriptar
    $rol = "cliente"; // Forzar rol cliente

    // Hashea (encripta) la contraseña antes de guardarla en la base de datos
    // PASSWORD_DEFAULT asegura el uso del algoritmo de encriptación más seguro disponible
    $clave_segura = password_hash($clave_plana, PASSWORD_DEFAULT);

    // Consulta SQL para insertar un nuevo usuario en la tabla Usuarios
    $sql = "INSERT INTO Usuarios 
            (nombre_completo, email, telefono, direccion, dui, usuario, clave, rol)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepara la consulta para prevenir inyección SQL
    $stmt = $conn->prepare($sql);

    // Vincula los parámetros con los datos del formulario
    $stmt->bind_param("ssssssss", $nombre_completo, $email, $telefono, $direccion, $dui, $usuario, $clave_segura, $rol);

    // Ejecuta la consulta y verifica si fue exitosa
    if ($stmt->execute()) {
        // Si el registro fue exitoso, muestra un mensaje y redirige al login
        echo "<script>
                alert('Usuario registrado con éxito.');
                window.location.href='login.php';
              </script>";
    } else {
        // Si hubo un error, lo muestra
        echo "Error al registrar: " . $conn->error;
    }
}
?>
