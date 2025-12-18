
<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

$usuarioId = $_GET['id'] ?? 0;

// Obtener información del usuario
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuarioId]);
$usuario = $stmt->fetch();

if (!$usuario) {
    redirect(BASE_URL);
}

// Obtener anuncios del usuario
$stmt = $db->prepare("
    SELECT a.*,
           (SELECT COUNT(*) FROM fotos WHERE anuncio_id = a.id) as num_fotos
    FROM anuncios a
    WHERE a.usuario_id = ? AND a.activo = 1
    ORDER BY a.fecha_creacion DESC
");
$stmt->execute([$usuarioId]);
$anuncios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncios de <?= e($usuario['nombre_completo'] ?? $usuario['username']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>👤 <a href="<?= BASE_URL ?>public/detalle_usuario.php?id=<?= $usuario['id'] ?>"><?= e($usuario['nombre_completo'] ?? $usuario['username']) ?></a></h1>
            <a href="<?= BASE_URL ?>" class="btn">← Volver al inicio</a>
        </div>

        <?php if (empty($anuncios)): ?>
            <div class="empty-state">
                <p>Este usuario no tiene anuncios publicados</p>
            </div>
        <?php else: ?>
            <div class="mis-anuncios-lista">
                <?php foreach ($anuncios as $anuncio): ?>
                    <div class="anuncio-item">
                        <div class="anuncio-miniatura">
                            <?php if ($anuncio['imagen_principal']): ?>
                                <?php 
                                    $rutaMiniatura = str_replace('uploads/fotos/', 'uploads/miniaturas/', $anuncio['imagen_principal']);
                                ?>
                                <img src="<?= BASE_URL . $rutaMiniatura ?>" alt="<?= e($anuncio['titulo']) ?>">
                            <?php else: ?>
                                <div class="sin-imagen-small">📷</div>
                            <?php endif; ?>
                        </div>
                        <div class="anuncio-detalles">
                            <h3><?= e($anuncio['titulo']) ?></h3>
                            <p class="descripcion"><?= e(substr($anuncio['descripcion'], 0, 100)) ?>...</p>
                            <p class="precio"><?= formatearPrecio($anuncio['precio']) ?></p>
                            <p class="meta">
                                📅 <?= formatearFecha($anuncio['fecha_creacion']) ?> | 📷 <?= $anuncio['num_fotos'] ?> fotos
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
