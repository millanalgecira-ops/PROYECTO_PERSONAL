<?php
// ============================================================
//  Configuración de rutas del sistema
//  Detecta automáticamente si está en local o en hosting
// ============================================================

// Ruta absoluta a la raíz del proyecto
define('ROOT_PATH', dirname(__DIR__));

// URL base del proyecto (detecta automáticamente)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = $_SERVER['SCRIPT_NAME'] ?? '';

// Detectar la carpeta raíz del proyecto en la URL
// Sube hasta encontrar la carpeta que contiene Config/, Controllers/, etc.
$parts    = explode('/', trim($script, '/'));
$base     = '';

// Buscar si hay subcarpeta del proyecto en la URL
foreach ($parts as $i => $part) {
    if (in_array($part, ['Public', 'Views', 'Controllers', 'Config', 'Models'])) {
        $base = '/' . implode('/', array_slice($parts, 0, $i));
        break;
    }
}

define('BASE_URL', $protocol . '://' . $host . $base);

// URLs de las secciones principales
define('URL_PUBLIC',      BASE_URL . '/Public');
define('URL_VIEWS',       BASE_URL . '/Views');
define('URL_CONTROLLERS', BASE_URL . '/Controllers');
define('URL_LOGIN',       BASE_URL . '/Views/usuarios/login.php');
define('URL_REGISTRO',    BASE_URL . '/Views/usuarios/registre.php');
define('URL_INDEX',       BASE_URL . '/Public/index.php');
?>
