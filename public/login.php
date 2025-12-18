<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        redirect(BASE_URL . 'private/mis_anuncios.php');
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>🔐 Iniciar Sesión</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
            </form>
            
            <div class="login-info">
                <p><strong>Usuarios de prueba:</strong></p>
                <p>📌 usuario1 / 123456</p>
                <p>📌 usuario2 / 123456</p>
                <p>📌 admin / 123456</p>
            </div>
            
            <a href="<?= BASE_URL ?>" class="btn-back-link">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>
