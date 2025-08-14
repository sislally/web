<?php
// Iniciar la sesión para acceder a las variables de sesión
session_start();

// Conectar a la base de datos (archivo con la configuración de conexión)
require_once "conexion.php";

// ---------------------------
// VERIFICAR SESIÓN DE CLIENTE
// ---------------------------

// Si no hay sesión iniciada o el rol no es "cliente", redirigir al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit(); // Detener ejecución
}

// Guardar en variable el ID del usuario que inició sesión
$usuario_id = $_SESSION['id_usuario'];

// Variables para mensajes de éxito o error
$mensaje = '';
$error = '';

// ---------------------------
// VERIFICAR ID DEL AUTO
// ---------------------------

// Si no se recibe el parámetro "id_auto" en la URL, detener la ejecución
if (!isset($_GET['id_auto'])) {
    die("No se seleccionó ningún auto.");
}

// Guardar el ID del auto recibido por GET
$id_auto = $_GET['id_auto'];

// ---------------------------
// OBTENER DATOS DEL AUTO
// ---------------------------

// Preparar consulta para obtener la información del auto por su ID
$stmt = $conexion->prepare("SELECT * FROM Autos WHERE id_auto = ?");
$stmt->bind_param("i", $id_auto); // "i" indica entero
$stmt->execute();
$result = $stmt->get_result();

// Si no existe el auto, mostrar mensaje y detener ejecución
if ($result->num_rows === 0) {
    die("Auto no encontrado.");
}

// Guardar la información del auto en un array asociativo
$auto = $result->fetch_assoc();
$stmt->close();

// ---------------------------
// PROCESAR LA RESERVA
// ---------------------------

