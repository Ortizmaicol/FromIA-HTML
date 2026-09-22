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

    public function contarTotal() {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM usuarios WHERE estado_usr != 'eliminado'");
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }

    public function obtenerRecientes($limite = 5) {
        $stmt = $this->db->prepare("
            SELECT id_usuario, nombre, apellido, email, nomb_rol, estado_usr, nombre_plan 
            FROM v_usuarios 
            ORDER BY id_usuario DESC 
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.telefono, u.id_rol, u.estado_usr, u.creado_en, r.nomb_rol AS rol
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.id_usuario = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetch();
    }

    public function emailExisteParaOtro($email, $id_usuario) {
        $stmt = $this->db->prepare("
            SELECT id_usuario 
            FROM usuarios 
            WHERE email = :email AND id_usuario != :id 
            LIMIT 1
        ");
        $stmt->execute([':email' => $email, ':id' => $id_usuario]);
        return (bool)$stmt->fetch();
    }

    public function actualizarContacto($id_usuario, $email, $telefono) {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET email = :email, telefono = :telefono, actualizado_en = NOW() 
            WHERE id_usuario = :id
        ");
        return $stmt->execute([
            ':email'    => $email,
            ':telefono' => $telefono,
            ':id'       => $id_usuario
        ]);
    }

    public function obtenerTodos($busqueda = '', $rol = '', $estado = '') {
        $sql = "SELECT * FROM v_usuarios WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE :busq1 OR apellido LIKE :busq2 OR email LIKE :busq3)";
            $params[':busq1'] = '%' . $busqueda . '%';
            $params[':busq2'] = '%' . $busqueda . '%';
            $params[':busq3'] = '%' . $busqueda . '%';
        }

        if (!empty($rol)) {
            $sql .= " AND nomb_rol = :rol";
            $params[':rol'] = $rol;
        }

        if (!empty($estado)) {
            $sql .= " AND estado_usr = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY id_usuario DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function contarPorEstado($estado) {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE estado_usr = :estado");
        $stmt->execute([':estado' => $estado]);
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }

    public function darDeBaja($id_usuario) {
        $stmt = $this->db->prepare("CALL sp_dar_baja_usuario(:id)");
        return $stmt->execute([':id' => $id_usuario]);
    }

    public function cambiarRol($id_usuario, $id_rol) {
        $stmt = $this->db->prepare("UPDATE usuarios SET id_rol = :id_rol, actualizado_en = NOW() WHERE id_usuario = :id");
        return $stmt->execute([
            ':id_rol' => $id_rol,
            ':id'     => $id_usuario
        ]);
    }

    public function cambiarEstado($id_usuario, $estado) {
        $stmt = $this->db->prepare("UPDATE usuarios SET estado_usr = :estado, actualizado_en = NOW() WHERE id_usuario = :id");
        return $stmt->execute([
            ':estado' => $estado,
            ':id'     => $id_usuario
        ]);
    }

    public function obtenerSesionesActivas() {
        $stmt = $this->db->query("SELECT * FROM v_sesiones_act ORDER BY creado_en DESC");
        return $stmt->fetchAll();
    }

    public function registrarSesionActiva($id_usuario) {
        $stmt = $this->db->prepare("
            INSERT INTO sesiones (id_usuario, creado_en, expira_en, revocada)
            VALUES (:id_usuario, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 0)
        ");
        return $stmt->execute([':id_usuario' => $id_usuario]);
    }

    public function revocarSesiones($id_usuario) {
        $stmt = $this->db->prepare("UPDATE sesiones SET revocada = 1 WHERE id_usuario = :id_usuario");
        return $stmt->execute([':id_usuario' => $id_usuario]);
    }

    public function obtenerReporteCompletoUsuarios() {
        $stmt = $this->db->query("
            SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.estado_usr, u.nomb_rol,
                   u.estado_scrip, u.suscripcion_vence, u.nombre_plan, u.precio_mensual,
                   COALESCE(rp.total_pagos, 0) AS total_pagos,
                   COALESCE(rp.total_pagado, 0.00) AS total_pagado,
                   rp.ultimo_pago
            FROM v_usuarios u
            LEFT JOIN v_resumen_pagos rp ON u.id_usuario = rp.id_usuario
            ORDER BY u.id_usuario DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
