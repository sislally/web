<?php
session_start();

// Permitir acceso a administradores y empleados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'empleado')) {
    header("Location: login.php");
    exit();
}


require_once "../conexion.php";

// AGREGAR o ACTUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];
    $tipo = $_POST['tipo'];
    $anio = $_POST['anio'];
    $estado = $_POST['estado'];
    $precio_dia = $_POST['precio_dia'];

    // Imagen
$imagen_nombre = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    // Carpeta uploads en la raíz del proyecto (subimos un nivel desde /nav)
    $ruta_destino = __DIR__ . '/../uploads/';
    
    // Verificar que exista la carpeta
    if (!is_dir($ruta_destino)) {
        mkdir($ruta_destino, 0777, true);
    }

    // Crear nombre único
    $imagen_nombre = uniqid() . '_' . basename($_FILES['imagen']['name']);

    // Mover archivo a la carpeta uploads en la raíz
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino . $imagen_nombre);
}
    }

    // AGREGAR NUEVO
    if (isset($_POST['agregar'])) {
        $stmt = $conexion->prepare("INSERT INTO Autos (marca, modelo, placa, tipo, año, estado, precio_dia, imagen) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssds", $marca, $modelo, $placa, $tipo, $anio, $estado, $precio_dia, $imagen_nombre);
        $stmt->execute();
        $stmt->close();
    }

    // EDITAR EXISTENTE
    if (isset($_POST['editar']) && isset($_POST['id_auto'])) {
        $id_auto = $_POST['id_auto'];

        // Si no se sube una nueva imagen, no se actualiza
        if (!empty($imagen_nombre)) {
            $stmt = $conexion->prepare("UPDATE Autos SET marca=?, modelo=?, placa=?, tipo=?, año=?, estado=?, precio_dia=?, imagen=? WHERE id_auto=?");
            $stmt->bind_param("ssssssdsi", $marca, $modelo, $placa, $tipo, $anio, $estado, $precio_dia, $imagen_nombre, $id_auto);
        } else {
            $stmt = $conexion->prepare("UPDATE Autos SET marca=?, modelo=?, placa=?, tipo=?, año=?, estado=?, precio_dia=? WHERE id_auto=?");
            $stmt->bind_param("ssssssdi", $marca, $modelo, $placa, $tipo, $anio, $estado, $precio_dia, $id_auto);
        }
        $stmt->execute();
        $stmt->close();
    }


// ELIMINAR
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    $stmt = $conexion->prepare("DELETE FROM Autos WHERE id_auto = ?");
    $stmt->bind_param("i", $id_eliminar);
    $stmt->execute();
    $stmt->close();
}

