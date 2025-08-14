<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

require_once '../conexion.php';

// Obtener lista de autos para el <select>
$autos = $conexion->query("SELECT id_auto, marca, modelo, placa FROM Autos");

// AGREGAR MANTENIMIENTO
if (isset($_POST['agregar'])) {
    $id_auto = $_POST['id_auto'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $tipo = $_POST['tipo'];

    $stmt = $conexion->prepare("INSERT INTO Mantenimientos (id_auto, descripcion, fecha_inicio, fecha_fin, tipo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_auto, $descripcion, $fecha_inicio, $fecha_fin, $tipo);
    $stmt->execute();
    $stmt->close();
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM Mantenimientos WHERE id_mantenimiento = $id");
}

// EDITAR
if (isset($_POST['editar'])) {
    $id = $_POST['id_mantenimiento'];
    $id_auto = $_POST['id_auto'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $tipo = $_POST['tipo'];

    $stmt = $conexion->prepare("UPDATE Mantenimientos SET id_auto=?, descripcion=?, fecha_inicio=?, fecha_fin=?, tipo=? WHERE id_mantenimiento=?");
    $stmt->bind_param("issssi", $id_auto, $descripcion, $fecha_inicio, $fecha_fin, $tipo, $id);
    $stmt->execute();
    $stmt->close();
}

// LISTAR MANTENIMIENTOS
$mantenimientos = $conexion->query("
    SELECT m.*, a.marca, a.modelo, a.placa
    FROM Mantenimientos m
    JOIN Autos a ON m.id_auto = a.id_auto
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mantenimientos</title>
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

        h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #a855f7, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 40px 0 20px 0;
            color: #c084fc;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-top: 3px solid transparent;
            border-image: linear-gradient(90deg, #a855f7, #06b6d4) 1;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            margin-bottom: 8px;
            color: #c084fc;
            font-size: 0.95rem;
        }

        select, input[type="date"], textarea {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(168, 85, 247, 0.3);
            border-radius: 12px;
            padding: 12px 15px;
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        select:focus, input[type="date"]:focus, textarea:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
            grid-column: 1 / -1;
        }

        .btn {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.6);
            background: linear-gradient(135deg, #9333ea, #6d28d9);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
        }

        .table-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            overflow-x: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
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
        }

        td {
            padding: 12px;
            border-bottom: 1px solid rgba(168, 85, 247, 0.2);
            vertical-align: top;
        }

        tr:hover {
            background: rgba(168, 85, 247, 0.1);
        }

        .table-row-form {
            display: contents;
        }

        .table-row-form select,
        .table-row-form input,
        .table-row-form textarea {
            width: 100%;
            min-width: 120px;
            padding: 8px 10px;
            font-size: 0.9rem;
        }

        .table-row-form textarea {
            min-height: 60px;
            max-width: 200px;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .actions-cell .btn {
            padding: 8px 12px;
            font-size: 0.85rem;
            min-width: auto;
        }

        .id-cell {
            font-weight: 600;
            color: #06b6d4;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            h2 {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                padding: 15px;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 10px 8px;
            }

            .actions-cell {
                flex-direction: column;
                gap: 5px;
            }

            .actions-cell .btn {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 1.5rem;
            }

            .form-card {
                padding: 20px;
            }

            .table-container {
                padding: 10px;
            }
        }

        /* Estilos para opciones de select */
        option {
            background: #1e1b4b;
            color: #e2e8f0;
        }

        /* Animaciones */
        .form-card, .table-container {
            animation: fadeInUp 0.6s ease-out;
        }

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
    </style>
</head>
</head>
<body>
    <div class="container">
        <!-- Título de la sección de gestión de mantenimientos -->
        <h2><i class="fas fa-tools"></i> Gestión de Mantenimientos</h2>

        <!-- Formulario para agregar un nuevo mantenimiento -->
        <div class="form-card">
            <form method="POST">
                <div class="form-grid">
                    <!-- Selección del auto para el mantenimiento -->
                    <div class="form-group">
                        <label for="id_auto"><i class="fas fa-car"></i> Seleccionar Auto</label>
                        <select name="id_auto" id="id_auto" required>
                            <option value="">-- Seleccionar Auto --</option>
                            <?php
                            // Reinicia el puntero de la lista de autos para iterar desde el inicio
                            mysqli_data_seek($autos, 0);
                            while ($a = $autos->fetch_assoc()):
                            ?>
                            <option value="<?= $a['id_auto'] ?>">
                                <?= $a['marca'] ?> <?= $a['modelo'] ?> - <?= $a['placa'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Campo de fecha de inicio del mantenimiento -->
                    <div class="form-group">
                        <label for="fecha_inicio"><i class="fas fa-calendar-alt"></i> Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                    </div>

                    <!-- Campo de fecha de finalización del mantenimiento -->
                    <div class="form-group">
                        <label for="fecha_fin"><i class="fas fa-calendar-check"></i> Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" required>
                    </div>

                    <!-- Selección del tipo de mantenimiento -->
                    <div class="form-group">
                        <label for="tipo"><i class="fas fa-cog"></i> Tipo de Mantenimiento</label>
                        <select name="tipo" id="tipo" required>
                            <option value="preventivo">Preventivo</option>
                            <option value="correctivo">Correctivo</option>
                        </select>
                    </div>

                    <!-- Campo de descripción del mantenimiento -->
                    <div class="form-group">
                        <label for="descripcion"><i class="fas fa-edit"></i> Descripción</label>
                        <textarea name="descripcion" id="descripcion" placeholder="Descripción del mantenimiento..." required></textarea>
                    </div>
                </div>

                <!-- Botón para enviar el formulario y agregar el mantenimiento -->
                <button type="submit" name="agregar" class="btn">
                    <i class="fas fa-plus"></i> Agregar Mantenimiento
                </button>
            </form>
        </div>

        <!-- Listado de mantenimientos registrados -->
        <h3><i class="fas fa-list"></i> Registros de Mantenimiento</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Auto</th>
                        <th>Descripción</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($m = $mantenimientos->fetch_assoc()): ?>
                    <tr>
                        <!-- Cada fila es un formulario independiente para poder editar ese mantenimiento -->
                        <form method="POST" class="table-row-form">
                            <input type="hidden" name="id_mantenimiento" value="<?= $m['id_mantenimiento'] ?>">

                            <!-- Mostrar ID del mantenimiento -->
                            <td class="id-cell"><?= $m['id_mantenimiento'] ?></td>

                            <!-- Selector del auto relacionado con este mantenimiento -->
                            <td>
                                <select name="id_auto" required>
                                    <?php
                                    // Reinicia el puntero de autos para mostrar todas las opciones
                                    mysqli_data_seek($autos, 0);
                                    while ($a = $autos->fetch_assoc()):
                                    $selected = $a['id_auto'] == $m['id_auto'] ? 'selected' : '';
                                    ?>
                                    <option value="<?= $a['id_auto'] ?>" <?= $selected ?>>
                                        <?= $a['marca'] ?> <?= $a['modelo'] ?> - <?= $a['placa'] ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </td>

                            <!-- Campo de descripción editable -->
                            <td><textarea name="descripcion"><?= $m['descripcion'] ?></textarea></td>

                            <!-- Campos de fechas editables -->
                            <td><input type="date" name="fecha_inicio" value="<?= $m['fecha_inicio'] ?>"></td>
                            <td><input type="date" name="fecha_fin" value="<?= $m['fecha_fin'] ?>"></td>

                            <!-- Selector del tipo de mantenimiento editable -->
                            <td>
                                <select name="tipo">
                                    <option value="preventivo" <?= $m['tipo'] == 'preventivo' ? 'selected' : '' ?>>Preventivo</option>
                                    <option value="correctivo" <?= $m['tipo'] == 'correctivo' ? 'selected' : '' ?>>Correctivo</option>
                                </select>
                            </td>

                            <!-- Botones de acción: guardar cambios o eliminar mantenimiento -->
                            <td>
                                <div class="actions-cell">
                                    <button type="submit" name="editar" class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                    <a href="?eliminar=<?= $m['id_mantenimiento'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('¿Eliminar mantenimiento?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
