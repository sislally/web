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
// 1. Ingresos mensuales
$queryIngresos = "
    SELECT MONTH(fecha_pago) AS mes, SUM(monto_total) AS total
    FROM Pagos
    WHERE estado_pago = 'pagado' AND YEAR(fecha_pago) = YEAR(CURDATE())
    GROUP BY MONTH(fecha_pago)
";
$resultIngresos = $conexion->query($queryIngresos);
$ingresosPorMes = array_fill(1, 12, 0);
while ($row = $resultIngresos->fetch_assoc()) {
    $ingresosPorMes[(int)$row['mes']] = (float)$row['total'];
}

// 2. Reservas por mes
$queryReservas = "
    SELECT MONTH(fecha_inicio) AS mes, COUNT(*) AS total
    FROM Reservas
    WHERE YEAR(fecha_inicio) = YEAR(CURDATE())
    GROUP BY MONTH(fecha_inicio)
";
$resultReservas = $conexion->query($queryReservas);
$reservasPorMes = array_fill(1, 12, 0);
while ($row = $resultReservas->fetch_assoc()) {
    $reservasPorMes[(int)$row['mes']] = (int)$row['total'];
}

// 3. Estado de autos
$queryEstadosAutos = "
    SELECT estado, COUNT(*) AS total
    FROM Autos
    GROUP BY estado
";
$resultEstados = $conexion->query($queryEstadosAutos);
$estadosAutos = [];
while ($row = $resultEstados->fetch_assoc()) {
    $estadosAutos[$row['estado']] = (int)$row['total'];
}

// 4. Multas pagadas vs pendientes
$queryMultas = "
    SELECT estado_pago, COUNT(*) AS total
    FROM Multas
    GROUP BY estado_pago
";
$resultMultas = $conexion->query($queryMultas);
$multasEstado = ['pagado' => 0, 'pendiente' => 0];
while ($row = $resultMultas->fetch_assoc()) {
    $multasEstado[$row['estado_pago']] = (int)$row['total'];
}

// Últimas actividades
$queryActividades = "
    (SELECT 'reserva' as tipo, CONCAT('Nueva reserva de ', u.nombre_completo) as descripcion, r.fecha_inicio as fecha
     FROM Reservas r 
     JOIN Usuarios u ON r.id_usuario = u.id_usuario 
     ORDER BY r.fecha_inicio DESC LIMIT 3)
    UNION ALL
    (SELECT 'pago' as tipo, CONCAT('Pago recibido: $', FORMAT(monto_total, 2)) as descripcion, fecha_pago as fecha
     FROM Pagos 
     WHERE estado_pago = 'pagado'
     ORDER BY fecha_pago DESC LIMIT 3)
    ORDER BY fecha DESC LIMIT 5
