<?php
// Inicia la sesión para poder acceder a variables de sesión
session_start();

// Incluye el archivo de conexión a la base de datos
require_once "../conexion.php";

// Verifica que el usuario haya iniciado sesión y tenga rol de administrador o empleado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'empleado')) {
    // Si no cumple con el rol requerido, redirige al login
    header("Location: login.php");
    exit();
}

// Verifica que se haya recibido el parámetro 'id' por GET
if (!isset($_GET['id'])) {
    // Si no se recibe el ID del pago, redirige a la página de pagos
    header("Location: pagos.php");
    exit();
}

// Convierte el parámetro recibido en entero para mayor seguridad
$id_pago = intval($_GET['id']);

// Construye la consulta SQL para eliminar el pago con el ID especificado
$sql_delete = "DELETE FROM Pagos WHERE id_pago = $id_pago";

// Ejecuta la consulta y verifica si fue exitosa
if ($conexion->query($sql_delete)) {
    // Si se eliminó correctamente, redirige a la página de pagos
    header("Location: pagos.php");
    exit();
} else {
    // Si hubo un error, muestra el mensaje de error
    echo "Error al eliminar: " . $conexion->error;
}
?>