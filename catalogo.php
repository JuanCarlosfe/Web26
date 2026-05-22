<?php 
require_once 'conexion.php';
$stmt = $pdo->query("SELECT * FROM habitaciones ORDER BY id DESC");
$habitaciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroHaven Hotel - Catálogo de Habitaciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Encabezado (Mantener idéntico al de inicio para consistencia) -->
    <header class="main-header">
        <div class="logo">
            <span class="logo-accent">Aero</span>Haven
        </div>
        <nav class="nav-menu">
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="catalogo.php" class="active">Catálogo</a></li>
                <li><a href="index.html#nosotros">Nosotros</a></li>
                <li><a href="index.html#contacto">Contacto</a></li>

                <?php
                // Iniciamos sesión en la cabecera si no se ha iniciado antes
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                // Si hay una sesión activa, mostramos opciones de cuenta
                if (isset($_SESSION['usuario_rol'])): 
                    if ($_SESSION['usuario_rol'] === 'Administrador'): ?>
                        <li><a href="admin.php" style="color: #00bcff; font-weight: bold;">Panel Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="logout-link" style="color: #ff4d4d;">Salir</a></li>
                <?php else: ?>
                    <li><a href="login.php" style="color: #3aff78; font-weight: bold;">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Encabezado del Catálogo -->
    <section class="catalog-hero">
        <h1>Nuestras Suites Eco-Digitales</h1>
        <p>Espacios diseñados con la armonía de la naturaleza y la tecnología del mañana.</p>
    </section>

    <!-- Contenedor Principal del Catálogo -->
    <main class="container">
    <div class="rooms-grid">
        
        <?php if(empty($habitaciones)): ?>
            <p style="text-align:center; grid-column: 1/-1;">Por el momento no hay suites disponibles.</p>
        <?php endif; ?>

        <?php foreach ($habitaciones as $hab): ?>
        <article class="room-card">
            <div class="room-image-wrapper">
                <?php if($hab['imagen'] !== 'default.jpg' && file_exists("uploads/".$hab['imagen'])): ?>
                    <img src="uploads/<?php echo $hab['imagen']; ?>" style="width:100%; height:100%; object-fit:cover;">
                <?php else: ?>
                    <div class="room-image-placeholder suite-biosphere"></div>
                <?php endif; ?>
                <span class="room-tag"><?php echo htmlspecialchars($hab['estado']); ?></span>
            </div>
            <div class="room-info">
                <h3><?php echo htmlspecialchars($hab['nombre']); ?></h3>
                <p style="display:none;"><?php echo htmlspecialchars($hab['categoria']); ?></p>
                <p class="room-description"><?php echo htmlspecialchars($hab['descripcion']); ?></p>
                
                <div class="room-features">
                    <span><i class="feature-icon">🌿</i> Eco-Aero</span>
                    <span><i class="feature-icon">🌐</i> Wi-Fi 7</span>
                </div>

                <div class="room-footer">
                    <span class="room-price">$<?php echo $hab['price'] ?? $hab['precio']; ?> <small>/ noche</small></span>
                    <a href="#" class="btn-glossy btn-small">Reservar</a>
                </div>
            </div>
        </article>
        <?php endforeach; ?>

    </div>
</main>

    <!-- Footer -->
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Tu propio archivo de lógica dinámica -->
    <script src="main.js"></script>

</body>
</html>