";
$resultActividades = $conexion->query($queryActividades);
$actividades = [];
while ($row = $resultActividades->fetch_assoc()) {
    $actividades[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - BlackCat Rent a Car</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
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

        /* Charts Grid */
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
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .chart-canvas {
            position: relative;
            height: 300px;
        }

        /* Activity Feed */
        .activity-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .activity-icon.reservation {
            background: rgba(99, 102, 241, 0.2);
            color: var(--primary-color);
        }

        .activity-icon.payment {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }

        .activity-content {
            flex: 1;
        }

        .activity-description {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .sidebar.collapsed ~ .main-content {
                margin-left: 0;
            }

            .toggle-btn {
                left: 1rem;
            }

            .sidebar.collapsed + .toggle-btn {
                left: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .charts-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 0.5rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .chart-container {
                padding: 1rem;
            }
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
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-cat"></i>
            </div>
            <h2 class="sidebar-title">BlackCat Admin</h2>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <div class="nav-item">
                    <a href="#" class="nav-link active">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Gestión</div>
                <div class="nav-item">
                    <a href="nav/autos.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-car"></i></span>
                        <span class="nav-text">Autos</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="nav/reservas.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                        <span class="nav-text">Reservas</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="nav/clientes.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-users"></i></span>
                        <span class="nav-text">Clientes</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="nav/empleados.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-user-tie"></i></span>
                        <span class="nav-text">Empleados</span>
                    </a>
                </div>
            </div>
            
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
                <div class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                        <span class="nav-text">Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <div class="header-title">
                <div class="welcome-text">Bienvenido de vuelta, <?php echo htmlspecialchars($_SESSION['usuario']); ?></div>
                <h1 class="page-title">Dashboard</h1>
            </div>
            <div class="header-actions">
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
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

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-title">Ingresos del Mes</div>
                        <div class="stat-value">$<?php echo number_format($total_ingresos, 2); ?></div>
                        <?php if ($porcentaje_cambio != 0): ?>
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

        <!-- Charts Grid -->
        <div class="charts-grid">
            <div class="chart-container">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">Ingresos Mensuales</h3>
                        <p class="chart-subtitle">Año <?= date('Y') ?></p>
                    </div>
                </div>
                <div class="chart-canvas">
                    <canvas id="graficaIngresos"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">Reservas por Mes</h3>
                        <p class="chart-subtitle">Tendencia anual</p>
                    </div>
                </div>
                <div class="chart-canvas">
                    <canvas id="graficaReservas"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">Estado de la Flota</h3>
                        <p class="chart-subtitle">Distribución actual</p>
                    </div>
                </div>
                <div class="chart-canvas">
                    <canvas id="graficaEstadoAutos"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">Estado de Multas</h3>
                        <p class="chart-subtitle">Pagadas vs Pendientes</p>
                    </div>
                </div>
                <div class="chart-canvas">
                    <canvas id="graficaMultas"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <?php if (!empty($actividades)): ?>
        <div class="activity-section">
            <div class="activity-header">
                <h3 class="chart-title">Actividad Reciente</h3>
                <a href="#" class="nav-link" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                    Ver todo <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php foreach ($actividades as $actividad): ?>
            <div class="activity-item">
                <div class="activity-icon <?= $actividad['tipo'] === 'reserva' ? 'reservation' : 'payment' ?>">
                    <i class="fas fa-<?= $actividad['tipo'] === 'reserva' ? 'calendar-plus' : 'dollar-sign' ?>"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-description"><?= htmlspecialchars($actividad['descripcion']) ?></div>
                    <div class="activity-time">
                        <i class="fas fa-clock"></i>
                        <?= date('d/m/Y H:i', strtotime($actividad['fecha'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <script>
        // Toggle Sidebar
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const toggleIcon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            toggleIcon.className = sidebar.classList.contains('collapsed') ? 'fas fa-bars' : 'fas fa-times';
        });

        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        // Chart.js configuration
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = '#334155';
        Chart.defaults.backgroundColor = 'rgba(99, 102, 241, 0.1)';

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: '#334155',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: '#334155',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 11
                        }
                    }
                }
            }
        };

        // Data from PHP
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const ingresosMes = <?= json_encode(array_values($ingresosPorMes)) ?>;
        const reservasMes = <?= json_encode(array_values($reservasPorMes)) ?>;
        const estadosAutos = <?= json_encode($estadosAutos) ?>;
        const multasEstado = <?= json_encode($multasEstado) ?>;

        // Income Chart
        const ctxIngresos = document.getElementById('graficaIngresos').getContext('2d');
        new Chart(ctxIngresos, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: ingresosMes,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    ...chartOptions.scales,
                    y: {
                        ...chartOptions.scales.y,
                        beginAtZero: true,
                        ticks: {
                            ...chartOptions.scales.y.ticks,
                            callback: function(value) {
                                return ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Reservations Chart
        const ctxReservas = document.getElementById('graficaReservas').getContext('2d');
        new Chart(ctxReservas, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Reservas',
                    data: reservasMes,
                    borderColor: '#00d4ff',
                    backgroundColor: 'rgba(0, 212, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#00d4ff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    ...chartOptions.scales,
                    y: {
                        ...chartOptions.scales.y,
                        beginAtZero: true,
                        ticks: {
                            ...chartOptions.scales.y.ticks,
                            precision: 0
                        }
                    }
                }
            }
        });

        // Car Status Pie Chart
        const ctxEstadoAutos = document.getElementById('graficaEstadoAutos').getContext('2d');
        const carColors = {
            'disponible': '#10b981',
            'rentado': '#6366f1',
            'mantenimiento': '#f59e0b',
            'fuera_servicio': '#ef4444'
        };

        new Chart(ctxEstadoAutos, {
            type: 'doughnut',
            data: {
                labels: Object.keys(estadosAutos).map(estado => {
                    return estado.charAt(0).toUpperCase() + estado.slice(1).replace('_', ' ');
                }),
                datasets: [{
                    data: Object.values(estadosAutos),
                    backgroundColor: Object.keys(estadosAutos).map(estado => carColors[estado] || '#64748b'),
                    borderWidth: 0,
                    hoverBorderWidth: 2,
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: 'Inter',
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Fines Chart
        const ctxMultas = document.getElementById('graficaMultas').getContext('2d');
        new Chart(ctxMultas, {
            type: 'doughnut',
            data: {
                labels: ['Pagadas', 'Pendientes'],
                datasets: [{
                    data: [multasEstado.pagado || 0, multasEstado.pendiente || 0],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverBorderWidth: 2,
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: 'Inter',
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Auto-refresh data every 5 minutes
        setInterval(() => {
            location.reload();
        }, 300000);

        // Add loading states
        document.addEventListener('DOMContentLoaded', () => {
            // Simulate loading for better UX
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
            });

            setTimeout(() => {
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 100);
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('open');
            }
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                toggleBtn.click();
            }
        });
    </script>
</body>
</html>