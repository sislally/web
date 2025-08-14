<?php
session_start();

// Permitir acceso a administradores y empleados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'empleado')) {
    header("Location: login.php");
    exit();
}

require_once '../conexion.php';

// AGREGAR RESERVA
if (isset($_POST['agregar'])) {
    $id_usuario = $_POST['id_usuario'];
    $id_auto = $_POST['id_auto'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $estado = $_POST['estado_reserva'];

    $stmt = $conexion->prepare("INSERT INTO Reservas (id_usuario, id_auto, fecha_inicio, fecha_fin, estado_reserva) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $id_usuario, $id_auto, $fecha_inicio, $fecha_fin, $estado);
    $stmt->execute();
    $stmt->close();
}

// ELIMINAR RESERVA
if (isset($_GET['eliminar'])) {
    $id_reserva = $_GET['eliminar'];
    $conexion->query("DELETE FROM Reservas WHERE id_reserva = $id_reserva");
}

// OBTENER DATOS PARA SELECTS
$usuarios = $conexion->query("SELECT id_usuario, nombre_completo FROM Usuarios");
$autos = $conexion->query("SELECT id_auto, marca, modelo FROM Autos");

// OBTENER RESERVAS
$reservas = $conexion->query("
    SELECT r.id_reserva, u.nombre_completo, a.marca, a.modelo, r.fecha_inicio, r.fecha_fin, r.estado_reserva
    FROM Reservas r
    JOIN Usuarios u ON r.id_usuario = u.id_usuario
    JOIN Autos a ON r.id_auto = a.id_auto
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas</title>
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
            max-width: 1200px;
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
        }

        h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: #c084fc;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid;
            border-image: linear-gradient(135deg, #7c3aed, #06b6d4) 1;
        }

        /* Formulario */
        form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        /* Inputs y Selects */
        select, input[type="date"] {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(124, 58, 237, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            color: #e5e7eb;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        select:focus, input[type="date"]:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        select option {
            background: #1f2937;
            color: #e5e7eb;
            padding: 10px;
        }

        /* Botón principal */
        button[type="submit"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        button[type="submit"]::after {
            content: '\f067';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
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
            padding: 16px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 500;
        }

        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        tr:hover {
            background: rgba(124, 58, 237, 0.1);
            transition: all 0.3s ease;
        }

        /* Estados de reserva */
        td:nth-child(6) {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        /* Link eliminar */
        a[href*="eliminar"] {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        a[href*="eliminar"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        }

        a[href*="eliminar"]::before {
            content: '\f2ed';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            form {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            table {
                font-size: 14px;
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
                padding: 12px 0 12px 120px !important;
            }

            td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 110px;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 600;
                color: #c084fc;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            td:last-child {
                border-bottom: none;
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

            form {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botón para regresar al panel correspondiente según el rol de usuario -->
        <a href="<?php 
            if(isset($_SESSION['rol'])) {
                // Si es administrador, ir a bienvenida_admin.php, si es empleado, bienvenida_empleado.php
                echo $_SESSION['rol'] === 'administrador' ? '../bienvenida_admin.php' : '../bienvenida_empleado.php';
            } else {
                echo '../login.php'; // Si no hay sesión activa, redirige al login
            }
        ?>" class="btn-regresar">Regresar al Panel</a>

        <!-- Título de la sección de gestión de reservas -->
        <h2><i class="fas fa-calendar-alt"></i> Gestión de Reservas</h2>

        <!-- Formulario para agregar una nueva reserva -->
        <form method="POST">
            <!-- Selección de usuario (cliente) -->
            <select name="id_usuario" required>
                <option value="">Seleccionar Usuario</option>
                <?php while ($u = $usuarios->fetch_assoc()): ?>
                    <option value="<?= $u['id_usuario'] ?>"><?= $u['nombre_completo'] ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Selección de auto disponible -->
            <select name="id_auto" required>
                <option value="">Seleccionar Auto</option>
                <?php while ($a = $autos->fetch_assoc()): ?>
                    <option value="<?= $a['id_auto'] ?>"><?= $a['marca'] . ' ' . $a['modelo'] ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Fecha de inicio y fin de la reserva -->
            <input type="date" name="fecha_inicio" required>
            <input type="date" name="fecha_fin" required>

            <!-- Estado inicial de la reserva -->
            <select name="estado_reserva" required>
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="cancelada">Cancelada</option>
                <option value="finalizada">Finalizada</option>
            </select>

            <!-- Botón para enviar el formulario y agregar la reserva -->
            <button type="submit" name="agregar">Agregar Reserva</button>
        </form>

        <!-- Listado de reservas registradas -->
        <h3><i class="fas fa-list"></i> Reservas Registradas</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Auto</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $reservas->fetch_assoc()): ?>
                <tr>
                    <!-- Datos de la reserva -->
                    <td data-label="ID"><?= $r['id_reserva'] ?></td>
                    <td data-label="Cliente"><?= $r['nombre_completo'] ?></td>
                    <td data-label="Auto"><?= $r['marca'] . ' ' . $r['modelo'] ?></td>
                    <td data-label="Inicio"><?= $r['fecha_inicio'] ?></td>
                    <td data-label="Fin"><?= $r['fecha_fin'] ?></td>
                    <td data-label="Estado"><?= ucfirst($r['estado_reserva']) ?></td>

                    <!-- Acciones disponibles para cada reserva -->
                    <td data-label="Acciones">
                        <!-- Link para eliminar la reserva, con confirmación de usuario -->
                        <a href="?eliminar=<?= $r['id_reserva'] ?>" onclick="return confirm('¿Eliminar reserva?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
