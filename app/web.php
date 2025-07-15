<?php
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

$namespace = '';
$controller = 'EventoController';
$action = 'index';
$params = [];

// analizar URL
if (!empty($url)) {
    $parts = explode('/', $url);
    
    if ($parts[0] === 'admin') {
        $namespace = 'admin';
        $controller = ucfirst(strtolower($parts[1] ?? 'home')) . 'Controller';
        $action = strtolower($parts[2] ?? 'index');
        $params = array_slice($parts, 3);
    } else {
        $controller = ucfirst(strtolower($parts[0] ?? 'home')) . 'Controller';
        $action = strtolower($parts[1] ?? 'index');
        $params = array_slice($parts, 2);
    }
}

// Ruta al archivo controlador
if ($namespace === 'admin') {
    $controllerFile = "./app/controllers/admin/{$controller}.php";
} else {
    $controllerFile = "./app/controllers/{$controller}.php";
}
// comprobar si existe
if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>Controlador no encontrado: {$controller}</p>";
    exit;
}

require_once $controllerFile;

// Comprobar clase
if (!class_exists($controller)) {
    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1>";
    echo "<p>Clase del controlador no encontrada: {$controller}</p>";
    exit;
}

$ctrl = new $controller();

// comprobar método
if (!method_exists($ctrl, $action)) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>Método no encontrado: {$action} en {$controller}</p>";
    exit;
}

// Ejecutar acción con params
call_user_func_array([$ctrl, $action], $params);
