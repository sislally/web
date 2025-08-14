<?php
// Incluye el archivo de conexión a la base de datos
include "../conexion.php";

// Obtiene el ID del pago desde la URL (GET) o usa 1 por defecto si no se envía
$id_pago = $_GET['id'] ?? 1;

// Consulta SQL para obtener los datos del pago y su relación con reservas, usuarios y autos
$sql = "SELECT 
            p.*,                -- Todos los campos de la tabla Pagos
            r.fecha_inicio,     -- Fecha de inicio de la reserva
            r.fecha_fin,        -- Fecha de fin de la reserva
            u.nombre_completo,  -- Nombre del cliente
            u.dui,              -- DUI del cliente
            u.direccion,        -- Dirección del cliente
            a.marca,            -- Marca del auto
            a.modelo,           -- Modelo del auto
            a.precio_dia        -- Precio por día del auto
        FROM Pagos p
        JOIN Reservas r ON p.id_reserva = r.id_reserva
        JOIN Usuarios u ON r.id_usuario = u.id_usuario
        JOIN Autos a ON r.id_auto = a.id_auto
        WHERE p.id_pago = $id_pago"; // Filtra por el pago indicado

// Ejecuta la consulta
$resultado = $conexion->query($sql);

// Obtiene los datos del pago como un array asociativo
$pago = $resultado->fetch_assoc();

// Si no se encuentra el pago, detiene la ejecución
if (!$pago) {
    die("Pago no encontrado.");
}

// Calcula la cantidad de días de alquiler
$dias = (strtotime($pago['fecha_fin']) - strtotime($pago['fecha_inicio'])) / (60 * 60 * 24);

// Calcula el subtotal (precio por día * días)
$subtotal = $pago['precio_dia'] * $dias;

// Calcula el IVA (13% del subtotal)
$iva = $subtotal * 0.13;

// Calcula el total (subtotal + IVA)
$total = $subtotal + $iva;

// Datos del emisor (empresa que factura)
$emisor = [
    "nombre" => "BlackCat Rent a Car S.A. de C.V.",
    "nit" => "0614-290822-102-3",
    "direccion" => "Calle El Mirador #123, San Salvador",
    "nrc" => "323456-7",
    "serie_factura" => "B001", // Serie de la factura
    // Número de factura formateado con ceros a la izquierda (ej: 000001)
    "numero_factura" => str_pad($id_pago, 6, "0", STR_PAD_LEFT)
];

