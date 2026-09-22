<?php
require_once __DIR__ . '/../config/Database.php';

class PagoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerTotalIngresos() {
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(monto), 0) AS total 
            FROM pagos 
            WHERE estado = 'completado'
        ");
        $row = $stmt->fetch();
        return $row ? (float)$row['total'] : 0.0;
    }

    public function obtenerPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT p.id_pago, p.monto, p.moneda, p.metodo, p.nro_transaccion, p.estado, p.creado_en, pl.nombre_plan
            FROM pagos p
            LEFT JOIN suscripciones s ON p.id_suscripcion = s.id_suscripcion
            LEFT JOIN planes pl ON s.id_plan = pl.id_plan
            WHERE p.id_usuario = :id_usuario
            ORDER BY p.creado_en DESC
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }

    public function registrarPago($id_usuario, $id_suscripcion, $monto, $metodo = 'tarjeta', $nro_transaccion = null, $moneda = 'USD') {
        $nro_transaccion = $nro_transaccion ?? ('TXN-FA-' . date('Ymd') . '-' . rand(1000, 9999));
        $idempotency = 'IDEMP-' . uniqid('', true);

        $stmt = $this->db->prepare("
            INSERT INTO pagos (id_usuario, id_suscripcion, monto, moneda, metodo, nro_transaccion, estado, creado_en, idempotency_key)
            VALUES (:id_usuario, :id_suscripcion, :monto, :moneda, :metodo, :nro_transaccion, 'completado', NOW(), :idempotency)
        ");
        return $stmt->execute([
            ':id_usuario'      => $id_usuario,
            ':id_suscripcion'  => $id_suscripcion,
            ':monto'           => $monto,
            ':moneda'          => $moneda,
            ':metodo'          => $metodo,
            ':nro_transaccion' => $nro_transaccion,
            ':idempotency'     => $idempotency
        ]);
    }

    public function obtenerTodos($limite = 100) {
        $stmt = $this->db->prepare("
            SELECT p.id_pago, p.id_usuario, p.monto, p.moneda, p.metodo, p.nro_transaccion, p.estado, p.creado_en,
                   u.nombre, u.apellido, u.email,
                   pl.nombre_plan
            FROM pagos p
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            LEFT JOIN suscripciones s ON p.id_suscripcion = s.id_suscripcion
            LEFT JOIN planes pl ON s.id_plan = pl.id_plan
            ORDER BY p.creado_en DESC, p.id_pago DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenPorUsuarios() {
        $stmt = $this->db->query("
            SELECT id_usuario, nombre, apellido, total_pagos, total_pagado, ultimo_pago
            FROM v_resumen_pagos
            WHERE total_pagos > 0
            ORDER BY total_pagado DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerReporteIngresos($fechaInicio, $fechaFin) {
        // Asegura inclusión completa del día final al llamar al Stored Procedure
        $finInclusive = date('Y-m-d', strtotime($fechaFin . ' +1 day'));
        $stmt = $this->db->prepare("CALL sp_reporte_ingresos(:inicio, :fin)");
        $stmt->execute([
            ':inicio' => $fechaInicio,
            ':fin'    => $finInclusive
        ]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $resultados;
    }

    public function contarTotalPagos() {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM pagos");
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }

    public function obtenerMontoPromedio() {
        $stmt = $this->db->query("SELECT AVG(monto) AS promedio FROM pagos WHERE estado = 'completado'");
        $row = $stmt->fetch();
        return $row ? (float)$row['promedio'] : 0.0;
    }

    public function obtenerPorRangoFechas($fechaInicio, $fechaFin) {
        $finInclusive = date('Y-m-d', strtotime($fechaFin . ' +1 day'));
        $stmt = $this->db->prepare("
            SELECT p.id_pago, p.id_usuario, p.monto, p.moneda, p.metodo, p.nro_transaccion, p.estado, p.creado_en,
                   u.nombre, u.apellido, u.email,
                   pl.nombre_plan
            FROM pagos p
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            LEFT JOIN suscripciones s ON p.id_suscripcion = s.id_suscripcion
            LEFT JOIN planes pl ON s.id_plan = pl.id_plan
            WHERE p.creado_en >= :inicio AND p.creado_en < :fin
            ORDER BY p.creado_en DESC, p.id_pago DESC
        ");
        $stmt->execute([
            ':inicio' => $fechaInicio,
            ':fin'    => $finInclusive
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
