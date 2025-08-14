<?php
session_start();
require_once "conexion.php"; // Incluye la conexión a la base de datos

// Verificar que el usuario haya iniciado sesión y tenga rol de cliente
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php"); // Si no está logueado o no es cliente, lo redirige al login
    exit();
}

$id_usuario = $_SESSION['id_usuario']; // ID del usuario logueado (cliente)

// Consulta para obtener las reservas del cliente junto con la información del auto y pago
$sql = "
    SELECT 
        r.id_reserva,        -- ID único de la reserva
        a.marca,             -- Marca del auto
        a.modelo,            -- Modelo del auto
        a.tipo,              -- Tipo de auto (SUV, sedán, etc.)
        a.placa,             -- Placa del auto
        r.fecha_inicio,      -- Fecha de inicio de la reserva
        r.fecha_fin,         -- Fecha de fin de la reserva
        r.estado_reserva,    -- Estado actual de la reserva (pendiente, confirmada, etc.)
        p.monto_total,       -- Monto total del pago (puede ser NULL si no hay pago registrado)
        p.estado_pago        -- Estado del pago (pendiente, pagado, etc.)
    FROM Reservas r
    INNER JOIN Autos a ON r.id_auto = a.id_auto      -- Une las reservas con los autos
    LEFT JOIN Pagos p ON r.id_reserva = p.id_reserva -- Une opcionalmente con los pagos (puede no existir)
    WHERE r.id_usuario = ?                           -- Filtra por el cliente logueado
    ORDER BY r.fecha_inicio DESC                     -- Ordena las reservas por fecha más reciente
";

// Preparar la consulta
$stmt = $conexion->prepare($sql);

// Asignar el valor del ID de usuario al parámetro de la consulta
$stmt->bind_param("i", $id_usuario);

// Ejecutar la consulta
$stmt->execute();

