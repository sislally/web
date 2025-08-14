<?php
// Configuración de errores y sesión
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();

// Verificación de autenticación y rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

// Función para formatear precio
function formatearPrecio($precio) {
    return number_format($precio, 2, '.', ',');
}

// Función para sanitizar salida
function sanitizar($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Función para verificar si la imagen existe
function obtenerImagenAuto($imagen) {
    if (!empty($imagen) && file_exists("uploads/" . $imagen)) {
        return "uploads/" . $imagen;
    }
    return "uploads/sin_imagen.png";
}

// Variables para filtros y búsqueda
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filtro_marca = isset($_GET['marca']) ? $_GET['marca'] : '';
$filtro_precio_max = isset($_GET['precio_max']) ? floatval($_GET['precio_max']) : 0;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'marca';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    // Construir consulta base
    $sql = "SELECT * FROM Autos WHERE estado = 'disponible'";
    $params = [];
    $types = "";

    // Aplicar filtros
    if (!empty($filtro_tipo)) {
        $sql .= " AND tipo = ?";
        $params[] = $filtro_tipo;
        $types .= "s";
    }

    if (!empty($filtro_marca)) {
        $sql .= " AND marca = ?";
        $params[] = $filtro_marca;
        $types .= "s";
    }

    if ($filtro_precio_max > 0) {
        $sql .= " AND precio_dia <= ?";
        $params[] = $filtro_precio_max;
        $types .= "d";
    }

    if (!empty($busqueda)) {
        $sql .= " AND (marca LIKE ? OR modelo LIKE ? OR tipo LIKE ?)";
        $busqueda_param = "%" . $busqueda . "%";
        $params[] = $busqueda_param;
        $params[] = $busqueda_param;
        $params[] = $busqueda_param;
        $types .= "sss";
    }

    // Aplicar ordenamiento
    $orden_seguro = ['marca', 'precio_dia', 'año', 'tipo'];
    if (in_array($orden, $orden_seguro)) {
        $sql .= " ORDER BY " . $orden;
        if ($orden === 'precio_dia') {
            $sql .= " ASC";
        } else {
            $sql .= " ASC";
        }
    } else {
        $sql .= " ORDER BY marca ASC";
    }

    // Ejecutar consulta preparada
    $stmt = $conexion->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Obtener datos para filtros
    $stmt_tipos = $conexion->prepare("SELECT DISTINCT tipo FROM Autos WHERE estado = 'disponible' ORDER BY tipo");
    $stmt_tipos->execute();
    $tipos_disponibles = $stmt_tipos->get_result();

    $stmt_marcas = $conexion->prepare("SELECT DISTINCT marca FROM Autos WHERE estado = 'disponible' ORDER BY marca");
    $stmt_marcas->execute();
    $marcas_disponibles = $stmt_marcas->get_result();

} catch (Exception $e) {
    error_log("Error en consulta de autos: " . $e->getMessage());
    $resultado = false;
    $error_mensaje = "Error al cargar los autos disponibles. Por favor, inténtelo más tarde.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autos Disponibles - Alquiler</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --dark-bg: #0a0a0a;
        --card-bg: #1a1a2e;
        --surface: #16213e;
        --text-primary: #ffffff;
        --text-secondary: #b8bcc8;
        --text-accent: #667eea;
        --border: rgba(255, 255, 255, 0.1);
        --shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        --radius: 20px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--dark-bg);
        background-image: 
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.1) 0%, transparent 50%);
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        line-height: 1.6;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }

    .header::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: var(--primary-gradient);
        border-radius: 2px;
    }

    h1 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
        font-weight: 400;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Filtros Mejorados */
    .filtros-container {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .filtros-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--accent-gradient);
    }

    .filtros-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .form-group label {
        color: var(--text-accent);
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input,
    .form-group select {
        padding: 1rem 1.25rem;
        border: 2px solid transparent;
        border-radius: 12px;
        background: var(--surface);
        color: var(--text-primary);
        font-size: 1rem;
        font-weight: 500;
        transition: var(--transition);
        outline: none;
        position: relative;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--text-accent);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .form-group input::placeholder {
        color: var(--text-secondary);
        font-weight: 400;
    }

    /* Botones Modernos */
    .btn-filtrar, .btn-limpiar {
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .btn-filtrar {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-filtrar:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    .btn-filtrar:active {
        transform: translateY(-1px);
    }

    .btn-limpiar {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        border: 2px solid var(--border);
        backdrop-filter: blur(10px);
    }

    .btn-limpiar:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--text-accent);
        transform: translateY(-2px);
    }

    .info-resultados {
        text-align: center;
        margin-bottom: 2rem;
        padding: 1rem;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 50px;
        border: 1px solid rgba(102, 126, 234, 0.2);
        font-weight: 500;
    }

    .info-resultados strong {
        color: var(--text-accent);
        font-weight: 700;
    }

    /* Cards de Autos Ultra Modernas */
    .contenedor-autos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .auto-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
        box-shadow: var(--shadow);
        transform-style: preserve-3d;
    }

    .auto-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--accent-gradient);
        transform: scaleX(0);
        transition: transform 0.3s ease;
        z-index: 1;
    }

    .auto-card:hover {
        transform: translateY(-10px) rotateX(5deg);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
        border-color: rgba(102, 126, 234, 0.3);
    }

    .auto-card:hover::before {
        transform: scaleX(1);
    }

    .auto-imagen {
        position: relative;
        height: 250px;
        overflow: hidden;
        background: linear-gradient(45deg, var(--surface), var(--card-bg));
    }

    .auto-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
        filter: brightness(0.9) contrast(1.1);
    }

    .auto-card:hover img {
        transform: scale(1.1) rotate(2deg);
        filter: brightness(1) contrast(1.2);
    }

    .precio-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--secondary-gradient);
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 8px 25px rgba(245, 87, 108, 0.3);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .auto-info {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .auto-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--text-primary), var(--text-accent));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
        line-height: 1.3;
    }

    .auto-detalles {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .detalle-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .detalle-item strong {
        color: var(--text-accent);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detalle-item span {
        color: var(--text-primary);
        font-weight: 500;
        font-size: 0.95rem;
    }

    .btn-elegir {
        padding: 1rem 2rem;
        background: var(--primary-gradient);
        color: white;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        font-size: 1rem;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-elegir::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-elegir:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    .btn-elegir:hover::before {
        left: 100%;
    }

    /* Estados de Error y Sin Resultados */
    .no-resultados, .error-mensaje {
        text-align: center;
        padding: 4rem 2rem;
        border-radius: var(--radius);
        margin-bottom: 2rem;
    }

    .no-resultados {
        background: rgba(102, 126, 234, 0.05);
        border: 1px solid rgba(102, 126, 234, 0.1);
    }

    .error-mensaje {
        background: rgba(245, 87, 108, 0.1);
        border: 1px solid rgba(245, 87, 108, 0.2);
        color: #ff9999;
    }

    .no-resultados h3 {
        font-size: 2rem;
        margin-bottom: 1rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .navigation {
        text-align: center;
        margin-top: 3rem;
    }

    .btn-volver {
        display: inline-block;
        padding: 1rem 2rem;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        border: 2px solid var(--border);
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        backdrop-filter: blur(10px);
    }

    .btn-volver:hover {
        background: rgba(102, 126, 234, 0.1);
        border-color: var(--text-accent);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    }

    /* Responsive Design Mejorado */
    @media (max-width: 1024px) {
        .contenedor-autos {
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
        
        .filtros-form {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .contenedor-autos {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .auto-info {
            padding: 1.5rem;
        }
        
        .auto-detalles {
            grid-template-columns: 1fr;
        }
        
        h1 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 480px) {
        .filtros-container {
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .auto-info {
            padding: 1rem;
        }
        
        .precio-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
    }

    /* Animaciones Avanzadas */
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

    .auto-card {
        animation: fadeInUp 0.6s ease forwards;
    }

    .auto-card:nth-child(odd) {
        animation-delay: 0.1s;
    }

    .auto-card:nth-child(even) {
        animation-delay: 0.2s;
    }

    /* Efectos de Glassmorphism */
    .filtros-container,
    .auto-card {
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    /* Modo Oscuro Refinado */
    @media (prefers-color-scheme: dark) {
        :root {
            --dark-bg: #000000;
            --card-bg: #111111;
            --surface: #1a1a1a;
        }
    }

    /* Scrollbar Personalizado */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: var(--dark-bg);
    }

    ::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--secondary-gradient);
    }

    /* Soporte para dispositivos táctiles */
    @media (hover: none) {
        .auto-card:hover {
            transform: none;
        }
        
        .btn-elegir:hover {
            transform: none;
        }
    }
</style>

</head>
<body>
    <div class="container">

        <!-- ===========================
             Encabezado principal
             Muestra el título y subtítulo de la página
        =========================== -->
        <div class="header">
            <h1>Autos Disponibles</h1>
            <p class="subtitle">Encuentra el vehículo perfecto para tu próxima aventura</p>
        </div>

        <!-- ===========================
             Formulario de filtros y búsqueda
             Permite al usuario buscar y filtrar autos
        =========================== -->
        <div class="filtros-container">
            <form method="GET" class="filtros-form">

                <!-- Campo de búsqueda por texto libre -->
                <div class="form-group">
                    <label for="busqueda">Buscar:</label>
                    <input type="text" id="busqueda" name="busqueda" 
                           placeholder="Marca, modelo o tipo..." 
                           value="<?= sanitizar($busqueda) ?>">
                </div>

                <!-- Filtro por tipo de vehículo -->
                <div class="form-group">
                    <label for="tipo">Tipo de vehículo:</label>
                    <select id="tipo" name="tipo">
                        <option value="">Todos los tipos</option>
                        <?php if (isset($tipos_disponibles)): ?>
                            <?php while ($tipo = $tipos_disponibles->fetch_assoc()): ?>
                                <option value="<?= sanitizar($tipo['tipo']) ?>" 
                                        <?= $filtro_tipo === $tipo['tipo'] ? 'selected' : '' ?>>
                                    <?= sanitizar($tipo['tipo']) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtro por marca de vehículo -->
                <div class="form-group">
                    <label for="marca">Marca:</label>
                    <select id="marca" name="marca">
                        <option value="">Todas las marcas</option>
                        <?php if (isset($marcas_disponibles)): ?>
                            <?php while ($marca = $marcas_disponibles->fetch_assoc()): ?>
                                <option value="<?= sanitizar($marca['marca']) ?>" 
                                        <?= $filtro_marca === $marca['marca'] ? 'selected' : '' ?>>
                                    <?= sanitizar($marca['marca']) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

<!-- ===========================
     Campo de filtro: Precio máximo por día
     Permite al usuario ingresar un valor numérico como límite superior de precio
=========================== -->
<div class="form-group">
    <label for="precio_max">Precio máximo/día:</label>
    <input type="number" id="precio_max" name="precio_max" 
           min="0" step="0.01" placeholder="Sin límite"
           value="<?= $filtro_precio_max > 0 ? $filtro_precio_max : '' ?>">
</div>

<!-- ===========================
     Campo de filtro: Ordenar resultados
     El usuario puede elegir el criterio de orden (marca, precio, año, tipo)
=========================== -->
<div class="form-group">
    <label for="orden">Ordenar por:</label>
    <select id="orden" name="orden">
        <option value="marca" <?= $orden === 'marca' ? 'selected' : '' ?>>Marca</option>
        <option value="precio_dia" <?= $orden === 'precio_dia' ? 'selected' : '' ?>>Precio</option>
        <option value="año" <?= $orden === 'año' ? 'selected' : '' ?>>Año</option>
        <option value="tipo" <?= $orden === 'tipo' ? 'selected' : '' ?>>Tipo</option>
    </select>
</div>

<!-- Botón para aplicar filtros -->
<div class="form-group">
    <button type="submit" class="btn-filtrar">Filtrar</button>
</div>

<!-- Botón para limpiar filtros y volver a mostrar todos los vehículos -->
<div class="form-group">
    <a href="?" class="btn-limpiar">Limpiar</a>
</div>
</form>
</div>

<!-- ===========================
     Mostrar mensaje de error si existe
=========================== -->
<?php if (isset($error_mensaje)): ?>
    <div class="error-mensaje">
        <?= sanitizar($error_mensaje) ?>
    </div>
<?php endif; ?>

<!-- ===========================
     Mostrar listado de vehículos si hay resultados
=========================== -->
<?php if ($resultado && $resultado->num_rows > 0): ?>
    <div class="info-resultados">
        <strong><?= $resultado->num_rows ?></strong> vehículos encontrados
    </div>

    <div class="contenedor-autos">
        <?php while ($auto = $resultado->fetch_assoc()): ?>
            <div class="auto-card">
                
                <!-- Imagen del vehículo con precio/día -->
                <div class="auto-imagen">
                    <img src="<?= sanitizar(obtenerImagenAuto($auto['imagen'])) ?>" 
                         alt="<?= sanitizar($auto['marca'] . ' ' . $auto['modelo']) ?>"
                         loading="lazy">
                    <div class="precio-badge">
                        $<?= formatearPrecio($auto['precio_dia']) ?>/día
                    </div>
                </div>
                
                <!-- Información y detalles del vehículo -->
                <div class="auto-info">
                    <h3><?= sanitizar($auto['marca'] . ' ' . $auto['modelo']) ?></h3>
                    
                    <div class="auto-detalles">
                        <div class="detalle-item">
                            <strong>Tipo:</strong>
                            <?= sanitizar($auto['tipo']) ?>
                        </div>
                        <div class="detalle-item">
                            <strong>Año:</strong>
                            <?= sanitizar($auto['año']) ?>
                        </div>
                        <?php if (!empty($auto['combustible'])): ?>
                            <div class="detalle-item">
                                <strong>Combustible:</strong>
                                <?= sanitizar($auto['combustible']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($auto['transmision'])): ?>
                            <div class="detalle-item">
                                <strong>Transmisión:</strong>
                                <?= sanitizar($auto['transmision']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botón para iniciar solicitud de alquiler -->
                    <a href="pedir_cita.php?id_auto=<?= urlencode($auto['id_auto']) ?>" 
                       class="btn-elegir">
                        Solicitar Alquiler
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <!-- ===========================
         Mensaje cuando no hay resultados
    =========================== -->
    <div class="no-resultados">
        <h3>No hay vehículos disponibles</h3>
        <p>No encontramos vehículos que coincidan con tus criterios de búsqueda.</p>
        <p>Intenta ajustar los filtros o 
           <a href="?" style="color: #a58cff;">ver todos los vehículos</a>.
        </p>
    </div>
<?php endif; ?>

<!-- Botón para regresar al panel principal -->
<div class="navigation">
    <a href="index.php" class="btn-volver">Volver al Panel</a>
</div>
</div>

<script>
    // ==================================================
    // Script para mejorar la experiencia de usuario
    // - Filtros automáticos
    // - Lazy loading de imágenes
    // - Animación de cards al entrar en pantalla
    // ==================================================
    document.addEventListener('DOMContentLoaded', function() {

        // =========================================
        // 1. Auto-submit al cambiar filtros (opcional)
        // Detecta cambios en los selectores de tipo, marca y orden
        // =========================================
        const selectores = document.querySelectorAll('select[name="tipo"], select[name="marca"], select[name="orden"]');
        selectores.forEach(selector => {
            selector.addEventListener('change', function() {
                // Si se descomenta, enviará el formulario automáticamente
                // this.form.submit();
            });
        });

        // =========================================
        // 2. Lazy loading mejorado para imágenes
        // Usa IntersectionObserver para cargar animación cuando
        // las imágenes entren en el viewport
        // =========================================
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { // Cuando la imagen entra en pantalla
                        const img = entry.target;
                        img.classList.add('loaded'); // Agregar clase para efectos CSS
                        observer.unobserve(img); // Dejar de observar esta imagen
                    }
                });
            });

            // Observar todas las imágenes dentro de tarjetas de autos
            document.querySelectorAll('.auto-card img').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // =========================================
        // 3. Animación suave para las cards
        // Las tarjetas se animan solo cuando entran en pantalla
        // =========================================
        const cards = document.querySelectorAll('.auto-card');
        const observerOptions = {
            threshold: 0.1, // Activa la animación cuando el 10% de la card es visible
            rootMargin: '0px 0px -50px 0px' // Margen inferior para anticipar la animación
        };

        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) { // Cuando la tarjeta entra en pantalla
                    entry.target.style.animationPlayState = 'running'; // Reanudar animación CSS
                }
            });
        }, observerOptions);

        // Observar todas las tarjetas de autos
        cards.forEach(card => {
            cardObserver.observe(card);
        });
    });
</script>
</body>
</html>