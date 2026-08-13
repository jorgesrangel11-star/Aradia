<?php
require_once __DIR__ . '/../config/db.php';



function obtener_paquetes_premium_admin() {
    global $conn;
    
    $sql = "SELECT * FROM paquetes_premium WHERE activo = 1 ORDER BY id_paquete DESC";
    $resultado = mysqli_query($conn, $sql);
    
    $paquetes = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $paquetes[] = $fila;
        }
        mysqli_free_result($resultado);
    }
    
    return $paquetes;
}

function obtener_paquete_por_id($id_paquete) {
    global $conn;
    
    $sql = "SELECT * FROM paquetes_premium WHERE id_paquete = ? AND activo = 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id_paquete);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    $paquete = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    
    return $paquete;
}

function crear_paquete($datos) {
    global $conn;
    
    $sql = "INSERT INTO paquetes_premium (nombre, descripcion, imagen, precio, incluye) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['estatus' => 'error', 'mensaje' => 'Error al preparar la consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, "sssds", 
        $datos['nombre'],
        $datos['descripcion'],
        $datos['imagen'],
        $datos['precio'],
        $datos['incluye']
    );
    
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($ok) {
        return ['estatus' => 'exito', 'mensaje' => 'Paquete creado correctamente'];
    } else {
        return ['estatus' => 'error', 'mensaje' => 'Error al crear el paquete: ' . mysqli_error($conn)];
    }
}

function editar_paquete($id, $datos) {
    global $conn;
    
    $sql = "UPDATE paquetes_premium SET 
            nombre = ?, 
            descripcion = ?, 
            imagen = ?, 
            precio = ?, 
            incluye = ? 
            WHERE id_paquete = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['estatus' => 'error', 'mensaje' => 'Error al preparar la consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, "sssdsi", 
        $datos['nombre'],
        $datos['descripcion'],
        $datos['imagen'],
        $datos['precio'],
        $datos['incluye'],
        $id
    );
    
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($ok) {
        return ['estatus' => 'exito', 'mensaje' => 'Paquete actualizado correctamente'];
    } else {
        return ['estatus' => 'error', 'mensaje' => 'Error al actualizar el paquete'];
    }
}

function eliminar_paquete($id) {
    global $conn;
    
    // Soft delete - solo marcar como inactivo
    $sql = "UPDATE paquetes_premium SET activo = 0 WHERE id_paquete = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['estatus' => 'error', 'mensaje' => 'Error al preparar la consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($ok) {
        return ['estatus' => 'exito', 'mensaje' => 'Paquete eliminado correctamente'];
    } else {
        return ['estatus' => 'error', 'mensaje' => 'Error al eliminar el paquete'];
    }
}

// Función para el catálogo de usuarios (la misma pero con otro nombre para no confundir)
function obtener_paquetes_activos() {
    return obtener_paquetes_premium_admin();
}

// Procesar peticiones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    $accion = $_POST['accion'];
    $redirect = "../public/admin/gestionar_catalogos.php";
    
    switch ($accion) {
        case 'crear':
            $resultado = crear_paquete($_POST);
            break;
            
        case 'editar':
            $resultado = editar_paquete($_POST['id_paquete'], $_POST);
            break;
            
        case 'eliminar':
            $resultado = eliminar_paquete($_POST['id_paquete']);
            break;
            
        default:
            $resultado = ['estatus' => 'error', 'mensaje' => 'Acción no válida'];
    }
    
    $clave = ($resultado['estatus'] === 'exito') ? 'msg' : 'error';
    header("Location: $redirect?$clave=" . urlencode($resultado['mensaje']));
    exit;
}
?>