<?php
require_once __DIR__ . '/../config/Database.php';

class PatronModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT id_patron, nomb_patron, desc_patron, tipo_patron, estado_patron, fecha_creacion
            FROM patron
            WHERE id_usuario = :id_usuario AND estado_patron != 'eliminado'
            ORDER BY fecha_creacion DESC
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public function contarActivosPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM patron
            WHERE id_usuario = :id_usuario AND estado_patron = 'activo'
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }
}
