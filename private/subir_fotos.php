<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/image_functions.php';

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

$mensaje = '';
$error = '';

// Procesar subida masiva usando $_FILES como se explica en el documento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fotos'])) {
    // $_FILES es un array multidimensional donde cada posición representa un fichero
    $archivos = $_FILES['fotos'];
    $modo = $_POST['modo'] ?? 'simple';
    $titulos = $_POST['titulos'] ?? [];
    $textos_alt = $_POST['textos_alt'] ?? [];
    
    $subidas = 0;
    $errores = 0;
    $erroresDetalle = [];
    
    // Procesar cada archivo subido
    for ($i = 0; $i < count($archivos['name']); $i++) {
        if ($archivos['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $archivos['tmp_name'][$i];
            $nombreOriginal = $archivos['name'][$i];
            
            // Validar imagen (usando nombre original para la extensión)
            if (esImagenValida($tmpName, $nombreOriginal)) {
                $nombreUnico = generarNombreUnico($nombreOriginal);
                // Directorios reales usados por la app
                $dirFotos = UPLOADS_DIR . 'fotos/';
                $dirMini  = UPLOADS_DIR . 'miniaturas/';

                if (!is_dir($dirFotos)) {
                    mkdir($dirFotos, 0777, true);
                }
                if (!is_dir($dirMini)) {
                    mkdir($dirMini, 0777, true);
                }

                $rutaDestino = $dirFotos . $nombreUnico;
                $rutaMiniatura = $dirMini . $nombreUnico;
                
                // Mover archivo original
                if (move_uploaded_file($tmpName, $rutaDestino)) {
                    // Crear miniatura usando imagecopyresampled()
                    $thumbOK = crearMiniatura($rutaDestino, $rutaMiniatura);
                    if ($thumbOK) {
                        // Obtener título y texto alternativo según el modo
                        if ($modo === 'avanzado' && isset($titulos[$i]) && isset($textos_alt[$i])) {
                            $titulo = trim($titulos[$i]);
                            $textoAlt = trim($textos_alt[$i]);
                        } else {
                            $titulo = pathinfo($nombreOriginal, PATHINFO_FILENAME);
                            $textoAlt = $titulo;
                        }
                        
                        $stmtFoto = $db->prepare("
                            INSERT INTO fotos (anuncio_id, nombre_archivo, ruta_original, ruta_miniatura, titulo, texto_alternativo)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmtFoto->execute([
                            $anuncioId,
                            $nombreOriginal,
                            'uploads/fotos/' . $nombreUnico,
                            'uploads/miniaturas/' . $nombreUnico,
                            $titulo,
                            $textoAlt
                        ]);
                        
                        // Si es la primera foto, establecer como imagen principal
                        $stmtCheck = $db->prepare("SELECT imagen_principal FROM anuncios WHERE id = ?");
                        $stmtCheck->execute([$anuncioId]);
                        $imagenPrincipal = $stmtCheck->fetchColumn();
                        
                        if (!$imagenPrincipal) {
                            $stmtUpdate = $db->prepare("UPDATE anuncios SET imagen_principal = ? WHERE id = ?");
                            $stmtUpdate->execute(['uploads/fotos/' . $nombreUnico, $anuncioId]);
                        }
                        
                        $subidas++;
                    } else {
                        $errores++;
                        $erroresDetalle[] = "$nombreOriginal: no se pudo crear miniatura (verificar GD habilitado)";
                    }
                }
            } else {
                $errores++;
                $erroresDetalle[] = "$nombreOriginal: formato inválido o imagen corrupta";
            }
        }
    }
    
    if ($subidas > 0) {
        $mensaje = "✅ Se han subido correctamente $subidas foto(s)";
    }
    if ($errores > 0) {
        $error = "⚠️ $errores archivo(s) no se pudieron subir. " . implode(' | ', $erroresDetalle);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Fotos - <?= e($anuncio['titulo']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>📤 Subida Masiva de Fotos</h1>
            <p>Anuncio: <strong><?= e($anuncio['titulo']) ?></strong></p>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <div class="upload-modes">
            <div class="mode-tabs">
                <button type="button" class="tab-btn active" onclick="cambiarModo('simple')">📋 Modo Simple</button>
                <button type="button" class="tab-btn" onclick="cambiarModo('avanzado')">⚙️ Modo Avanzado</button>
            </div>

            <!-- Modo Simple -->
            <div id="modo-simple" class="upload-mode active">
                <h3>Modo Simple</h3>
                <p>Selecciona las fotos y se les asignará automáticamente el nombre del archivo como título.</p>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="modo" value="simple">
                    
                    <div class="form-group">
                        <label for="fotos-simple">Seleccionar fotos:</label>
                        <input type="file" id="fotos-simple" name="fotos[]" multiple accept="image/jpeg,image/jpg,image/png,image/gif" required>
                        <small>Puedes seleccionar múltiples imágenes (JPG/JPEG, PNG, GIF).</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">⬆️ Subir Fotos</button>
                    <a href="<?= BASE_URL ?>private/mis_anuncios.php" class="btn">Cancelar</a>
                </form>
            </div>

            <!-- Modo Avanzado -->
            <div id="modo-avanzado" class="upload-mode">
                <h3>Modo Avanzado</h3>
                <p>Selecciona las fotos y podrás personalizar el título y texto alternativo de cada una.</p>
                
                <form method="POST" enctype="multipart/form-data" id="form-avanzado">
                    <input type="hidden" name="modo" value="avanzado">
                    
                    <div class="form-group">
                        <label for="fotos-avanzado">Seleccionar fotos:</label>
                        <input type="file" id="fotos-avanzado" name="fotos[]" multiple accept="image/jpeg,image/jpg,image/png,image/gif" required onchange="mostrarCamposAvanzados()">
                        <small>Selecciona las imágenes. Después aparecerán campos para cada una.</small>
                    </div>
                    
                    <div id="campos-avanzados"></div>
                    
                    <div id="botones-avanzado" style="display:none;">
                        <button type="submit" class="btn btn-primary">⬆️ Subir Fotos</button>
                        <a href="<?= BASE_URL ?>private/mis_anuncios.php" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script>
        function cambiarModo(modo) {
            // Cambiar tabs activos
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Cambiar secciones visibles
            document.querySelectorAll('.upload-mode').forEach(section => section.classList.remove('active'));
            document.getElementById('modo-' + modo).classList.add('active');
        }
        
        function mostrarCamposAvanzados() {
            const input = document.getElementById('fotos-avanzado');
            const container = document.getElementById('campos-avanzados');
            const botones = document.getElementById('botones-avanzado');
            
            if (input.files.length === 0) {
                container.innerHTML = '';
                botones.style.display = 'none';
                return;
            }
            
            container.innerHTML = '<h4>Información de las fotos seleccionadas:</h4>';
            
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const nombreSinExt = file.name.replace(/\.[^/.]+$/, '');
                
                const fieldset = document.createElement('fieldset');
                fieldset.className = 'foto-info-item';
                fieldset.innerHTML = `
                    <legend>📷 Foto ${i + 1}: ${file.name}</legend>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="titulos[]" value="${nombreSinExt}" required>
                        </div>
                        <div class="form-group">
                            <label>Texto alternativo:</label>
                            <input type="text" name="textos_alt[]" value="${nombreSinExt}" required>
                        </div>
                    </div>
                `;
                container.appendChild(fieldset);
            }
            
            botones.style.display = 'block';
        }
    </script>
</body>
</html>