// Fecha en que se emitió el pago/factura
$fecha_emision = $pago['fecha_pago'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título dinámico con el número de factura -->
    <title>Factura <?= $emisor['serie_factura'] . '-' . $emisor['numero_factura'] ?></title>
<style>
    /* Estilos básicos para toda la página de la factura */
    * {
        margin: 0; /* Elimina márgenes por defecto */
        padding: 0; /* Elimina padding por defecto */
        box-sizing: border-box; /* Incluye padding y border en el ancho total de los elementos */
    }
    
    /* Estilo general del body */
    body {
        font-family: 'Arial', 'Helvetica', sans-serif; /* Tipografía base */
        background: #f5f5f5; /* Color de fondo gris claro */
        color: #333; /* Color de texto oscuro */
        line-height: 1.4; /* Altura de línea */
        font-size: 14px; /* Tamaño de fuente base */
    }
    
    /* Contenedor principal de la factura */
    .invoice-container {
        max-width: 21cm; /* Tamaño máximo de ancho tipo hoja A4 */
        min-height: 29.7cm; /* Altura mínima tipo hoja A4 */
        margin: 20px auto; /* Centrado horizontal con margen superior/inferior */
        background: #ffffff; /* Fondo blanco */
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15); /* Sombra sutil */
        padding: 0;
    }
    
    /* Cabecera de la factura */
    .header {
        background: linear-gradient(135deg, #2c3e50, #34495e); /* Degradado de color */
        color: white; /* Texto blanco */
        padding: 30px 40px; /* Espaciado interno */
        display: flex; /* Layout flexible */
        justify-content: space-between; /* Distribuye contenido a los extremos */
        align-items: center; /* Alinea verticalmente */
    }
    
    /* Información de la empresa en la cabecera */
    .company-info h1 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
        letter-spacing: 1px; /* Espaciado entre letras */
    }
    
    .company-info p {
        font-size: 13px;
        opacity: 0.9; /* Ligera transparencia */
        margin: 2px 0;
    }
    
    /* Número de factura */
    .invoice-number {
        text-align: right; /* Alinea a la derecha */
    }
    
    .invoice-number h2 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #ecf0f1; /* Color claro */
    }
    
    .invoice-number p {
        font-size: 14px;
        opacity: 0.9;
    }
    
    /* Sección principal de contenido de la factura */
    .content {
        padding: 40px;
    }
    
    /* Sección de información (cliente, empresa, etc.) */
    .info-section {
        display: flex; /* Distribuye bloques horizontalmente */
        justify-content: space-between;
        margin-bottom: 40px;
        gap: 40px; /* Espacio entre bloques */
    }
    
    /* Bloques individuales de información */
    .info-block {
        flex: 1; /* Ocupa igual espacio dentro del contenedor */
    }
    
    .info-block h3 {
        font-size: 16px;
        color: #2c3e50;
        border-bottom: 2px solid #3498db; /* Línea decorativa */
        padding-bottom: 8px;
        margin-bottom: 15px;
        font-weight: bold;
    }
    
    .info-item {
        margin-bottom: 8px;
        display: flex; /* Para alinear label y valor en una fila */
    }
    
    .info-label {
        font-weight: bold;
        min-width: 80px; /* Anchura mínima para alinear valores */
        color: #555;
    }
    
    .info-value {
        flex: 1; /* Ocupa el resto del espacio disponible */
        color: #333;
    }
    
    /* Tabla de servicios incluidos en la factura */
    .services-table {
        width: 100%;
        border-collapse: collapse; /* Quita espacio entre bordes de celdas */
        margin: 30px 0;
        border: 1px solid #ddd;
    }
    
    .services-table thead {
        background: #34495e;
        color: white;
    }
    
    .services-table th {
        padding: 15px 12px;
        text-align: left;
        font-weight: bold;
        font-size: 14px;
    }
    
    .services-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }
    
    .services-table tbody tr:hover {
        background: #f8f9fa; /* Cambio de color al pasar el mouse */
    }
    
    /* Alineaciones de texto */
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    /* Sección de totales */
    .totals-section {
        margin-top: 30px;
        display: flex;
        justify-content: flex-end; /* Alinea a la derecha */
    }
    
    .totals-table {
        width: 350px;
        border: 1px solid #ddd;
    }
    
    .totals-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .totals-table .label {
        font-weight: bold;
        background: #f8f9fa;
        color: #555;
    }
    
    .totals-table .amount {
        text-align: right;
        font-weight: bold;
    }
    
    .total-row {
        background: #34495e !important; /* Resalta la fila total */
        color: white !important;
    }
    
    .total-row td {
        font-size: 16px;
        font-weight: bold;
    }
    
    /* Información de pie de página */
    .footer-info {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
        text-align: center;
        color: #666;
        font-size: 12px;
        line-height: 1.6;
    }
    
    /* Aviso legal */
    .legal-notice {
        background: #f8f9fa;
        padding: 20px;
        margin-top: 30px;
        border-left: 4px solid #3498db;
        font-size: 12px;
        color: #666;
        line-height: 1.5;
    }
    
    /* Estilos específicos para impresión */
    @media print {
        body {
            background: white;
        }
        .invoice-container {
            box-shadow: none; /* Quita sombra al imprimir */
            margin: 0;
            max-width: none;
        }
    }
    
    /* Estilos responsivos para pantallas pequeñas */
    @media (max-width: 768px) {
        .header {
            flex-direction: column; /* Pone cabecera en columna */
            text-align: center;
            gap: 20px;
        }
        
        .info-section {
            flex-direction: column;
            gap: 30px;
        }
        
        .content {
            padding: 20px; /* Reduce padding */
        }
        
        .services-table {
            font-size: 12px; /* Fuente más pequeña */
        }
        
        .services-table th,
        .services-table td {
            padding: 8px 6px; /* Reduce padding en celdas */
        }
        
        .totals-section {
            justify-content: center; /* Centra totales */
        }
        
        .totals-table {
            width: 100%; /* Tabla ocupa todo el ancho disponible */
        }
    }
</style>

