<?php
/**
 * Funciones auxiliares generales
 */

/**
 * Redirigir a una URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Obtener estadísticas de fotos subidas en los últimos 7 días
 */
function obtenerEstadisticasUltimos7Dias() {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->query("
        SELECT 
            DATE(fecha_subida) as fecha,
            COUNT(*) as total
        FROM fotos
        WHERE fecha_subida >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(fecha_subida)
        ORDER BY fecha ASC
    ");
    
    $resultados = $stmt->fetchAll();
    
    // Crear array con todos los días (incluso si no hay datos)
    $estadisticas = [];
    for ($i = 6; $i >= 0; $i--) {
        $fecha = date('Y-m-d', strtotime("-$i days"));
        $estadisticas[$fecha] = 0;
    }
    
    // Rellenar con datos reales
    foreach ($resultados as $row) {
        $estadisticas[$row['fecha']] = (int)$row['total'];
    }
    
    return $estadisticas;
}

/**
 * Generar diagrama de barras usando GD Library y esquema data:
 * Basado en el ejemplo de la sección 4.1 y 4.2 del documento
 */
function generarDiagramaBarras($datos, $ancho = 700, $alto = 300) {
    // Crear imagen usando imagecreatetruecolor()
    $image = imagecreatetruecolor($ancho, $alto);
    
    // Definir colores usando imagecolorallocate()
    $blanco = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
    $azul = imagecolorallocate($image, 0x66, 0x7E, 0xEA);
    $azulOscuro = imagecolorallocate($image, 0x55, 0x68, 0xD3);
    $gris = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
    $negro = imagecolorallocate($image, 0x00, 0x00, 0x00);
    
    // Rellenar fondo usando imagefill()
    imagefill($image, 0, 0, $blanco);
    
    // Calcular dimensiones
    $margen = 50;
    $anchoBarra = ($ancho - 2 * $margen) / count($datos);
    $maxValor = max($datos) > 0 ? max($datos) : 1;
    $altoGrafico = $alto - 2 * $margen;
    
    $x = $margen;
    $i = 0;
    
    foreach ($datos as $fecha => $valor) {
        // Calcular altura de la barra
        $alturaBarra = ($valor / $maxValor) * $altoGrafico;
        $y = $alto - $margen - $alturaBarra;
        
        // Dibujar barra usando imagefilledrectangle()
        $x1 = (int)round($x + 5);
        $x2 = (int)round($x + $anchoBarra - 5);
        $y1 = (int)round($y);
        $y2 = (int)round($alto - $margen);

        imagefilledrectangle($image, $x1, $y1, $x2, $y2, $azul);
        
        // Dibujar borde de barra usando imagerectangle()
        imagerectangle($image, $x1, $y1, $x2, $y2, $azulOscuro);
        
        // Dibujar valor usando imagestring()
        if ($valor > 0) {
            $valorTexto = strval($valor);
            $tx = (int)round($x + ($anchoBarra / 2) - 5);
            $ty = (int)round($y - 15);
            imagestring($image, 3, $tx, $ty, $valorTexto, $negro);
        }
        
        // Dibujar etiqueta de fecha
        $dia = date('d/m', strtotime($fecha));
        $fx = (int)round($x + 10);
        $fy = (int)round($alto - $margen + 5);
        imagestring($image, 2, $fx, $fy, $dia, $negro);
        
        $x += $anchoBarra;
        $i++;
    }
    
    // Dibujar eje X usando imageline()
    imageline($image, $margen, $alto - $margen, $ancho - $margen, $alto - $margen, $negro);
    
    // Dibujar eje Y
    imageline($image, $margen, $margen, $margen, $alto - $margen, $negro);
    
    // Usar buffer de salida como se explica en el documento (sección 4.2)
    ob_start();
    imagepng($image);
    $imageData = ob_get_contents();
    ob_end_clean();
    
    // Liberar recursos usando imagedestroy()
    imagedestroy($image);
    
    // Retornar en formato data URI con base64
    return "data:image/png;base64," . base64_encode($imageData);
}

/**
 * Escapar HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear precio
 */
function formatearPrecio($precio) {
    return number_format($precio, 2, ',', '.') . ' €';
}

/**
 * Formatear fecha
 */
function formatearFecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}

/**
 * Calcular número total de páginas
 */
function calcularTotalPaginas($totalItems, $itemsPorPagina = ITEMS_POR_PAGINA) {
    return ceil($totalItems / $itemsPorPagina);
}

/**
 * Obtener página actual
 */
function obtenerPaginaActual() {
    return isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
}

/**
 * Calcular offset para consultas paginadas
 */
function calcularOffset($paginaActual, $itemsPorPagina = ITEMS_POR_PAGINA) {
    return ($paginaActual - 1) * $itemsPorPagina;
}
