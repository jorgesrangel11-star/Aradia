<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

require_once __DIR__ . '/../../lib/gestor_pictogramas.php';
require_once __DIR__ . '/../../lib/gestor_compras.php';
require_once __DIR__ . '/../../lib/gestor_paquetes.php';

$pictogramas = mostrar_pictogramas();
$paquetes_premium = obtener_paquetes_premium_admin();

$mensaje_ok = isset($_GET['msg']) ? $_GET['msg'] : '';
$mensaje_error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Catálogos - MindKind Admin</title>
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

        .admin-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .admin-tab {
            padding: 10px 25px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .admin-tab:hover {
            background: #f3f4f6;
        }

        .admin-tab.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
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

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
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

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .paquetes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .paquete-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .paquete-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .paquete-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .paquete-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .paquete-price {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin: 15px 0;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        @media (max-width: 768px) {
            .admin-main {
                padding: 20px;
            }
            .admin-tabs {
                flex-direction: column;
            }
            .admin-tab {
                text-align: center;
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
            <a href="gestionar_catalogos.php" class="admin-nav-link active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Gestionar Catálogos
            </a>
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
        <div class="admin-header">
            <h1>
                <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Gestionar Catálogos
            </h1>
        </div>

        <?php if ($mensaje_ok): ?>
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <?php echo htmlspecialchars($mensaje_ok); ?>
            </div>
        <?php endif; ?>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.995-.833-2.767 0L4.33 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <?php echo htmlspecialchars($mensaje_error); ?>
            </div>
        <?php endif; ?>

        <div class="admin-tabs">
            <a href="#pictogramas" class="admin-tab active" onclick="showTab('pictogramas')">🖼️ Pictogramas</a>
            <a href="#paquetes" class="admin-tab" onclick="showTab('paquetes')">📦 Paquetes Premium</a>
            <a href="#nuevo-paquete" class="admin-tab" onclick="showTab('nuevo-paquete')">➕ Nuevo Paquete</a>
        </div>

        <div id="tab-pictogramas" class="tab-content active">
            <div class="table-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin:0; font-size: 20px;">Lista de Pictogramas</h2>
                    <a href="alta_pictogramas.php" class="btn btn-primary btn-sm">➕ Nuevo Pictograma</a>
                </div>
                
                <?php if (empty($pictogramas)): ?>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">No hay pictogramas registrados.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pictogramas as $p): ?>
                                <tr>
                                    <td>#<?php echo str_pad($p['id_pic'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <img src="../assets/img/<?php echo htmlspecialchars($p['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars(substr($p['descripcion'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($p['estatus'] == 1) ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo ($p['estatus'] == 1) ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="editar_pictogramas.php?id_pic=<?php echo $p['id_pic']; ?>" 
                                           class="btn btn-warning btn-sm">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-paquetes" class="tab-content">
            <div class="table-container">
                <h2 style="margin-bottom: 20px;">Paquetes Premium</h2>
                
                <?php if (empty($paquetes_premium)): ?>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">No hay paquetes premium creados.</p>
                <?php else: ?>
                    <div class="paquetes-grid">
                        <?php foreach ($paquetes_premium as $paquete): ?>
                            <div class="paquete-card">
                                <img src="../assets/img/<?php echo htmlspecialchars($paquete['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($paquete['nombre']); ?>"
                                     class="paquete-img"
                                     onerror="this.src='../assets/img/placeholder.jpg'">
                                <h3 class="paquete-title"><?php echo htmlspecialchars($paquete['nombre']); ?></h3>
                                <p style="color: #6b7280; font-size: 14px; margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($paquete['descripcion']); ?>
                                </p>
                                <p style="font-size: 13px; color: #6b7280; margin-bottom: 10px;">
                                    <strong>Incluye:</strong> <?php echo htmlspecialchars($paquete['incluye']); ?>
                                </p>
                                <div class="paquete-price">
                                    $<?php echo number_format($paquete['precio'], 2); ?>
                                </div>
                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <a href="editar_paquete.php?id_paquete=<?php echo $paquete['id_paquete']; ?>" 
                                       class="btn btn-warning btn-sm">Editar</a>
                                    <form method="POST" action="../../lib/gestor_paquetes.php" 
                                          onsubmit="return confirm('¿Eliminar este paquete?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_paquete" value="<?php echo $paquete['id_paquete']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-nuevo-paquete" class="tab-content">
            <div class="form-card">
                <h2 style="margin-bottom: 20px;">Crear Nuevo Paquete Premium</h2>
                
                <form action="../../lib/gestor_paquetes.php" method="POST">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="form-group">
                        <label class="form-label">Nombre del paquete</label>
                        <input type="text" name="nombre" class="form-control" required
                               placeholder="Ej: Emociones Avanzadas">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" required
                                  placeholder="Describe el contenido del paquete..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">¿Qué incluye?</label>
                        <input type="text" name="incluye" class="form-control" required
                               placeholder="Ej: 50 pictogramas, 10 actividades">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Precio</label>
                        <input type="number" name="precio" class="form-control" step="0.01" required
                               placeholder="4.99">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Imagen del paquete</label>
                        <select name="imagen" class="form-select" required>
                            <option value="">Selecciona una imagen</option>
                            <option value="bailar.jpg">bailar.jpg</option>
                            <option value="cantar.jpg">cantar.jpg</option>
                            <option value="dormir.jpg">dormir.jpg</option>
                            <option value="jugar.jpg">jugar.jpg</option>
                            <option value="pintar.jpg">pintar.jpg</option>
                            <option value="aprender.jpg">aprender.jpg</option>
                            <option value="comer.png">comer.png</option>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Crear Paquete</button>
                        <button type="reset" class="btn btn-danger">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    function showTab(tabName) {
        event.preventDefault();
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        document.querySelectorAll('.admin-tab').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }
</script>

</body>
</html>