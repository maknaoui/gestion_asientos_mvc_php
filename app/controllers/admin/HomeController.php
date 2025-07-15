<?php
require_once './app/controllers/admin/AuthController.php';


class HomeController
{
    public function index()
    {
        // Proteger el acceso: solo admins logueados
        AuthController::check();

        // Aquí podrías traer estadísticas o datos para el dashboard
        $usuario = AuthController::usuarioActual();
        header("Location: /admin/reserva");
        exit;
        require './app/views/admin/home/index.php';
    }
}
