<?php
require_once __DIR__ . '/../config/Database.php';

class SuscripcionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerPorUsuario($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT s.id_suscripcion, s.id_plan, s.ciclo, s.estado_scrip, s.fecha_inicio, s.fecha_fin, s.auto_renovar,
                   p.nombre_plan, p.precio_mensual, p.precio_anual
            FROM suscripciones s
            INNER JOIN planes p ON s.id_plan = p.id_plan
            WHERE s.id_usuario = :id_usuario
            ORDER BY s.fecha_fin DESC, s.id_suscripcion DESC
            LIMIT 1
        ");
        $stmt->execute([':id_usuario' => $id_usuario]);
        $sub = $stmt->fetch();

        if ($sub) {
            $hoy = strtotime(date('Y-m-d'));
            $fechaFin = strtotime($sub['fecha_fin']);

            if ($sub['estado_scrip'] === 'cancelada') {
                $sub['estado_real'] = 'cancelada';
                $sub['dias_restantes'] = max(0, (int)round(($fechaFin - $hoy) / 86400));
                $sub['dias_vencido'] = $fechaFin < $hoy ? (int)round(($hoy - $fechaFin) / 86400) : 0;
            } elseif ($fechaFin < $hoy || $sub['estado_scrip'] === 'vencida') {
                $sub['estado_real'] = 'vencida';
                $sub['dias_restantes'] = 0;
                $sub['dias_vencido'] = (int)round(($hoy - $fechaFin) / 86400);

                // Sincronizar en base de datos si todavia estaba como 'activa'
                if ($sub['estado_scrip'] === 'activa') {
                    $updateStmt = $this->db->prepare("UPDATE suscripciones SET estado_scrip = 'vencida' WHERE id_suscripcion = :id");
                    $updateStmt->execute([':id' => $sub['id_suscripcion']]);
                    $sub['estado_scrip'] = 'vencida';
                }
            } else {
                $sub['estado_real'] = 'activa';
                $sub['dias_restantes'] = max(0, (int)round(($fechaFin - $hoy) / 86400));
                $sub['dias_vencido'] = 0;
            }
        }

        return $sub;
    }

    public function obtenerPlanesDisponibles() {
        $stmt = $this->db->query("
            SELECT id_plan, nombre_plan, precio_mensual, precio_anual, estado_plan
            FROM planes 
            WHERE estado_plan = 1 
            ORDER BY precio_mensual ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPlanPorId($id_plan) {
        $stmt = $this->db->prepare("
            SELECT id_plan, nombre_plan, precio_mensual, precio_anual, estado_plan
            FROM planes 
            WHERE id_plan = :id_plan AND estado_plan = 1 
            LIMIT 1
        ");
        $stmt->execute([':id_plan' => $id_plan]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearOActualizar($id_usuario, $id_plan, $ciclo = 'mensual') {
        $ciclo = ($ciclo === 'anual') ? 'anual' : 'mensual';
        $diasPeriodo = ($ciclo === 'anual') ? '+1 year' : '+1 month';

        $fechaInicio = date('Y-m-d');
        $fechaFin    = date('Y-m-d', strtotime($diasPeriodo));

        // Verificar si ya existe alguna suscripción previa
        $stmtExiste = $this->db->prepare("
            SELECT id_suscripcion, fecha_fin, estado_scrip 
            FROM suscripciones 
            WHERE id_usuario = :id_u 
            ORDER BY fecha_fin DESC, id_suscripcion DESC 
            LIMIT 1
        ");
        $stmtExiste->execute([':id_u' => $id_usuario]);
        $subActual = $stmtExiste->fetch(PDO::FETCH_ASSOC);

        if ($subActual) {
            $idSub = (int)$subActual['id_suscripcion'];

            // Si la suscripción actual sigue activa y no ha vencido, podemos extenderla a partir de su fecha_fin
            if ($subActual['estado_scrip'] === 'activa' && strtotime($subActual['fecha_fin']) > time()) {
                $fechaInicio = date('Y-m-d');
                $fechaFin = date('Y-m-d', strtotime($subActual['fecha_fin'] . ' ' . $diasPeriodo));
            }

            $stmtUpdate = $this->db->prepare("
                UPDATE suscripciones 
                SET id_plan = :id_plan, ciclo = :ciclo, estado_scrip = 'activa',
                    fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin,
                    auto_renovar = 1, actualizado_en = NOW()
                WHERE id_suscripcion = :id_sub
            ");
            $stmtUpdate->execute([
                ':id_plan'      => $id_plan,
                ':ciclo'        => $ciclo,
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin'    => $fechaFin,
                ':id_sub'       => $idSub
            ]);
            return $idSub;
        } else {
            $stmtInsert = $this->db->prepare("
                INSERT INTO suscripciones (id_usuario, id_plan, ciclo, estado_scrip, fecha_inicio, fecha_fin, auto_renovar, creado_en)
                VALUES (:id_u, :id_plan, :ciclo, 'activa', :fecha_inicio, :fecha_fin, 1, NOW())
            ");
            $stmtInsert->execute([
                ':id_u'         => $id_usuario,
                ':id_plan'      => $id_plan,
                ':ciclo'        => $ciclo,
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin'    => $fechaFin
            ]);
            return (int)$this->db->lastInsertId();
        }
    }
}
