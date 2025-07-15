<?php
require_once './app/models/Database.php';
require_once './app/models/Usuario.php';

class AuthController
{
    public function __construct()
    {
        // inicia sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Muestra el formulario de login y procesa POST
     */
    public function login()
    {
        if (!empty($_SESSION['usuario'])) {
            header('Location: /admin');
            exit;
        }

        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'], $_POST['clave'])) {
            $correo = $_POST['correo'] ?? '';
            $clave = $_POST['clave'] ?? '';
            
            $usuarioModel = new Usuario();
            
            $usuario = $usuarioModel->verificarCredenciales($correo, $clave);
            if ($usuario) {
                $_SESSION['usuario'] = $usuario;
                header('Location: /admin');
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos';
            }
        }

        require './app/views/admin/auth/login.php';
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        session_destroy();
        header('Location: /admin/auth/login');
        exit;
    }

    /**
     * Verifica si hay usuario en sesión, si no, redirige a login
     */
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            header('Location: /admin/auth/login');
            exit;
        }
    }

    /**
     * Devuelve los datos del usuario actual (o null)
     */
    public static function usuarioActual()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['usuario'] ?? null;
    }
}
