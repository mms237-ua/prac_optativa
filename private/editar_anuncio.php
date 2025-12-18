<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

// Requerir autenticacion
requerirAutenticacion();

$usuarioId = getUsuarioId();
$anuncioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($anuncioId <= 0) {
    redirect(BASE_URL . 'private/mis_anuncios.php');
}

$db = Database::getInstance()->getConnection();

// Obtener anuncio y validar propiedad
$stmt = $db->prepare('SELECT * FROM anuncios WHERE id = ? AND usuario_id = ?');
$stmt->execute([$anuncioId, $usuarioId]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
    redirect(BASE_URL . 'private/mis_anuncios.php');
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0.0;
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($titulo === '') {
        $error = 'El título es obligatorio';
    } else {
        $stmtUpdate = $db->prepare('UPDATE anuncios SET titulo = ?, descripcion = ?, precio = ?, activo = ? WHERE id = ? AND usuario_id = ?');
        $exito = $stmtUpdate->execute([$titulo, $descripcion, $precio, $activo, $anuncioId, $usuarioId]);

        if ($exito) {
            redirect(BASE_URL . 'private/mis_anuncios.php');
        } else {
            $error = 'No se pudo actualizar el anuncio';
        }
    }

    // Mantener valores enviados si hay error
    $anuncio['titulo'] = $titulo;
    $anuncio['descripcion'] = $descripcion;
    $anuncio['precio'] = $precio;
    $anuncio['activo'] = $activo;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Anuncio</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>✏️ Editar Anuncio</h1>
            <a href="<?= BASE_URL ?>private/mis_anuncios.php" class="btn">← Volver</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php elseif ($mensaje): ?>
            <div class="alert alert-success"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" class="upload-modes">
            <div class="form-group">
                <label for="titulo">Título del anuncio: *</label>
                <input type="text" id="titulo" name="titulo" required value="<?= e($anuncio['titulo']) ?>">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="5"><?= e($anuncio['descripcion']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="precio">Precio (€): *</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required value="<?= e(number_format((float)$anuncio['precio'], 2, '.', '')) ?>">
            </div>

            <div class="form-group form-inline">
                <label for="activo">
                    <input type="checkbox" id="activo" name="activo" value="1" <?= !empty($anuncio['activo']) ? 'checked' : '' ?>>
                    Publicar (activo)
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="<?= BASE_URL ?>private/mis_anuncios.php" class="btn">Cancelar</a>
        </form>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
