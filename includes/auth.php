<?php
/**
 * Funciones de autenticación y sesión
 */

/**
 * Iniciar sesión
 */
function iniciarSesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
        
        // Regenerar ID de sesión periódicamente
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > SESSION_LIFETIME) {
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }
    }
}

/**
 * Verificar si el usuario está autenticado
 */
function estaAutenticado() {
    iniciarSesion();
    return isset($_SESSION['usuario_id']);
}

/**
 * Requerir autenticación (redirige si no está autenticado)
 */
function requerirAutenticacion() {
    if (!estaAutenticado()) {
        header('Location: ' . BASE_URL . 'public/login.php');
        exit;
    }
}

/**
 * Login de usuario
 */
function login($username, $password) {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, username, password, nombre_completo FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($password, $usuario['password'])) {
        iniciarSesion();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
        return true;
    }
    
    return false;
}

/**
 * Cerrar sesión
 */
function logout() {
    iniciarSesion();
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Obtener ID del usuario actual
 */
function getUsuarioId() {
    iniciarSesion();
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtener nombre del usuario actual
 */
function getNombreUsuario() {
    iniciarSesion();
    return $_SESSION['nombre_completo'] ?? $_SESSION['username'] ?? 'Invitado';
}