// OBTENER AUTO PARA EDITAR
$auto_editar = null;
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM Autos WHERE id_auto = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $result = $stmt->get_result();
    $auto_editar = $result->fetch_assoc();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Autos</title>
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
            background: linear-gradient(135deg, #1e1b3e 0%, #2a1b4d 35%, #1a1735 100%);
            color: #e0e0e0;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .btn-regresar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #7f5fff 0%, #9c7eff 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(127, 95, 255, 0.3);
        }

        .btn-regresar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(127, 95, 255, 0.4);
        }

        .btn-regresar::before {
            content: '\f060';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(135deg, #7f5fff 0%, #64d9ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 40px 0 20px 0;
            color: #64d9ff;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 40px;
            border-top: 3px solid;
            border-image: linear-gradient(90deg, #7f5fff, #64d9ff) 1;
        }

        form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #b8b8d1;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        select,
        input[type="file"] {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 12px 16px;
            color: #e0e0e0;
            font-size: 14px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            outline: none;
            border-color: #7f5fff;
            box-shadow: 0 0 20px rgba(127, 95, 255, 0.3);
            background: rgba(255, 255, 255, 0.12);
        }

        select option {
            background: #2a1b4d;
            color: #e0e0e0;
        }

        .btn-submit {
            background: linear-gradient(135deg, #7f5fff 0%, #9c7eff 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(127, 95, 255, 0.3);
            grid-column: 1 / -1;
            justify-self: center;
            max-width: 300px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(127, 95, 255, 0.4);
        }

        .table-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid;
            border-image: linear-gradient(90deg, #7f5fff, #64d9ff) 1;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: linear-gradient(135deg, rgba(127, 95, 255, 0.2) 0%, rgba(100, 217, 255, 0.2) 100%);
            font-weight: 600;
            color: #64d9ff;
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        tr {
            transition: all 0.3s ease;
        }

        tr:hover {
            background: rgba(127, 95, 255, 0.1);
        }

        .action-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .action-links a {
            color: #64d9ff;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .action-links a:hover {
            background: rgba(100, 217, 255, 0.2);
            transform: translateY(-1px);
        }

        .action-links a[href*="eliminar"] {
            color: #ff6f91;
        }

        .action-links a[href*="eliminar"]:hover {
            background: rgba(255, 111, 145, 0.2);
        }

        .car-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }

        .car-image:hover {
            transform: scale(1.1);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-disponible {
            background: linear-gradient(135deg, #00c851 0%, #4caf50 100%);
            color: white;
        }

        .status-alquiler {
            background: linear-gradient(135deg, #ff8800 0%, #ff6f00 100%);
            color: white;
        }

        .status-mantenimiento {
            background: linear-gradient(135deg, #ffbb33 0%, #ff8800 100%);
            color: white;
        }

        .status-no-disponible {
            background: linear-gradient(135deg, #ff6f91 0%, #ff3547 100%);
            color: white;
        }

        .price-tag {
            font-weight: 700;
            color: #4caf50;
            font-size: 16px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 2rem;
            }

            .form-card, .table-container {
                padding: 20px;
                margin-bottom: 20px;
            }

            form {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                min-width: 800px;
            }

            th, td {
                padding: 10px 8px;
                font-size: 13px;
            }

            .action-links {
                flex-direction: column;
                gap: 8px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }

            .form-card, .table-container {
                padding: 15px;
            }

            .btn-regresar {
                padding: 10px 16px;
                font-size: 13px;
            }
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

    <h1><i class="fas fa-car"></i> Gestión de Autos</h1>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($auto_editar): ?>
                <input type="hidden" name="id_auto" value="<?= $auto_editar['id_auto'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" placeholder="Ej: Toyota" value="<?= $auto_editar['marca'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo" placeholder="Ej: Corolla" value="<?= $auto_editar['modelo'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Placa</label>
                <input type="text" name="placa" placeholder="Ej: ABC-123" value="<?= $auto_editar['placa'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Tipo</label>
                <input type="text" name="tipo" placeholder="Ej: Sedán" value="<?= $auto_editar['tipo'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Año</label>
                <input type="number" name="anio" placeholder="2023" min="1900" max="2099" value="<?= $auto_editar['año'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Precio por Día ($)</label>
                <input type="number" step="0.01" name="precio_dia" placeholder="50.00" value="<?= $auto_editar['precio_dia'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estado" required>
                    <?php
                    $estados = ['disponible', 'en alquiler', 'mantenimiento', 'no disponible'];
                    foreach ($estados as $estado) {
                        $selected = ($auto_editar && $auto_editar['estado'] === $estado) ? 'selected' : '';
                        echo "<option value='$estado' $selected>$estado</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Imagen del Auto</label>
                <input type="file" name="imagen" accept="image/*">
            </div>

            <button type="submit" name="<?= $auto_editar ? 'editar' : 'agregar' ?>" class="btn-submit">
                <i class="fas fa-<?= $auto_editar ? 'edit' : 'plus' ?>"></i>
                <?= $auto_editar ? 'Actualizar Auto' : 'Agregar Auto' ?>
            </button>
        </form>
    </div>

    <h2><i class="fas fa-list"></i> Listado de Autos</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Placa</th>
                    <th>Tipo</th>
                    <th>Año</th>
                    <th>Estado</th>
                    <th>Precio/Día</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conexion->query("SELECT * FROM Autos");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id_auto']}</td>";
                    echo "<td>{$row['marca']}</td>";
                    echo "<td>{$row['modelo']}</td>";
                    echo "<td>{$row['placa']}</td>";
                    echo "<td>{$row['tipo']}</td>";
                    echo "<td>{$row['año']}</td>";
                    echo "<td>";
                    
                    // Estado con badge colorido
                    $estado_class = '';
                    switch($row['estado']) {
                        case 'disponible': $estado_class = 'status-disponible'; break;
                        case 'en alquiler': $estado_class = 'status-alquiler'; break;
                        case 'mantenimiento': $estado_class = 'status-mantenimiento'; break;
                        case 'no disponible': $estado_class = 'status-no-disponible'; break;
                    }
                    echo "<span class='status-badge $estado_class'>{$row['estado']}</span>";
                    echo "</td>";
                    
                    echo "<td><span class='price-tag'>$ {$row['precio_dia']}</span></td>";
                    echo "<td>";
                    if (!empty($row['imagen']) && file_exists(__DIR__ . '/../uploads/' . $row['imagen'])) {
                        echo "<img src='../uploads/{$row['imagen']}' alt='Imagen' class='car-image'>";
                    } else {
                        echo "<span style='color: #888;'><i class='fas fa-image'></i> Sin imagen</span>";
                    }
                    echo "</td>";
                    echo "<td class='action-links'>
                            <a href='autos.php?editar={$row['id_auto']}'><i class='fas fa-edit'></i> Editar</a>
                            <a href='autos.php?eliminar={$row['id_auto']}' onclick=\"return confirm('¿Eliminar auto?');\"><i class='fas fa-trash'></i> Eliminar</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>