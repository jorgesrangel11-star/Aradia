<?php
@session_start();

if (!isset($_SESSION['tip_usu']) || (int)$_SESSION['tip_usu'] !== 1) {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

$mensaje_ok  = isset($_GET['msg'])   ? $_GET['msg']   : '';
$mensaje_err = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Alta de pictograma - MindKind Admin</title>
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

        /* Botones */
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

        /* Formulario */
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

        /* Vista previa de imagen */
        .image-preview {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 10px;
            border: 2px dashed #d1d5db;
            display: none;
        }

        .image-preview.visible {
            display: flex;
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

        /* Ejemplos de imágenes */
        .image-examples {
            margin-top: 16px;
            padding: 16px;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .examples-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .examples-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .example-item {
            font-size: 12px;
            color: #6b7280;
            background-color: #f3f4f6;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
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
            <a href="catalogo_pictogramas.php" class="admin-nav-link">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar pictograma
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

        <!-- Mensajes -->
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.995-.833-2.767 0L4.33 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div><strong>Error:</strong> <?php echo htmlspecialchars($mensaje_err); ?></div>
            </div>
        <?php } ?>

        <!-- Formulario -->
        <div class="form-card">
            <form action="../../lib/gestor_pictogramas.php" method="POST">
                <input type="hidden" name="accion" value="agregar">

                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre del pictograma</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required
                           placeholder="Ej: Aprender, Comer, Dormir">
                    <div class="form-hint">Nombre descriptivo del pictograma</div>
                </div>

                <div class="form-group">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required
                              placeholder="Describe el significado o uso del pictograma..."></textarea>
                    <div class="form-hint">Explica qué representa este pictograma</div>
                </div>

                <div class="form-group">
                    <label for="categoria" class="form-label">Categoría</label>
                    <input type="text" name="categoria" id="categoria" class="form-control" required
                           placeholder="Ej: Rutina, Emociones, Alimentos, Acciones">
                    <div class="form-hint">Agrupa pictogramas similares por categoría</div>
                </div>

                <div class="form-group">
                    <label for="imagen" class="form-label">Archivo de imagen</label>
                    <input type="text" name="imagen" id="imagen" class="form-control" required
                           placeholder="ejemplo.jpg">
                    <div class="form-hint">El archivo debe estar en la carpeta <code>assets/img</code></div>
                    
                    <!-- Vista previa dinámica -->
                    <div class="image-preview" id="imagePreviewContainer">
                        <img src="" alt="Vista previa" id="imagePreview">
                        <div class="image-preview-info">
                            <div class="image-preview-name">Vista previa</div>
                            <div class="image-preview-path" id="imagePath"></div>
                            <div class="image-preview-status" id="imageStatus">
                                <span style="color: #6b7280;">Ingresa el nombre del archivo para ver la vista previa</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ejemplos de imágenes disponibles -->
                    <div class="image-examples">
                        <div class="examples-title">Ejemplos de nombres de archivo:</div>
                        <div class="examples-list">
                            <span class="example-item">aprender.jpg</span>
                            <span class="example-item">comer.png</span>
                            <span class="example-item">dormir.jpg</span>
                            <span class="example-item">feliz.png</span>
                            <span class="example-item">triste.jpg</span>
                            <span class="example-item">jugar.png</span>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar pictograma
                    </button>
                    <a href="/U3_INTEGRADORA/U3_INTEGRADORA/U3_INTEGRADORA/public/admin/catalogo_pictogramas.php" class="btn btn-secondary">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                </div>
                    </a>
                </div>
            </form>
        </div>

        <!-- Script para vista previa de imagen -->
      <!--  <script>
            document.addEventListener('DOMContentLoaded', function() {
                const imageInput = document.getElementById('imagen');
                const imagePreview = document.getElementById('imagePreview');
                const previewContainer = document.getElementById('imagePreviewContainer');
                const imagePath = document.getElementById('imagePath');
                const imageStatus = document.getElementById('imageStatus');
                
                imageInput.addEventListener('input', function() {
                    const filename = this.value.trim();
                    
                    if (filename) {
                        // Mostrar contenedor de vista previa
                        previewContainer.classList.add('visible');
                        
                        // Actualizar imagen
                        const newSrc = '../assets/img/' + filename;
                        imagePreview.src = newSrc;
                        imagePreview.alt = 'Vista previa de ' + filename;
                        
                        // Actualizar ruta
                        imagePath.textContent = filename;
                        
                        // Verificar si el archivo existe
                        checkFileExists(filename);
                    } else {
                        // Ocultar vista previa si no hay nombre
                        previewContainer.classList.remove('visible');
                        imageStatus.innerHTML = '<span style="color: #6b7280;">Ingresa el nombre del archivo para ver la vista previa</span>';
                    }
                });
                
                function checkFileExists(filename) {
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
                                <span class="status-error">Archivo no encontrado - Asegúrate de subir la imagen a la carpeta assets/img</span>
                            `;
                        });
                }
                
                // Manejar error de carga de imagen
                imagePreview.addEventListener('error', function() {
                    this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNGOEY4RjgiLz48cGF0aCBkPSJNNjIgNTBDNjIgNTUuNTIyOCA1Ny41MjI4IDYwIDUyIDYwQzQ2LjQ3NzIgNjAgNDIgNTUuNTIyOCA0MiA1MEM0MiA0NC40NzcyIDQ2LjQ3NzIgNDAgNTIgNDBDNTcuNTIyOCA0MCA2MiA0NC40NzcyIDYyIDUwWiIgZmlsbD0iI0QxRDFEMSIvPjxwYXRoIGQ9Ik03NSA2Mkg4MEM4MS4xMDQ2IDYyIDgyIDYyLjg5NTQgODIgNjRWNjhDODIgNjkuMTA0NiA4MS4xMDQ2IDcwIDgwIDcwSDE5QzE3Ljg5NTQgNzAgMTcgNjkuMTA0NiAxNyA2OFY2NEMxNyA2Mi44OTU0IDE3Ljg5NTQgNjIgMTkgNjJIMjNMMzIgMzZINjhMNzQgNjJMNzUgNjJaIiBmaWxsPSIjRTBFMEUwIi8+PC9zdmc+';
                });
            });
        </script> -->
    </main>
</div>

</body>
</html>