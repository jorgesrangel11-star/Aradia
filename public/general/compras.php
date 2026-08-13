<?php
session_start();

/* Solo usuarios generales */
if (!isset($_SESSION['mail']) || $_SESSION['mail'] === 'admin@gmail.com') {
    session_destroy();
    header("Location: ../login.php?error=" . urlencode('Acceso no autorizado'));
    exit;
}

require_once __DIR__ . '/../../lib/gestor_compras.php';

$contenido_premium = obtener_contenido_premium();
$producto_seleccionado = null;
$mensaje_compra = '';
$error_compra = '';

// Verificar si hay un ID de producto específico
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($contenido_premium as $producto) {
        if ($producto['id_paquete'] == $id) {  // CAMBIADO: 'id' por 'id_paquete'
            $producto_seleccionado = $producto;
            break;
        }
    }
}

// Procesar compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar_contenido'])) {
    $id_contenido = $_POST['id_contenido'];
    $id_usuario = $_SESSION['id_usr'];
    $resultado = procesar_compra($id_usuario, $id_contenido);
    if ($resultado['exito']) {
        $mensaje_compra = $resultado['mensaje'];
    } else {
        $error_compra = $resultado['mensaje'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mindkind - Compras Premium</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
        }

        /* Header general - AGREGADO para que coincida con el index */
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

        /* Encabezado */
        .compras-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .compras-header h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .compras-header p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
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

        /* Detalle de producto */
        .detalle-producto {
            max-width: 600px;
            margin: 0 auto 40px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Mensajes */
        .compra-mensaje {
            max-width: 600px;
            margin: 20px auto;
            padding: 15px 25px;
            border-radius: 10px;
            text-align: center;
            font-weight: 500;
        }
        
        .compra-mensaje.exito {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        
        .compra-mensaje.error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        /* Botón de volver */
        .back-btn {
            display: inline-block;
            margin: 20px;
            padding: 10px 25px;
            background: white;
            color: #8b5cf6;
            border: 2px solid #8b5cf6;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #8b5cf6;
            color: white;
            transform: translateX(-5px);
        }

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
            
            .compras-header h1 {
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

    <!-- Encabezado -->
    <div class="compras-header">
        <h1> Compras Premium</h1>
        <p>Adquiere contenido exclusivo para potenciar tu experiencia</p>
    </div>

    <!-- Mensajes de compra -->
    <?php if ($mensaje_compra): ?>
        <div class="compra-mensaje exito"><?php echo $mensaje_compra; ?></div>
    <?php endif; ?>
    
    <?php if ($error_compra): ?>
        <div class="compra-mensaje error"><?php echo $error_compra; ?></div>
    <?php endif; ?>

    <!-- Si hay un producto seleccionado, mostrar detalle -->
    <?php if ($producto_seleccionado): ?>
        <div class="detalle-producto">
            <h2 style="color: #1f2937; margin-bottom: 20px;"><?php echo htmlspecialchars($producto_seleccionado['nombre']); ?></h2>
            <img src="../assets/img/<?php echo htmlspecialchars($producto_seleccionado['imagen']); ?>" 
                 alt="<?php echo htmlspecialchars($producto_seleccionado['nombre']); ?>"
                 style="width: 100%; max-height: 300px; object-fit: contain; margin-bottom: 20px;"
                 onerror="this.src='../assets/img/placeholder.jpg'">
            <p style="color: #6b7280; margin-bottom: 15px;"><?php echo htmlspecialchars($producto_seleccionado['descripcion']); ?></p>
            <p style="background: #f3f4f6; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <strong>Incluye:</strong> <?php echo htmlspecialchars($producto_seleccionado['incluye']); ?>
            </p>
            <p style="font-size: 32px; font-weight: bold; color: #8b5cf6; margin-bottom: 20px;">
                $<?php echo number_format($producto_seleccionado['precio'], 2); ?>  <!-- CAMBIADO: precio_formato por formato directo -->
            </p>
            <form method="POST">
                <input type="hidden" name="id_contenido" value="<?php echo $producto_seleccionado['id_paquete']; ?>">  <!-- CAMBIADO: id por id_paquete -->
                <button type="submit" name="comprar_contenido" 
                        style="background: #8b5cf6; color: white; border: none; padding: 15px 30px; border-radius: 10px; width: 100%; font-size: 18px; font-weight: bold; cursor: pointer;">
                    Confirmar compra
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Listado de todos los productos premium -->
    <h3 style="text-align: center; margin: 40px 0 20px; color: #1f2937;">Todos los paquetes premium</h3>
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
                    <div class="premium-card-price">$<?php echo number_format($producto['precio'], 2); ?></div>  <!-- CAMBIADO: precio_formato por formato directo -->
                    <a href="compras.php?id=<?php echo $producto['id_paquete']; ?>" class="premium-card-btn">  <!-- CAMBIADO: id por id_paquete -->
                        Ver detalles
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>



</body>
</html>