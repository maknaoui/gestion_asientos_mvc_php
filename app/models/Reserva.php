<?php

class Reserva {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Listar las reservas
     */
    public function listar($incluirEliminadas = false) {
        $sql = "
            SELECT 
                r.*, 
                GROUP_CONCAT(a.codigo ORDER BY a.codigo ASC SEPARATOR ', ') AS asientos,
                GROUP_CONCAT(a.id ORDER BY a.id ASC SEPARATOR ',') AS asientos_ids
            FROM reservas r
            LEFT JOIN reserva_asientos ra ON r.id = ra.reserva_id
            LEFT JOIN asientos a ON ra.asiento_id = a.id
            WHERE 1 = 1
        ";

        if (!$incluirEliminadas) {
            $sql .= " AND r.eliminado = 0";
        }

        $sql .= " GROUP BY r.id ORDER BY r.fecha_reserva DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista las reservas de un evento
     */
    public function listarPorEvento($evento_id, $incluirEliminadas = false) {
        $sql = "SELECT * FROM reservas WHERE evento_id = :evento_id";
        if (!$incluirEliminadas) {
            $sql .= " AND eliminado = 0";
        }
        $sql .= " ORDER BY fecha_reserva DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['evento_id' => $evento_id]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una reserva por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM reservas WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crea una nueva reserva
     * @param array $data — datos de la reserva
     * @param array $asientos — IDs de los asientos reservados
     */
    public function crear($data, $asientos = []) {
        $sql = "
            INSERT INTO reservas 
            (nombre, apellido, correo, fecha_nacimiento, telefono, rut, numero_personas, evento_id, estado, eliminado) 
            VALUES 
            (:nombre, :apellido, :correo, :fecha_nacimiento, :telefono, :rut, :numero_personas, :evento_id, 'pendiente', 0)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'correo' => $data['correo'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'telefono' => $data['telefono'],
            'rut' => $data['rut'],
            'numero_personas' => $data['numero_personas'],
            'evento_id' => $data['evento_id']
        ]);

        $reserva_id = $this->db->lastInsertId();

        // guardar asientos
        foreach ($asientos as $asiento_id) {
            $this->asignarAsiento($reserva_id, $asiento_id);
        }

        return $reserva_id;
    }

    /**
     * Asigna un asiento a una reserva
     */
    public function asignarAsiento($reserva_id, $asiento_id) {
        $stmt = $this->db->prepare("
            INSERT INTO reserva_asientos (reserva_id, asiento_id)
            VALUES (:reserva_id, :asiento_id)
        ");
        return $stmt->execute([
            'reserva_id' => $reserva_id,
            'asiento_id' => $asiento_id
        ]);
    }

    /**
     * Obtiene los asientos de una reserva
     */
    public function obtenerAsientos($reserva_id) {
        $stmt = $this->db->prepare("
            SELECT a.*
            FROM asientos a
            JOIN reserva_asientos ra ON ra.asiento_id = a.id
            WHERE ra.reserva_id = :reserva_id
        ");
        $stmt->execute(['reserva_id' => $reserva_id]);
        return $stmt->fetchAll();
    }
    
    public function obtenerAsientosReservados($evento_id)
    {
        $sql = "
            SELECT ra.asiento_id
            FROM reserva_asientos ra
            JOIN reservas r ON r.id = ra.reserva_id
            WHERE r.evento_id = :evento_id
            AND r.estado IN ('pendiente', 'confirmado')
            AND r.eliminado = 0
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['evento_id' => $evento_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * Cambia el estado de una reserva (confirmado/rechazado)
     */
    public function cambiarEstado($id, $estado, $usuario_id) {
        $sql = "
            UPDATE reservas 
            SET estado = :estado,
                aprobado_por = :usuario_id,
                fecha_aprobacion = NOW()
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'estado' => $estado,
            'usuario_id' => $usuario_id,
            'id' => $id
        ]);
    }

    /**
     * Eliminar una reserva
     */
    public function eliminar($id, $usuario_id) {
        $sql = "
            UPDATE reservas
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
     * Restaurar una reserva
     */
    public function restaurar($id) {
        $sql = "
            UPDATE reservas
            SET eliminado = 0,
                eliminado_por = 0,
                fecha_eliminacion = NULL
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
