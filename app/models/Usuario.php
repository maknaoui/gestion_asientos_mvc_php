<?php

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lista todos los usuarios
     */
    public function listar() {
        $stmt = $this->db->query("SELECT id, nombre, apellido, correo FROM usuarios ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT id, nombre, apellido, correo FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener usuario por correo (incluye la clave para login)
     */
    public function obtenerPorCorreo($correo) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE correo = :correo");
        $stmt->execute(['correo' => $correo]);
        return $stmt->fetch();
    }

    /**
     * Crear usuario
     */
    public function crear($nombre, $apellido, $correo, $clave) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, apellido, correo, clave)
            VALUES (:nombre, :apellido, :correo, :clave)
        ");

        return $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'clave' => $hash
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $nombre, $apellido, $correo, $clave = null) {
        if ($clave) {
            $hash = password_hash($clave, PASSWORD_DEFAULT);

            $sql = "
                UPDATE usuarios
                SET nombre = :nombre, apellido = :apellido, correo = :correo, clave = :clave
                WHERE id = :id
            ";
            $params = [
                'id' => $id,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'clave' => $hash
            ];
        } else {
            $sql = "
                UPDATE usuarios
                SET nombre = :nombre, apellido = :apellido, correo = :correo
                WHERE id = :id
            ";
            $params = [
                'id' => $id,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Verifica credenciales
     */
    public function verificarCredenciales($correo, $clave) {
        $usuario = $this->obtenerPorCorreo($correo);

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            unset($usuario['clave']);
            return $usuario;
        }

        return false;
    }
}
