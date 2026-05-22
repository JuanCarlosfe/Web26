<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['item-id']) ? intval($_POST['item-id']) : null;
    $nombre = trim($_POST['item-name']);
    $categoria = trim($_POST['item-type']);
    $precio = floatval($_POST['item-price']);
    $estado = trim($_POST['item-status']);
    $descripcion = trim($_POST['item-description']);
    
    // Gestión de la Imagen
    $nombre_imagen = null;
    if (isset($_FILES['item-image']) && $_FILES['item-image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['item-image']['tmp_name'];
        $fileName = $_FILES['item-image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Sanitizar el nombre del archivo para evitar problemas
        $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
        
        // Crear directorio si no existe
        $uploadFileDir = './uploads/';
        if(!is_dir($uploadFileDir)){
            mkdir($uploadFileDir, 0755, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $nombre_imagen = $newFileName;
        }
    }

    if ($id === null) {
        // --- ALTA (INSERT) ---
        $img_final = $nombre_imagen ? $nombre_imagen : 'default.jpg';
        $sql = "INSERT INTO habitaciones (nombre, categoria, precio, estado, descripcion, imagen) 
                VALUES (:nombre, :categoria, :precio, :estado, :descripcion, :imagen)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':categoria' => $categoria,
            ':precio' => $precio,
            ':estado' => $estado,
            ':descripcion' => $descripcion,
            ':imagen' => $img_final
        ]);
    } else {
        // --- MODIFICACIÓN (UPDATE) ---
        if ($nombre_imagen) {
            // Si subió una nueva imagen, actualizamos todo incluyendo la ruta del archivo
            $sql = "UPDATE habitaciones SET nombre = :nombre, categoria = :categoria, precio = :precio, 
                    estado = :estado, descripcion = :descripcion, imagen = :imagen WHERE id = :id";
            $params = [
                ':nombre' => $nombre, ':categoria' => $categoria, ':precio' => $precio,
                ':estado' => $estado, ':descripcion' => $descripcion, ':imagen' => $nombre_imagen, ':id' => $id
            ];
        } else {
            // Si no subió imagen, mantenemos la que ya estaba
            $sql = "UPDATE habitaciones SET nombre = :nombre, categoria = :categoria, precio = :precio, 
                    estado = :estado, descripcion = :descripcion WHERE id = :id";
            $params = [
                ':nombre' => $nombre, ':categoria' => $categoria, ':precio' => $precio,
                ':estado' => $estado, ':descripcion' => $descripcion, ':id' => $id
            ];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    // Redireccionar de vuelta al panel de administración
    header("Location: admin.php");
    exit();
}
?>