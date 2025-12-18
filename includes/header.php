<?php
// Asegurar dependencias cuando se incluye desde páginas públicas
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

if (function_exists('iniciarSesion')) {
    iniciarSesion();
}
?>
<header class="main-header">
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <a href="<?= BASE_URL ?>">🏪 Portal Anuncios</a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?= BASE_URL ?>">Inicio</a></li>
                <?php if (estaAutenticado()): ?>
                    <li><a href="<?= BASE_URL ?>private/mis_anuncios.php">Mis Anuncios</a></li>
                    <li><a href="<?= BASE_URL ?>private/crear_anuncio.php">Crear Anuncio</a></li>
                    <li class="usuario-info">
                        <?php $usuarioId = getUsuarioId(); ?>
                        👤 <a href="<?= BASE_URL ?>public/detalle_usuario.php?id=<?= $usuarioId ?>"><?= e(getNombreUsuario()) ?></a>
                    </li>
                    <li><a href="<?= BASE_URL ?>public/logout.php" class="btn-logout">Salir</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>public/login.php" class="btn-login">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
