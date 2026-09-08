<?php
require_once __DIR__ . '/../config/Database.php';

class UsuarioModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerPorEmail($email) {
        $stmt = $this->db->prepare("
            SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.telefono, u.password_hash, u.estado_usr, r.nomb_rol AS rol
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function registrar($nombre, $apellido, $telefono, $email, $password, $id_rol = 2) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, apellido, telefono, email, password_hash, id_rol, estado_usr)
            VALUES (:nombre, :apellido, :telefono, :email, :password_hash, :id_rol, 'activo')
        ");
        return $stmt->execute([
            ':nombre'        => $nombre,
            ':apellido'      => $apellido,
            ':telefono'      => $telefono,
            ':email'         => $email,
            ':password_hash' => $hash,
            ':id_rol'        => $id_rol
        ]);
    }

    public function actualizarUltimoLogin($id_usuario) {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET ultimo_login = NOW() 
            WHERE id_usuario = :id
        ");
        return $stmt->execute([':id' => $id_usuario]);
    }
}
