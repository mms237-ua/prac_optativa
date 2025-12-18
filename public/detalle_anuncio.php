<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

$anuncioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($anuncioId <= 0) {
    header('Location: ' . BASE_URL);
    exit;
}

$db = Database::getInstance()->getConnection();

// Obtener anuncio con datos del usuario
$stmt = $db->prepare("SELECT a.*, u.username, u.nombre_completo, u.email
                      FROM anuncios a
                      JOIN usuarios u ON u.id = a.usuario_id
                      WHERE a.id = ?");
$stmt->execute([$anuncioId]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body><h1>404 - Anuncio no encontrado</h1><p><a href="' . BASE_URL . '">Volver al inicio</a></p></body></html>';
    exit;
}

// Obtener fotos del anuncio
$stmtFotos = $db->prepare("SELECT * FROM fotos WHERE anuncio_id = ? ORDER BY orden ASC, fecha_subida DESC");
$stmtFotos->execute([$anuncioId]);
$fotos = $stmtFotos->fetchAll();

$imagenPrincipal = $anuncio['imagen_principal'] ?: ($fotos[0]['ruta_original'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($anuncio['titulo']) ?> - Detalle</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>📰 <?= e($anuncio['titulo']) ?></h1>
            <p class="muted">Publicado: <?= formatearFecha($anuncio['fecha_creacion']) ?></p>
        </div>

        <div class="detalle-anuncio">
            <div class="detalle-media">
                <?php if ($imagenPrincipal): ?>
                    <img class="principal" src="<?= BASE_URL . $imagenPrincipal ?>" alt="<?= e($anuncio['titulo']) ?>">
                <?php else: ?>
                    <div class="empty-state">Sin imagen</div>
                <?php endif; ?>

                <?php if ($fotos && count($fotos) > 1): ?>
                    <div class="galeria-thumbs">
                        <?php foreach ($fotos as $foto): ?>
                            <img src="<?= BASE_URL . ($foto['ruta_miniatura'] ?: $foto['ruta_original']) ?>" 
                                 alt="<?= e($foto['texto_alternativo'] ?: $foto['titulo']) ?>"
                                 title="<?= e($foto['titulo']) ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detalle-info">
                <p class="precio"><?= $anuncio['precio'] !== null ? formatearPrecio($anuncio['precio']) : 'Precio no indicado' ?></p>
                <p><?= nl2br(e($anuncio['descripcion'])) ?></p>
                <div class="autor">
                    <p><strong>Vendedor:</strong> <?= e($anuncio['nombre_completo'] ?: $anuncio['username']) ?></p>
                    <p><strong>Contacto:</strong> <?= e($anuncio['email']) ?></p>
                </div>
            </div>
        </div>

        <div class="toolbar">
            <a class="btn" href="<?= BASE_URL ?>">← Volver al inicio</a>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
