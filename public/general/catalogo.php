<?php
session_start();

/* Solo usuarios generales */
if (!isset($_SESSION['mail']) || $_SESSION['mail'] === 'admin@gmail.com') {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

require_once __DIR__ . '/../../lib/gestor_pictogramas.php';
require_once __DIR__ . '/../../lib/gestor_compras.php';

$pictogramas_activos = mostrar_pictogramas_activos();
if (!is_array($pictogramas_activos)) {
    $pictogramas_activos = [];
}

$contenido_premium = obtener_contenido_premium();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mindkind - Catálogo</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
        }

        /* Header general */
        .general-header {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .gh-left, .gh-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .header-btn {
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .header-btn-login {
            background: #3b82f6;
            color: white;
        }
        
        .header-btn-register {
            background: white;
            color: #3b82f6;
            border: 2px solid #3b82f6;
        }
        
        .gh-logo {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .gh-link {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
        }
        
        .gh-link:hover {
            color: #3b82f6;
        }

        /* Encabezado del catálogo */
        .catalogo-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .catalogo-header h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .catalogo-header p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Subpestañas */
        .catalogo-subtabs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0 40px;
            flex-wrap: wrap;
        }
        
        .catalogo-subtab {
            padding: 12px 30px;
            background: white;
            border: 2px solid #8b5cf6;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            color: #8b5cf6;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .catalogo-subtab:hover {
            background: #8b5cf6;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }
        
        .catalogo-subtab.active {
            background: #8b5cf6;
            color: white;
        }
        
        .subtab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .subtab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Grid de pictogramas */
        .pictos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .picto-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .picto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px rgba(0,0,0,0.15);
        }
        
        .picto-img {
            height: 200px;
            overflow: hidden;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .picto-img img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .picto-body {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .picto-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .picto-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        /* Grid premium */
        .premium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .premium-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            color: white;
        }
        
        .premium-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 40px rgba(0, 0, 0, 0.3);
        }
        
        .premium-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: white;
        }
        
        .premium-card-body {
            padding: 25px;
        }
        
        .premium-card-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .premium-card-desc {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .premium-card-incluye {
            font-size: 13px;
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .premium-card-price {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .premium-card-btn {
            background: white;
            color: #764ba2;
            border: none;
            padding: 15px 20px;
            border-radius: 10px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .premium-card-btn:hover {
            background: #f3f4f6;
            transform: scale(1.02);
        }

        /* Botón de volver */
        .back-btn {
            display: inline-block;
            margin: 20px;
            padding: 10px 25px;
            background: white;
            color: #3b82f6;
            border: 2px solid #3b82f6;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #3b82f6;
            color: white;
            transform: translateX(-5px);
        }

        /* Badge para productos */
        .badge-gratis {
            background: #10b981;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .general-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .gh-left, .gh-right {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .catalogo-subtabs {
                flex-direction: column;
                align-items: center;
            }
            
            .catalogo-subtab {
                width: 200px;
                text-align: center;
            }
            
            .catalogo-header h1 {
                font-size: 32px;
            }
            
            .back-btn {
                display: block;
                width: fit-content;
                margin: 20px auto;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER ÚNICO -->
    <header class="general-header">
        <div class="gh-left">
            <a href="../login.php" class="header-btn header-btn-login">Inicio de sesión</a>
            <a href="../registro.php" class="header-btn header-btn-register">Registro</a>
        </div>

        <div class="gh-center">
            <img src="../assets/img/mindkind.jpeg" alt="Mindkind" class="gh-logo">
        </div>

        <div class="gh-right">
            <a href="index.php#quienes" class="gh-link">Quiénes somos</a>
            <a href="index.php#servicios" class="gh-link">Servicios</a>
            <a href="index.php#contacto" class="gh-link">Contacto</a>
        </div>
    </header>

    <!-- Botón para volver al inicio -->
    <div style="max-width: 1200px; margin: 0 auto;">
        <a href="index.php" class="back-btn">← Volver al inicio</a>
    </div>

    <!-- Encabezado del catálogo -->
    <div class="catalogo-header">
        <h1> Catálogo de Pictogramas</h1>
        <p>Explora nuestra colección completa de pictogramas gratuitos y premium</p>
    </div>

    <!-- SUBPESTAÑAS DEL CATÁLOGO -->
    <div class="catalogo-subtabs">
        <div class="catalogo-subtab active" onclick="showCatalogSubtab('gratuitos')"> Gratuitos</div>
        <div class="catalogo-subtab" onclick="showCatalogSubtab('premium')"> Premium</div>
        <div class="catalogo-subtab" onclick="showCatalogSubtab('todos')"> Todos</div>
    </div>

    <!-- CONTENIDO GRATUITOS -->
    <div id="subtab-gratuitos" class="subtab-content active">
        <h3 style="text-align: center; margin-bottom: 30px; color: #059669;">Pictogramas Gratuitos</h3>
        <?php if (empty($pictogramas_activos)): ?>
            <p style="text-align: center; color: #6b7280; padding: 40px;">No hay pictogramas gratuitos disponibles.</p>
        <?php else: ?>
            <div class="pictos-grid">
                <?php foreach ($pictogramas_activos as $p): ?>
                    <div class="picto-card">
                        <div class="picto-img">
                            <img src="../assets/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                                 alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                                 onerror="this.src='../assets/img/placeholder.jpg'">
                        </div>
                        <div class="picto-body">
                            <h3 class="picto-name"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            <p class="picto-desc"><?php echo htmlspecialchars($p['descripcion']); ?></p>
                            <span class="badge-gratis">GRATUITO</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- CONTENIDO PREMIUM -->
    <div id="subtab-premium" class="subtab-content">
        <h3 style="text-align: center; margin-bottom: 30px; color: #8b5cf6;">Contenido Premium</h3>
        
        <?php if (empty($contenido_premium)): ?>
            <p style="text-align: center; color: #6b7280; padding: 40px;">No hay contenido premium disponible.</p>
        <?php else: ?>
            <div class="premium-grid">
                <?php foreach ($contenido_premium as $producto): ?>
                    <div class="premium-card">
                        <img src="../assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                             class="premium-card-img"
                             onerror="this.src='../assets/img/placeholder.jpg'">
                        <div class="premium-card-body">
                            <h3 class="premium-card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="premium-card-desc"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p class="premium-card-incluye"><strong>Incluye:</strong> <?php echo htmlspecialchars($producto['incluye']); ?></p>
                            <div class="premium-card-price"><?php echo $producto['precio_formato']; ?></div>
                            <a href="compras.php?id=<?php echo $producto['id_paquete']; ?>" class="premium-card-btn">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- CONTENIDO TODOS -->
    <div id="subtab-todos" class="subtab-content">
        <h3 style="text-align: center; margin-bottom: 30px; color: #1f2937;">Todo el Contenido</h3>
        
        <?php if (empty($pictogramas_activos) && empty($contenido_premium)): ?>
            <p style="text-align: center; color: #6b7280; padding: 40px;">No hay contenido disponible.</p>
        <?php else: ?>
            <div class="pictos-grid">
                <!-- Pictogramas gratuitos -->
                <?php if (!empty($pictogramas_activos)): ?>
                    <?php foreach ($pictogramas_activos as $p): ?>
                        <div class="picto-card">
                            <div class="picto-img">
                                <img src="../assets/img/<?php echo htmlspecialchars($p['imagen']); ?>"
                                     alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                                     onerror="this.src='../assets/img/placeholder.jpg'">
                            </div>
                            <div class="picto-body">
                                <h3 class="picto-name"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                                <p class="picto-desc"><?php echo htmlspecialchars($p['descripcion']); ?></p>
                                <span class="badge-gratis">GRATUITO</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Productos premium -->
                <?php if (!empty($contenido_premium)): ?>
                    <?php foreach ($contenido_premium as $producto): ?>
                        <div class="picto-card" style="border: 2px solid #8b5cf6;">
                            <div class="picto-img">
                                <img src="../assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                     onerror="this.src='../assets/img/placeholder.jpg'">
                            </div>
                            <div class="picto-body">
                                <h3 class="picto-name"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p class="picto-desc"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <p style="font-size: 13px; color: #6b7280; margin-bottom: 10px;">
                                    <strong>Incluye:</strong> <?php echo htmlspecialchars($producto['incluye']); ?>
                                </p>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: #8b5cf6; font-weight: bold; font-size: 20px;"><?php echo $producto['precio_formato']; ?></span>
                                    <a href="compras.php?id=<?php echo $producto['id_paquete']; ?>" 
                                       style="background: #8b5cf6; color: white; border: none; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                                        Comprar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>



    <script>
        // Función para mostrar subpestañas del catálogo
        function showCatalogSubtab(subtabName) {
            document.querySelectorAll('.subtab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById('subtab-' + subtabName).classList.add('active');
            
            document.querySelectorAll('.catalogo-subtab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>