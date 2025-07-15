<?php
class Auth
{
    /**
     * Verifica si el usuario está autenticado
     */
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            self::redirectToLogin();
        }
    }

    /**
     * Redirige al login
     */
    public static function redirectToLogin()
    {
        header("Location: /admin/auth/login");
        exit;
    }

    /**
     * Devuelve el usuario actual (opcional)
     */
    public static function user()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['usuario'] ?? null;
    }

    /**
     * Cierra la sesión
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        self::redirectToLogin();
    }
}
