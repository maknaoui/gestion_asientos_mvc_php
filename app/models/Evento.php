<?php
class Evento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lista todos los eventos (puede incluir eliminados)
     */
    public function listar($incluirEliminados = false) {
        if ($incluirEliminados) {
            $sql = "SELECT * FROM eventos ORDER BY fecha_evento ASC";
            $stmt = $this->db->query($sql);
        } else {
            $sql = "SELECT * FROM eventos WHERE eliminado = 0 ORDER BY fecha_evento ASC";
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    public function listarConFecha($incluirEliminados = false) {
        $hoy = date('Y-m-d H:i:s'); // fecha y hora actual

        if ($incluirEliminados) {
            $sql = "SELECT * FROM eventos 
                    WHERE fecha_evento > :hoy 
                    ORDER BY fecha_evento ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['hoy' => $hoy]);
        } else {
            $sql = "SELECT * FROM eventos 
                    WHERE eliminado = 0 
                    AND fecha_evento > :hoy 
                    ORDER BY fecha_evento ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['hoy' => $hoy]);
        }

        return $stmt->fetchAll();
    }

    public function listarConOcupados() {
        $sql = "
            SELECT 
                e.*, 
                COALESCE((
                    SELECT COUNT(ra.id)
                    FROM reservas r
                    JOIN reserva_asientos ra ON ra.reserva_id = r.id
                    WHERE r.evento_id = e.id 
                    AND r.estado IN ('pendiente', 'confirmado') 
                    AND r.eliminado = 0
                ),0) AS ocupados
            FROM eventos e
            WHERE e.eliminado = 0
            ORDER BY e.fecha_evento DESC
        ";

        return $this->db->query($sql)->fetchAll();
    }
    public function listarConOcupadosPaginado($startIndex = 0, $pageSize = 10, $sorting = 'id ASC', $filtroNombre = null)
    {
        // Por seguridad: validar ordenamiento permitido
        $allowedSortFields = ['id', 'nombre', 'fecha_evento', 'maxima_capacidad'];
        [$campo, $direccion] = explode(' ', $sorting);
        if (!in_array($campo, $allowedSortFields)) {
            $campo = 'id';
        }
        $campo="e.".$campo;
        $direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
        $orden = "$campo $direccion";

        $params = [];
        $where = "WHERE e.eliminado = 0";

        if (!empty($filtroNombre)) {
            $where .= " AND e.nombre LIKE :nombre";
            $params[':nombre'] = "%$filtroNombre%";
        }

        // contar total
        $sqlCount = "
            SELECT COUNT(*) 
            FROM eventos e
            $where
        ";
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalCount = $stmtCount->fetchColumn();

        // obtener registros
        $sql = "
            SELECT 
                e.*, 
                COALESCE((
                    SELECT COUNT(ra.id)
                    FROM reservas r
                    JOIN reserva_asientos ra ON ra.reserva_id = r.id
                    WHERE r.evento_id = e.id 
                    AND r.estado IN ('pendiente', 'confirmado') 
                    AND r.eliminado = 0
                ),0) AS ocupados
            FROM eventos e
            $where
            ORDER BY $orden
            LIMIT :start, :size
        ";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        
        $stmt->bindValue(':start', (int)$startIndex, PDO::PARAM_INT);
        $stmt->bindValue(':size', (int)$pageSize, PDO::PARAM_INT);
        $stmt->execute();

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'Result' => 'OK',
            'TotalRecordCount' => $totalCount,
            'Records' => $records
        ];
    }


    /**
     * Lista de todos los tipos de eventos para jtable
     */
    public function listarjson() {
        $stmt = $this->db->query("SELECT id as Value, nombre as DisplayText FROM eventos where eliminado=0 ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un evento por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM eventos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crear un nuevo evento
     */
    public function crear($nombre, $maxima_capacidad, $tipo_id, $fecha_evento, $imagen = null, $descripcion = null) {
        $sql = "
            INSERT INTO eventos 
            (nombre, maxima_capacidad, tipo_id, fecha_evento, imagen, descripcion, eliminado) 
            VALUES 
            (:nombre, :maxima_capacidad, :tipo_id, :fecha_evento, :imagen, :descripcion, 0)
        ";
        $stmt = $this->db->prepare($sql);
        if($stmt->execute([
            'nombre' => $nombre,
            'maxima_capacidad' => $maxima_capacidad,
            'tipo_id' => $tipo_id,
            'fecha_evento' => $fecha_evento,
            'imagen' => $imagen,
            'descripcion' => $descripcion
        ])) {
            return $this->db->lastInsertId();
        }
        return ;
    }


    /**
     * Actualizar un evento existente
     */
    public function actualizar($id, $nombre, $maxima_capacidad, $fecha_evento, $imagen = null, $descripcion = null) {
        $sql = "
            UPDATE eventos 
            SET nombre = :nombre,
                maxima_capacidad = :maxima_capacidad,
                fecha_evento = :fecha_evento,
                imagen = :imagen,
                descripcion = :descripcion
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'maxima_capacidad' => $maxima_capacidad,
            'fecha_evento' => $fecha_evento,
            'imagen' => $imagen,
            'descripcion' => $descripcion
        ]);
    }


    /**
     * Soft-delete de un evento
     */
    public function eliminar($id, $usuario_id) {
        $sql = "
            UPDATE eventos
            SET eliminado = 1,
                eliminado_por = :usuario_id,
                fecha_eliminacion = NOW()
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario_id' => $usuario_id,
            'id' => $id
        ]);
    }

    /**
     * Restaurar un evento eliminado
     */
    public function restaurar($id) {
        $sql = "
            UPDATE eventos
            SET eliminado = 0,
                eliminado_por = 0,
                fecha_eliminacion = NULL
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
