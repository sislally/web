<?php
session_start();
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

require_once "../conexion.php";

// AGREGAR EMPLEADO/ADMIN
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $usuario = $_POST['usuario'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $sql = "INSERT INTO Usuarios (nombre_completo, email, telefono, direccion, dui, usuario, clave, rol)
            VALUES ('$nombre', '$email', '$telefono', '$direccion', '$dui', '$usuario', '$clave', '$rol')";
    mysqli_query($conexion, $sql);
    header("Location: empleados.php");
    exit();
}

// EDITAR EMPLEADO/ADMIN
if (isset($_POST['editar'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    // Si la clave está vacía, no se actualiza
    if (!empty($_POST['clave'])) {
        $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
        $sql = "UPDATE Usuarios SET nombre_completo='$nombre', email='$email', telefono='$telefono',
                direccion='$direccion', dui='$dui', usuario='$usuario', clave='$clave', rol='$rol'
                WHERE id_usuario=$id_usuario";
    } else {
        $sql = "UPDATE Usuarios SET nombre_completo='$nombre', email='$email', telefono='$telefono',
                direccion='$direccion', dui='$dui', usuario='$usuario', rol='$rol'
                WHERE id_usuario=$id_usuario";
    }
    mysqli_query($conexion, $sql);
    header("Location: empleados.php");
    exit();
}

// ELIMINAR EMPLEADO/ADMIN
if (isset($_GET['eliminar'])) {
    $id_usuario = $_GET['eliminar'];
    mysqli_query($conexion, "DELETE FROM Usuarios WHERE id_usuario=$id_usuario");
    header("Location: empleados.php");
    exit();
}

// LISTAR EMPLEADOS Y ADMINISTRADORES
$sql = "SELECT * FROM Usuarios WHERE rol IN ('empleado','administrador')";
$usuarios = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
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
            max-width: 1600px;
            margin: 0 auto;
        }

        h1 {
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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.select-group {
            position: relative;
        }

        label {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            margin-bottom: 8px;
            color: #c084fc;
            font-size: 0.95rem;
        }

        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(168, 85, 247, 0.3);
            border-radius: 12px;
            padding: 12px 15px;
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, 
        input[type="email"]:focus, 
        input[type="password"]:focus, 
        select:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }

        input::placeholder {
            color: #94a3b8;
        }

        select {
            cursor: pointer;
        }

        option {
            background: #1e1b4b;
            color: #e2e8f0;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.6);
            background: linear-gradient(135deg, #9333ea, #6d28d9);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            text-decoration: none;
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

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6);
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #a855f7, #06b6d4, transparent);
            margin: 40px 0;
            border-radius: 2px;
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
            font-size: 0.9rem;
            min-width: 1200px;
        }

        th {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            padding: 15px 8px;
            text-align: left;
            border-bottom: 2px solid rgba(168, 85, 247, 0.3);
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 0.85rem;
        }

        td {
            padding: 15px 8px;
            border-bottom: 1px solid rgba(168, 85, 247, 0.2);
            vertical-align: top;
        }

        tr:hover {
            background: rgba(168, 85, 247, 0.1);
        }

        .id-cell {
            font-weight: 600;
            color: #06b6d4;
            text-align: center;
            width: 60px;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-admin {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .role-employee {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .actions-cell {
            min-width: 300px;
        }

        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 280px;
        }

        .edit-form input,
        .edit-form select {
            padding: 6px 8px;
            font-size: 0.8rem;
            border-radius: 8px;
        }

        .edit-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .edit-actions .btn {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            h1 {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                padding: 15px;
            }

            table {
                font-size: 0.8rem;
                min-width: 900px;
            }

            th, td {
                padding: 10px 6px;
            }

            .edit-form {
                max-width: 200px;
            }

            .edit-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            .form-card {
                padding: 20px;
            }

            .table-container {
                padding: 10px;
            }
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

        /* Efectos adicionales */
        .form-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.15), 0 15px 15px -5px rgba(0, 0, 0, 0.06);
        }

        .table-container:hover {
            transform: translateY(-1px);
            box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.15), 0 15px 15px -5px rgba(0, 0, 0, 0.06);
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
?>" class="btn-regresar">
            <i class="fas fa-arrow-left"></i> Regresar al Panel
        </a>
        <h1><i class="fas fa-users-cog"></i> Gestión de Empleados y Administradores</h1>

        <!-- Formulario Agregar -->
        <div class="form-card">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre_completo"><i class="fas fa-user"></i> Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Nombre Completo" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" id="email" placeholder="Email" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                        <input type="text" name="telefono" id="telefono" placeholder="Teléfono" required>
                    </div>

                    <div class="form-group">
                        <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                        <input type="text" name="direccion" id="direccion" placeholder="Dirección" required>
                    </div>

                    <div class="form-group">
                        <label for="dui"><i class="fas fa-id-card"></i> DUI</label>
                        <input type="text" name="dui" id="dui" placeholder="DUI" required>
                    </div>

                    <div class="form-group">
                        <label for="usuario"><i class="fas fa-user-circle"></i> Usuario</label>
                        <input type="text" name="usuario" id="usuario" placeholder="Usuario" required>
                    </div>

                    <div class="form-group">
                        <label for="clave"><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="clave" id="clave" placeholder="Contraseña" required>
                    </div>

                    <div class="form-group select-group">
                        <label for="rol"><i class="fas fa-user-tag"></i> Rol</label>
                        <select name="rol" id="rol" required>
                            <option value="">Seleccione Rol</option>
                            <option value="empleado">Empleado</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="agregar" class="btn">
                    <i class="fas fa-user-plus"></i> Agregar Usuario
                </button>
            </form>
        </div>

        <div class="divider"></div>

        <!-- Listado -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>DUI</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($usuarios)) { ?>
                    <tr>
                        <td class="id-cell"><?= $u['id_usuario'] ?></td>
                        <td><?= $u['nombre_completo'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td><?= $u['telefono'] ?></td>
                        <td><?= $u['direccion'] ?></td>
                        <td><?= $u['dui'] ?></td>
                        <td><?= $u['usuario'] ?></td>
                        <td>
                            <span class="role-badge <?= $u['rol'] == 'administrador' ? 'role-admin' : 'role-employee' ?>">
                                <i class="fas fa-<?= $u['rol'] == 'administrador' ? 'crown' : 'user' ?>"></i>
                                <?= $u['rol'] ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <!-- Formulario de edición -->
                            <form method="POST" class="edit-form">
                                <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                <input type="text" name="nombre_completo" value="<?= $u['nombre_completo'] ?>" placeholder="Nombre" required>
                                <input type="email" name="email" value="<?= $u['email'] ?>" placeholder="Email" required>
                                <input type="text" name="telefono" value="<?= $u['telefono'] ?>" placeholder="Teléfono" required>
                                <input type="text" name="direccion" value="<?= $u['direccion'] ?>" placeholder="Dirección" required>
                                <input type="text" name="dui" value="<?= $u['dui'] ?>" placeholder="DUI" required>
                                <input type="text" name="usuario" value="<?= $u['usuario'] ?>" placeholder="Usuario" required>
                                <input type="password" name="clave" placeholder="Nueva contraseña (opcional)">
                                <select name="rol">
                                    <option value="empleado" <?= $u['rol']=='empleado'?'selected':'' ?>>Empleado</option>
                                    <option value="administrador" <?= $u['rol']=='administrador'?'selected':'' ?>>Administrador</option>
                                </select>
                                
                                <div class="edit-actions">
                                    <button type="submit" name="editar" class="btn btn-success">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <a href="empleados.php?eliminar=<?= $u['id_usuario'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('¿Eliminar este usuario?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>