<?php
require_once './app/models/Database.php';
require_once './app/models/Reserva.php';
require_once './app/models/Evento.php';
require_once './app/helpers/Auth.php';

class ReservaController {
    private $reserva;

    public function __construct() {
        Auth::check();
        $this->reserva = new Reserva();
    }

    public function index() {
        $this->evento = new Evento();
        $eventos = $this->evento->listarjson();
        require './app/views/admin/reservas/index.php';
    }

    public function list() {
        $reservas = $this->reserva->listar();
        echo json_encode([
            'Result' => 'OK',
            'TotalRecordCount' => count($reservas),
            'Records' => $reservas
        ]);
    }

    public function create() {
        $data = $_POST;
        $data['estado'] = $data['estado'] ?? 'pendiente';

        $success = $this->reserva->crear($data);

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'Error al crear la reserva'
        ]);
    }

    public function update() {
        $data = $_POST;
        $id = $data['id'];
        $estado = $data['estado'] ?? 'pendiente';
        $user_id=Auth::user()['id'] ?? 0;
        $success = $this->reserva->cambiarEstado($id, $estado, $user_id);

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'Error al actualizar la reserva'
        ]);
    }

    public function delete() {
        $id = $_POST['id'];
        $user_id=Auth::user()['id'] ?? 0;
        $success = $this->reserva->eliminar($id,$user_id);

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'No se pudo eliminar la reserva'
        ]);
    }
}
