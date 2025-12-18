<?php
/**
 * Funciones auxiliares para tratamiento de imágenes con GD Library
 * Basado en las técnicas del documento de la asignatura
 */

/**
 * Crear miniatura de una imagen usando imagecopyresampled()
 * Como se explica en la sección 4.3 del documento
 */
function crearMiniatura($rutaOriginal, $rutaDestino, $ancho = THUMBNAIL_WIDTH, $alto = THUMBNAIL_HEIGHT, $calidad = THUMBNAIL_QUALITY) {
    // Si GD no está habilitado, abortar sin fatal error
    if (!function_exists('imagecreatetruecolor') || !function_exists('imagecreatefromjpeg')) {
        return false;
    }

    // Verificar que el archivo existe
    if (!file_exists($rutaOriginal)) {
        return false;
    }

    // Obtener información de la imagen usando getimagesize()
    $info = getimagesize($rutaOriginal);
    if ($info === false) {
        return false;
    }

    list($anchoOriginal, $altoOriginal, $tipo) = $info;

    // Crear recurso de imagen según el tipo usando las funciones específicas
    // imagecreatefromjpeg(), imagecreatefrompng(), imagecreatefromgif()
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagenOriginal = imagecreatefromjpeg($rutaOriginal);
            break;
        case IMAGETYPE_PNG:
            $imagenOriginal = imagecreatefrompng($rutaOriginal);
            break;
        case IMAGETYPE_GIF:
            $imagenOriginal = imagecreatefromgif($rutaOriginal);
            break;
        default:
            return false;
    }

    if (!$imagenOriginal) {
        return false;
    }

    // Calcular dimensiones manteniendo proporción
    $proporcion = $anchoOriginal / $altoOriginal;
    
    if ($ancho / $alto > $proporcion) {
        $ancho = (int)round($alto * $proporcion);
    } else {
        $alto = (int)round($ancho / $proporcion);
    }

    // Crear imagen de destino usando imagecreatetruecolor()
    $miniatura = imagecreatetruecolor($ancho, $alto);

    // Preservar transparencia para PNG y GIF
    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
        imagealphablending($miniatura, false);
        imagesavealpha($miniatura, true);
        $transparente = imagecolorallocatealpha($miniatura, 0, 0, 0, 127);
        imagefill($miniatura, 0, 0, $transparente);
    }

    // Redimensionar con imagecopyresampled() como en el documento
    // Esta función copia y cambia el tamaño de parte de una imagen
    imagecopyresampled(
        $miniatura,
        $imagenOriginal,
        0, 0, 0, 0,
        $ancho, $alto,
        $anchoOriginal, $altoOriginal
    );

    // Crear directorio si no existe
    $dirDestino = dirname($rutaDestino);
    if (!file_exists($dirDestino)) {
        mkdir($dirDestino, 0755, true);
    }

    // Guardar miniatura usando imagejpeg(), imagepng() o imagegif()
    $resultado = false;
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $resultado = imagejpeg($miniatura, $rutaDestino, $calidad);
            break;
        case IMAGETYPE_PNG:
            $resultado = imagepng($miniatura, $rutaDestino, 9);
            break;
        case IMAGETYPE_GIF:
            $resultado = imagegif($miniatura, $rutaDestino);
            break;
    }

    // Liberar memoria con imagedestroy()
    imagedestroy($imagenOriginal);
    imagedestroy($miniatura);

    return $resultado;
}

/**
 * Convertir imagen a escala de grises pixel a pixel
 * Implementación basada en el ejemplo del documento (sección 4.3)
 */
