<?php
require_once './app/models/Database.php';
require_once './app/models/Evento.php';
require_once './app/models/Asiento.php';
require_once './app/models/TipoEvento.php';
require_once './app/helpers/Auth.php';

class EventoController
{
    private $evento;
    private $tipo;

    public function __construct()
    {
        Auth::check();
        $this->evento = new Evento();
    }

    public function index() {
        $this->tipo = new TipoEvento();
        $tipos = $this->tipo->listarjson();
        require './app/views/admin/eventos/index.php';
    }

    /*public function list() {
        $nombre = $_GET['nombre'] ?? '';
        $eventos = $this->evento->listarConOcupados();

        if ($nombre) {
            $eventos = array_filter($eventos, fn($e) => stripos($e['nombre'], $nombre) !== false);
        }

        echo json_encode([
            'Result' => 'OK',
            'TotalRecordCount' => count($eventos),
            'Records' => array_values($eventos)
        ]);
    }*/
        public function list() {
            $startIndex = $_GET['jtStartIndex'] ?? 0;
            $pageSize   = $_GET['jtPageSize'] ?? 10;
            $sorting    = $_GET['jtSorting'] ?? 'e.id ASC';
            $nombre     = $_POST['nombre'] ?? '';
            
            $eventos = $this->evento->listarConOcupadosPaginado(
                intval($startIndex),
                intval($pageSize),
                $sorting,
                $nombre
            );

            echo json_encode($eventos);
        }


    public function create() {
        $data = $_POST;
        $errores = [];

        if (empty($data['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        if (empty($data['maxima_capacidad']) || intval($data['maxima_capacidad']) < 1) {
            $errores[] = 'La capacidad debe ser mayor o igual a 1';
        }
        if (empty($data['fecha_evento'])) {
            $errores[] = 'La fecha del evento es obligatoria';
        }
        if (empty($data['descripcion'])) {
            $errores[] = 'La descripción es obligatoria';
        }
        if (!empty($errores)) {
            echo json_encode([
                'Result' => 'ERROR',
                'Message' => implode('; ', $errores)
            ]);
            return;
        }

        $fecha_evento_sql = $this->formatearFechaSQL($data['fecha_evento']);

        if ($fecha_evento_sql === false) {
            echo json_encode([
                'Result' => 'ERROR',
                'Message' => 'Formato de fecha inválido. Usa DD/MM/YYYY HH:mm'
            ]);
            return;
        }
        $imagenPath = $this->procesarImagen();

        $evento_id = $this->evento->crear(
            $data['nombre'],
            intval($data['maxima_capacidad']),
            intval($data['tipo_id']),
            $fecha_evento_sql,
            $imagenPath,
            $data['descripcion']
        );

        if (!$evento_id) {
            echo json_encode([
                'Result' => 'ERROR',
                'Message' => 'Error al crear el evento'
            ]);
            return;
        }

        $tipoEvento = new TipoEvento();
        $tipo = $tipoEvento->obtenerPorId($data['tipo_id']);

        if (!$tipo || empty($tipo['matrix'])) {
            echo json_encode([
                'Result' => 'ERROR',
                'Message' => 'No se encontró matrix para el tipo de evento'
            ]);
            return;
        }

        $matrix = json_decode($tipo['matrix'], true);

        $asientoModel = new Asiento();
        $asientoModel->generarAsientos($evento_id, $matrix, intval($data['maxima_capacidad']));

        echo json_encode([
            'Result' => 'OK',
            'Record' => ['id' => $evento_id],
            'Message' => null
        ]);
    }

    /**
     * Convierte una fecha en formato DD/MM/YYYY HH:mm a YYYY-MM-DD HH:mm:ss
     */
    private function formatearFechaSQL($fecha) {
        $dt = DateTime::createFromFormat('d/m/Y H:i', $fecha);
        if ($dt) {
            return $dt->format('Y-m-d H:i:s');
        }
        return false;
    }



    public function update() {
        $data = $_POST;

        $imagenPath = $data['imagen_actual'] ?? null;

        if (!empty($_FILES['imagen']['name'])) {
            try {
                $imagenPath = $this->procesarImagen();
            } catch (Exception $e) {
                echo json_encode([
                    'Result' => 'ERROR',
                    'Message' => $e->getMessage()
                ]);
                exit;
            }
        }

        $success = $this->evento->actualizar(
            $data['id'],
            $data['nombre'] ?? '',
            $data['maxima_capacidad'] ?? 0,
            $data['fecha_evento'] ?? '',
            $imagenPath,
            $data['descripcion'] ?? ''
        );

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'Error al actualizar el evento'
        ]);
    }

    public function delete() {
        $id = $_POST['id'] ?? null;
        $user_id=Auth::user()['id'] ?? 0;

        $success = $this->evento->eliminar($id, $user_id);
        $asiento= new Asiento();
        $asiento->eliminarPorEvento($id, $user_id);

        echo json_encode([
            'Result' => $success ? 'OK' : 'ERROR',
            'Message' => $success ? null : 'No se pudo eliminar el evento'
        ]);
    }

    public function getTiposEvento() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, nombre FROM tipo_eventos WHERE eliminado=0");
        $tipos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        echo json_encode($tipos);
    }

    private function procesarImagen(): ?string
    {
        if (empty($_FILES['imagen']['name'])) {
            return null; // no se subió nada
        }

        if (!isset($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            throw new Exception('No se recibió un archivo válido.');
        }

        // Validar tamaño máximo (4 MB)
        $maxSize = 4 * 1024 * 1024;
        if ($_FILES['imagen']['size'] > $maxSize) {
            throw new Exception('El archivo supera el tamaño máximo permitido de 4 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        $extensionesValidas = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $extensionesValidas)) {
            throw new Exception('El archivo subido no es una imagen válida.');
        }

        $uploadDir = './uploads/eventos/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            throw new Exception('No se pudo crear el directorio de destino.');
        }

        $extension = $extensionesValidas[$mime];
        $filename = time() . "-" . bin2hex(random_bytes(8)) . "." . $extension;
        $targetFile = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
            throw new Exception('Error al mover el archivo subido.');
        }

        return "uploads/eventos/$filename";
    }



}
