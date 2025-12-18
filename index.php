<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

iniciarSesion();

// Obtener estadísticas
$estadisticas = obtenerEstadisticasUltimos7Dias();

// Generar diagrama con GD Library (opcional, usa esquema data:)
$diagramaGD = null;
try {
    if (function_exists('imagecreatetruecolor')) {
        $diagramaGD = generarDiagramaBarras($estadisticas);
    }
} catch (Exception $e) {
    error_log('Error generando diagrama: ' . $e->getMessage());
}

// Obtener anuncios recientes
$db = Database::getInstance()->getConnection();
$stmt = $db->query("
    SELECT a.*, u.username, u.nombre_completo,
           (SELECT COUNT(*) FROM fotos WHERE anuncio_id = a.id) as num_fotos
    FROM anuncios a
    JOIN usuarios u ON a.usuario_id = u.id
    WHERE a.activo = 1
    ORDER BY a.fecha_creacion DESC
    LIMIT 12
");
$anuncios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Portal de Anuncios</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container">
        <div class="hero">
            <h1>📊 Portal de Anuncios Clasificados</h1>
            <p>Compra, vende y encuentra lo que necesitas</p>
        </div>

        <!-- Diagrama de barras: Fotos subidas últimos 7 días -->
        <!-- Generado 100% con PHP usando GD Library (secciones 4.1 y 4.2 del documento) -->
        <section class="estadisticas-section">
            <h2>📈 Fotografías subidas en los últimos 7 días</h2>
            
            <div class="chart-gd-container">
                <!-- Usando esquema data: como se explica en el documento (sección 4.2) -->
                <img src="<?= $diagramaGD ?>" alt="Diagrama de barras de fotografías subidas" class="diagram-image">
            </div>
            
            <div class="chart-stats">
                <h3>Resumen:</h3>
                <ul>
                    <?php 
                    $totalFotos = array_sum($estadisticas);
                    foreach ($estadisticas as $fecha => $total): 
                        if ($total > 0):
                    ?>
                        <li>
                            <strong><?= date('d/m/Y (l)', strtotime($fecha)) ?>:</strong> 
                            <?= $total ?> foto<?= $total > 1 ? 's' : '' ?>
                        </li>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </ul>
                <p class="total-fotos"><strong>Total últimos 7 días: <?= $totalFotos ?> fotografías</strong></p>
            </div>
        </section>

        <!-- Anuncios recientes -->
        <section class="anuncios-section">
            <h2>🆕 Anuncios Recientes</h2>
            <div class="anuncios-grid">
                <?php foreach ($anuncios as $anuncio): ?>
                    <div class="anuncio-card">
                        <div class="anuncio-imagen">
                            <?php if ($anuncio['imagen_principal']): ?>
                                <?php 
                                    $rutaMiniatura = str_replace('uploads/fotos/', 'uploads/miniaturas/', $anuncio['imagen_principal']);
                                ?>
                                <img src="<?= BASE_URL . $rutaMiniatura ?>" alt="<?= e($anuncio['titulo']) ?>">
                            <?php else: ?>
                                <div class="sin-imagen">📷 Sin imagen</div>
                            <?php endif; ?>
                        </div>
                        <div class="anuncio-info">
                            <h3><?= e($anuncio['titulo']) ?></h3>
                            <p class="precio"><?= formatearPrecio($anuncio['precio']) ?></p>
                            <p class="usuario">Por: <?= e($anuncio['nombre_completo'] ?? $anuncio['username']) ?></p>
                            <p class="fotos-count">📷 <?= $anuncio['num_fotos'] ?> fotos</p>
                            <a href="<?= BASE_URL ?>public/detalle_anuncio.php?id=<?= $anuncio['id'] ?>" class="btn-ver">Ver detalles</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
