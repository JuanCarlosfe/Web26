<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

header("Location: admin.php?tab=usuarios");
exit();