<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/gestor_paquetes.php'; // Importamos las funciones de paquetes


function obtener_contenido_premium() {
    // Usamos la misma función del gestor de paquetes
    $paquetes = obtener_paquetes_premium_admin();
    
    // Agregamos el formato del precio para mostrarlo bonito
    foreach ($paquetes as &$paquete) {
        $paquete['precio_formato'] = '$' . number_format($paquete['precio'], 2);
    }
    
    return $paquetes;
}

function procesar_compra($id_usuario, $id_contenido) {
    global $conn;
    
    // Verificar que el usuario existe
    $sql_usuario = "SELECT id_usr FROM usuario WHERE id_usr = ?";
    $stmt = mysqli_prepare($conn, $sql_usuario);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($resultado) === 0) {
        return [
            'exito' => false,
            'mensaje' => 'Usuario no encontrado'
        ];
    }
    
    // Obtener precio del paquete
    $sql_precio = "SELECT precio FROM paquetes_premium WHERE id_paquete = ? AND activo = 1";
    $stmt = mysqli_prepare($conn, $sql_precio);
    mysqli_stmt_bind_param($stmt, "i", $id_contenido);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $paquete = mysqli_fetch_assoc($resultado);
    
    if (!$paquete) {
        return [
            'exito' => false,
            'mensaje' => 'Paquete no encontrado'
        ];
    }
    
    // Registrar la compra
    $sql = "INSERT INTO compras (id_usuario, id_paquete, monto) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iid", $id_usuario, $id_contenido, $paquete['precio']);
    
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($ok) {
        return [
            'exito' => true,
            'mensaje' => '¡Compra realizada con éxito! El contenido estará disponible en tu aplicación móvil en unos minutos.'
        ];
    } else {
        return [
            'exito' => false,
            'mensaje' => 'Error al procesar la compra'
        ];
    }
}

function obtener_compras_usuario($id_usuario) {
    global $conn;
    
    $sql = "SELECT c.*, p.nombre as producto, p.imagen 
            FROM compras c
            JOIN paquetes_premium p ON c.id_paquete = p.id_paquete
            WHERE c.id_usuario = ?
            ORDER BY c.fecha_compra DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    $compras = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $compras[] = $fila;
    }
    
    mysqli_stmt_close($stmt);
    return $compras;
}

function obtener_todas_las_compras() {
    global $conn;
    
    $sql = "SELECT c.*, u.nom_usr, u.mail, p.nombre as producto 
            FROM compras c
            JOIN usuario u ON c.id_usuario = u.id_usr
            JOIN paquetes_premium p ON c.id_paquete = p.id_paquete
            ORDER BY c.fecha_compra DESC";
    
    $resultado = mysqli_query($conn, $sql);
    
    $compras = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $compras[] = [
                'id' => $fila['id_compra'],
                'fecha' => $fila['fecha_compra'],
                'usuario' => $fila['nom_usr'],
                'email' => $fila['mail'],
                'producto' => $fila['producto'],
                'monto' => $fila['monto']
            ];
        }
        mysqli_free_result($resultado);
    }
    
    return $compras;
}

function obtener_estadisticas_compras() {
    global $conn;
    
    $stats = [
        'total_compras' => 0,
        'total_ingresos' => 0,
        'usuarios_unicos' => 0,
        'compra_promedio' => 0
    ];
    
    // Total de compras
    $sql = "SELECT COUNT(*) as total FROM compras";
    $resultado = mysqli_query($conn, $sql);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $stats['total_compras'] = $fila['total'];
    }
    
    // Total de ingresos
    $sql = "SELECT SUM(monto) as total FROM compras";
    $resultado = mysqli_query($conn, $sql);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $stats['total_ingresos'] = $fila['total'] ?? 0;
    }
    
    // Usuarios únicos que compraron
    $sql = "SELECT COUNT(DISTINCT id_usuario) as total FROM compras";
    $resultado = mysqli_query($conn, $sql);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $stats['usuarios_unicos'] = $fila['total'];
    }
    
    // Compra promedio
    if ($stats['total_compras'] > 0) {
        $stats['compra_promedio'] = round($stats['total_ingresos'] / $stats['total_compras'], 2);
    }
    
    return $stats;
}

function filtrar_compras($compras, $fecha = '', $usuario = '') {
    if (empty($fecha) && empty($usuario)) {
        return $compras;
    }
    
    return array_filter($compras, function($compra) use ($fecha, $usuario) {
        $cumple_fecha = true;
        $cumple_usuario = true;
        
        if ($fecha) {
            $fecha_compra = date('Y-m-d', strtotime($compra['fecha']));
            $cumple_fecha = ($fecha_compra === $fecha);
        }
        
        if ($usuario) {
            $cumple_usuario = (stripos($compra['usuario'], $usuario) !== false || 
                              stripos($compra['email'], $usuario) !== false);
        }
        
        return $cumple_fecha && $cumple_usuario;
    });
}
?>