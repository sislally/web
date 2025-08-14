<?php
session_start();

// Permitir acceso a administradores y empleados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'empleado')) {
    header("Location: login.php");
    exit();
}

require_once "../conexion.php";

// AGREGAR MULTA
if (isset($_POST['agregar'])) {
    $id_reserva = $_POST['id_reserva'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $fecha_multa = $_POST['fecha_multa'];

    $sql = "INSERT INTO Multas (id_reserva, descripcion, monto, fecha_multa) 
            VALUES ('$id_reserva', '$descripcion', '$monto', '$fecha_multa')";
    mysqli_query($conexion, $sql);
    header("Location: gestion_multas.php");
    exit();
}

// EDITAR MULTA
if (isset($_POST['editar'])) {
    $id_multa = $_POST['id_multa'];
    $id_reserva = $_POST['id_reserva'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $fecha_multa = $_POST['fecha_multa'];
    $estado_pago = $_POST['estado_pago'];

    $sql = "UPDATE Multas 
            SET id_reserva='$id_reserva', descripcion='$descripcion', monto='$monto', fecha_multa='$fecha_multa', estado_pago='$estado_pago' 
            WHERE id_multa=$id_multa";
    mysqli_query($conexion, $sql);
    header("Location: gestion_multas.php");
    exit();
}

// ELIMINAR MULTA
if (isset($_GET['eliminar'])) {
    $id_multa = $_GET['eliminar'];
    mysqli_query($conexion, "DELETE FROM Multas WHERE id_multa=$id_multa");
    header("Location: gestion_multas.php");
    exit();
}

// OBTENER MULTAS CON INFORMACIÓN DE CLIENTE Y AUTO
$sql = "SELECT m.*, r.id_reserva, u.nombre_completo, a.marca, a.modelo
        FROM Multas m
        INNER JOIN Reservas r ON m.id_reserva = r.id_reserva
        INNER JOIN Usuarios u ON r.id_usuario = u.id_usuario
        INNER JOIN Autos a ON r.id_auto = a.id_auto";
$multas = mysqli_query($conexion, $sql);

// OBTENER RESERVAS PARA SELECT
$reservas = mysqli_query($conexion, "SELECT r.id_reserva, u.nombre_completo, a.marca, a.modelo 
                                     FROM Reservas r
                                     INNER JOIN Usuarios u ON r.id_usuario = u.id_usuario
                                     INNER JOIN Autos a ON r.id_auto = a.id_auto");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Multas</title>
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
        h1 {
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

        h1::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #ef4444;
            font-size: 28px;
        }

        /* Formulario agregar */
        form:first-of-type {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        /* Inputs y Selects */
        select, input[type="text"], input[type="number"], input[type="date"] {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(124, 58, 237, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            color: #e5e7eb;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        select:focus, input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus {
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

        /* Botón agregar */
        button[name="agregar"] {
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

        button[name="agregar"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        button[name="agregar"]::after {
            content: '\f067';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Separador */
        hr {
            border: none;
            height: 2px;
            background: linear-gradient(135deg, #7c3aed, #06b6d4);
            margin: 30px 0;
            border-radius: 2px;
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
        }

        td {
            padding: 16px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 500;
            font-size: 14px;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        tr:hover {
            background: rgba(124, 58, 237, 0.1);
            transition: all 0.3s ease;
        }

        /* Monto destacado */
        td:nth-child(6) {
            font-weight: 700;
            color: #ef4444;
            font-size: 16px;
        }

        /* Estado del pago */
        td:nth-child(8) {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        /* Formularios inline en tabla */
        form[style*="inline-block"] {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        form[style*="inline-block"] input,
        form[style*="inline-block"] select {
            padding: 8px 12px;
            font-size: 12px;
            min-width: 120px;
        }

        form[style*="inline-block"] input[type="hidden"] {
            display: none;
        }

        /* Botón actualizar */
        button[name="editar"] {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        button[name="editar"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
        }

        button[name="editar"]::before {
            content: '\f044';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        /* Botón eliminar */
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
            gap: 4px;
            margin-top: 8px;
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

            h1 {
                font-size: 24px;
                flex-direction: column;
                gap: 10px;
            }

            form:first-of-type {
                grid-template-columns: 1fr;
                padding: 20px;
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
                margin-bottom: 20px;
                border-radius: 12px;
                padding: 15px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            td {
                border: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                position: relative;
                padding: 12px 0 12px 120px !important;
                text-align: left !important;
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
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            td:last-child {
                border-bottom: none;
                padding-left: 0 !important;
            }

            td:last-child:before {
                display: none;
            }

            /* Formularios inline responsive */
            form[style*="inline-block"] {
                flex-direction: column;
                align-items: stretch;
            }

            form[style*="inline-block"] input,
            form[style*="inline-block"] select,
            form[style*="inline-block"] button {
                width: 100%;
                margin: 2px 0;
            }
        }

        @media (max-width: 480px) {
            .btn-regresar {
                padding: 10px 16px;
                font-size: 12px;
            }

            h1 {
                font-size: 20px;
            }

            form:first-of-type {
                padding: 15px;
            }
        }

        /* Estados específicos de multas */
        tr:has(td:nth-child(8):contains("pendiente")) {
            border-left: 4px solid #f59e0b;
        }

        tr:has(td:nth-child(8):contains("pagado")) {
            border-left: 4px solid #10b981;
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

        <h1>Gestión de Multas</h1>

        <!-- Formulario Agregar -->
        <form method="POST">
            <select name="id_reserva" required>
                <option value="">Seleccione Reserva</option>
                <?php while ($res = mysqli_fetch_assoc($reservas)) { ?>
                    <option value="<?= $res['id_reserva'] ?>">
                        <?= $res['id_reserva'] ?> - <?= $res['nombre_completo'] ?> (<?= $res['marca'] ?> <?= $res['modelo'] ?>)
                    </option>
                <?php } ?>
            </select>
            <input type="text" name="descripcion" placeholder="Descripción" required>
            <input type="number" step="0.01" name="monto" placeholder="Monto" required>
            <input type="date" name="fecha_multa" required>
            <button type="submit" name="agregar">Agregar Multa</button>
        </form>

        <hr>

        <!-- Listado -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reserva</th>
                    <th>Cliente</th>
                    <th>Auto</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($m = mysqli_fetch_assoc($multas)) { ?>
                    <tr>
                        <td data-label="ID"><?= $m['id_multa'] ?></td>
                        <td data-label="Reserva"><?= $m['id_reserva'] ?></td>
                        <td data-label="Cliente"><?= $m['nombre_completo'] ?></td>
                        <td data-label="Auto"><?= $m['marca'] ?> <?= $m['modelo'] ?></td>
                        <td data-label="Descripción"><?= $m['descripcion'] ?></td>
                        <td data-label="Monto">$<?= $m['monto'] ?></td>
                        <td data-label="Fecha"><?= $m['fecha_multa'] ?></td>
                        <td data-label="Estado"><?= $m['estado_pago'] ?></td>
                        <td data-label="Acciones">
                            <!-- Botón Editar -->
                            <form method="POST" style="display:inline-block">
                                <input type="hidden" name="id_multa" value="<?= $m['id_multa'] ?>">
                                <input type="hidden" name="id_reserva" value="<?= $m['id_reserva'] ?>">
                                <input type="text" name="descripcion" value="<?= $m['descripcion'] ?>" required>
                                <input type="number" step="0.01" name="monto" value="<?= $m['monto'] ?>" required>
                                <input type="date" name="fecha_multa" value="<?= $m['fecha_multa'] ?>" required>
                                <select name="estado_pago">
                                    <option value="pendiente" <?= $m['estado_pago']=='pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="pagado" <?= $m['estado_pago']=='pagado'?'selected':'' ?>>Pagado</option>
                                </select>

                                <button type="submit" name="editar">Actualizar</button>
                            </form>
                            <!-- Botón Eliminar -->
                            <a href="gestion_multas.php?eliminar=<?= $m['id_multa'] ?>" onclick="return confirm('¿Eliminar multa?')">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>