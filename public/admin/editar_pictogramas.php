<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

if (!isset($_GET['id_pic'])) {
    header("Location: catalogo_pictogramas.php?error=" . urlencode('No se recibió id_pic.'));
    exit;
}

require_once __DIR__ . '/../../lib/gestor_pictogramas.php';

$id_pic = (int) $_GET['id_pic'];
$p = obtener_pictograma_por_id($id_pic);

if (!$p) {
    header("Location: catalogo_pictogramas.php?error=" . urlencode('Pictograma no encontrado.'));
    exit;
}

$mensaje_ok  = isset($_GET['msg'])   ? $_GET['msg']   : '';
$mensaje_err = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar pictograma - MindKind Admin</title>
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

        /* SIDEBAR */
        .admin-sidebar {
            width: 260px;
            background-color: #111827;
            color: #ffffff;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* LOGO MÁS PEQUEÑO Y ARRIBA */
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
            height: 36px; /* Más pequeño */
            width: 36px;  /* Más pequeño */
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

        .admin-nav-link:hover {
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

        /* Tarjeta del formulario */
        .form-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
            font-size: 15px;
        }

        .form-control, .form-select {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-hint {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

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

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
            transform: translateY(-1px);
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
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
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
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

        /* Preview de imagen */
        .image-preview {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 10px;
            border: 2px dashed #d1d5db;
        }

        .image-preview img {
            height: 100px;
            width: 100px;
            object-fit: contain;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            background-color: white;
            padding: 8px;
        }

        .image-preview-info {
            flex: 1;
        }

        .image-preview-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .image-preview-path {
            font-size: 13px;
            color: #6b7280;
            font-family: monospace;
            background-color: #f3f4f6;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .image-preview-status {
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-success {
            color: #059669;
        }

        .status-error {
            color: #dc2626;
        }

        .status-icon {
            width: 14px;
            height: 14px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

<div class="admin-wrapper">
    <!-- Menú mejorado -->
    <div class="admin-sidebar">
        <!-- Logo más pequeño y arriba -->
        <div class="admin-logo-section">
            <a href="index.php" class="admin-logo">
                <img src="../assets/img/mindkind.jpeg" alt="MindKind" class="admin-logo-img">
                <span class="admin-logo-text">MindKind Admin</span>
            </a>
        </div>

        <!-- Navegación -->
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-link">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>
            <a href="catalogo_pictogramas.php" class="admin-nav-link">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Pictogramas
            </a>
        </nav>

        <!-- Información de usuario y logout -->
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar pictograma
                <span class="badge <?php echo ((int)$p['estatus'] === 1) ? 'badge-active' : 'badge-inactive'; ?>">
                    <?php echo ((int)$p['estatus'] === 1) ? 'Activo' : 'Inactivo'; ?>
                </span>
            </h1>
            <div>
                <a href="catalogo_pictogramas.php" class="btn btn-secondary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al catálogo
                </a>
            </div>
        </div>

        <?php if ($mensaje_ok) { ?>
            <div class="alert alert-success">
                <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <div><strong>Éxito:</strong> <?php echo htmlspecialchars($mensaje_ok); ?></div>
            </div>
        <?php } ?>
        
        <?php if ($mensaje_err) { ?>
            <div class="alert alert-danger">
                <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <div><strong>Error:</strong> <?php echo htmlspecialchars($mensaje_err); ?></div>
            </div>
        <?php } ?>

        <div class="form-card">
            <form action="../../lib/gestor_pictogramas.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_pic" value="<?php echo (int)$p['id_pic']; ?>">

                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre del pictograma</label>
                    <input type="text" name="nombre" id="nombre" class="form-control"
                           value="<?php echo htmlspecialchars($p['nombre']); ?>" required
                           placeholder="Ingresa el nombre del pictograma">
                </div>

                <div class="form-group">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required
                              placeholder="Describe el pictograma..."><?php echo htmlspecialchars($p['descripcion']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="categoria" class="form-label">Categoría</label>
                    <input type="text" name="categoria" id="categoria" class="form-control"
                           value="<?php echo htmlspecialchars($p['categoria']); ?>" required
                           placeholder="Ej: Emociones, Acciones, Alimentos">
                    <div class="form-hint">Agrupa pictogramas similares por categoría</div>
                </div>

                <div class="form-group">
                    <label for="imagen" class="form-label">Archivo de imagen</label>
                    <input type="text" name="imagen" id="imagen" class="form-control"
                           value="<?php echo htmlspecialchars($p['imagen']); ?>" required
                           placeholder="ejemplo.jpg">
                    <div class="form-hint">El archivo debe estar en la carpeta <code>assets/img</code></div>
                    
                    <div class="image-preview">
                        <img src="../assets/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                             alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                             id="imagePreview"
                             onerror="this.style.display='none'">
                        <div class="image-preview-info">
                            <div class="image-preview-name">Vista previa</div>
                            <div class="image-preview-path"><?php echo htmlspecialchars($p['imagen']); ?></div>
                            <div class="image-preview-status" id="imageStatus">
                                <?php 
                                $imagePath = '../assets/img/' . $p['imagen'];
                                if(file_exists($imagePath)): 
                                ?>
                                    <svg class="status-icon status-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="status-success">Archivo encontrado</span>
                                <?php else: ?>
                                    <svg class="status-icon status-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.995-.833-2.767 0L4.33 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <span class="status-error">Archivo no encontrado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="estatus" class="form-label">Estatus</label>
                    <select name="estatus" id="estatus" class="form-select">
                        <option value="1" <?php echo ((int)$p['estatus'] === 1) ? 'selected' : ''; ?>>Activo (visible para usuarios)</option>
                        <option value="0" <?php echo ((int)$p['estatus'] === 0) ? 'selected' : ''; ?>>Inactivo (no visible)</option>
                    </select>
                    <div class="form-hint">Los pictogramas inactivos no serán visibles para los usuarios</div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar cambios
                    </button>
                    <a href="catalogo_pictogramas.php" class="btn btn-secondary">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Script para actualizar vista previa en tiempo real -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const imageInput = document.getElementById('imagen');
                const imagePreview = document.getElementById('imagePreview');
                const imageStatus = document.getElementById('imageStatus');
                
                imageInput.addEventListener('input', function() {
                    const filename = this.value.trim();
                    if (filename) {
                        // Actualizar imagen
                        const newSrc = '../assets/img/' + filename;
                        imagePreview.src = newSrc;
                        imagePreview.style.display = 'block';
                        imagePreview.alt = 'Vista previa de ' + filename;
                        
                        // Actualizar información del archivo
                        const pathElement = document.querySelector('.image-preview-path');
                        if (pathElement) {
                            pathElement.textContent = filename;
                        }
                        
                        // Verificar si el archivo existe
                        checkFileExists(filename);
                    } else {
                        imagePreview.style.display = 'none';
                        imageStatus.innerHTML = '<span class="status-error">No se ha especificado archivo</span>';
                    }
                });
                
                function checkFileExists(filename) {
                    if (!filename) return;
                    
                    fetch('../assets/img/' + filename)
                        .then(response => {
                            if (response.ok) {
                                imageStatus.innerHTML = `
                                    <svg class="status-icon status-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="status-success">Archivo encontrado</span>
                                `;
                            } else {
                                throw new Error('File not found');
                            }
                        })
                        .catch(() => {
                            imageStatus.innerHTML = `
                                <svg class="status-icon status-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.995-.833-2.767 0L4.33 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <span class="status-error">Archivo no encontrado</span>
                            `;
                        });
                }
            });
        </script>
    </main>
</div>

</body>
</html>