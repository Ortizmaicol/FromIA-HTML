<?php
/**
 * Script de Poblado de Datos Realistas para FormAI
 * Genera 3 registros de pago por día desde el 01/01/2025 hasta la fecha actual,
 * además de nuevos clientes, suscripciones asociadas y proyectos en la tabla patron.
 */

ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../config/Database.php';

$isCli = (php_sapi_name() === 'cli');
$eol = $isCli ? PHP_EOL : "<br>";

echo "=== INICIANDO SCRIPT DE POBLADO DE DATOS (FORM-AI) ===" . $eol;

$db = Database::getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $db->beginTransaction();

    // ==========================================
    // 1. POOL DE CLIENTES NUEVOS
    // ==========================================
    echo "1. Verificando y creando pool de clientes..." . $eol;

    $clientesNuevos = [
        ['Camila', 'Restrepo', '3104567890', 'camila.restrepo@textil.co'],
        ['Daniela', 'Vargas', '3157891234', 'dvargas.moda@gmail.com'],
        ['Santiago', 'Herrera', '3189012345', 's.herrera@atelier.com'],
        ['Mateo', 'Gómez', '3201234567', 'mateo.gomez@confecciones.net'],
        ['Lucía', 'Morales', '3112345678', 'lucia.morales@estilomoda.com'],
        ['Andrés', 'Castro', '3145678901', 'acastro@patronajedigital.com'],
        ['Valeria', 'Mendoza', '3167890123', 'valeria.mendoza@creaciones.co'],
        ['Alejandro', 'Silva', '3178901234', 'a.silva@disenotextil.org'],
        ['Mariana', 'Ríos', '3190123456', 'marianarios@tendencias.com'],
        ['Felipe', 'Torres', '3212345678', 'ftorres@manufactura.com'],
        ['Carolina', 'Navarro', '3223456789', 'c.navarro@couture.es'],
        ['Esteban', 'Rincón', '3234567890', 'esteban.rincon@indumentaria.co'],
        ['Gabriela', 'Cárdenas', '3245678901', 'gabriela.c@fashionstudio.com'],
        ['Tomás', 'Pardo', '3256789012', 'tomas.pardo@alta costura.com'],
        ['Julieta', 'Romero', '3267890123', 'julieta.romero@modasostenible.com']
    ];

    $defaultHash = password_hash('cliente123', PASSWORD_BCRYPT, ['cost' => 10]);

    $stmtCheckUser = $db->prepare("SELECT id_usuario FROM usuarios WHERE email = :email LIMIT 1");
    $stmtInsertUser = $db->prepare("
        INSERT INTO usuarios (nombre, apellido, telefono, email, password_hash, id_rol, estado_usr, creado_en)
        VALUES (:nombre, :apellido, :telefono, :email, :hash, 2, 'activo', '2024-12-20 10:00:00')
    ");

    $usuariosClientesIds = [];

    // Obtener clientes existentes que sean rol 2 y activos
    $stmtExistentes = $db->query("SELECT id_usuario FROM usuarios WHERE id_rol = 2 AND estado_usr = 'activo'");
    while ($row = $stmtExistentes->fetch(PDO::FETCH_ASSOC)) {
        $usuariosClientesIds[] = (int)$row['id_usuario'];
    }

    foreach ($clientesNuevos as $c) {
        $stmtCheckUser->execute([':email' => $c[3]]);
        $existe = $stmtCheckUser->fetch(PDO::FETCH_ASSOC);
        if ($existe) {
            $usuariosClientesIds[] = (int)$existe['id_usuario'];
        } else {
            $stmtInsertUser->execute([
                ':nombre'    => $c[0],
                ':apellido'  => $c[1],
                ':telefono'  => $c[2],
                ':email'     => $c[3],
                ':hash'      => $defaultHash
            ]);
            $usuariosClientesIds[] = (int)$db->lastInsertId();
        }
    }

    $usuariosClientesIds = array_values(array_unique($usuariosClientesIds));
    echo " -> Pool de clientes listo con " . count($usuariosClientesIds) . " usuarios clientes activos." . $eol;

    // ==========================================
    // 2. CREACIÓN / ASIGNACIÓN DE SUSCRIPCIONES
    // ==========================================
    echo "2. Verificando suscripciones para los clientes..." . $eol;

    $stmtCheckSub = $db->prepare("SELECT id_suscripcion, id_plan FROM suscripciones WHERE id_usuario = :id_u LIMIT 1");
    $stmtInsertSub = $db->prepare("
        INSERT INTO suscripciones (id_usuario, id_plan, ciclo, estado_scrip, fecha_inicio, fecha_fin, auto_renovar, creado_en)
        VALUES (:id_u, :id_plan, 'mensual', 'activa', '2025-01-01', '2027-12-31', 1, '2025-01-01 08:00:00')
    ");

    // Precios y planes: 1 -> Estudiante ($9.99), 2 -> Profesional ($24.99), 3 -> Empresa ($79.99)
    $usuarioSubMap = []; // [id_usuario => ['id_sub' => ..., 'id_plan' => ..., 'monto' => ...]]

    $planPrecios = [
        1 => 9.99,
        2 => 24.99,
        3 => 79.99
    ];

    foreach ($usuariosClientesIds as $idx => $idU) {
        $stmtCheckSub->execute([':id_u' => $idU]);
        $sub = $stmtCheckSub->fetch(PDO::FETCH_ASSOC);
        if ($sub) {
            $idPlan = (int)$sub['id_plan'];
            $idSub  = (int)$sub['id_suscripcion'];
        } else {
            // Asignar plan rotativo: Profesional mayoría, Estudiante y Empresa balanceados
            $idPlan = ($idx % 3 === 0) ? 3 : (($idx % 2 === 0) ? 1 : 2);
            $stmtInsertSub->execute([
                ':id_u'    => $idU,
                ':id_plan' => $idPlan
            ]);
            $idSub = (int)$db->lastInsertId();
        }

        $usuarioSubMap[$idU] = [
            'id_sub'  => $idSub,
            'id_plan' => $idPlan,
            'monto'   => $planPrecios[$idPlan] ?? 24.99
        ];
    }
    echo " -> Suscripciones verificadas y vinculadas correctamente." . $eol;

    // ==========================================
    // 3. GENERACIÓN DE 3 PAGOS POR DÍA
    // ==========================================
    $fechaInicioStr = '2025-01-01';
    $fechaFinStr    = '2026-09-22';

    echo "3. Generando 3 pagos diarios desde {$fechaInicioStr} hasta {$fechaFinStr}..." . $eol;

    $inicio = new DateTime($fechaInicioStr);
    $fin    = new DateTime($fechaFinStr);
    $fin->modify('+1 day'); // Para incluir el día fin

    $stmtInsertPago = $db->prepare("
        INSERT INTO pagos (id_usuario, id_suscripcion, monto, moneda, metodo, nro_transaccion, estado, creado_en, idempotency_key)
        VALUES (:id_usuario, :id_suscripcion, :monto, 'USD', :metodo, :nro_transaccion, :estado, :creado_en, :idempotency)
    ");

    $metodos = ['tarjeta', 'tarjeta', 'tarjeta', 'paypal', 'paypal', 'transferencia'];
    $contadorPagos = 0;
    $totalRecaudadoGenerado = 0.0;
    $diasProcesados = 0;

    $intervalo = new DateInterval('P1D');
    $periodo   = new DatePeriod($inicio, $intervalo, $fin);

    foreach ($periodo as $dia) {
        $diasProcesados++;
        $diaYmd = $dia->format('Y-m-d');
        $diaSimple = $dia->format('Ymd');

        // Generar exactamente 3 transacciones en diferentes momentos del día
        $horas = [
            rand(8, 11) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
            rand(12, 16) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
            rand(17, 22) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT)
        ];

        for ($k = 0; $k < 3; $k++) {
            // Seleccionar usuario aleatorio del pool
            $randomUserId = $usuariosClientesIds[array_rand($usuariosClientesIds)];
            $subInfo = $usuarioSubMap[$randomUserId];

            $timestampStr = $diaYmd . ' ' . $horas[$k];
            $metodo = $metodos[array_rand($metodos)];
            $monto = $subInfo['monto'];

            // Distribución de estados: 95% completado, 3% pendiente, 2% reembolsado
            $randEstado = rand(1, 100);
            if ($randEstado <= 95) {
                $estado = 'completado';
                $totalRecaudadoGenerado += $monto;
            } elseif ($randEstado <= 98) {
                $estado = 'pendiente';
            } else {
                $estado = 'reembolsado';
            }

            $nroTxn = 'TXN-FA-' . $diaSimple . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . '-' . ($k + 1);
            $idempKey = 'IDEMP-' . $diaSimple . '-' . substr(bin2hex(random_bytes(8)), 0, 16) . '-' . ($k + 1);

            $stmtInsertPago->execute([
                ':id_usuario'      => $randomUserId,
                ':id_suscripcion'  => $subInfo['id_sub'],
                ':monto'           => $monto,
                ':metodo'          => $metodo,
                ':nro_transaccion' => $nroTxn,
                ':estado'          => $estado,
                ':creado_en'       => $timestampStr,
                ':idempotency'     => $idempKey
            ]);

            $contadorPagos++;
        }
    }

    echo " -> Se generaron exitosamente {$contadorPagos} pagos en {$diasProcesados} días." . $eol;
    echo " -> Total recaudado aproximado generado: $" . number_format($totalRecaudadoGenerado, 2) . " USD." . $eol;

    // ==========================================
    // 4. GENERACIÓN DE PROYECTOS / PATRONES (OPCIÓN B)
    // ==========================================
    echo "4. Generando proyectos en la tabla patron a lo largo del período..." . $eol;

    $modelosPatrones = [
        ['Vestido de Gala Silueta Sirena', 'Patrón digital de vestido largo con corte sirena, escote corazón y pinzas entalladas.', 'patronaje'],
        ['Blusa Camisera Manga Ranglan', 'Diseño de blusa ejecutiva con manga ranglan y cuello camisero clásico.', 'diseño'],
        ['Pantalón Sastre Tiro Alto', 'Gradación completa de pantalón sastre con pinzas dobles y bolsillos inclinados.', 'patronaje'],
        ['Chaqueta Denim Oversize 3D', 'Simulación 3D de chaqueta de mezclilla con bolsillos plaqué y costuras reforzadas.', '3d'],
        ['Falda Plisada Longitud Midi', 'Patronaje de falda con pliegues simétricos de 3cm para tejido plano.', 'patronaje'],
        ['Top Corsetero Estructurado', 'Diseño mixto con varillas de refuerzo y copas preformadas graduables.', 'mixto'],
        ['Abrigo Clásico Lana Paño', 'Moldería de abrigo invernal cruzado con solapas anchas y forro interior.', 'patronaje'],
        ['Conjunto Deportivo Seamless 3D', 'Render y molde de conjunto deportivo elástico transpirable para training.', '3d'],
        ['Bikini Dos Piezas Texturizado', 'Patrón de traje de baño con forro antibacterial y costuras invisibles.', 'patronaje'],
        ['Jumpsuit Cuello Halter Fiesta', 'Enterizo palazzo con espalda descubierta y escote halter estructurado.', 'diseño'],
        ['Blazer Entallado Solapa Esmoquin', 'Sastrería femenina premium con corte francés y botón forrado.', 'mixto'],
        ['Camisa Cuello Mao Lino', 'Patrón digital unisex de camisa casual veraniega en lino ligero.', 'patronaje'],
        ['Pantalón Jogger Cargo Urbano', 'Diseño urbano con bolsillos de fuelle laterales y pretina elástica.', 'diseño'],
        ['Vestido Asimétrico Drapeado 3D', 'Muestra volumétrica tridimensional con drapeado fluido sobre maniquí.', '3d']
    ];

    $stmtInsertPatron = $db->prepare("
        INSERT INTO patron (id_usuario, nomb_patron, desc_patron, tipo_patron, estado_patron, fecha_creacion, ubicacion, archivo_json)
        VALUES (:id_usuario, :nomb, :desc, :tipo, 'activo', :fecha, '', :json)
    ");

    $contadorProyectos = 0;
    $diasArray = iterator_to_array($periodo);

    // Generar un proyecto cada 1 a 2 días a lo largo de todo el período (~450 a 500 proyectos)
    foreach ($diasArray as $idxDia => $diaObj) {
        if ($idxDia % 1 === 0 || rand(0, 1) === 1) {
            $modelo = $modelosPatrones[array_rand($modelosPatrones)];
            $randomUserId = $usuariosClientesIds[array_rand($usuariosClientesIds)];
            
            $horaProj = str_pad(rand(9, 19), 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
            $fechaProj = $diaObj->format('Y-m-d') . ' ' . $horaProj;

            $jsonSpec = json_encode([
                'software'    => 'FormAI Pattern Studio',
                'version'     => '1.0',
                'proyecto'    => $modelo[0],
                'tipo'        => $modelo[2],
                'descripcion' => $modelo[1],
                'creado_en'   => $fechaProj,
                'unidades'    => 'cm',
                'piezas'      => ['delantero', 'espalda', 'mangas', 'vistas']
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $stmtInsertPatron->execute([
                ':id_usuario' => $randomUserId,
                ':nomb'       => $modelo[0] . ' (Ref. ' . ($idxDia + 1) . ')',
                ':desc'       => $modelo[1],
                ':tipo'       => $modelo[2],
                ':fecha'      => $fechaProj,
                ':json'       => $jsonSpec
            ]);

            $contadorProyectos++;
        }
    }

    echo " -> Se generaron exitosamente {$contadorProyectos} proyectos en la tabla patron." . $eol;

    // Confirmar todas las operaciones en la base de datos
    $db->commit();

    echo $eol . "==========================================" . $eol;
    echo "¡POBLADO COMPLETADO CON ÉXITO!" . $eol;
    echo "==========================================" . $eol;
    echo " Resumen de la ejecución:" . $eol;
    echo "  - Total de Días Evaluados: {$diasProcesados}" . $eol;
    echo "  - Total de Pagos Insertados: {$contadorPagos} (3 por día)" . $eol;
    echo "  - Total de Proyectos Insertados: {$contadorProyectos}" . $eol;
    echo "  - Total Clientes en el Pool: " . count($usuariosClientesIds) . $eol;
    echo "  - Total Facturado Generado: $" . number_format($totalRecaudadoGenerado, 2) . " USD" . $eol;
    echo "==========================================" . $eol;

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR: Falló el poblado de datos: " . $e->getMessage() . $eol;
    echo "Se realizó ROLLBACK. No se alteró la base de datos." . $eol;
}
