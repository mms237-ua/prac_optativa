<?php
/**
 * Configuración general de la aplicación
 */

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'prac_optativa');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de rutas
define('BASE_URL', 'http://localhost/prac_optativa/');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// Configuración de paginación (CONFIGURABLE)
define('ITEMS_POR_PAGINA', 10); // Cambiar aquí para modificar la paginación

// Configuración de miniaturas
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);
define('THUMBNAIL_QUALITY', 85);

// Configuración de sesión
define('SESSION_NAME', 'PRAC_OPTATIVA');
define('SESSION_LIFETIME', 3600); // 1 hora

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Modo desarrollo (cambiar a false en producción)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuración de upload
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_file_uploads', 50);
