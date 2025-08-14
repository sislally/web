<?php
// Inicia la sesión para poder usar variables de sesión
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

// Consulta para obtener los datos del pago según el ID
$sql = "SELECT * FROM Pagos WHERE id_pago = $id_pago";
$resultado = $conexion->query($sql);

// Verifica si se encontró el pago
if ($resultado->num_rows === 0) {
    // Si no existe, muestra mensaje y termina el script
    echo "Pago no encontrado";
    exit();
}

// Obtiene los datos del pago en un arreglo asociativo
$pago = $resultado->fetch_assoc();

// Verifica si el formulario fue enviado mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura los datos enviados desde el formulario
    $monto_total = $_POST['monto_total'];
    $metodo_pago = $_POST['metodo_pago'];
    $estado_pago = $_POST['estado_pago'];

    // Construye la consulta SQL para actualizar el pago
    $sql_update = "UPDATE Pagos SET 
                    monto_total = '$monto_total',
                    metodo_pago = '$metodo_pago',
                    estado_pago = '$estado_pago'
                   WHERE id_pago = $id_pago";

    // Ejecuta la consulta y verifica si fue exitosa
    if ($conexion->query($sql_update)) {
        // Si se actualizó correctamente, redirige a la página de pagos
        header("Location: pagos.php");
        exit();
    } else {
        // Si hubo un error, muestra el mensaje de error
        echo "Error al actualizar: " . $conexion->error;
    }
}
?>

<!-- Formulario para editar los datos del pago -->
<h2>Editar Pago</h2>
<form method="post">
    <!-- Campo para el monto total -->
    Monto Total: <input type="number" step="0.01" name="monto_total" value="<?= $pago['monto_total'] ?>" required><br>

    <!-- Campo para el método de pago -->
    Método de Pago: <input type="text" name="metodo_pago" value="<?= $pago['metodo_pago'] ?>" required><br>

    <!-- Selector para el estado del pago -->
    Estado: 
    <select name="estado_pago">
        <option value="pendiente" <?= $pago['estado_pago'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="pagado" <?= $pago['estado_pago'] === 'pagado' ? 'selected' : '' ?>>Pagado</option>
    </select><br>

    <!-- Botón para enviar el formulario -->
    <button type="submit">Actualizar Pago</button>
</form>