<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Opcional: Podrías buscar el nombre de la imagen en la BD y borrar el archivo físico con unlink()
    
    $sql = "DELETE FROM habitaciones WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

header("Location: admin.php");
exit();
?>