// Si se envió el formulario de reserva
if (isset($_POST['reservar'])) {
    // Capturar datos enviados por POST
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $precio_total = $_POST['precio_total'];

    // Validar que ambas fechas se hayan seleccionado
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        $error = "Debes seleccionar ambas fechas.";
    } else {
        // Preparar consulta para insertar la reserva en la base de datos
        $stmt = $conexion->prepare(
            "INSERT INTO Reservas (id_usuario, id_auto, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)"
        );
        // Vincular parámetros: entero, entero, string, string
        $stmt->bind_param("iiss", $usuario_id, $id_auto, $fecha_inicio, $fecha_fin);

        // Ejecutar y verificar si la reserva fue exitosa
        if ($stmt->execute()) {
            // Mensaje de éxito con el total a pagar formateado con 2 decimales
            $mensaje = "Reserva realizada con éxito. Total a pagar: $" . number_format($precio_total, 2);
        } else {
            // Mensaje de error con detalle de la base de datos
            $error = "Error al realizar la reserva: " . $conexion->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Auto</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
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
            margin-bottom: 20px;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.6);
            background: linear-gradient(135deg, #0891b2, #0e7490);
        }

        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInDown 0.5s ease-out;
        }

        .message.success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2));
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .message.error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .auto-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-top: 3px solid transparent;
            border-image: linear-gradient(90deg, #a855f7, #06b6d4) 1;
            animation: fadeInUp 0.6s ease-out;
        }

        .car-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .car-image:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .car-info {
            margin-bottom: 30px;
        }

        .car-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #c084fc;
            margin-bottom: 15px;
            text-align: center;
        }

        .car-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(168, 85, 247, 0.2);
        }

        .detail-icon {
            color: #06b6d4;
            font-size: 1.1rem;
        }

        .detail-label {
            font-weight: 600;
            color: #94a3b8;
        }

        .detail-value {
            color: #e2e8f0;
            margin-left: auto;
        }

        .price-highlight {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .form-section {
            margin-top: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="date"] {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(168, 85, 247, 0.3);
            border-radius: 12px;
            padding: 12px 15px;
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }

        .total-section {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.1), rgba(6, 182, 212, 0.1));
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
            border: 2px solid rgba(168, 85, 247, 0.3);
        }

        .total {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #06b6d4;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.6);
            background: linear-gradient(135deg, #9333ea, #6d28d9);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            h1 {
                font-size: 2rem;
            }

            .auto-card {
                padding: 20px;
            }

            .car-details {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .total {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            .back-button {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .car-image {
                height: 200px;
            }

            .total {
                font-size: 1.3rem;
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

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Color picker personalizado para inputs date */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
    
<script>
    /**
     * Función para calcular el total de la reserva.
     * @param {number} precioDia - Precio de renta por día del auto.
     */
    function calcularTotal(precioDia) {
        // Obtener las fechas seleccionadas en los campos del formulario
        const inicio = new Date(document.getElementById('fecha_inicio').value);
        const fin = new Date(document.getElementById('fecha_fin').value);
        
        // Solo calcular si ambas fechas están seleccionadas
        if (inicio && fin) {
            // Calcular la diferencia en días (incluyendo el día de inicio, por eso +1)
            const dias = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
            
            if (dias > 0) {
                // Calcular el costo total multiplicando por el precio diario
                const total = dias * precioDia;

                // Mostrar el total en pantalla con formato de 2 decimales y cantidad de días
                document.getElementById('total').innerHTML = `
                    <i class="fas fa-calculator"></i>
                    Total: $${total.toFixed(2)} 
                    <span style="font-size: 1rem; font-weight: 400; color: #94a3b8;">
                        (${dias} ${dias === 1 ? 'día' : 'días'})
                    </span>
                `;

                // Guardar el total en el campo oculto para enviarlo al backend
                document.getElementById('precio_total').value = total.toFixed(2);

                // Habilitar el botón de reserva
                document.querySelector('.btn').disabled = false;
            } else {
                // Si las fechas son inválidas (fin antes de inicio)
                document.getElementById('total').innerHTML = `
                    <i class="fas fa-exclamation-triangle"></i>
                    Fechas inválidas
                `;
                document.getElementById('precio_total').value = 0;
                document.querySelector('.btn').disabled = true;
            }
        } else {
            // Si no hay fechas seleccionadas, mostrar $0.00
            document.getElementById('total').innerHTML = `
                <i class="fas fa-calculator"></i>
                Total: $0.00
            `;
            document.getElementById('precio_total').value = 0;
            document.querySelector('.btn').disabled = true;
        }
    }

    // Cuando la página cargue
    window.onload = function() {
        // Obtener la fecha de hoy en formato YYYY-MM-DD
        const today = new Date().toISOString().split('T')[0];

        // Establecer que no se puedan seleccionar fechas anteriores a hoy
        document.getElementById('fecha_inicio').min = today;
        document.getElementById('fecha_fin').min = today;
        
        // Cada vez que cambie la fecha de inicio, actualizar la fecha mínima de fin
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            document.getElementById('fecha_fin').min = this.value;
            
            // Si la fecha de fin actual es menor a la de inicio, ajustarla
            if (document.getElementById('fecha_fin').value < this.value) {
                document.getElementById('fecha_fin').value = this.value;
            }
        });
    }
</script>

</head>
<body>
    <div class="container">
        <!-- Botón para volver a la página anterior -->
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver atrás
        </a>

        <!-- Título principal de la página -->
        <h1><i class="fas fa-car"></i> Reservar Auto</h1>
        
        <!-- Mostrar mensaje de éxito si existe -->
        <?php if($mensaje): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <?= $mensaje ?>
            </div>
        <?php endif; ?>
        
        <!-- Mostrar mensaje de error si existe -->
        <?php if($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Tarjeta con información del auto -->
        <div class="auto-card">
            <!-- Imagen del auto (si no existe, mostrar imagen por defecto) -->
            <img src="<?= $auto['imagen'] ? '../uploads/'.$auto['imagen'] : 'sin-imagen.jpg' ?>" 
                 alt="<?= $auto['marca'] . ' ' . $auto['modelo'] ?>" 
                 class="car-image">

            
<!-- Contenedor con la información principal del auto -->
<div class="car-info">
    <!-- Título con marca y modelo del auto -->
    <h3 class="car-title"><?= $auto['marca'] . " " . $auto['modelo'] ?></h3>
    
    <!-- Contenedor con detalles del auto -->
    <div class="car-details">
        <!-- Tipo de vehículo -->
        <div class="detail-item">
            <i class="fas fa-car detail-icon"></i>
            <span class="detail-label">Tipo:</span>
            <span class="detail-value"><?= $auto['tipo'] ?></span>
        </div>
        
        <!-- Año del vehículo -->
        <div class="detail-item">
            <i class="fas fa-calendar detail-icon"></i>
            <span class="detail-label">Año:</span>
            <span class="detail-value"><?= $auto['año'] ?></span>
        </div>
        
        <!-- Precio por día -->
        <div class="detail-item price-highlight">
            <i class="fas fa-dollar-sign detail-icon"></i>
            <span class="detail-label">Precio por día:</span>
            <span class="detail-value">$<?= $auto['precio_dia'] ?></span>
        </div>
    </div>
</div>

<!-- Formulario para reservar el auto -->
<form method="POST" class="form-section">
    <div class="form-grid">
        <!-- Campo de fecha de inicio de la reserva -->
        <div class="form-group">
            <label for="fecha_inicio">
                <i class="fas fa-calendar-alt"></i>
                Fecha de inicio:
            </label>
            <!-- Al cambiar la fecha, se ejecuta la función calcularTotal enviando el precio por día -->
            <input type="date" 
                   id="fecha_inicio" 
                   name="fecha_inicio" 
                   onchange="calcularTotal(<?= $auto['precio_dia'] ?>)" 
                   required>
        </div>

        <!-- Campo de fecha de fin de la reserva -->
        <div class="form-group">
            <label for="fecha_fin">
                <i class="fas fa-calendar-check"></i>
                Fecha de fin:
            </label>
            <!-- Igual que el inicio, al cambiar se recalcula el total -->
            <input type="date" 
                   id="fecha_fin" 
                   name="fecha_fin" 
                   onchange="calcularTotal(<?= $auto['precio_dia'] ?>)" 
                   required>
        </div>
    </div>

    <!-- Sección para mostrar el total calculado -->
    <div class="total-section">
        <div class="total" id="total">
            <i class="fas fa-calculator"></i>
            Total: $0.00
        </div>
    </div>

    <!-- Campo oculto donde se guarda el total para enviarlo en el POST -->
    <input type="hidden" name="precio_total" id="precio_total" value="0">

    <!-- Botón para confirmar la reserva (deshabilitado hasta que se calculen las fechas) -->
    <button type="submit" name="reservar" class="btn" disabled>
        <i class="fas fa-calendar-plus"></i>
        Confirmar Reserva
    </button>
</form>

            </form>
        </div>
    </div>
</body>
</html>