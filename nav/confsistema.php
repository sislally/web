<?php

// Datos de conexión
$host = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "autosdb";

// Ruta del mysqldump (XAMPP Windows)
$mysqldump_path = "C:/xampp/mysql/bin/mysqldump.exe";

// Carpeta donde se guardarán los respaldos
$carpeta_respaldo = __DIR__ . '/respaldo';

// Crear carpeta si no existe
if (!is_dir($carpeta_respaldo)) {
    mkdir($carpeta_respaldo, 0777, true);
}

// Crear nombres de archivo con fecha dentro de la carpeta respaldo
$fecha = date("Ymd-His");
$archivo_sql = $carpeta_respaldo . '/' . $base_datos . '_' . $fecha . '.sql';
$archivo_zip = $carpeta_respaldo . '/' . $base_datos . '_' . $fecha . '.zip';

// Comando mysqldump con o sin contraseña
$dump_command = empty($contraseña)
    ? "\"$mysqldump_path\" -h$host -u$usuario $base_datos > \"$archivo_sql\""
    : "\"$mysqldump_path\" -h$host -u$usuario -p$contraseña $base_datos > \"$archivo_sql\"";

// Ejecutar comando y capturar salida
exec($dump_command . " 2>&1", $output, $status);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Respaldo de Base de Datos - AutosDB</title>
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
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #c084fc 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .header .icon {
            color: #06b6d4;
            font-size: 32px;
        }

        .header p {
            color: #9ca3af;
            font-size: 14px;
            font-weight: 500;
        }

        /* Mensajes de estado */
        .mensaje {
            padding: 20px 24px;
            border-radius: 12px;
            margin: 20px 0;
            font-weight: 500;
            font-size: 16px;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            text-align: left;
        }

        .mensaje.exito {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .mensaje.error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .mensaje .icon {
            font-size: 20px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .mensaje strong {
            color: #ffffff;
            font-weight: 700;
        }

        /* Botón de descarga */
        a[download] {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        a[download]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        a[download]::before {
            content: '\f019';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 14px;
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid rgba(124, 58, 237, 0.3);
            border-radius: 50%;
            border-top-color: #7c3aed;
            animation: spin 1s ease-in-out infinite;
            margin: 20px 0;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Progress bar */
        .progress-container {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            border-radius: 3px;
            width: 100%;
            animation: progress 2s ease-in-out;
        }

        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }

        /* Info adicional */
        .info-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }

        .info-box h3 {
            color: #c084fc;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
        }

        .info-box li {
            padding: 6px 0;
            color: #9ca3af;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #10b981;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
                flex-direction: column;
                gap: 8px;
            }

            .header .icon {
                font-size: 28px;
            }

            .mensaje {
                padding: 16px 20px;
                font-size: 14px;
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            a[download] {
                padding: 14px 24px;
                font-size: 14px;
            }
        }

        /* Animación de entrada */
        .container {
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
<body>
    <div class="container">
        <!-- Cabecera de la página con título y descripción del sistema de respaldo -->
        <div class="header">
            <h1><i class="fas fa-database icon"></i> Respaldo AutosDB</h1>
            <p>Sistema de respaldo automático de base de datos</p>
        </div>

        <!-- Barra de progreso para mostrar el avance del respaldo -->
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>

        <?php
        // Verifica si el respaldo se ejecutó correctamente (status = 0) y si el archivo SQL existe
        if ($status === 0 && file_exists($archivo_sql)) {
            // Crear un archivo ZIP que contenga el respaldo
            $zip = new ZipArchive();
            if ($zip->open($archivo_zip, ZipArchive::CREATE) === TRUE) {
                // Agrega el archivo SQL al ZIP
                $zip->addFile($archivo_sql, basename($archivo_sql));
                // Cierra el archivo ZIP
                $zip->close();
                // Elimina el archivo SQL original (ya está dentro del ZIP)
                unlink($archivo_sql);

                // Muestra mensaje de éxito en la interfaz
                echo "<div class='mensaje exito'>";
                echo "<i class='fas fa-check-circle icon'></i>";
                echo "<div>";
                echo "Respaldo creado y comprimido exitosamente:<br>";
                echo "<strong>" . basename($archivo_zip) . "</strong><br>";
                echo "<small>Fecha: " . date('d/m/Y H:i:s') . "</small>";
                echo "</div>";
                echo "</div>";

                // Enlace para descargar el respaldo ZIP generado
                echo "<a href='respaldo/" . basename($archivo_zip) . "' download>Descargar Respaldo</a>";
            } else {
                // Mensaje de error si no se pudo crear el archivo ZIP
                echo "<div class='mensaje error'>";
                echo "<i class='fas fa-exclamation-triangle icon'></i>";
                echo "<div>Error al crear el archivo ZIP.</div>";
                echo "</div>";
            }
        } else {
            // Mensaje de error si falló la ejecución del respaldo
            echo "<div class='mensaje error'>";
            echo "<i class='fas fa-times-circle icon'></i>";
            echo "<div>";
            echo "Error al ejecutar el respaldo.<br><strong>Detalles del error:</strong><br>";
            // Muestra cada línea de salida de error del comando de respaldo
            foreach ($output as $line) {
                echo htmlspecialchars($line) . "<br>";
            }
            echo "</div>";
            echo "</div>";
        }
        ?>

        <!-- Caja informativa con detalles del respaldo generado -->
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Información del Respaldo</h3>
            <ul>
                <li>Base de datos: <strong><?= $base_datos ?></strong></li>
                <li>Servidor: <?= $host ?></li>
                <li>Formato: SQL comprimido (ZIP)</li>
                <li>Ubicación: /respaldo/</li>
                <li>Incluye: Estructura y datos completos</li>
            </ul>
        </div>
    </div>
</body>
</html>