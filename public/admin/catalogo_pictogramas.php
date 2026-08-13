<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

// Gestor de pictogramas
require_once __DIR__ . '/../../lib/gestor_pictogramas.php';

// Traer TODOS los pictogramas (activos e inactivos)
$pictogramas = mostrar_pictogramas();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Catálogo de pictogramas - MindKind Admin</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    
    <!-- Estilos del layout de admin -->
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
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e5e7eb;
        }

        .admin-header h1 {
            font-size: 24px;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-icon {
            width: 24px;
            height: 24px;
            color: #3b82f6;
        }

        /* Botones PRINCIPALES */
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        /* Botones PEQUEÑOS para la tabla */
        .btn-xs {
            padding: 6px 12px !important;
            font-size: 12px !important;
            height: 32px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            border-radius: 6px !important;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Estadísticas */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }

        /* Tabla */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead {
            background-color: #f9fafb;
        }

        th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            vertical-align: top;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Imágenes */
        .pictogram-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
            background-color: white;
            padding: 3px;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        /* Acciones - BOTONES PEQUEÑOS Y VERTICALES */
        .actions-cell {
            white-space: nowrap;
            min-width: 110px;
            max-width: 120px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 6px;
            width: 100%;
        }

        .action-buttons .btn-xs {
            width: 100%;
            min-width: 90px;
            max-width: 100px;
            box-sizing: border-box;
        }

        .action-buttons form {
            width: 100%;
            margin: 0;
        }

        .action-buttons button {
            width: 100%;
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        /* Iconos pequeños */
        .icon-xs {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
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
                Home
            </a>
            <a href="catalogo_pictogramas.php" class="admin-nav-link active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Pictogramas
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Catálogo de pictogramas
            </h1>
            <a href="alta_pictogramas.php" class="btn btn-primary">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar pictograma
            </a>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_GET['msg'])) { ?>
            <div class="alert alert-success">
                <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <div><?php echo htmlspecialchars($_GET['msg']); ?></div>
            </div>
        <?php } ?>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger">
                <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.995-.833-2.767 0L4.33 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div><?php echo htmlspecialchars($_GET['error']); ?></div>
            </div>
        <?php } ?>

        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($pictogramas); ?></div>
                <div class="stat-label">Total de pictogramas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $activos = array_filter($pictogramas, function($p) {
                        return $p['estatus'] == 1;
                    });
                    echo count($activos);
                    ?>
                </div>
                <div class="stat-label">Pictogramas activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $inactivos = array_filter($pictogramas, function($p) {
                        return $p['estatus'] == 0;
                    });
                    echo count($inactivos);
                    ?>
                </div>
                <div class="stat-label">Pictogramas inactivos</div>
            </div>
            <?php if (!empty($pictogramas)): ?>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $categorias = array_unique(array_column($pictogramas, 'categoria'));
                    echo count($categorias);
                    ?>
                </div>
                <div class="stat-label">Categorías distintas</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tabla de pictogramas -->
        <div class="table-container">
            <?php if (!empty($pictogramas)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Imagen</th>
                                <th>Estatus</th>
                                <th class="actions-cell">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pictogramas as $p) { ?>
                                <tr>
                                    <td><strong>#<?php echo str_pad($p['id_pic'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td>
                                        <div style="font-weight: 500; font-size: 14px;"><?php echo htmlspecialchars($p['nombre']); ?></div>
                                    </td>
                                    <td>
                                        <div style="max-width: 250px; line-height: 1.4; font-size: 13px;">
                                            <?php echo htmlspecialchars($p['descripcion']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="background-color: #f3f4f6; padding: 4px 10px; border-radius: 6px; display: inline-block; font-size: 12px;">
                                            <?php echo htmlspecialchars($p['categoria']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="../assets/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                                             alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                                             class="pictogram-image"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iODAiIGhlaWdodD0iODAiIGZpbGw9IiNGOEY4RjgiLz48cGF0aCBkPSJNNTAgNDBDNTAgNDQuNDE4MyA0Ni40MTgzIDQ4IDQyIDQ4QzM3LjU4MTcgNDggMzQgNDQuNDE4MyAzNCA0MEMzNCAzNS41ODE3IDM3LjU4MTcgMzIgNDIgMzJDNDYuNDE4MyAzMiA1MCAzNS41ODE3IDUwIDQwWiIgZmlsbD0iI0QxRDFEMSIvPjxwYXRoIGQ9Ik02MCA1MEg2NEM2NS4xMDQ2IDUwIDY2IDUwLjg5NTQgNjYgNTJWNjRDNjYgNjUuMTA0NiA2NS4xMDQ2IDY2IDY0IDY2SDE2QzE0Ljg5NTQzIDY2IDE0IDY1LjEwNDYgMTQgNjRWNTJDMTQgNTAuODk1NCAxNC44OTU0MyA1MCAxNiA1MEgyMEwyNiAyOEg1NEw2MCA1MFoiIGZpbGw9IiNFMEUwRTAiLz48L3N2Zz4='">
                                        <?php if (!file_exists('../assets/img/' . $p['imagen'])): ?>
                                            <div style="font-size: 10px; color: #dc2626; margin-top: 4px;">No encontrado</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($p['estatus'] == 1) ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo ($p['estatus'] == 1) ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <div class="action-buttons">
                                            <!-- Botón Editar - MÁS PEQUEÑO -->
                                            <a href="editar_pictogramas.php?id_pic=<?php echo (int)$p['id_pic']; ?>"
                                               class="btn btn-warning btn-xs">
                                                <svg class="icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Editar
                                            </a>
                                            
                                            <!-- Botón Eliminar - MÁS PEQUEÑO -->
                                            <form action="../../lib/gestor_pictogramas.php"
                                                  method="post"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este pictograma? Esta acción no se puede deshacer.');">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id_pic" value="<?php echo (int)$p['id_pic']; ?>">
                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    <svg class="icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 style="color: #4b5563; margin-bottom: 8px;">No hay pictogramas registrados</h3>
                    <p style="color: #6b7280; margin-bottom: 24px;">Comienza agregando tu primer pictograma</p>
                    <a href="alta_pictogramas.php" class="btn btn-primary">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar primer pictograma
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>