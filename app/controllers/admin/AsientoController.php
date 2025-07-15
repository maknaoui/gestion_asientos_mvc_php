<?php
require_once './app/models/Database.php';
require_once './app/models/Asiento.php';
require_once './app/models/Evento.php';
require_once './app/helpers/Auth.php';

class AsientoController {
    private $asiento;
    private $evento;

    public function __construct() {
        Auth::check();
        $this->asiento = new Asiento();
        $this->evento = new Evento();
    }
    /**
     * Lista asientos de un evento
     */
    public function list($evento_id) {
        $evento_id = intval($evento_id);
        $asientos = $this->asiento->listarPorEvento($evento_id);
        $evento = $this->evento->obtenerPorId($evento_id);
        require './app/views/admin/asientos/index.php';
    }

    public function listjson($evento_id) {
        $evento_id = intval($evento_id);

        $startIndex = $_GET['jtStartIndex'] ?? 0;
        $pageSize   = $_GET['jtPageSize'] ?? 10;
        $sorting    = $_GET['jtSorting'] ?? 'id ASC';
        $codigo     = $_POST['codigo'] ?? '';

        $result = $this->asiento->listarPorEventoPaginado(
            $evento_id,
            $startIndex,
            $pageSize,
            $sorting,
            $codigo
        );
        echo json_encode($result);
    }


    /**
     * Crea un asiento para un evento
     */
    public function create($evento_id) {
        $evento_id = intval($evento_id);

        $codigo = $_POST['codigo'] ?? '';
        $top = intval($_POST['top_pos'] ?? 0);
        $left = intval($_POST['left_pos'] ?? 0);
        $discapacitado = isset($_POST['discapacitado']) && $_POST['discapacitado'] == '1' ? 1 : 0;

        $this->asiento->crear([
            'evento_id' => $evento_id,
            'codigo' => $codigo,
            'top_pos' => $top,
            'left_pos' => $left,
            'discapacitado' => $discapacitado
        ]);

        echo json_encode([
            'Result' => 'OK',
            'Message' => 'Asiento creado correctamente'
        ]);
    }

    /**
     * Actualiza un asiento
     */
    public function update() {
        $id = intval($_POST['id']);
        $codigo = $_POST['codigo'] ?? '';
        $top = intval($_POST['top_pos'] ?? 0);
        $left = intval($_POST['left_pos'] ?? 0);
        $discapacitado = isset($_POST['discapacitado']) && $_POST['discapacitado'] == '1' ? 1 : 0;
        $estado = (isset($_POST['estado']) && $_POST['estado'] == '1') ? 'habilitado' : 'deshabilitado';

        $this->asiento->actualizar($id,
            $codigo,
            $top,
            $left,
            $discapacitado,
            $estado
        );

        echo json_encode([
            'Result' => 'OK',
            'Message' => 'Asiento actualizado correctamente'
        ]);
    }


    /**
     * Eliminar un asiento
     */
    public function delete($id) {
        $id = intval($_POST['id']);
        $user_id=Auth::user()['id'] ?? 0;
        $this->asiento->eliminar($id,$user_id);

        echo json_encode([
            'Result' => 'OK',
            'Message' => 'Asiento eliminado correctamente'
        ]);
    }
}
