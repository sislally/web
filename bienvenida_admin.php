<?php
session_start();
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

// Total de autos disponibles
$autos = $conexion->query("SELECT COUNT(*) AS total FROM Autos WHERE estado = 'disponible'");
$total_autos = $autos->fetch_assoc()['total'];

// Total de reservas activas (pendiente o confirmada)
$reservas = $conexion->query("
    SELECT COUNT(*) AS total FROM Reservas 
    WHERE estado_reserva IN ('pendiente', 'confirmada')
");
$total_reservas = $reservas->fetch_assoc()['total'];

// Total de clientes
$clientes = $conexion->query("
    SELECT COUNT(*) AS total FROM Usuarios 
    WHERE rol = 'cliente'
");
$total_clientes = $clientes->fetch_assoc()['total'];

// Ingresos del mes actual
$ingresos = $conexion->query("
    SELECT SUM(monto_total) AS total 
    FROM Pagos 
    WHERE estado_pago = 'pagado'
        AND MONTH(fecha_pago) = MONTH(CURDATE()) 
        AND YEAR(fecha_pago) = YEAR(CURDATE())
");
$total_ingresos = $ingresos->fetch_assoc()['total'] ?? 0;

// Comparación con mes anterior
$ingresos_mes_anterior = $conexion->query("
    SELECT SUM(monto_total) AS total 
    FROM Pagos 
    WHERE estado_pago = 'pagado'
        AND MONTH(fecha_pago) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
        AND YEAR(fecha_pago) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
");
$total_ingresos_anterior = $ingresos_mes_anterior->fetch_assoc()['total'] ?? 0;

// Calcular porcentaje de cambio
$porcentaje_cambio = 0;
if ($total_ingresos_anterior > 0) {
    $porcentaje_cambio = (($total_ingresos - $total_ingresos_anterior) / $total_ingresos_anterior) * 100;
}

// Datos para gráficas
// 1. Autos disponibles y rentados
$sql_autos = "SELECT estado, COUNT(*) AS total FROM Autos GROUP BY estado";
$result_autos = mysqli_query($conexion, $sql_autos);
$autos = ["disponible" => 0, "en alquiler" => 0];
while ($row = mysqli_fetch_assoc($result_autos)) {
    $autos[$row['estado']] = $row['total'];
}

// 2. Clientes registrados (Usuarios con rol 'cliente')
$sql_clientes = "SELECT COUNT(*) AS total_clientes FROM Usuarios WHERE rol = 'cliente'";
$total_clientes = mysqli_fetch_assoc(mysqli_query($conexion, $sql_clientes))['total_clientes'];

// 3. Total de ingresos (Pagos con estado pagado)
$sql_ingresos = "SELECT SUM(monto_total) AS total_ingresos FROM Pagos WHERE estado_pago = 'pagado'";
$total_ingresos = mysqli_fetch_assoc(mysqli_query($conexion, $sql_ingresos))['total_ingresos'] ?? 0;

// 4. Ingresos por mes (últimos 6 meses)
$sql_ingresos_mes = "
    SELECT DATE_FORMAT(fecha_pago, '%Y-%m') AS mes, SUM(monto_total) AS total_mes
    FROM Pagos
    WHERE estado_pago = 'pagado'
    GROUP BY mes
    ORDER BY mes DESC
    LIMIT 6
";
$result_ingresos_mes = mysqli_query($conexion, $sql_ingresos_mes);

$meses = [];
$totales_mes = [];
while ($row = mysqli_fetch_assoc($result_ingresos_mes)) {
    $meses[] = $row['mes'];
    $totales_mes[] = $row['total_mes'];
}
$meses = array_reverse($meses);
$totales_mes = array_reverse($totales_mes);
// FIN DE CODIGO GRAFICAS

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - BlackCat Rent a Car</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
:root {
    --primary-color: #6366f1;
    --primary-dark: #4338ca;
    --secondary-color: #0f172a;
    --background-color: #0f0f23;
    --card-bg: #1a1a3a;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --accent-color: #00d4ff;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --border-color: #334155;
    --sidebar-width: 280px;
    --sidebar-collapsed: 80px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--background-color);
    color: var(--text-primary);
    line-height: 1.6;
    overflow-x: hidden;
}

/* Sidebar */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(135deg, var(--card-bg) 0%, #252545 100%);
    backdrop-filter: blur(20px);
    border-right: 1px solid var(--border-color);
    transition: var(--transition);
    z-index: 1000;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) transparent;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 3px;
}

.sidebar.collapsed {
    width: var(--sidebar-collapsed);
}

.sidebar-header {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sidebar-logo {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sidebar-logo i {
    color: white;
    font-size: 1.2rem;
}

.sidebar-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    white-space: nowrap;
    opacity: 1;
    transition: var(--transition);
}

.sidebar.collapsed .sidebar-title {
    opacity: 0;
    width: 0;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-section-title {
    padding: 0 1.5rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transition: var(--transition);
}

.sidebar.collapsed .nav-section-title {
    opacity: 0;
}

.nav-item {
    margin: 0.25rem 1rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.875rem 1rem;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 12px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    transition: var(--transition);
    z-index: -1;
}

.nav-link:hover::before,
.nav-link.active::before {
    width: 100%;
}

.nav-link:hover,
.nav-link.active {
    color: white;
    transform: translateX(4px);
}

.nav-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.nav-text {
    font-weight: 500;
    white-space: nowrap;
    transition: var(--transition);
}

.sidebar.collapsed .nav-text {
    opacity: 0;
    width: 0;
}

.toggle-btn {
    position: fixed;
    top: 1rem;
    left: calc(var(--sidebar-width) - 25px);
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    transition: var(--transition);
    z-index: 1001;
}

.sidebar.collapsed + .toggle-btn {
    left: calc(var(--sidebar-collapsed) - 25px);
}

/* Main Content */
.main-content {
    margin-left: var(--sidebar-width);
    padding: 2rem;
    transition: var(--transition);
    min-height: 100vh;
}

.sidebar.collapsed ~ .main-content {
    margin-left: var(--sidebar-collapsed);
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-title {
    flex: 1;
}

.welcome-text {
    font-size: 1.125rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.date-display {
    background: var(--card-bg);
    padding: 0.75rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
}

.stat-icon.success {
    background: linear-gradient(135deg, var(--success-color), #059669);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, var(--warning-color), #d97706);
    color: white;
}

.stat-icon.info {
    background: linear-gradient(135deg, var(--accent-color), #0284c7);
    color: white;
}

.stat-content {
    text-align: left;
}

.stat-title {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-change.positive {
    color: var(--success-color);
}

.stat-change.negative {
    color: var(--danger-color);
}

/* Charts Section */
.charts-section {
    margin-top: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: var(--primary-color);
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.chart-container {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.5rem;
    position: relative;
    transition: var(--transition);
}

.chart-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 16px 16px 0 0;
}

.chart-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.chart-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.chart-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.chart-canvas {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-full-width {
    grid-column: 1 / -1;
}

/* Loading Animation */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid var(--border-color);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.collapsed {
        transform: translateX(0);
        width: var(--sidebar-collapsed);
    }
    
    .main-content {
        margin-left: 0;
        padding: 1rem;
    }
    
    .sidebar.collapsed ~ .main-content {
        margin-left: var(--sidebar-collapsed);
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        padding: 1rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .chart-canvas {
        height: 250px;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--background-color);
}

::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-color);
}
    </style>
</head>
<body>
    <!-- Sidebar lateral del panel de administración -->
    <aside class="sidebar" id="sidebar">
        <!-- Encabezado del sidebar con logo y título -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-cat"></i>
            </div>
            <h2 class="sidebar-title">BlackCat Admin</h2>
        </div>
        
        <!-- Menú de navegación del sidebar -->
        <nav class="sidebar-nav">
            <!-- Sección Principal -->
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <div class="nav-item">
                    <a href="#" class="nav-link active">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
            </div>
            
            <!-- Sección Gestión -->
            <div class="nav-section">
                <div class="nav-section-title">Gestión</div>
                <!-- Link a Autos -->
                <div class="nav-item">
                    <a href="nav/autos.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-car"></i></span>
                        <span class="nav-text">Autos</span> 
                    </a>
                </div>
                <!-- Link a Reservas -->
                <div class="nav-item">
                    <a href="nav/reservas.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                        <span class="nav-text">Reservas</span>
                    </a>
                </div>
                <!-- Link a Clientes -->
                <div class="nav-item">
                    <a href="nav/clientes.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">Clientes</span>
                    </a>
                </div>
                <!-- Link a Empleados -->
                <div class="nav-item">
                    <a href="nav/empleados.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-user-tie"></i></span>
                        <span class="nav-text">Empleados</span>
                    </a>
                </div>
            </div>
            
            <!-- Sección Operaciones -->
            <div class="nav-section">
                <div class="nav-section-title">Operaciones</div>
                <div class="nav-item">
                    <a href="nav/mantenimientos.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-wrench"></i></span>
                        <span class="nav-text">Mantenimientos</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="nav/pagos.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-credit-card"></i></span>
                        <span class="nav-text">Pagos y Facturación</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="nav/multas.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="nav-text">Multas</span>
                    </a>
                </div>
            </div>
            
            <!-- Sección Sistema -->
            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                <div class="nav-item">
                    <a href="nav/confsistema.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-cog"></i></span>
                        <span class="nav-text">Configuración</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="nav/seguridad.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-shield-alt"></i></span>
                        <span class="nav-text">Seguridad</span>
                    </a>
                </div>
                <!-- Cerrar sesión -->
                <div class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                        <span class="nav-text">Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Botón para colapsar/expandir el sidebar -->
    <button class="toggle-btn" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Contenido principal del dashboard -->
    <main class="main-content">
        <!-- Encabezado con título de la página y fecha actual -->
        <div class="header">
            <div class="header-title">
                <div class="welcome-text">Bienvenido de vuelta, <?php echo htmlspecialchars($_SESSION['usuario']); ?></div>
                <h1 class="page-title">Dashboard</h1>
            </div>
            <div class="header-actions">
                <!-- Muestra la fecha actual -->
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>
        </div>

        <!-- Tarjetas de estadísticas rápidas -->
        <div class="stats-grid">
            <!-- Autos disponibles -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Autos Disponibles</div>
                        <div class="stat-value"><?php echo number_format($total_autos); ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            Listos para rentar
                        </div>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
            </div>

            <!-- Reservas activas -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Reservas Activas</div>
                        <div class="stat-value"><?php echo number_format($total_reservas); ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-clock"></i>
                            En proceso
                        </div>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>

            <!-- Clientes registrados -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Clientes Registrados</div>
                        <div class="stat-value"><?php echo number_format($total_clientes); ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-user-plus"></i>
                            Base de clientes
                        </div>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Ingresos del mes -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Ingresos del Mes</div>
                        <div class="stat-value">$<?php echo number_format($total_ingresos, 2); ?></div>
                        <?php if ($porcentaje_cambio != 0): ?>
                            <!-- Cambio porcentual respecto al mes anterior -->
                            <div class="stat-change <?php echo $porcentaje_cambio >= 0 ? 'positive' : 'negative'; ?>">
                                <i class="fas fa-arrow-<?php echo $porcentaje_cambio >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo abs(round($porcentaje_cambio, 1)); ?>% vs mes anterior
                            </div>
                        <?php else: ?>
                            <div class="stat-change">
                                <i class="fas fa-minus"></i>
                                Sin cambios
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <h1>Panel Grafico</h1>

<br>

<h2>📈 Gráfica: Ingresos por Mes</h2>

<style>
    /* Contenedor de las gráficas: se usa un grid responsive */
    .charts-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); /* columnas flexibles según el espacio */
        gap: 20px; /* espacio entre las gráficas */
        margin: 30px auto;
        max-width: 900px; /* ancho máximo de la sección */
        padding: 10px;
    }

    /* Caja individual de cada gráfica */
    .chart-box {
        background: #261779ff; /* color de fondo */
        border-radius: 10px; /* esquinas redondeadas */
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* sombra ligera */
        text-align: center;
    }

    /* Ajuste de tamaño del canvas de la gráfica */
    .chart-box canvas {
        max-width: 100%; /* se adapta al ancho de la caja */
        height: 300px !important; /* altura fija */
    }
</style>

<!-- Contenedor de todas las gráficas -->
<div class="charts-wrapper">

    <!-- Gráfica de Ingresos -->
    <div class="chart-box">
        <h3>Ingresos</h3>
        <canvas id="graficaIngresos"></canvas>
    </div>

    <!-- Gráfica de Autos -->
    <div class="chart-box">
        <h3>Autos</h3>
        <canvas id="graficaAutos"></canvas>
    </div>
</div>

</main>

<script>
/* =============================
   Gráfica de ingresos por mes
   ============================= */
new Chart(document.getElementById('graficaIngresos'), {
    type: 'bar', // tipo de gráfica: barras
    data: {
        labels: <?php echo json_encode($meses); ?>, // etiquetas del eje X: nombres de los meses
        datasets: [{
            label: 'Ingresos ($)', // nombre de la serie
            data: <?php echo json_encode($totales_mes); ?>, // datos de ingresos por mes
            backgroundColor: 'rgba(54, 162, 235, 0.7)', // color de las barras
            borderColor: 'rgba(54, 162, 235, 1)', // borde de las barras
            borderWidth: 1
        }]
    }
});

/* ==================================
   Gráfica de autos disponibles/en alquiler
   ================================== */
new Chart(document.getElementById('graficaAutos'), {
    type: 'pie', // tipo de gráfica: pastel
    data: {
        labels: ['Disponibles', 'En alquiler'], // etiquetas de cada porción
        datasets: [{
            label: 'Cantidad', // nombre de la serie
            data: [<?php echo $autos['disponible']; ?>, <?php echo $autos['en alquiler']; ?>], // datos de cantidad de autos
            backgroundColor: ['green', 'red'] // colores para cada porción
        }]
    }
});
</script>
</body>
</html>