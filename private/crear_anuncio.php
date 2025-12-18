<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

// Requerir autenticación
requerirAutenticacion();

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    
    if (empty($titulo)) {
        $error = 'El título es obligatorio';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO anuncios (usuario_id, titulo, descripcion, precio)
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt->execute([getUsuarioId(), $titulo, $descripcion, $precio])) {
            $nuevoId = $db->lastInsertId();
            redirect(BASE_URL . 'private/subir_fotos.php?id=' . $nuevoId);
        } else {
            $error = 'Error al crear el anuncio';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Anuncio</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>➕ Crear Nuevo Anuncio</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="upload-modes">
            <form method="POST">
                <div class="form-group">
                    <label for="titulo">Título del anuncio: *</label>
                    <input type="text" id="titulo" name="titulo" required 
                           placeholder="Ej: iPhone 13 Pro Max">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" 
                              placeholder="Describe tu artículo..."></textarea>
                </div>

                <div class="form-group">
                    <label for="precio">Precio (€): *</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" required
                           placeholder="0.00">
                </div>

                <button type="submit" class="btn btn-primary">Crear Anuncio</button>
                <a href="<?= BASE_URL ?>private/mis_anuncios.php" class="btn">Cancelar</a>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
