<?php
require_once 'conexion.php';

if (!isset($_GET['id_auto'])) {
    exit("ID de auto no especificado");
}

$id_auto = intval($_GET['id_auto']);

// Obtener imagen desde la base de datos
$sql = "SELECT imagen FROM Autos WHERE id_auto = $id_auto";
$resultado = mysqli_query($conexion, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    header("Content-Type: image/jpeg"); // Cambiar si es PNG
    echo $fila['imagen']; // Mostrar el BLOB
} else {
    // Imagen de respaldo si no existe
    header("Content-Type: image/png");
    readfile("sin_imagen.png");
}
?>