// Obtener el resultado de la consulta
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
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
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e3a8a 100%);
            min-height: 100vh;
            color: #e2e8f0;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            margin-bottom: 30px;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.6);
            background: linear-gradient(135deg, #0891b2, #0e7490);
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #a855f7, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .reservations-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-top: 3px solid transparent;
            border-image: linear-gradient(90deg, #a855f7, #06b6d4) 1;
            animation: fadeInUp 0.6s ease-out;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
            min-width: 900px;
        }

        th {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            padding: 15px 12px;
            text-align: left;
            border-bottom: 2px solid rgba(168, 85, 247, 0.3);
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 0.9rem;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid rgba(168, 85, 247, 0.2);
            vertical-align: middle;
        }

        tr:hover {
            background: rgba(168, 85, 247, 0.1);
        }

        .id-cell {
            font-weight: 600;
            color: #06b6d4;
            text-align: center;
            width: 80px;
        }

        .car-info {
            font-weight: 600;
            color: #c084fc;
        }

        .car-type {
            font-size: 0.85rem;
            color: #94a3b8;
            font-style: italic;
        }

        .plate-cell {
            font-family: 'Poppins', monospace;
            font-weight: 700;
            color: #fbbf24;
            text-align: center;
            background: rgba(251, 191, 36, 0.1);
            border-radius: 6px;
            padding: 4px 8px;
            display: inline-block;
        }

        .date-cell {
            color: #e2e8f0;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pendiente {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .status-confirmada {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-cancelada {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .status-completada {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .payment-pendiente {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .payment-pagado {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .payment-cancelado {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .amount-cell {
            font-weight: 700;
            color: #34d399;
            font-size: 1.05rem;
        }

        .amount-pending {
            color: #94a3b8;
            font-style: italic;
        }

        .no-reservations {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .no-reservations-icon {
            font-size: 4rem;
            color: #6366f1;
            margin-bottom: 20px;
        }

        .no-reservations h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            color: #c084fc;
            margin-bottom: 10px;
        }

        .no-reservations p {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .explore-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
            margin-top: 20px;
        }

        .explore-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.6);
            background: linear-gradient(135deg, #9333ea, #6d28d9);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            h1 {
                font-size: 2rem;
            }

            .reservations-container {
                padding: 20px;
            }

            table {
                font-size: 0.85rem;
                min-width: 700px;
            }

            th, td {
                padding: 10px 8px;
            }

            .no-reservations {
                padding: 40px 15px;
            }

            .no-reservations-icon {
                font-size: 3rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            .back-button, .explore-button {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .reservations-container {
                padding: 15px;
            }

            table {
                min-width: 600px;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reservations-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.15), 0 15px 15px -5px rgba(0, 0, 0, 0.06);
        }

        /* Efectos de carga para filas */
        tr {
            animation: fadeIn 0.5s ease-out forwards;
        }

        tr:nth-child(even) {
            animation-delay: 0.1s;
        }

        tr:nth-child(odd) {
            animation-delay: 0.05s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botón para regresar a la página anterior -->
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver atrás
        </a>

        <!-- Encabezado de la página -->
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> Historial de Reservas</h1>
            <p class="subtitle">Consulta todas tus reservas y su estado actual</p>
        </div>

        <!-- Contenedor principal de las reservas -->
        <div class="reservations-container">

            <!-- Si existen reservas -->
            <?php if ($result->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <!-- Encabezado de la tabla -->
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-car"></i> Auto</th>
                                <th><i class="fas fa-tag"></i> Tipo</th>
                                <th><i class="fas fa-id-badge"></i> Placa</th>
                                <th><i class="fas fa-calendar-alt"></i> Fecha Inicio</th>
                                <th><i class="fas fa-calendar-check"></i> Fecha Fin</th>
                                <th><i class="fas fa-info-circle"></i> Estado Reserva</th>
                                <th><i class="fas fa-dollar-sign"></i> Monto Total</th>
                                <th><i class="fas fa-credit-card"></i> Estado Pago</th>
                            </tr>
                        </thead>

                        <!-- Cuerpo de la tabla -->
                        <tbody>
                            <!-- Recorrer todas las reservas -->
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <!-- ID de la reserva -->
                                    <td class="id-cell"><?= $row['id_reserva'] ?></td>

                                    <!-- Marca y modelo del auto -->
                                    <td>
                                        <div class="car-info"><?= $row['marca'] . " " . $row['modelo'] ?></div>
                                    </td>

                                    <!-- Tipo de auto -->
                                    <td>
                                        <div class="car-type"><?= $row['tipo'] ?></div>
                                    </td>

                                    <!-- Placa del auto -->
                                    <td>
                                        <span class="plate-cell"><?= $row['placa'] ?></span>
                                    </td>

                                    <!-- Fecha de inicio formateada -->
                                    <td class="date-cell">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y', strtotime($row['fecha_inicio'])) ?>
                                    </td>

                                    <!-- Fecha de fin formateada -->
                                    <td class="date-cell">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y', strtotime($row['fecha_fin'])) ?>
                                    </td>

                                    <!-- Estado de la reserva con ícono -->
                                    <td>
                                        <?php 
                                        $estado = strtolower($row['estado_reserva']); // Normalizamos a minúsculas
                                        $icon = '';
                                        // Asignar ícono dependiendo del estado
                                        switch($estado) {
                                            case 'pendiente':
                                                $icon = 'fas fa-clock';
                                                break;
                                            case 'confirmada':
                                                $icon = 'fas fa-check';
                                                break;
                                            case 'cancelada':
                                                $icon = 'fas fa-times';
                                                break;
                                            case 'completada':
                                                $icon = 'fas fa-check-double';
                                                break;
                                            default:
                                                $icon = 'fas fa-question';
                                        }
                                        ?>
                                        <!-- Badge visual del estado -->
                                        <span class="status-badge status-<?= $estado ?>">
                                            <i class="<?= $icon ?>"></i>
                                            <?= ucfirst($row['estado_reserva']) ?>
                                        </span>
                                    </td>

                                    <!-- Monto total de la reserva -->
                                    <td>
                                        <?php if ($row['monto_total'] !== null): ?>
                                            <div class="amount-cell">$<?= number_format($row['monto_total'], 2) ?></div>
                                        <?php else: ?>
                                            <div class="amount-pending">No registrado</div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Estado del pago -->
                                    <td>
                                        <?php 
                                        // Si no tiene estado de pago, se considera pendiente
                                        $pago_estado = strtolower($row['estado_pago'] ?? 'pendiente');
                                        $pago_icon = '';
                                        // Asignar ícono según estado de pago
                                        switch($pago_estado) {
                                            case 'pagado':
                                                $pago_icon = 'fas fa-check-circle';
                                                break;
                                            case 'cancelado':
                                                $pago_icon = 'fas fa-times-circle';
                                                break;
                                            default:
                                                $pago_icon = 'fas fa-clock';
                                                $pago_estado = 'pendiente';
                                        }
                                        ?>
                                        <!-- Badge visual del estado de pago -->
                                        <span class="payment-badge payment-<?= $pago_estado ?>">
                                            <i class="<?= $pago_icon ?>"></i>
                                            <?= ucfirst($row['estado_pago'] ?? 'Pendiente') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <!-- Si no existen reservas -->
            <?php else: ?>
                <div class="no-reservations">
                    <div class="no-reservations-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>No tienes reservas registradas</h3>
                    <p>¡Es el momento perfecto para hacer tu primera reserva!<br>
                    Explora nuestra flota de vehículos disponibles.</p>
                    <a href="catalogo.php" class="explore-button">
                        <i class="fas fa-search"></i>
                        Explorar Autos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>