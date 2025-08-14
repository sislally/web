<?php
// Datos de conexión a la base de datos
$host = "localhost";   // Servidor de la base de datos (localhost)
$user = "root";        // Usuario de MySQL (por defecto en XAMPP es 'root')
$password = "";        // Contraseña del usuario MySQL (vacía por defecto en XAMPP)
$database = "autosdb"; // Nombre de la base de datos.

// Crear la conexión con MySQL usando la extensión mysqli
$conexion = new mysqli($host, $user, $password, $database);

// Verificar si ocurrió un error en la conexión
if ($conexion->connect_error) {
    // Si hay error, detener la ejecución y mostrar el mensaje
    die("Error en la conexión: " . $conexion->connect_error);
}
?>

