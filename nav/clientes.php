<?php
// Inicia la sesión para gestionar la autenticación
session_start();

// Verifica si el usuario inició sesión y tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    // Si no es administrador, redirige a la página de login y detiene la ejecución
    header("Location: login.php");
    exit();
}

// Incluye el archivo de conexión a la base de datos
require_once '../conexion.php';

/* ==============================
   AGREGAR CLIENTE
   ============================== */
if (isset($_POST['agregar'])) {
    // Obtiene los datos enviados desde el formulario
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $usuario = $_POST['usuario'];
    // Encripta la contraseña para almacenarla de forma segura
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    // Rol fijo para este tipo de registro
    $rol = "cliente";

    // Prepara e inserta el nuevo cliente usando consultas preparadas
    $stmt = $conexion->prepare("
        INSERT INTO Usuarios (nombre_completo, email, telefono, direccion, dui, usuario, clave, rol) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssss", $nombre, $email, $telefono, $direccion, $dui, $usuario, $clave, $rol);
    $stmt->execute();
    $stmt->close();
}

/* ==============================
   ELIMINAR CLIENTE
   ============================== */
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    // Elimina el usuario solo si es cliente
    $conexion->query("DELETE FROM Usuarios WHERE id_usuario = $id AND rol = 'cliente'");
}

/* ==============================
   EDITAR CLIENTE
   ============================== */
if (isset($_POST['editar'])) {
    // Recibe datos del formulario de edición
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $dui = $_POST['dui'];
    $usuario = $_POST['usuario'];

    // Actualiza la información del cliente usando consulta preparada
    $sql = "UPDATE Usuarios 
            SET nombre_completo=?, email=?, telefono=?, direccion=?, dui=?, usuario=? 
            WHERE id_usuario=? AND rol='cliente'";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssi", $nombre, $email, $telefono, $direccion, $dui, $usuario, $id);
    $stmt->execute();
    $stmt->close();
}

/* ==============================
   OBTENER LISTA DE CLIENTES
   ============================== */
$clientes = $conexion->query("SELECT * FROM Usuarios WHERE rol = 'cliente'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
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

        .btn-regresar {
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

        .btn-regresar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.6);
            background: linear-gradient(135deg, #0891b2, #0e7490);
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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            margin-bottom: 8px;
            color: #c084fc;
            font-size: 0.95rem;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(168, 85, 247, 0.3);
            border-radius: 12px;
            padding: 12px 15px;
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }

        input::placeholder {
            color: #94a3b8;
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
            text-decoration: none;
            display: inline-block;
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
            min-width: 800px;
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
            vertical-align: middle;
        }

        tr:hover {
            background: rgba(168, 85, 247, 0.1);
        }

        .table-row-form {
            display: contents;
        }

        .table-row-form input {
            width: 100%;
            min-width: 120px;
            padding: 8px 10px;
            font-size: 0.9rem;
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
            width: 60px;
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
                min-width: 600px;
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

            .btn-regresar {
                padding: 10px 15px;
                font-size: 0.9rem;
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

        /* Estilos para inputs específicos por tipo */
        input[type="email"] {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%2394a3b8' viewBox='0 0 16 16'%3e%3cpath d='M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
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
        <!-- Botón para regresar al panel correspondiente según el rol del usuario -->
        <a href="<?php 
            if(isset($_SESSION['rol'])) {
                // Si el usuario es administrador, regresa al panel de admin; si es empleado, al panel de empleado
                echo $_SESSION['rol'] === 'administrador' ? '../bienvenida_admin.php' : '../bienvenida_empleado.php';
            } else {
                echo '../login.php'; // Si no hay sesión activa, redirige al login
            }
        ?>" class="btn-regresar">
            <i class="fas fa-arrow-left"></i> Regresar al Panel
        </a>

        <!-- Título de la sección de Gestión de Clientes -->
        <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>

        <!-- Formulario para agregar un nuevo cliente -->
        <div class="form-card">
            <form method="POST">
                <div class="form-grid">
                    <!-- Campo para nombre completo del cliente -->
                    <div class="form-group">
                        <label for="nombre_completo"><i class="fas fa-user"></i> Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Nombre completo" required>
                    </div>

                    <!-- Campo para correo electrónico del cliente -->
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
                    </div>

                    <!-- Campo para teléfono -->
                    <div class="form-group">
                        <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                        <input type="text" name="telefono" id="telefono" placeholder="Teléfono" required>
                    </div>

                    <!-- Campo para dirección -->
                    <div class="form-group">
                        <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                        <input type="text" name="direccion" id="direccion" placeholder="Dirección" required>
                    </div>

                    <!-- Campo para DUI -->
                    <div class="form-group">
                        <label for="dui"><i class="fas fa-id-card"></i> DUI</label>
                        <input type="text" name="dui" id="dui" placeholder="DUI" required>
                    </div>

                    <!-- Campo para nombre de usuario -->
                    <div class="form-group">
                        <label for="usuario"><i class="fas fa-user-circle"></i> Nombre de Usuario</label>
                        <input type="text" name="usuario" id="usuario" placeholder="Nombre de usuario" required>
                    </div>

                    <!-- Campo para contraseña -->
                    <div class="form-group">
                        <label for="clave"><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" name="clave" id="clave" placeholder="Contraseña" required>
                    </div>
                </div>

                <!-- Botón para enviar el formulario y agregar el cliente -->
                <button type="submit" name="agregar" class="btn">
                    <i class="fas fa-user-plus"></i> Agregar Cliente
                </button>
            </form>
        </div>

        <!-- Tabla que muestra los clientes registrados -->
        <h3><i class="fas fa-list"></i> Clientes Registrados</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <!-- Encabezados de la tabla -->
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>DUI</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Bucle para mostrar cada cliente desde la base de datos -->
                    <?php while ($c = $clientes->fetch_assoc()): ?>
                    <tr>
                        <!-- Cada fila es un formulario para poder editar los datos de ese cliente individualmente -->
                        <form method="POST" class="table-row-form">
                            <!-- ID del cliente (oculto para enviar en POST) -->
                            <td class="id-cell">
                                <?= $c['id_usuario'] ?>
                                <input type="hidden" name="id_usuario" value="<?= $c['id_usuario'] ?>">
                            </td>

                            <!-- Campos editables con los datos del cliente -->
                            <td><input type="text" name="nombre_completo" value="<?= $c['nombre_completo'] ?>"></td>
                            <td><input type="email" name="email" value="<?= $c['email'] ?>"></td>
                            <td><input type="text" name="telefono" value="<?= $c['telefono'] ?>"></td>
                            <td><input type="text" name="direccion" value="<?= $c['direccion'] ?>"></td>
                            <td><input type="text" name="dui" value="<?= $c['dui'] ?>"></td>
                            <td><input type="text" name="usuario" value="<?= $c['usuario'] ?>"></td>

                            <!-- Acciones: Guardar cambios o eliminar cliente -->
                            <td>
                                <div class="actions-cell">
                                    <!-- Botón para guardar cambios en la fila actual -->
                                    <button type="submit" name="editar" class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                    <!-- Enlace para eliminar el cliente con confirmación -->
                                    <a href="?eliminar=<?= $c['id_usuario'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('¿Eliminar cliente?')">
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