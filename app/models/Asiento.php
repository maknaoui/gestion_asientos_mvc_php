<?php
class Asiento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lista de todos los asientos de un evento
     */
    public function listarPorEvento($evento_id, $incluirEliminados = false) {
        $sql = "SELECT * FROM asientos WHERE evento_id = :evento_id";
        if (!$incluirEliminados) {
            $sql .= " AND eliminado = 0";
        }
        $sql .= " ORDER BY id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['evento_id' => $evento_id]);
        return $stmt->fetchAll();
    }
    public function listarPorEventoPaginado($evento_id, $startIndex = 0, $pageSize = 10, $sorting = 'id ASC', $codigo = '', $incluirEliminados = false) {
        $evento_id = intval($evento_id);
        $startIndex = intval($startIndex);
        $pageSize = intval($pageSize);

        $params = [
            ':evento_id' => $evento_id
        ];

        $where = "WHERE evento_id = :evento_id";

        if (!$incluirEliminados) {
            $where .= " AND eliminado = 0";
        }

        if (!empty($codigo)) {
            $where .= " AND codigo LIKE :codigo";
            $params[':codigo'] = "%$codigo%";
        }

        // validación del ordenamiento
        $allowedFields = ['id', 'codigo', 'top_pos', 'left_pos', 'discapacitado', 'eliminado'];
        [$campo, $direccion] = explode(' ', $sorting);
        if (!in_array($campo, $allowedFields)) {
            $campo = 'id';
        }
        $direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
        $orderBy = "$campo $direccion";

        // contar total
        $sqlCount = "SELECT COUNT(*) FROM asientos $where";
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalCount = $stmtCount->fetchColumn();

        // traer registros paginados
        $sql = "
            SELECT *
            FROM asientos
            $where
            ORDER BY $orderBy
            LIMIT :start, :size
        ";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':start', $startIndex, PDO::PARAM_INT);
        $stmt->bindValue(':size', $pageSize, PDO::PARAM_INT);
        $stmt->execute();

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'Result' => 'OK',
            'TotalRecordCount' => $totalCount,
            'Records' => $records
        ];
    }



    /**
     * Obtener un asiento por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM asientos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crear un nuevo asiento
     */
    public function crear($codigo, $top_pos, $left_pos, $discapacitado, $estado, $evento_id) {
        $sql = "
            INSERT INTO asientos 
            (codigo, top_pos, left_pos, discapacitado, estado, evento_id) 
            VALUES 
            (:codigo, :top_pos, :left_pos, :discapacitado, :estado, :evento_id)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'codigo' => $codigo,
            'top_pos' => $top_pos,
            'left_pos' => $left_pos,
            'discapacitado' => $discapacitado,
            'estado' => $estado,
            'evento_id' => $evento_id
        ]);
    }

    /**
     * Actualizar un asiento
     */
    public function actualizar($id, $codigo, $top_pos, $left_pos, $discapacitado, $estado) {
        $sql = "
            UPDATE asientos 
            SET codigo = :codigo,
                top_pos = :top_pos,
                left_pos = :left_pos,
                discapacitado = :discapacitado,
                estado = :estado
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'codigo' => $codigo,
            'top_pos' => $top_pos,
            'left_pos' => $left_pos,
            'discapacitado' => $discapacitado,
            'estado' => $estado,
            'id' => $id
        ]);
    }

    /**
     * Marcar como eliminado (soft delete)
     */
    public function eliminar($id, $usuario_id) {
        $sql = "
            UPDATE asientos 
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
    public function eliminarPorEvento($evento_id, $usuario_id) {
        $sql = "
            UPDATE asientos 
            SET eliminado = 1,
                eliminado_por = :usuario_id,
                fecha_eliminacion = NOW()
            WHERE evento_id = :evento_id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario_id' => $usuario_id,
            'evento_id' => $evento_id
        ]);
    }


    /**
     * Restaurar un asiento eliminado
     */
    public function restaurar($id) {
        $sql = "
            UPDATE asientos 
            SET eliminado = 0,
                eliminado_por = 0,
                fecha_eliminacion = NULL
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Cambiar estado (habilitado/deshabilitado)
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE asientos SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'estado' => $estado,
            'id' => $id
        ]);
    }

    /**
     * Inserta los asientos a partir de un mapa generado desde la matrix JSON
     * @param array $mapa   — resultado de interpretarMatrixJson()
     * @param int $evento_id — id del evento
     * @param int $asiento_size — ancho/alto de un asiento en px
     * @param int $gap_asientos — espacio entre asientos en px
     * @param int $gap_espacios — espacio para "espacios" en px
     */
    public function insertarDesdeMapa($mapa, $evento_id, $tamano_asiento = 30, $gap_asientos = 5, $gap_espacios = 50) {
        $fila_num = 0;
        $top = 0;

        foreach ($mapa as $fila) {
            $fila_num++;
            $left = 0;

            foreach ($fila as $columna) {
                if ($columna === 'A') {
                    $codigo = "F{$fila_num}-" . ($left); // ejemplo: F1-0
                    $stmt = $this->db->prepare("
                        INSERT INTO asientos (codigo, top_pos, left_pos, discapacitado, estado, evento_id)
                        VALUES (:codigo, :top_pos, :left_pos, :discapacitado, :estado, :evento_id)
                    ");
                    $stmt->execute([
                        'codigo' => $codigo,
                        'top_pos' => $top,
                        'left_pos' => $left,
                        'discapacitado' => 0,
                        'estado' => 'habilitado',
                        'evento_id' => $evento_id
                    ]);

                    $left += $tamano_asiento + $gap_asientos;
                } elseif ($columna === ' ') {
                    $left += $gap_espacios;
                }
            }

            $top += $tamano_asiento + $gap_asientos;
        }
    }
    public function generarAsientos($evento_id, $matrix, $maxima_capacidad) {
        $patron = $matrix['patron'] ?? [];
        $filas_matrix = $matrix['filas'] ?? 1;

        $asientoWidth = 30;
        $espacioWidth = 50;
        $margen = 5;

        $letras = range('A', 'Z');

        $sql = "
            INSERT INTO asientos (codigo, top_pos, left_pos, discapacitado, estado, evento_id, eliminado)
            VALUES (:codigo, :top_pos, :left_pos, 0, 'habilitado', :evento_id, 0)
        ";
        $stmt = $this->db->prepare($sql);

        $totalAsientosGenerados = 0;
        $fila = 0;

        while ($totalAsientosGenerados < $maxima_capacidad) {
            $top = $fila * ($asientoWidth + $margen);
            $left = 0;
            $contador = 1;

            foreach ($patron as $bloque) {
                if ($totalAsientosGenerados >= $maxima_capacidad) {
                    break 2; // salir del foreach y del while
                }

                if (isset($bloque['asientos'])) {
                    for ($i = 0; $i < $bloque['asientos']; $i++) {
                        if ($totalAsientosGenerados >= $maxima_capacidad) {
                            break 2;
                        }

                        $codigo = $letras[$fila % 26] . $contador;

                        $stmt->execute([
                            'codigo' => $codigo,
                            'top_pos' => $top,
                            'left_pos' => $left,
                            'evento_id' => $evento_id
                        ]);

                        $contador++;
                        $totalAsientosGenerados++;
                        $left += $asientoWidth + $margen;
                    }
                } elseif (isset($bloque['espacio'])) {
                    $left += $espacioWidth * $bloque['espacio'];
                }
            }

            $fila++;
        }
    }



}
