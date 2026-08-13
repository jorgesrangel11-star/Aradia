<?php
@session_start();

require_once __DIR__ . '/../config/db.php';



function mostrar_pictogramas()
{
    global $conn;

    $sql = "SELECT * FROM pictogramas";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return [];
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $pictos = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $pictos[] = $fila;
        }
        mysqli_free_result($resultado);
    }

    mysqli_stmt_close($stmt);
    return $pictos;
}

function mostrar_pictogramas_activos()
{
    global $conn;

    $sql = "SELECT * FROM pictogramas WHERE estatus = 1 LIMIT 8";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return [];
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $pictos = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $pictos[] = $fila;
        }
        mysqli_free_result($resultado);
    }

    mysqli_stmt_close($stmt);
    return $pictos;
}

function obtener_pictograma_por_id($id_pic)
{
    global $conn;

    $sql = "SELECT * FROM pictogramas WHERE id_pic = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_pic);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $fila = mysqli_fetch_assoc($resultado);
    mysqli_free_result($resultado);
    mysqli_stmt_close($stmt);

    return $fila;
}

function agregar_pictograma($nombre, $descripcion, $imagen, $estatus)
{
    global $conn;

    $sql = "INSERT INTO pictogramas (nombre, descripcion, imagen, estatus)
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error al preparar la consulta.'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sssi",
        $nombre,
        $descripcion,
        $imagen,
        $estatus
    );

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        return [
            'estatus' => 'exito',
            'mensaje' => 'Pictograma agregado correctamente.'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error al agregar el pictograma.'
        ];
    }
}

function editar_pictograma($id_pic, $nombre, $descripcion, $imagen, $estatus)
{
    global $conn;

    $sql = "UPDATE pictogramas
            SET nombre = ?, descripcion = ?, imagen = ?, estatus = ?
            WHERE id_pic = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error al preparar la consulta de actualización.'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sssii",
        $nombre,
        $descripcion,
        $imagen,
        $estatus,
        $id_pic
    );

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        return [
            'estatus' => 'exito',
            'mensaje' => 'Pictograma actualizado correctamente.'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error al actualizar el pictograma.'
        ];
    }
}

function eliminar_pictograma($id_pic)
{
    global $conn;

    $sql = "DELETE FROM pictogramas WHERE id_pic = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [
            'estatus' => 'error',
            'mensaje' => 'Error al preparar la eliminación.'
        ];
    }

    mysqli_stmt_bind_param($stmt, "i", $id_pic);
    $ok = mysqli_stmt_execute($stmt);
    $filas = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($ok && $filas > 0) {
        return [
            'estatus' => 'exito',
            'mensaje' => 'Pictograma eliminado correctamente.'
        ];
    } else {
        return [
            'estatus' => 'error',
            'mensaje' => 'No se eliminó ningún registro.'
        ];
    }
}

/* ================================
   CONTROLADOR SENCILLO (POST)
   ================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['accion'])) {
        header("Location: ../public/admin/catalogo_pictogramas.php?error=" . urlencode('Acción no especificada.'));
        exit;
    }

    $accion = $_POST['accion'];

    switch ($accion) {
        case 'agregar':
            if (isset($_POST['nombre'], $_POST['descripcion'], $_POST['imagen'])) {
                $nombre = trim($_POST['nombre']);
                $descripcion = trim($_POST['descripcion']);
                $imagen = trim($_POST['imagen']);
                $estatus = 1; // activo por defecto

                $res = agregar_pictograma($nombre, $descripcion, $imagen, $estatus);
                $clave = ($res['estatus'] === 'exito') ? 'msg' : 'error';

                header("Location: ../public/admin/catalogo_pictogramas.php?$clave=" . urlencode($res['mensaje']));
                exit;
            } else {
                header("Location: ../public/admin/catalogo_pictograma.php?error=" . urlencode('Faltan datos.'));
                exit;
            }

        case 'editar':
            if (isset($_POST['id_pic'], $_POST['nombre'], $_POST['descripcion'], $_POST['imagen'], $_POST['estatus'])) {
                $id_pic = (int) $_POST['id_pic'];
                $nombre = trim($_POST['nombre']);
                $descripcion = trim($_POST['descripcion']);
                $imagen = trim($_POST['imagen']);
                $estatus = (int) $_POST['estatus'];

                $res = editar_pictograma($id_pic, $nombre, $descripcion, $imagen, $estatus);
                $clave = ($res['estatus'] === 'exito') ? 'msg' : 'error';

                header("Location: ../public/admin/catalogo_pictogramas.php?$clave=" . urlencode($res['mensaje']));
                exit;
            } else {
                header("Location: ../public/admin/catalogo_pictogramas.php?error=" . urlencode('Faltan datos para editar.'));
                exit;
            }

        case 'eliminar':
            if (isset($_POST['id_pic'])) {
                $id_pic = (int) $_POST['id_pic'];

                $res = eliminar_pictograma($id_pic);
                $clave = ($res['estatus'] === 'exito') ? 'msg' : 'error';

                header("Location: ../public/admin/catalogo_pictogramas.php?$clave=" . urlencode($res['mensaje']));
                exit;
            } else {
                header("Location: ../public/admin/catalogo_pictogramas.php?error=" . urlencode('No se recibió id_pic.'));
                exit;
            }

        default:
            header("Location: ../public/admin/catalogo_pictogramas.php?error=" . urlencode('Acción no reconocida.'));
            exit;
    }
}