</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1><?= $emisor['nombre'] ?></h1>
                <p><?= $emisor['direccion'] ?></p>
                <p>NIT: <?= $emisor['nit'] ?> | NRC: <?= $emisor['nrc'] ?></p>
            </div>
            <div class="invoice-number">
                <h2>FACTURA</h2>
                <p>No. <?= $emisor['serie_factura'] . '-' . $emisor['numero_factura'] ?></p>
                <p>Fecha: <?= date('d/m/Y', strtotime($fecha_emision)) ?></p>
            </div>
        </div>

<!-- Contenido principal de la factura -->
<div class="content">

    <!-- Sección de información del cliente y detalles de la empresa -->
    <div class="info-section">

        <!-- Bloque de datos del cliente -->
        <div class="info-block">
            <h3>Datos del Cliente</h3>
            <!-- Mostrar el nombre completo del cliente -->
            <div class="info-item">
                <span class="info-label">Nombre:</span>
                <span class="info-value"><?= $pago['nombre_completo'] ?></span>
            </div>
            <!-- Mostrar el DUI del cliente -->
            <div class="info-item">
                <span class="info-label">DUI:</span>
                <span class="info-value"><?= $pago['dui'] ?></span>
            </div>
            <!-- Mostrar la dirección del cliente -->
            <div class="info-item">
                <span class="info-label">Dirección:</span>
                <span class="info-value"><?= $pago['direccion'] ?></span>
            </div>
        </div>
        
        <!-- Bloque de detalles de la reserva -->
        <div class="info-block">
            <h3>Detalles de Reserva</h3>
            <!-- Fecha de inicio de la reserva -->
            <div class="info-item">
                <span class="info-label">Inicio:</span>
                <span class="info-value"><?= date('d/m/Y', strtotime($pago['fecha_inicio'])) ?></span>
            </div>
            <!-- Fecha de fin de la reserva -->
            <div class="info-item">
                <span class="info-label">Fin:</span>
                <span class="info-value"><?= date('d/m/Y', strtotime($pago['fecha_fin'])) ?></span>
            </div>
            <!-- Duración de la reserva en días -->
            <div class="info-item">
                <span class="info-label">Duración:</span>
                <span class="info-value"><?= $dias ?> día(s)</span>
            </div>
        </div>
    </div>

    <!-- Tabla de servicios incluidos en la factura -->
    <table class="services-table">
        <thead>
            <tr>
                <th>Descripción del Servicio</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Precio Unitario</th>
                <th class="text-center">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- Descripción del servicio: Renta de vehículo con marca y modelo -->
                <td>
                    <strong>Renta de Vehículo</strong><br>
                    <small><?= $pago['marca'] . ' ' . $pago['modelo'] ?></small>
                </td>
                <!-- Cantidad: número de días de la renta -->
                <td class="text-center"><?= $dias ?> día(s)</td>
                <!-- Precio por día del auto -->
                <td class="text-center">$<?= number_format($pago['precio_dia'], 2) ?></td>
                <!-- Subtotal calculado -->
                <td class="text-center">$<?= number_format($subtotal, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Sección de totales de la factura -->
    <div class="totals-section">
        <table class="totals-table">
            <!-- Subtotal de la renta -->
            <tr>
                <td class="label">Subtotal:</td>
                <td class="amount">$<?= number_format($subtotal, 2) ?></td>
            </tr>
            <!-- IVA (impuesto) aplicado -->
            <tr>
                <td class="label">IVA (13%):</td>
                <td class="amount">$<?= number_format($iva, 2) ?></td>
            </tr>
            <!-- Total a pagar por el cliente -->
            <tr class="total-row">
                <td class="label">TOTAL A PAGAR:</td>
                <td class="amount">$<?= number_format($total, 2) ?></td>
            </tr>
        </table>
    </div>

    <!-- Aviso legal obligatorio de la factura electrónica -->
    <div class="legal-notice">
        <strong>Aviso Legal:</strong> Esta factura ha sido generada electrónicamente conforme a las disposiciones legales vigentes. 
        Debe conservarse por un período mínimo de 10 años según lo establecido por la ley. 
        Para cualquier consulta o aclaración, contacte a nuestro departamento de facturación.
    </div>

    <!-- Pie de página de la factura -->
    <div class="footer-info">
        <p><strong>¡Gracias por confiar en BlackCat Rent a Car!</strong></p>
        <p>Esperamos volver a servirle pronto</p>
    </div>
</div>
    </div>
</body>
</html>