function convertirAEscalaDeGrises($rutaOriginal, $rutaDestino) {
    if (!file_exists($rutaOriginal)) {
        return false;
    }
    
    $info = getimagesize($rutaOriginal);
    if ($info === false) {
        return false;
    }
    
    list($ancho, $alto, $tipo) = $info;
    
    // Crear recurso de imagen
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $foto = imagecreatefromjpeg($rutaOriginal);
            break;
        case IMAGETYPE_PNG:
            $foto = imagecreatefrompng($rutaOriginal);
            break;
        case IMAGETYPE_GIF:
            $foto = imagecreatefromgif($rutaOriginal);
            break;
        default:
            return false;
    }
    
    // Recorrer pixel a pixel usando imagesx() e imagesy()
    for($x = 0; $x < imagesx($foto); $x++) {
        for($y = 0; $y < imagesy($foto); $y++) {
            // Obtener color del pixel usando imagecolorat()
            $rgb = imagecolorat($foto, $x, $y);
            
            // Realizar desplazamiento de bits para obtener cada componente
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // Calcular nivel de gris
            $nivel = ($r + $g + $b) / 3;
            
            // Crear nuevo color y establecerlo usando imagesetpixel()
            $color = imagecolorallocate($foto, $nivel, $nivel, $nivel);
            imagesetpixel($foto, $x, $y, $color);
        }
    }
    
    // Guardar imagen
    $resultado = false;
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $resultado = imagejpeg($foto, $rutaDestino);
            break;
        case IMAGETYPE_PNG:
            $resultado = imagepng($foto, $rutaDestino);
            break;
        case IMAGETYPE_GIF:
            $resultado = imagegif($foto, $rutaDestino);
            break;
    }
    
    imagedestroy($foto);
    return $resultado;
}

/**
 * Obtener imagen en formato base64 para usar con esquema data:
 * Como se explica en la sección 4.2 del documento
 */
function obtenerImagenComoDataURI($rutaImagen) {
    if (!file_exists($rutaImagen)) {
        return false;
    }
    
    $info = getimagesize($rutaImagen);
    if ($info === false) {
        return false;
    }
    
    list($ancho, $alto, $tipo) = $info;
    
    // Crear recurso de imagen
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagen = imagecreatefromjpeg($rutaImagen);
            $mimeType = 'image/jpeg';
            break;
        case IMAGETYPE_PNG:
            $imagen = imagecreatefrompng($rutaImagen);
            $mimeType = 'image/png';
            break;
        case IMAGETYPE_GIF:
            $imagen = imagecreatefromgif($rutaImagen);
            $mimeType = 'image/gif';
            break;
        default:
            return false;
    }
    
    // Activar buffer de salida usando ob_start()
    ob_start();
    
    // Volcar imagen al buffer
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($imagen);
            break;
        case IMAGETYPE_PNG:
            imagepng($imagen);
            break;
        case IMAGETYPE_GIF:
            imagegif($imagen);
            break;
    }
    
    // Obtener contenido del buffer usando ob_get_contents()
    $imageData = ob_get_contents();
    
    // Limpiar y deshabilitar buffer usando ob_end_clean()
    ob_end_clean();
    
    // Liberar recursos
    imagedestroy($imagen);
    
    // Retornar en formato data URI con codificación base64
    return "data:" . $mimeType . ";base64," . base64_encode($imageData);
}

/**
 * Validar que un archivo es una imagen válida
 * Acepta tmp_name sin extensión; usa nombre original para validar extensión
 */
function esImagenValida($archivoTmp, $nombreOriginal = '') {
    $tiposPermitidos = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'];
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Intentar obtener MIME con getimagesize (no requiere fileinfo)
    $info = @getimagesize($archivoTmp);
    $mimeType = $info['mime'] ?? null;

    // Si no se obtuvo con getimagesize, probar con fileinfo si existe
    if (!$mimeType && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivoTmp);
        finfo_close($finfo);
    }

    if (!$mimeType || !in_array($mimeType, $tiposPermitidos)) {
        return false;
    }
    
    // Verificar extensión usando nombre original si se proporciona
    $extension = strtolower(pathinfo($nombreOriginal ?: $archivoTmp, PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas)) {
        return false;
    }
    
    return true;
}

/**
 * Generar nombre único para archivo
 */
function generarNombreUnico($nombreOriginal) {
    $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
    return uniqid('img_', true) . '.' . $extension;
}
