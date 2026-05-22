<?php
session_start();
require_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    if (!empty($correo) && !empty($password)) {
        // Buscamos al usuario por su correo
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = :correo LIMIT 1");
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        // Verificamos si existe y si la contraseña coincide con el hash de la BD
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Guardamos las variables de sesión del navegador
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            // Redirigimos según su rol
            if ($usuario['rol'] === 'Administrador') {
                header("Location: admin.php");
            } else {
                header("Location: catalogo.php");
            }
            exit();
        } else {
            $error = "El correo o la contraseña son incorrectos.";
        }
    } else {
        $error = "Por favor, llena todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AeroHaven - Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .login-box { max-width: 400px; margin: 100px auto; padding: 30px; }
        .error-msg { background: rgba(255,0,0,0.2); color: #cc0000; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-panel login-box">
            <h2 style="text-align: center; margin-bottom: 20px;"><span class="logo-accent">Aero</span>Haven Login</h2>
            
            <?php if(!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="admin-form">
                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" required placeholder="tu-correo@aerohaven.com">
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-glossy" style="width: 100%; margin-top: 10px;">Ingresar al Sistema</button>
            </form>
            <p style="text-align:center; margin-top:15px; font-size:0.85rem;"><a href="index.html">Volver al Inicio</a></p>
        </div>
    </div>

        <!-- Pie de página -->
    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; 2026 AeroHaven Hotel. Todos los derechos reservados.</p>
            
            <!-- Bloque del Validador -->
            <div class="w3c-validator">
                <a href="https://jigsaw.w3.org/css-validator/check/referer">
                    <img style="border:0;width:88px;height:31px"
                        src="https://jigsaw.w3.org/css-validator/images/vcss"
                        alt="¡CSS Válido!" />
                </a>
            </div>
        </div>
    </footer>
</body>
</html>