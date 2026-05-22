<?php
require_once 'conexion.php';

// Desactivar cualquier salida de errores visible que pueda corromper el JSON
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'])) {
    $correo = trim($_POST['correo']);
    
    try {
        // Consultamos si ya existe ese correo en la tabla de usuarios
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo LIMIT 1");
        $stmt->execute([':correo' => $correo]);
        
        if ($stmt->fetch()) {
            // Si el correo ya existe en la BD
            echo json_encode(['repetido' => true]);
        } else {
            // Si el correo está libre para registrarse
            echo json_encode(['repetido' => false]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => true, 'mensaje' => $e->getMessage()]);
    }
    exit();
}

// Si entran de forma incorrecta
echo json_encode(['error' => true, 'mensaje' => 'Petición inválida']);