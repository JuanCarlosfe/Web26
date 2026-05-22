<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['user-id']) ? intval($_POST['user-id']) : null;
    $nombre = trim($_POST['user-name']);
    $correo = trim($_POST['user-email']);
    $rol = trim($_POST['user-role']);
    $password_raw = trim($_POST['user-password']);

    if (empty($id)) {
        // --- ALTA (INSERT) ---
        // Ciframos la contraseña obligatoriamente para nuevos usuarios
        $password_cifrada = password_hash($password_raw, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, correo, password, rol) VALUES (:nombre, :correo, :password, :rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':password' => $password_cifrada,
            ':rol' => $rol
        ]);
    } else {
        // --- MODIFICACIÓN (UPDATE) ---
        if (!empty($password_raw)) {
            // Si el admin escribió una nueva contraseña, la ciframos y actualizamos
            $password_cifrada = password_hash($password_raw, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol, password = :password WHERE id = :id";
            $params = [
                ':nombre' => $nombre, ':correo' => $correo, ':rol' => $rol, ':password' => $password_cifrada, ':id' => $id
            ];
        } else {
            // Si dejó la contraseña en blanco, mantenemos la que ya tiene
            $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol WHERE id = :id";
            $params = [
                ':nombre' => $nombre, ':correo' => $correo, ':rol' => $rol, ':id' => $id
            ];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
}

// Redireccionar a la pestaña de administración de usuarios (la añadiremos a admin.php)
header("Location: admin.php?tab=usuarios");
exit();