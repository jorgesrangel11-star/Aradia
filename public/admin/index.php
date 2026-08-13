<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>MindKind - Panel Admin</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
 
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background-color: #f8fafc;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR - Mantenemos el mismo estilo */
        .admin-sidebar {
            width: 260px;
            background-color: #111827;
            color: #ffffff;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .admin-logo-section {
            margin-bottom: 20px;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #ffffff;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .admin-logo:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .admin-logo-img {
            height: 36px;
            width: 36px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #3b82f6;
        }

        .admin-logo-text {
            font-size: 16px;
            font-weight: 600;
        }

        .admin-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #e5e7eb;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .admin-nav-link:hover,
        .admin-nav-link.active {
            background-color: #1f2937;
            color: #ffffff;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            opacity: 0.8;
        }

        .admin-sidebar-footer {
            margin-top: auto;
            font-size: 13px;
            color: #d1d5db;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-user {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .admin-logout {
            color: #fca5a5;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .admin-logout:hover {
            color: #f87171;
            text-decoration: underline;
        }

        .logout-icon {
            width: 14px;
            height: 14px;
        }

        /* CONTENIDO PRINCIPAL */
        .admin-main {
            flex: 1;
            padding: 32px 40px;
            background-color: #f8fafc;
            overflow-y: auto;
        }

        /* Header */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .admin-header h1 {
            font-size: 28px;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            width: 28px;
            height: 28px;
            color: #3b82f6;
        }

        /* Tarjetas de estadísticas */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .card-icon {
            width: 24px;
            height: 24px;
            color: #3b82f6;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .card-trend {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .trend-up {
            color: #059669;
        }

        .trend-down {
            color: #dc2626;
        }

        /* Acciones rápidas */
        .quick-actions {
            margin-top: 40px;
        }

        .actions-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .action-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .action-card:hover {
            background-color: #f0f9ff;
            border-color: #3b82f6;
            transform: translateY(-2px);
        }

        .action-icon {
            width: 40px;
            height: 40px;
            color: #3b82f6;
            margin-bottom: 12px;
            background-color: #eff6ff;
            padding: 8px;
            border-radius: 10px;
        }

        .action-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .action-desc {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.4;
        }
    </style>
</head>

<body>

<div class="admin-wrapper">
    <!-- Sidebar con nuevas opciones -->
    <div class="admin-sidebar">
        <div class="admin-logo-section">
            <a href="index.php" class="admin-logo">
                <img src="../assets/img/mindkind.jpeg" alt="MindKind" class="admin-logo-img">
                <span class="admin-logo-text">MindKind Admin</span>
            </a>
        </div>

        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-link active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <a href="catalogo_pictogramas.php" class="admin-nav-link">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Pictogramas
            </a>
            <!-- Nuevo: Gestionar Catálogos -->
            <a href="gestionar_catalogos.php" class="admin-nav-link">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Gestionar Catálogos
            </a>
            <!-- Nuevo: Ver Compras -->
            <a href="ver_compras.php" class="admin-nav-link">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Ver Compras
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <div class="admin-user">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['nom_usr'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['nom_usr']); ?></div>
                    <div style="font-size: 12px; color: #9ca3af;">Administrador</div>
                </div>
            </div>
            <a href="../login.php" class="admin-logout">
                <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar sesión
            </a>
        </div>
    </div>

    <main class="admin-main">
        <!-- Header -->
        <div class="admin-header">
            <h1>
                <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Panel de Administración
            </h1>
        </div>

        <?php
        // Incluir gestores para obtener estadísticas
        require_once __DIR__ . '/../../lib/gestor_pictogramas.php';
        require_once __DIR__ . '/../../lib/gestor_compras.php';
        require_once __DIR__ . '/../../lib/gestor_paquetes.php';
        
        $pictogramas = mostrar_pictogramas();
        $activos = array_filter($pictogramas, function($p) { return $p['estatus'] == 1; });
        $inactivos = array_filter($pictogramas, function($p) { return $p['estatus'] == 0; });
        $categorias = !empty($pictogramas) ? array_unique(array_column($pictogramas, 'categoria')) : [];
        
        $paquetes = obtener_paquetes_premium_admin();
        $estadisticas_compras = obtener_estadisticas_compras();
        ?>

        <!-- Estadísticas -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-title">Total de Pictogramas</span>
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="card-value"><?php echo count($pictogramas); ?></div>
                <div class="card-trend trend-up">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Todos los pictogramas del sistema</span>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-title">Pictogramas Activos</span>
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905a3.61 3.61 0 01-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                    </svg>
                </div>
                <div class="card-value"><?php echo count($activos); ?></div>
                <div class="card-trend trend-up">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Visibles para los usuarios</span>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-title">Categorías</span>
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="card-value"><?php echo count($categorias); ?></div>
                <div class="card-trend">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Grupos diferentes</span>
                </div>
            </div>

            <!-- NUEVA TARJETA: Paquetes Premium -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-title">Paquetes Premium</span>
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <div class="card-value"><?php echo count($paquetes); ?></div>
                <div class="card-trend">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 019.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <span>Disponibles para compra</span>
                </div>
            </div>

            <!-- NUEVA TARJETA: Ventas Totales -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-title">Ventas Totales</span>
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="card-value">$<?php echo number_format($estadisticas_compras['total_ingresos'], 2); ?></div>
                <div class="card-trend trend-up">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span><?php echo $estadisticas_compras['total_compras']; ?> transacciones</span>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="quick-actions">
            <div class="actions-title">Acciones Rápidas</div>
            <div class="actions-grid">
                <a href="alta_pictogramas.php" class="action-card">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <div class="action-title">Agregar Pictograma</div>
                    <div class="action-desc">Crea un nuevo pictograma para el sistema</div>
                </a>

                <a href="catalogo_pictogramas.php" class="action-card">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <div class="action-title">Ver Catálogo</div>
                    <div class="action-desc">Explora y gestiona todos los pictogramas</div>
                </a>

                <a href="catalogo_pictogramas.php" class="action-card">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <div class="action-title">Editar Pictogramas</div>
                    <div class="action-desc">Modifica pictogramas existentes</div>
                </a>

                <!-- NUEVO BOTÓN: Gestionar Catálogos -->
                <a href="gestionar_catalogos.php" class="action-card">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <div class="action-title">Gestionar Catálogos</div>
                    <div class="action-desc">Administra paquetes premium y contenido</div>
                </a>

                <!-- NUEVO BOTÓN: Ver Compras -->
                <a href="ver_compras.php" class="action-card">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <div class="action-title">Ver Compras</div>
                    <div class="action-desc">Revisa el historial de ventas y reportes</div>
                </a>
            </div>
        </div>
    </main>
</div>

</body>
</html>