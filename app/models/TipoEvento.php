<?php

class TipoEvento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lista de todos los tipos de eventos
     */
    public function listar() {
        $stmt = $this->db->query("SELECT * FROM tipo_eventos where eliminado=0 ORDER BY id ASC");
        return $stmt->fetchAll();
    }
    /**
     * Lista de todos los tipos de eventos para jtable
     */
    public function listarjson() {
        $stmt = $this->db->query("SELECT id as Value, nombre as DisplayText FROM tipo_eventos where eliminado=0 ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener un tipo de evento por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM tipo_eventos WHERE id = :id and eliminado=0");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crear un nuevo tipo de evento
     */
    public function crear($nombre, $matrix) {
        $stmt = $this->db->prepare("
            INSERT INTO tipo_eventos (nombre, matrix)
            VALUES (:nombre, :matrix)
        ");
        return $stmt->execute([
            'nombre' => $nombre,
            'matrix' => $matrix
        ]);
    }

    /**
     * Actualizar un tipo de evento
     */
    public function actualizar($id, $nombre, $matrix) {
        $stmt = $this->db->prepare("
            UPDATE tipo_eventos
            SET nombre = :nombre, matrix = :matrix
            WHERE id = :id and eliminado=0
        ");
        if($stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'matrix' => $matrix
        ])) {
            return $this->db->lastInsertId();
        }
        return false;
        /**
         *  Ejemplo de matrix:
            *{
            *"filas": 40,
            *    "patron": [
            *    { "asientos": 3 },
            *    { "espacio": 1 },
            *    { "asientos": 4 },
            *    { "espacio": 2 },
            *    { "asientos": 3 }
            *    ]
            *}
         */
    }

    public function interpretarMatrixJson($matrixJson,$asiento="A", $espacio=" ") {
        $data = json_decode($matrixJson, true);

        if (!$data || !isset($data['filas'], $data['patron'])) {
            throw new Exception("Matrix inválida");
        }

        $filas = $data['filas'];
        $patron = $data['patron'];

        $mapa = [];

        for ($f = 1; $f <= $filas; $f++) {
            $fila = [];
            foreach ($patron as $bloque) {
                if (isset($bloque['asientos'])) {
                    for ($i = 0; $i < $bloque['asientos']; $i++) {
                        $fila[] = $asiento; // A → asiento
                    }
                } elseif (isset($bloque['espacio'])) {
                    for ($i = 0; $i < $bloque['espacio']; $i++) {
                        $fila[] = $espacio; // espacio vacío
                    }
                }
            }
            $mapa[] = $fila;
        }

        return $mapa;
    }


    /**
     * Eliminar un tipo de evento
     */
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE tipo_eventos SET eliminado=1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

}
