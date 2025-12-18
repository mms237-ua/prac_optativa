<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

// Requerir autenticación
requerirAutenticacion();

$usuarioId = getUsuarioId();

// Paginación
$paginaActual = obtenerPaginaActual();
$itemsPorPagina = ITEMS_POR_PAGINA;
$offset = calcularOffset($paginaActual, $itemsPorPagina);

// Obtener total de anuncios del usuario
$db = Database::getInstance()->getConnection();
$stmtCount = $db->prepare("SELECT COUNT(*) FROM anuncios WHERE usuario_id = ?");
$stmtCount->execute([$usuarioId]);
$totalAnuncios = $stmtCount->fetchColumn();
$totalPaginas = calcularTotalPaginas($totalAnuncios, $itemsPorPagina);

// Obtener anuncios paginados
$stmt = $db->prepare("
    SELECT a.*, 
           (SELECT COUNT(*) FROM fotos WHERE anuncio_id = a.id) as num_fotos
    FROM anuncios a
    WHERE a.usuario_id = ?
    ORDER BY a.fecha_creacion DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $usuarioId, PDO::PARAM_INT);
$stmt->bindValue(2, $itemsPorPagina, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$anuncios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Anuncios</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>📋 Mis Anuncios</h1>
            <a href="<?= BASE_URL ?>private/crear_anuncio.php" class="btn btn-primary">+ Crear Anuncio</a>
        </div>

        <?php if (empty($anuncios)): ?>
            <div class="empty-state">
                <p>No tienes anuncios todavía</p>
                <a href="<?= BASE_URL ?>private/crear_anuncio.php" class="btn btn-primary">Crear tu primer anuncio</a>
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
                                📅 <?= formatearFecha($anuncio['fecha_creacion']) ?> | 
                                📷 <?= $anuncio['num_fotos'] ?> fotos
                            </p>
                        </div>
                        <div class="anuncio-acciones">
                            <a href="<?= BASE_URL ?>private/ver_fotos.php?id=<?= $anuncio['id'] ?>" class="btn btn-small">📷 Ver fotos</a>
                            <a href="<?= BASE_URL ?>private/subir_fotos.php?id=<?= $anuncio['id'] ?>" class="btn btn-small">⬆️ Subir fotos</a>
                            <a href="<?= BASE_URL ?>private/editar_anuncio.php?id=<?= $anuncio['id'] ?>" class="btn btn-small">✏️ Editar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="paginacion">
                    <!-- Primera página -->
                    <?php if ($paginaActual > 1): ?>
                        <a href="?pagina=1" class="btn-pag">⏮️ Primera</a>
                        <a href="?pagina=<?= $paginaActual - 1 ?>" class="btn-pag">⬅️ Anterior</a>
                    <?php endif; ?>

                    <!-- Números de página -->
                    <span class="pagina-info">
                        Página <?= $paginaActual ?> de <?= $totalPaginas ?>
                    </span>

                    <!-- Última página -->
                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?pagina=<?= $paginaActual + 1 ?>" class="btn-pag">Siguiente ➡️</a>
                        <a href="?pagina=<?= $totalPaginas ?>" class="btn-pag">Última ⏭️</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
