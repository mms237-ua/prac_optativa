<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

// Requerir autenticación
requerirAutenticacion();

$anuncioId = $_GET['id'] ?? 0;
$usuarioId = getUsuarioId();

// Verificar que el anuncio existe y pertenece al usuario
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM anuncios WHERE id = ? AND usuario_id = ?");
$stmt->execute([$anuncioId, $usuarioId]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    redirect(BASE_URL . 'private/mis_anuncios.php');
}

// Paginación
$paginaActual = obtenerPaginaActual();
$itemsPorPagina = ITEMS_POR_PAGINA;
$offset = calcularOffset($paginaActual, $itemsPorPagina);

// Obtener total de fotos
$stmtCount = $db->prepare("SELECT COUNT(*) FROM fotos WHERE anuncio_id = ?");
$stmtCount->execute([$anuncioId]);
$totalFotos = $stmtCount->fetchColumn();
$totalPaginas = calcularTotalPaginas($totalFotos, $itemsPorPagina);

// Obtener fotos paginadas
$stmt = $db->prepare("
    SELECT * FROM fotos
    WHERE anuncio_id = ?
    ORDER BY orden ASC, fecha_subida DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $anuncioId, PDO::PARAM_INT);
$stmt->bindValue(2, $itemsPorPagina, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$fotos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotos - <?= e($anuncio['titulo']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>📷 Fotos del Anuncio</h1>
            <p><strong><?= e($anuncio['titulo']) ?></strong></p>
        </div>

        <div class="toolbar">
            <a href="<?= BASE_URL ?>private/mis_anuncios.php" class="btn">← Volver a mis anuncios</a>
            <a href="<?= BASE_URL ?>private/subir_fotos.php?id=<?= $anuncioId ?>" class="btn btn-primary">⬆️ Subir más fotos</a>
        </div>

        <?php if (empty($fotos)): ?>
            <div class="empty-state">
                <p>Este anuncio no tiene fotos todavía</p>
                <a href="<?= BASE_URL ?>private/subir_fotos.php?id=<?= $anuncioId ?>" class="btn btn-primary">Subir fotos</a>
            </div>
        <?php else: ?>
            <p class="info-total">Total: <?= $totalFotos ?> foto(s) | Mostrando <?= ITEMS_POR_PAGINA ?> por página</p>
            
            <div class="fotos-grid">
                <?php foreach ($fotos as $foto): ?>
                    <div class="foto-card">
                        <div class="foto-miniatura">
                            <img src="<?= BASE_URL . $foto['ruta_miniatura'] ?>" 
                                 alt="<?= e($foto['texto_alternativo']) ?>"
                                 title="<?= e($foto['titulo']) ?>">
                        </div>
                        <div class="foto-info">
                            <h4><?= e($foto['titulo']) ?></h4>
                            <p class="fecha-subida">📅 <?= formatearFecha($foto['fecha_subida']) ?></p>
                            <a href="<?= BASE_URL . $foto['ruta_original'] ?>" target="_blank" class="btn btn-small">🔍 Ver original</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="paginacion">
                    <?php if ($paginaActual > 1): ?>
                        <a href="?id=<?= $anuncioId ?>&pagina=1" class="btn-pag">⏮️ Primera</a>
                        <a href="?id=<?= $anuncioId ?>&pagina=<?= $paginaActual - 1 ?>" class="btn-pag">⬅️ Anterior</a>
                    <?php endif; ?>

                    <span class="pagina-info">
                        Página <?= $paginaActual ?> de <?= $totalPaginas ?>
                    </span>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?id=<?= $anuncioId ?>&pagina=<?= $paginaActual + 1 ?>" class="btn-pag">Siguiente ➡️</a>
                        <a href="?id=<?= $anuncioId ?>&pagina=<?= $totalPaginas ?>" class="btn-pag">Última ⏭️</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
