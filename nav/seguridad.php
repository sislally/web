<?php
// seguridad.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Información de Seguridad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
            color: #333;
        }
        h1 {
            color: #7f5fff;
        }
        .info {
            background-color: #e0e0ff;
            border-left: 6px solid #7f5fff;
            padding: 15px 20px;
            margin-top: 20px;
            line-height: 1.6;
            border-radius: 5px;
            max-width: 700px;
        }
    </style>
</head>
<body>
    <h1>Seguridad en el Sistema de Gestión de Autos</h1>
    <div class="info">
        <p><strong>Autenticación:</strong> El sistema utiliza roles (cliente, empleado, administrador) para controlar el acceso y las acciones permitidas.</p>
        <p><strong>Contraseñas:</strong> Las contraseñas se deben almacenar de forma segura, usando funciones de hash como <code>password_hash()</code> en PHP.</p>
        <p><strong>Validación:</strong> Es importante validar y sanitizar todos los datos de entrada para evitar inyecciones SQL y ataques XSS.</p>
        <p><strong>Sesiones:</strong> Se utilizan sesiones PHP para mantener la autenticación de usuarios y proteger páginas restringidas.</p>
        <p><strong>Respaldo:</strong> El sistema permite hacer respaldos periódicos de la base de datos para evitar pérdida de información.</p>
        <p><strong>Permisos:</strong> Solo usuarios con el rol adecuado pueden acceder a funciones administrativas.</p>
        <p><em>Recuerda siempre mantener actualizado el sistema y las dependencias para mejorar la seguridad.</em></p>
    </div>
</body>
</html>
