<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

require_once __DIR__ . '/../../lib/gestor_compras.php';

$compras = obtener_todas_las_compras();
$estadisticas = obtener_estadisticas_compras();

$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';

if ($filtro_fecha || $filtro_usuario) {
    $compras = filtrar_compras($compras, $filtro_fecha, $filtro_usuario);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver Compras - MindKind Admin</title>
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

        .admin-main {
            flex: 1;
            padding: 32px 40px;
            background-color: #f8fafc;
            overflow-y: auto;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 5px;
        }

        .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            height: 38px;
        }

        .filter-btn:hover {
            background: #2563eb;
        }

        .reset-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            height: 38px;
            line-height: 18px;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        tr:hover {
            background: #f9fafb;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        @media (max-width: 768px) {
            .admin-main {
                padding: 20px;
            }
            .filter-section {
                flex-direction: column;
            }
            .filter-group {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="admin-wrapper">
    <div class="admin-sidebar">
        <div class="admin-logo-section">
            <a href="index.php" class="admin-logo">
                <img src="../assets/img/mindkind.jpeg" alt="MindKind" class="admin-logo-img">
                <span class="admin-logo-text">MindKind Admin</span>
            </a>
        </div>

        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-link">
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
            <a href="gestionar_catalogos.php" class="admin-nav-link">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Gestionar Catálogos
            </a>
            <a href="ver_compras.php" class="admin-nav-link active">
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
        <div class="admin-header">
            <h1>
                <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Historial de Compras
            </h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['total_compras']; ?></div>
                <div class="stat-label">Total de compras</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($estadisticas['total_ingresos'], 2); ?></div>
                <div class="stat-label">Ingresos totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $estadisticas['usuarios_unicos']; ?></div>
                <div class="stat-label">Usuarios que compraron</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo $estadisticas['compra_promedio']; ?></div>
                <div class="stat-label">Compra promedio</div>
            </div>
        </div>

        <div class="filter-section">
            <div class="filter-group">
                <label class="filter-label">Fecha</label>
                <input type="date" class="filter-input" id="filtroFecha" value="<?php echo $filtro_fecha; ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">Usuario</label>
                <input type="text" class="filter-input" id="filtroUsuario" placeholder="Nombre o email" value="<?php echo $filtro_usuario; ?>">
            </div>
            <button class="filter-btn" onclick="aplicarFiltros()">Aplicar Filtros</button>
            <a href="ver_compras.php" class="reset-btn">Limpiar</a>
        </div>

        <div class="table-container">
            <h3 style="margin-bottom: 20px;">Detalle de Compras</h3>
            
            <?php if (empty($compras)): ?>
                <p style="text-align: center; color: #6b7280; padding: 40px;">No hay compras registradas.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Compra</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Producto</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td>#<?php echo str_pad($compra['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($compra['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($compra['email']); ?></td>
                                <td><?php echo htmlspecialchars($compra['producto']); ?></td>
                                <td><strong>$<?php echo number_format($compra['monto'], 2); ?></strong></td>
                                <td><span class="badge badge-success">Completada</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function aplicarFiltros() {
        const fecha = document.getElementById('filtroFecha').value;
        const usuario = document.getElementById('filtroUsuario').value;
        let url = 'ver_compras.php?';
        if (fecha) url += 'fecha=' + fecha + '&';
        if (usuario) url += 'usuario=' + encodeURIComponent(usuario);
        window.location.href = url;
    }
</script>

</body>
</html>