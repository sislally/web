<?php
session_start();

// Permitir acceso solo a administradores y empleados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'empleado')) {
    header("Location: login_admin.php");
    exit();
}

require_once "../conexion.php"; // Tu archivo de conexión

$mensaje = "";
$id_reserva = "";
$monto_total = "";
$metodo_pago = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $id_reserva = $_POST['id_reserva'];
    $monto_total = $_POST['monto_total'];
    $metodo_pago = $_POST['metodo_pago'];
    $fecha_pago = date('Y-m-d'); // Fecha actual

    if (!empty($id_reserva) && !empty($monto_total) && !empty($metodo_pago)) {
        $sql = "INSERT INTO Pagos (id_reserva, monto_total, metodo_pago, fecha_pago, estado_pago) VALUES (?, ?, ?, ?, 'pagado')";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("idss", $id_reserva, $monto_total, $metodo_pago, $fecha_pago);

        if ($stmt->execute()) {
            $mensaje = "Pago agregado correctamente.";
            $id_reserva = $monto_total = $metodo_pago = "";
        } else {
            $mensaje = "Error al agregar el pago: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensaje = "Por favor, completa todos los campos.";
    }
}

// Obtener reservas pendientes con info del cliente y auto
$reservas = $conexion->query("
    SELECT r.id_reserva, u.nombre_completo, a.marca, a.modelo, r.fecha_inicio, r.fecha_fin
    FROM Reservas r
    JOIN Usuarios u ON r.id_usuario = u.id_usuario
    JOIN Autos a ON r.id_auto = a.id_auto
    WHERE r.estado_reserva='pendiente'
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Pago</title>
</head>
<body>
    <h2>Agregar Pago</h2>
    <?php if($mensaje != "") echo "<p>$mensaje</p>"; ?>

    <form method="POST" action="">
        <label>Reserva:</label>
        <select name="id_reserva" required>
            <option value="">Selecciona una reserva</option>
            <?php while($row = $reservas->fetch_assoc()): ?>
                <option value="<?= $row['id_reserva'] ?>" <?= $row['id_reserva']==$id_reserva?'selected':'' ?>>
                    <?= $row['nombre_completo'] ?> - <?= $row['marca'] ?> <?= $row['modelo'] ?> (<?= $row['fecha_inicio'] ?> a <?= $row['fecha_fin'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label>Monto Total:</label>
        <input type="number" name="monto_total" step="0.01" value="<?= $monto_total ?>" required>
        <br><br>

        <label>Método de Pago:</label>
        <select name="metodo_pago" required>
            <option value="">Selecciona método</option>
            <option value="efectivo" <?= $metodo_pago=='efectivo'?'selected':'' ?>>Efectivo</option>
            <option value="tarjeta" <?= $metodo_pago=='tarjeta'?'selected':'' ?>>Tarjeta</option>
        </select>
        <br><br>

        <button type="submit">Agregar Pago</button>
    </form>
</body>
</html>