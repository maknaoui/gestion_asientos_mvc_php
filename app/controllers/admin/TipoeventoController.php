<?php
require_once './app/models/Database.php';
require_once './app/models/TipoEvento.php';
require_once './app/helpers/Auth.php';

class TipoEventoController {
    private $tipo;

    public function __construct() {
        Auth::check();
        $this->tipo = new TipoEvento();
    }

    public function index() {
        require './app/views/admin/tipo_eventos/index.php';
    }

    public function list() {
        $tipos = $this->tipo->listar();

        echo json_encode([
            'Result' => 'OK',
            'TotalRecordCount' => count($tipos),
            'Records' => $tipos
        ]);
    }
    public function listjson() {
        $tipos = $this->tipo->listarjson();

        echo json_encode($tipos);
    }

    public function create() {
        $data = $_POST;

        $success = $this->tipo->crear(
            $data['nombre'] ?? '',
            $data['matrix'] ?? ''
        );

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Record' => array("id"=> $success),
            'Message' => $success ? null : 'Error al crear el tipo de evento'
        ]);
    }

    public function update() {
        $data = $_POST;

        $success = $this->tipo->actualizar(
            $data['id'],
            $data['nombre'] ?? '',
            $data['matrix'] ?? ''
        );

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'Error al actualizar el tipo de evento'
        ]);
    }

    public function delete() {
        $id = $_POST['id'] ?? null;

        $success = $this->tipo->eliminar($id);

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'No se pudo eliminar el tipo de evento'
        ]);
    }
}
