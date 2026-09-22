<?php
require_once __DIR__ . '/../config/Database.php';

class PatronModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT id_patron, nomb_patron, desc_patron, tipo_patron, estado_patron, fecha_creacion, ubicacion, archivo_json
            FROM patron
            WHERE id_usuario = :id_usuario AND estado_patron != 'eliminado'
            ORDER BY fecha_creacion DESC
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public function obtenerPorIdYUsuario($id_patron, $id_usuario) {
        $stmt = $this->db->prepare("
            SELECT id_patron, nomb_patron, desc_patron, tipo_patron, estado_patron, fecha_creacion, ubicacion, archivo_json
            FROM patron
            WHERE id_patron = :id_patron AND id_usuario = :id_usuario AND estado_patron != 'eliminado'
            LIMIT 1
        ");
        $stmt->execute([
            ':id_patron'  => $id_patron,
            ':id_usuario' => $id_usuario
        ]);
        return $stmt->fetch();
    }

    public function crear($id_usuario, $nomb_patron, $desc_patron, $tipo_patron, $ubicacion = '', $archivo_json = '{}') {
        $tiposPermitidos = ['patronaje', 'diseño', '3d', 'mixto'];
        if (!in_array($tipo_patron, $tiposPermitidos, true)) {
            $tipo_patron = 'patronaje';
        }

        $stmt = $this->db->prepare("
            INSERT INTO patron (id_usuario, nomb_patron, desc_patron, tipo_patron, estado_patron, ubicacion, archivo_json)
            VALUES (:id_usuario, :nomb_patron, :desc_patron, :tipo_patron, 'activo', :ubicacion, :archivo_json)
        ");
        $res = $stmt->execute([
            ':id_usuario'   => $id_usuario,
            ':nomb_patron'  => $nomb_patron,
            ':desc_patron'  => $desc_patron,
            ':tipo_patron'  => $tipo_patron,
            ':ubicacion'    => $ubicacion,
            ':archivo_json' => !empty($archivo_json) ? $archivo_json : '{}'
        ]);

        return $res ? (int)$this->db->lastInsertId() : false;
    }

    public function actualizar($id_patron, $id_usuario, $nomb_patron, $desc_patron, $tipo_patron, $estado_patron = 'activo') {
        $tiposPermitidos = ['patronaje', 'diseño', '3d', 'mixto'];
        if (!in_array($tipo_patron, $tiposPermitidos, true)) {
            $tipo_patron = 'patronaje';
        }

        $estadosPermitidos = ['activo', 'archivado'];
        if (!in_array($estado_patron, $estadosPermitidos, true)) {
            $estado_patron = 'activo';
        }

        $stmt = $this->db->prepare("
            UPDATE patron
            SET nomb_patron = :nomb_patron,
                desc_patron = :desc_patron,
                tipo_patron = :tipo_patron,
                estado_patron = :estado_patron
            WHERE id_patron = :id_patron AND id_usuario = :id_usuario AND estado_patron != 'eliminado'
        ");
        return $stmt->execute([
            ':nomb_patron'   => $nomb_patron,
            ':desc_patron'   => $desc_patron,
            ':tipo_patron'   => $tipo_patron,
            ':estado_patron' => $estado_patron,
            ':id_patron'     => $id_patron,
            ':id_usuario'    => $id_usuario
        ]);
    }

    public function cambiarEstado($id_patron, $id_usuario, $nuevo_estado) {
        $estadosPermitidos = ['activo', 'archivado', 'eliminado'];
        if (!in_array($nuevo_estado, $estadosPermitidos, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE patron
            SET estado_patron = :nuevo_estado
            WHERE id_patron = :id_patron AND id_usuario = :id_usuario
        ");
        return $stmt->execute([
            ':nuevo_estado' => $nuevo_estado,
            ':id_patron'    => $id_patron,
            ':id_usuario'   => $id_usuario
        ]);
    }

    public function eliminar($id_patron, $id_usuario) {
        // 1. Si tenía un archivo físico adjunto, eliminarlo del disco
        $patron = $this->obtenerPorIdYUsuario($id_patron, $id_usuario);
        if ($patron && !empty($patron['ubicacion'])) {
            $archivoRuta = __DIR__ . '/../' . $patron['ubicacion'];
            if (file_exists($archivoRuta) && is_file($archivoRuta)) {
                @unlink($archivoRuta);
            }
        }

        // 2. Borrado físico permanente de la fila en MySQL
        $stmt = $this->db->prepare("
            DELETE FROM patron 
            WHERE id_patron = :id_patron AND id_usuario = :id_usuario
        ");
        return $stmt->execute([
            ':id_patron'  => $id_patron,
            ':id_usuario' => $id_usuario
        ]);
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

    public function contarTotalPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM patron
            WHERE id_usuario = :id_usuario AND estado_patron != 'eliminado'
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }

    public function contarTotal() {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total
            FROM patron
            WHERE estado_patron != 'eliminado'
        ");
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }

    public function obtenerReporteProyectos($fechaInicio = null, $fechaFin = null) {
        $sql = "
            SELECT p.id_patron, p.nomb_patron, p.desc_patron, p.tipo_patron, p.estado_patron, p.fecha_creacion,
                   u.nombre, u.apellido, u.email
            FROM patron p
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.estado_patron != 'eliminado'
        ";
        $params = [];
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $finInclusive = date('Y-m-d', strtotime($fechaFin . ' +1 day'));
            $sql .= " AND p.fecha_creacion >= :inicio AND p.fecha_creacion < :fin";
            $params[':inicio'] = $fechaInicio;
            $params[':fin']    = $finInclusive;
        }
        $sql .= " ORDER BY p.fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
