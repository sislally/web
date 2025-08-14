<?php
session_start();

// Permitir acceso a administradores y empleados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'empleado')) {
    header("Location: login.php");
    exit();
}

require_once "../conexion.php";

// Obtener pagos con datos de cliente y auto
$sql = "SELECT 
            p.id_pago, p.monto_total, p.metodo_pago, p.fecha_pago, p.estado_pago,
            r.id_reserva, r.fecha_inicio, r.fecha_fin,
            a.marca, a.modelo,
            u.nombre_completo
        FROM Pagos p
        INNER JOIN Reservas r ON p.id_reserva = r.id_reserva
        INNER JOIN Autos a ON r.id_auto = a.id_auto
        INNER JOIN Usuarios u ON r.id_usuario = u.id_usuario";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1f2937 100%);
            min-height: 100vh;
            color: #e5e7eb;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Botón regresar */
        .btn-regresar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .btn-regresar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
            background: linear-gradient(135deg, #8b5cf6 0%, #c084fc 100%);
        }

        .btn-regresar::before {
            content: '\f053';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Títulos */
        h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #c084fc 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        h2 i {
            color: #10b981;
            font-size: 28px;
        }

        /* Tabla */
        table {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            margin-bottom: 40px;
        }

        th {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            padding: 16px 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        td {
            padding: 16px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 500;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        tr:hover {
            background: rgba(124, 58, 237, 0.1);
            transition: all 0.3s ease;
        }

        /* Monto destacado */
        td:nth-child(5) {
            font-weight: 700;
            color: #10b981;
            font-size: 16px;
        }

        /* Estado del pago */
        td:nth-child(8) {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        /* Acciones */
        td:nth-child(9) {
            white-space: nowrap;
        }

        td:nth-child(9) a {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        /* Botón Editar */
        a[href*="editar_pago"] {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);
        }

        a[href*="editar_pago"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
        }

        a[href*="editar_pago"]::before {
            content: '\f044';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Botón Eliminar */
        a[href*="eliminar_pago"] {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        a[href*="eliminar_pago"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        }

        a[href*="eliminar_pago"]::before {
            content: '\f2ed';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Botón Factura */
        a[href*="factura"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        a[href*="factura"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        a[href*="factura"]::before {
            content: '\f1c1';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Separadores en acciones */
        td:nth-child(9) {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Botón agregar pago */
        a[href*="agregar_pago"] {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        a[href*="agregar_pago"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.4);
            background: linear-gradient(135deg, #c084fc 0%, #a855f7 100%);
        }

        a[href*="agregar_pago"]::before {
            content: '\f067';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            h2 {
                font-size: 24px;
                flex-direction: column;
                gap: 10px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px 8px;
            }

            /* Tabla responsive */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                background: rgba(255, 255, 255, 0.05);
                margin-bottom: 15px;
                border-radius: 12px;
                padding: 15px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            td {
                border: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                position: relative;
                padding: 12px 0 12px 140px !important;
                text-align: left !important;
            }

            td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 130px;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 600;
                color: #c084fc;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            td:last-child {
                border-bottom: none;
            }

            /* Acciones en mobile */
            td:nth-child(9) a {
                display: block;
                margin: 4px 0;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .btn-regresar {
                padding: 10px 16px;
                font-size: 12px;
            }

            h2 {
                font-size: 20px;
            }

            a[href*="agregar_pago"] {
                padding: 12px 24px;
                font-size: 14px;
            }
        }

        /* Estados específicos de pagos */
        tr:has(td:nth-child(8):contains("Pendiente")) {
            border-left: 4px solid #f59e0b;
        }

        tr:has(td:nth-child(8):contains("Completado")) {
            border-left: 4px solid #10b981;
        }

        tr:has(td:nth-child(8):contains("Cancelado")) {
            border-left: 4px solid #ef4444;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="<?php 
        if(isset($_SESSION['rol'])) {
            echo $_SESSION['rol'] === 'administrador' ? '../bienvenida_admin.php' : '../bienvenida_empleado.php';
        } else {
            echo '../login.php'; // por si no hay sesión
        }
    ?>" class="btn-regresar">Regresar al Panel</a>

        <h2><i class="fas fa-credit-card"></i> Gestión de Pagos</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>Cliente</th>
                    <th>Auto</th>
                    <th>Fechas Reserva</th>
                    <th>Monto Total</th>
                    <th>Método</th>
                    <th>Fecha de Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td data-label="ID Pago"><?= $fila['id_pago'] ?></td>
            <td data-label="Cliente"><?= $fila['nombre_completo'] ?></td>
            <td data-label="Auto"><?= $fila['marca'] . ' ' . $fila['modelo'] ?></td>
            <td data-label="Fechas Reserva"><?= $fila['fecha_inicio'] . ' al ' . $fila['fecha_fin'] ?></td>
            <td data-label="Monto Total">$<?= number_format($fila['monto_total'], 2) ?></td>
            <td data-label="Método"><?= $fila['metodo_pago'] ?></td>
            <td data-label="Fecha de Pago"><?= $fila['fecha_pago'] ?></td>
            <td data-label="Estado"><?= ucfirst($fila['estado_pago']) ?></td>
            <td data-label="Acciones">
                <a href="editar_pago.php?id=<?= $fila['id_pago'] ?>">Editar</a>
                <a href="eliminar_pago.php?id=<?= $fila['id_pago'] ?>" onclick="return confirm('¿Estás seguro de eliminar este pago?')">Eliminar</a>
                <a href="../factura/factura.php?id=<?= $fila['id_pago'] ?>" target="_blank">Factura</a>
            </td>
        </tr>
    <?php endwhile; ?>
            </tbody>
        </table>

        <a href="agregar_pago.php">Registrar nuevo pago</a>
    </div>
</body>
</html>