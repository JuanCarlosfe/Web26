<?php
// Iniciar el manejo de sesiones para comprobar quién está navegando
session_start();

// CONTROL DE ACCESO: Si la sesión no existe o el rol guardado NO es 'Administrador',
// denegamos el acceso inmediatamente y lo redirigimos al inicio.
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    // Puedes redirigirlo a tu index o a un archivo login.php cuando lo crees
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';
// Consultar todas las habitaciones de la base de datos
$stmt = $pdo->query("SELECT * FROM habitaciones ORDER BY id DESC");
$habitaciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroHaven - Panel de Administración</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Barra de navegación del Administrador -->
    <header class="main-header admin-header">
        <div class="logo">
            <span class="logo-accent">Aero</span>Haven <small class="badge-admin">Admin</small>
        </div>
        <nav class="nav-menu">
            <ul>
                <li><a href="catalogo.php" target="_blank">Ver Catálogo</a></li>
                <li><a href="#" class="active">Control de Habitaciones</a></li>
                <li><a href="logout.php" class="logout-link">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        
        <!-- FORMULARIO DE ALTA Y MODIFICACIÓN -->
        <!-- Nota: El id "form-title" cambiará dinámicamente mediante backend/JS a "Modificar Habitación" cuando sea necesario -->
        <section class="card-glass admin-form-section">
            <h2 id="form-title" class="admin-title">Registrar Nueva Habitación o Servicio</h2>
            <p class="admin-subtitle">Completa los campos para actualizar el catálogo en tiempo real.</p>
            
            <form class="admin-form" action="guardar.php" method="POST" enctype="multipart/form-data">
                <!-- ID Oculto: Crucial para saber qué habitación modificar o eliminar en la Base de Datos -->
                <input type="hidden" id="item-id" name="item-id" value="">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="item-name">Nombre de la Habitación / Servicio</label>
                        <input type="text" id="item-name" name="item-name" placeholder="Ej. Suite Aqua Premium" required>
                    </div>

                    <div class="form-group">
                        <label for="item-type">Categoría</label>
                        <select id="item-type" name="item-type" required>
                            <option value="habitacion-estandar">Habitación Estándar</option>
                            <option value="habitacion-premium">Suite Premium</option>
                            <option value="servicio-spa">Servicio de Spa / Bienestar</option>
                            <option value="servicio-tour">Servicio de Experiencia / Tour</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="item-price">Precio por Noche / Sesión (USD)</label>
                        <input type="number" id="item-price" name="item-price" placeholder="Ej. 150" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="item-status">Estado Inicial</label>
                        <select id="item-status" name="item-status">
                            <option value="disponible">Disponible</option>
                            <option value="mantenimiento">En Mantenimiento</option>
                            <option value="ocupada">Ocupada</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="item-description">Descripción del Servicio o Habitación</label>
                        <textarea id="item-description" name="item-description" rows="3" placeholder="Describe las amenidades, tecnología Aero instalada o inclusiones del servicio..." required></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="item-image" class="file-label">Subir Imagen Vibrante (Formatos aceptados: JPG, PNG)</label>
                        <input type="file" id="item-image" name="item-image" accept="image/*">
                    </div>
                </div>

                <div class="form-actions">
                    <!-- Botón dinámico principal -->
                    <button type="submit" class="btn-glossy">Guardar Cambios</button>
                    <!-- Botón para cancelar en caso de estar en modo modificación -->
                    <button type="reset" class="btn-secondary" id="btn-cancelar">Limpiar Formulario</button>
                </div>
            </form>
        </section>

        <section class="card-glass table-section">
            <h2 class="admin-title">Inventario Actual de Productos y Servicios</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen Preview</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($habitaciones as $hab): ?>
                        <tr>
                            <td><strong>#<?php echo $hab['id']; ?></strong></td>
                            <td>
                                <?php if($hab['imagen'] !== 'default.jpg' && file_exists("uploads/".$hab['imagen'])): ?>
                                    <img src="uploads/<?php echo $hab['imagen']; ?>" style="width:60px; height:40px; object-fit:cover; border-radius:8px;">
                                <?php else: ?>
                                    <div class="table-img-preview suite-aqua"></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($hab['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($hab['categoria']); ?></td>
                            <td>$<?php echo $hab['precio']; ?> / noche</td>
                            <td>
                                <span class="status-badge <?php echo ($hab['estado'] == 'disponible') ? 'disponible' : 'alert'; ?>">
                                    <?php echo htmlspecialchars($hab['estado']); ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-action edit" 
                                        data-id="<?php echo $hab['id']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($hab['nombre']); ?>"
                                        data-categoria="<?php echo $hab['categoria']; ?>"
                                        data-precio="<?php echo $hab['precio']; ?>"
                                        data-estado="<?php echo $hab['estado']; ?>"
                                        data-descripcion="<?php echo htmlspecialchars($hab['descripcion']); ?>">✏️ Editar</button>
                                
                                <a href="eliminar.php?id=<?php echo $hab['id']; ?>" 
                                   class="btn-action delete" 
                                   style="text-decoration:none;"
                                   onclick="return confirm('¿Seguro que deseas eliminar permanentemente este producto/servicio?')">🗑️ Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <hr style="border: 0; height: 1px; background: var(--glass-border); margin: 40px 0;">

        <section class="card-glass admin-form-section" id="seccion-usuarios">
            <h2 id="user-form-title" class="admin-title">Registrar Nuevo Usuario</h2>
            <p class="admin-subtitle">Gestiona las cuentas de acceso, roles y contraseñas cifradas.</p>

            <form action="guardar_usuario.php" method="POST" class="admin-form">
                <input type="hidden" id="user-id" name="user-id">

                <div class="form-group">
                    <label for="user-name">Nombre Completo:</label>
                    <input type="text" id="user-name" name="user-name" required placeholder="Ej. Ana Martínez">
                </div>

                <div class="form-group">
                    <label for="user-email">Correo Electrónico:</label>
                    <input type="email" id="user-email" name="user-email" required placeholder="ejemplo@aerohaven.com">
                </div>

                <div class="form-group">
                    <label for="user-password">Contraseña:</label>
                    <input type="password" id="user-password" name="user-password" placeholder="Escribe una contraseña segura">
                    <small style="color: var(--text-dark); opacity: 0.7; display:block; margin-top:4px;">
                        (Dejar en blanco al editar si no deseas cambiarla)
                    </small>
                </div>

                <div class="form-group">
                    <label for="user-role">Rol / Tipo de Usuario:</label>
                    <select id="user-role" name="user-role" required>
                        <option value="Cliente">Cliente</option>
                        <option value="Recepcionista">Recepcionista</option>
                        <option value="Administrador">Administrador</option>
                    </select>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-glossy">Guardar Usuario</button>
                    <button type="button" id="btn-cancelar-usuario" class="btn-action">Limpiar</button>
                </div>
            </form>
        </section>

        <section class="card-glass admin-table-section" style="margin-top: 30px;">
            <h3 class="admin-title">Usuarios en el Sistema</h3>
            
            <?php
            // Consultar los usuarios existentes
            $stmt_users = $pdo->query("SELECT id, nombre, correo, rol FROM usuarios ORDER BY id DESC");
            $usuarios_lista = $stmt_users->fetchAll();
            ?>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios_lista as $user): ?>
                        <tr>
                            <td><strong>#<?php echo $user['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($user['correo']); ?></td>
                            <td>
                                <span class="status-badge <?php echo ($user['rol'] == 'Administrador') ? 'alert' : 'disponible'; ?>">
                                    <?php echo $user['rol']; ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-action edit edit-user" 
                                        data-id="<?php echo $user['id']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($user['nombre']); ?>"
                                        data-correo="<?php echo htmlspecialchars($user['correo']); ?>"
                                        data-rol="<?php echo $user['rol']; ?>">✏️ Editar</button>
                                
                                <a href="eliminar_usuario.php?id=<?php echo $user['id']; ?>" 
                                   class="btn-action delete" 
                                   style="text-decoration:none;"
                                   onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">🗑️ Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

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