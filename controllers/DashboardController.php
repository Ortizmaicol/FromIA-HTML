<?php
require_once __DIR__ . '/../models/PatronModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/PagoModel.php';
require_once __DIR__ . '/../models/SuscripcionModel.php';

class DashboardController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Validar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FromIA-HTML/login.php');
            exit;
        }

        $idUsuario = $_SESSION['user_id'];
        $rol       = strtolower($_SESSION['user_rol'] ?? 'cliente'); // 'administrador' o 'cliente'
        $nombre    = $_SESSION['user_nombre'] ?? 'Usuario';
        $seccion   = $_GET['seccion'] ?? 'inicio';

        // 2. Despachar según el Rol
        if ($rol === 'administrador' || $rol === 'admin') {
            $this->cargarDashboardAdmin($nombre, $seccion);
        } else {
            $this->cargarDashboardCliente($idUsuario, $nombre, $seccion);
        }
    }

    private function cargarDashboardCliente($idUsuario, $nombre, $seccion = 'inicio') {
        $usuarioModel     = new UsuarioModel();
        $patronModel      = new PatronModel();
        $suscripcionModel = new SuscripcionModel();
        $pagoModel        = new PagoModel();

        $mensajeExito = null;
        $mensajeError = null;

        // Descarga de archivo/JSON de patrón
        if (isset($_GET['descargar'])) {
            $idDescarga = (int)$_GET['descargar'];
            $patronDescarga = $patronModel->obtenerPorIdYUsuario($idDescarga, $idUsuario);
            if ($patronDescarga) {
                if (!empty($patronDescarga['ubicacion']) && file_exists(__DIR__ . '/../' . $patronDescarga['ubicacion'])) {
                    $filePath = __DIR__ . '/../' . $patronDescarga['ubicacion'];
                    $fileName = basename($filePath);
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $fileName . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                    exit;
                } elseif (!empty($patronDescarga['archivo_json']) && $patronDescarga['archivo_json'] !== '{}') {
                    $cleanTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $patronDescarga['nomb_patron']);
                    $jsonFileName = ($cleanTitle ?: 'patron_' . $idDescarga) . '.json';
                    header('Content-Type: application/json; charset=utf-8');
                    header('Content-Disposition: attachment; filename="' . $jsonFileName . '"');
                    echo $patronDescarga['archivo_json'];
                    exit;
                }
            }
        }

        // Helper para procesamiento seguro de archivos
        $procesarArchivo = function($idUsuario) {
            $ubicacion = '';
            $archivoJson = '{}';
            if (isset($_FILES['archivo_patron']) && $_FILES['archivo_patron']['error'] === UPLOAD_ERR_OK) {
                $fileTmp  = $_FILES['archivo_patron']['tmp_name'];
                $fileName = $_FILES['archivo_patron']['name'];
                $fileSize = $_FILES['archivo_patron']['size'];

                // Tamaño máx 15MB
                if ($fileSize <= 15 * 1024 * 1024) {
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $extPermitidas = ['dxf', 'json', 'pdf', 'png', 'jpg', 'jpeg', 'svg'];
                    if (in_array($ext, $extPermitidas, true)) {
                        $targetDir = __DIR__ . '/../uploads/patrones/';
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }
                        $cleanName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
                        $newFileName = 'patron_' . $idUsuario . '_' . time() . '_' . substr($cleanName, 0, 20) . '.' . $ext;
                        $destPath = $targetDir . $newFileName;
                        if (move_uploaded_file($fileTmp, $destPath)) {
                            $ubicacion = 'uploads/patrones/' . $newFileName;
                            if ($ext === 'json') {
                                $jsonContent = file_get_contents($destPath);
                                if (!empty($jsonContent) && json_decode($jsonContent) !== null) {
                                    $archivoJson = $jsonContent;
                                }
                            }
                        }
                    }
                }
            }
            return [$ubicacion, $archivoJson];
        };

        // Procesar solicitudes POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'actualizar_perfil') {
                $nuevoEmail    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $nuevoTelefono = trim($_POST['telefono'] ?? '');

                if (empty($nuevoEmail) || !filter_var($nuevoEmail, FILTER_VALIDATE_EMAIL)) {
                    $mensajeError = "Por favor ingresa un correo electrónico válido.";
                } elseif (!empty($nuevoTelefono) && !preg_match('/^[0-9]{7,15}$/', $nuevoTelefono)) {
                    $mensajeError = "El teléfono debe contener entre 7 y 15 dígitos numéricos.";
                } elseif ($usuarioModel->emailExisteParaOtro($nuevoEmail, $idUsuario)) {
                    $mensajeError = "El correo electrónico ya se encuentra en uso por otra cuenta.";
                } else {
                    $actualizado = $usuarioModel->actualizarContacto($idUsuario, $nuevoEmail, $nuevoTelefono);
                    if ($actualizado) {
                        $_SESSION['user_email'] = $nuevoEmail;
                        $mensajeExito = "¡Tus datos de contacto han sido actualizados con éxito!";
                    } else {
                        $mensajeError = "No se pudieron actualizar los datos. Intenta nuevamente.";
                    }
                }
            } elseif ($accion === 'crear_patron') {
                $nombPatron = trim($_POST['nomb_patron'] ?? '');
                $descPatron = trim($_POST['desc_patron'] ?? '');
                $tipoPatron = trim($_POST['tipo_patron'] ?? 'patronaje');

                if (empty($nombPatron)) {
                    $mensajeError = "El nombre del proyecto es obligatorio.";
                } else {
                    list($ubicacion, $archivoJson) = $procesarArchivo($idUsuario);

                    // Si no se subió un archivo externo, generar especificación digital FormAI por defecto
                    if (empty($ubicacion) && ($archivoJson === '{}' || empty($archivoJson))) {
                        $archivoJson = json_encode([
                            'software'    => 'FormAI Pattern Studio',
                            'version'     => '1.0',
                            'proyecto'    => $nombPatron,
                            'tipo'        => $tipoPatron,
                            'descripcion' => $descPatron,
                            'creado_en'   => date('Y-m-d H:i:s'),
                            'unidades'    => 'cm',
                            'piezas'      => []
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    }

                    $nuevoId = $patronModel->crear($idUsuario, $nombPatron, $descPatron, $tipoPatron, $ubicacion, $archivoJson);
                    if ($nuevoId) {
                        $mensajeExito = "¡Proyecto '" . htmlspecialchars($nombPatron, ENT_QUOTES, 'UTF-8') . "' creado con éxito!";
                    } else {
                        $mensajeError = "No se pudo crear el proyecto. Intenta nuevamente.";
                    }
                }
            } elseif ($accion === 'editar_patron') {
                $idPatron     = (int)($_POST['id_patron'] ?? 0);
                $nombPatron   = trim($_POST['nomb_patron'] ?? '');
                $descPatron   = trim($_POST['desc_patron'] ?? '');
                $tipoPatron   = trim($_POST['tipo_patron'] ?? 'patronaje');
                $estadoPatron = trim($_POST['estado_patron'] ?? 'activo');

                if (empty($nombPatron)) {
                    $mensajeError = "El nombre del proyecto no puede estar vacío.";
                } else {
                    $editado = $patronModel->actualizar($idPatron, $idUsuario, $nombPatron, $descPatron, $tipoPatron, $estadoPatron);
                    if ($editado) {
                        $mensajeExito = "¡Proyecto actualizado correctamente!";
                    } else {
                        $mensajeError = "No se pudo actualizar el proyecto.";
                    }
                }
            } elseif ($accion === 'cambiar_estado_patron') {
                $idPatron    = (int)($_POST['id_patron'] ?? 0);
                $nuevoEstado = trim($_POST['nuevo_estado'] ?? 'activo');
                $cambiado    = $patronModel->cambiarEstado($idPatron, $idUsuario, $nuevoEstado);
                if ($cambiado) {
                    $mensajeExito = "El estado del proyecto se actualizó a " . ucfirst($nuevoEstado) . ".";
                } else {
                    $mensajeError = "No se pudo cambiar el estado del proyecto.";
                }
            } elseif ($accion === 'eliminar_patron') {
                $idPatron  = (int)($_POST['id_patron'] ?? 0);
                $eliminado = $patronModel->eliminar($idPatron, $idUsuario);
                if ($eliminado) {
                    $mensajeExito = "El proyecto ha sido eliminado.";
                } else {
                    $mensajeError = "No se pudo eliminar el proyecto.";
                }
            } elseif ($accion === 'emular_pago_membresia' || $accion === 'adquirir_membresia') {
                $idPlanElegido = (int)($_POST['id_plan'] ?? 0);
                $cicloElegido  = trim($_POST['ciclo'] ?? 'mensual');
                $metodoPago    = trim($_POST['metodo'] ?? 'tarjeta');
                $permitidosMetodos = ['tarjeta', 'paypal', 'transferencia', 'otro'];
                if (!in_array($metodoPago, $permitidosMetodos, true)) {
                    $metodoPago = 'tarjeta';
                }

                $planInfo = $suscripcionModel->obtenerPlanPorId($idPlanElegido);
                if (!$planInfo) {
                    $mensajeError = "El plan seleccionado no existe o no se encuentra disponible.";
                } else {
                    $montoCobro = ($cicloElegido === 'anual') ? (float)$planInfo['precio_anual'] : (float)$planInfo['precio_mensual'];
                    
                    // 1. Crear o actualizar la suscripción activa
                    $idSub = $suscripcionModel->crearOActualizar($idUsuario, $idPlanElegido, $cicloElegido);

                    if ($idSub) {
                        // 2. Registrar el pago en la tabla pagos
                        $nroTxn = 'TXN-FA-' . date('Ymd') . '-' . rand(1000, 9999);
                        $pagoOk = $pagoModel->registrarPago($idUsuario, $idSub, $montoCobro, $metodoPago, $nroTxn, 'USD');

                        if ($pagoOk) {
                            $subPrevia = $suscripcionModel->obtenerPorUsuario($idUsuario);
                            $esRenovacion = ($subPrevia && ($subPrevia['estado_real'] ?? '') === 'vencida');
                            $tipoMensaje = $esRenovacion ? "renovada con éxito" : "activada exitosamente";
                            
                            $mensajeExito = "¡Pago de $" . number_format($montoCobro, 2) . " USD completado! Tu membresía '" . htmlspecialchars($planInfo['nombre_plan'], ENT_QUOTES, 'UTF-8') . "' ha sido {$tipoMensaje}. N° de comprobante: " . $nroTxn;
                        } else {
                            $mensajeError = "La membresía se registró pero hubo un problema al asentar la factura de pago.";
                        }
                    } else {
                        $mensajeError = "No se pudo procesar la suscripción. Por favor intenta nuevamente.";
                    }
                }
            }
        }

        $usuarioActual     = $usuarioModel->obtenerPorId($idUsuario);
        $proyectos         = $patronModel->obtenerPorUsuario($idUsuario);
        $totalActivos      = $patronModel->contarActivosPorUsuario($idUsuario);
        $totalProyectos    = count($proyectos);
        $suscripcion       = $suscripcionModel->obtenerPorUsuario($idUsuario);
        $historialPagos    = $pagoModel->obtenerPorUsuario($idUsuario);
        $planesDisponibles = $suscripcionModel->obtenerPlanesDisponibles();
        $diasRestantes     = $suscripcion['dias_restantes'] ?? 0;

        // Variables disponibles para la vista del cliente
        $extraCss = "dashboard.css";

        if ($seccion === 'perfil') {
            $pageTitle = "FormAI - Mi Perfil";
            require __DIR__ . '/../views/dashboard/cliente/perfil.php';
        } elseif ($seccion === 'membresia') {
            $pageTitle = "FormAI - Mi Membresía";
            require __DIR__ . '/../views/dashboard/cliente/membresia.php';
        } elseif ($seccion === 'proyectos') {
            $pageTitle = "FormAI - Mis Proyectos";
            require __DIR__ . '/../views/dashboard/cliente/proyectos.php';
        } else {
            $pageTitle = "FormAI - Mi Espacio";
            require __DIR__ . '/../views/dashboard/cliente/index.php';
        }
    }

    private function cargarDashboardAdmin($nombre, $seccion = 'inicio') {
        $usuarioModel = new UsuarioModel();
        $patronModel  = new PatronModel();
        $pagoModel    = new PagoModel();

        $mensajeExito = null;
        $mensajeError = null;

        // Procesar acciones administrativas vía POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'dar_baja_usuario') {
                $idUsuarioBaja = (int)($_POST['id_usuario'] ?? 0);
                if ($idUsuarioBaja > 0 && $idUsuarioBaja !== (int)($_SESSION['user_id'] ?? 0)) {
                    $ok = $usuarioModel->darDeBaja($idUsuarioBaja);
                    if ($ok) {
                        $mensajeExito = "El usuario #$idUsuarioBaja ha sido dado de baja exitosamente mediante el procedimiento seguro de la base de datos (sesiones revocadas y suscripciones canceladas).";
                    } else {
                        $mensajeError = "No se pudo procesar la baja del usuario.";
                    }
                } else {
                    $mensajeError = "ID de usuario inválido o no puedes darte de baja a ti mismo.";
                }
            } elseif ($accion === 'cambiar_rol') {
                $idUsuarioRol = (int)($_POST['id_usuario'] ?? 0);
                $nuevoRolId   = (int)($_POST['id_rol'] ?? 2);
                if ($idUsuarioRol > 0 && $idUsuarioRol !== (int)($_SESSION['user_id'] ?? 0)) {
                    $ok = $usuarioModel->cambiarRol($idUsuarioRol, $nuevoRolId);
                    if ($ok) {
                        $mensajeExito = "El rol del usuario ha sido actualizado correctamente.";
                    } else {
                        $mensajeError = "No se pudo actualizar el rol del usuario.";
                    }
                } else {
                    $mensajeError = "No puedes cambiar el rol de tu propia cuenta de administrador en sesión.";
                }
            } elseif ($accion === 'cambiar_estado_usuario') {
                $idUsuarioEstado = (int)($_POST['id_usuario'] ?? 0);
                $nuevoEstado     = trim($_POST['nuevo_estado'] ?? 'activo');
                $permitidos      = ['activo', 'inactivo', 'suspendido', 'eliminado'];
                if ($idUsuarioEstado > 0 && in_array($nuevoEstado, $permitidos, true)) {
                    if ($nuevoEstado === 'eliminado') {
                        $usuarioModel->darDeBaja($idUsuarioEstado);
                    } else {
                        $usuarioModel->cambiarEstado($idUsuarioEstado, $nuevoEstado);
                    }
                    $mensajeExito = "El estado del usuario ha sido actualizado a " . ucfirst($nuevoEstado) . ".";
                } else {
                    $mensajeError = "Estado o usuario inválido.";
                }
            }
        }

        $extraCss = "dashboard.css";

        if ($seccion === 'usuarios') {
            $pageTitle = "FormAI - Usuarios del Sistema";
            $busqueda  = trim($_GET['q'] ?? '');
            $filtroRol = trim($_GET['rol'] ?? '');
            $filtroEst = trim($_GET['estado'] ?? '');

            $usuarios         = $usuarioModel->obtenerTodos($busqueda, $filtroRol, $filtroEst);
            $totalUsuarios    = $usuarioModel->contarTotal();
            $activosCount     = $usuarioModel->contarPorEstado('activo');
            $suspendidosCount = $usuarioModel->contarPorEstado('suspendido');
            $eliminadosCount  = $usuarioModel->contarPorEstado('eliminado');
            $sesionesActivas  = $usuarioModel->obtenerSesionesActivas();

            require __DIR__ . '/../views/dashboard/administrador/usuarios.php';
        } elseif ($seccion === 'pagos') {
            $pageTitle       = "FormAI - Historial de Pagos";
            $historialPagos  = $pagoModel->obtenerTodos(150);
            $resumenClientes = $pagoModel->obtenerResumenPorUsuarios();
            $totalIngresos   = $pagoModel->obtenerTotalIngresos();
            $totalPagos      = $pagoModel->contarTotalPagos();
            $montoPromedio   = $pagoModel->obtenerMontoPromedio();

            require __DIR__ . '/../views/dashboard/administrador/pagos.php';
        } elseif ($seccion === 'reportes') {
            $pageTitle   = "FormAI - Reportes y Estadísticas";
            $tipoReporte = trim($_GET['tipo_reporte'] ?? 'ingresos');
            $fechaInicio = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-01-01');
            $fechaFin    = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
            $exportar    = trim($_GET['exportar'] ?? '');

            // Si se solicita exportación directa (Excel o CSV)
            if ($exportar === 'excel' || $exportar === 'csv') {
                $this->exportarReporte($tipoReporte, $exportar, $fechaInicio, $fechaFin, $pagoModel, $usuarioModel, $patronModel);
                exit;
            }

            // Datos comunes
            $totalIngresos = $pagoModel->obtenerTotalIngresos();
            $totalPagos    = $pagoModel->contarTotalPagos();

            // Datos específicos por tipo de reporte
            $reporteIngresos  = [];
            $transacciones    = [];
            $topClientes      = [];
            $usuariosReporte  = [];
            $proyectosReporte = [];
            $totalRango       = 0.0;
            $pagosRango       = 0;

            if ($tipoReporte === 'ingresos') {
                // Procedimiento Almacenado sp_reporte_ingresos + Transacciones en rango
                $reporteIngresos = $pagoModel->obtenerReporteIngresos($fechaInicio, $fechaFin);
                $transacciones   = $pagoModel->obtenerPorRangoFechas($fechaInicio, $fechaFin);
                $topClientes     = $pagoModel->obtenerResumenPorUsuarios();

                foreach ($reporteIngresos as $rep) {
                    $totalRango += (float)($rep['total_ingresos'] ?? 0);
                    $pagosRango += (int)($rep['num_pagos'] ?? 0);
                }
            } elseif ($tipoReporte === 'usuarios') {
                // Vista v_usuarios + v_resumen_pagos
                $usuariosReporte = $usuarioModel->obtenerReporteCompletoUsuarios();
            } elseif ($tipoReporte === 'proyectos') {
                // Tabla patron + usuarios
                $proyectosReporte = $patronModel->obtenerReporteProyectos($fechaInicio, $fechaFin);
            }

            $promedioRango = ($pagosRango > 0) ? ($totalRango / $pagosRango) : 0.0;
            $maxIngresoDia = 0.0;
            foreach ($reporteIngresos as $item) {
                if ((float)$item['total_ingresos'] > $maxIngresoDia) {
                    $maxIngresoDia = (float)$item['total_ingresos'];
                }
            }

            // Si es petición AJAX para actualización en tiempo real
            if (!empty($_GET['ajax'])) {
                header('Content-Type: application/json; charset=UTF-8');
                ob_start();
                require __DIR__ . '/../views/dashboard/administrador/reportes_contenido.php';
                $html = ob_get_clean();
                echo json_encode([
                    'success'     => true,
                    'html'        => $html,
                    'tipoReporte' => $tipoReporte,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin'    => $fechaFin,
                    'totalRango'  => $totalRango,
                    'pagosRango'  => $pagosRango
                ]);
                exit;
            }

            require __DIR__ . '/../views/dashboard/administrador/reportes.php';
        } else {
            // Dashboard Inicio (Métricas Globales)
            $pageTitle         = "FormAI - Panel de Administración";
            $totalUsuarios     = $usuarioModel->contarTotal();
            $totalPatrones     = $patronModel->contarTotal();
            $totalIngresos     = $pagoModel->obtenerTotalIngresos();
            $usuariosRecientes = $usuarioModel->obtenerRecientes(5);
            $sesionesActivas   = $usuarioModel->obtenerSesionesActivas();

            require __DIR__ . '/../views/dashboard/administrador/index.php';
        }
    }

    private function exportarReporte($tipoReporte, $formato, $fechaInicio, $fechaFin, $pagoModel, $usuarioModel, $patronModel) {
        $timestamp = date('Ymd_His');
        $filenameBase = "reporte_{$tipoReporte}_{$timestamp}";

        if ($tipoReporte === 'ingresos') {
            $transacciones = $pagoModel->obtenerPorRangoFechas($fechaInicio, $fechaFin);
            $totalMonto = 0.0;
            foreach ($transacciones as $t) {
                $totalMonto += (float)$t['monto'];
            }

            if ($formato === 'csv') {
                header('Content-Type: text/csv; charset=UTF-8');
                header("Content-Disposition: attachment; filename=\"{$filenameBase}.csv\"");
                header('Pragma: no-cache');
                header('Expires: 0');

                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
                fputcsv($out, ['Reporte Financiero y de Pagos - FormAI', "Rango: {$fechaInicio} al {$fechaFin}"], ';');
                fputcsv($out, [], ';');
                fputcsv($out, ['ID Pago', 'Fecha y Hora', 'Cliente', 'Email', 'Plan', 'Monto USD', 'Moneda', 'Metodo', 'Nro Transaccion', 'Estado'], ';');

                foreach ($transacciones as $t) {
                    fputcsv($out, [
                        $t['id_pago'],
                        $t['creado_en'],
                        $t['nombre'] . ' ' . $t['apellido'],
                        $t['email'],
                        $t['nombre_plan'] ?? 'Sin Plan',
                        number_format((float)$t['monto'], 2, '.', ''),
                        $t['moneda'] ?? 'USD',
                        ucfirst($t['metodo'] ?? 'tarjeta'),
                        $t['nro_transaccion'] ?? 'N/D',
                        ucfirst($t['estado'] ?? 'completado')
                    ], ';');
                }
                fputcsv($out, [], ';');
                fputcsv($out, ['TOTAL RECAUDADO', '', '', '', '', number_format($totalMonto, 2, '.', ''), 'USD', '', '', ''], ';');
                fclose($out);
                exit;
            } else {
                // Formato Excel .xls estructurado con HTML Table
                header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
                header("Content-Disposition: attachment; filename=\"{$filenameBase}.xls\"");
                header('Pragma: no-cache');
                header('Expires: 0');
                echo "\xEF\xBB\xBF";
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                  <meta charset="UTF-8">
                  <style>
                    body { font-family: Arial, sans-serif; }
                    .header-title { background-color: #1e143d; color: #ffffff; font-size: 16pt; font-weight: bold; padding: 10px; }
                    .meta-info { color: #555555; font-size: 10pt; }
                    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
                    th { background-color: #2c1e50; color: #ffffff; font-weight: bold; border: 1px solid #999; padding: 8px; text-align: left; }
                    td { border: 1px solid #cccccc; padding: 6px; font-size: 10pt; }
                    .number { text-align: right; }
                    .total-row { background-color: #f0ebfa; font-weight: bold; border-top: 2px solid #2c1e50; }
                  </style>
                </head>
                <body>
                  <div class="header-title">FormAI - Reporte Financiero de Ingresos y Transacciones</div>
                  <div class="meta-info">Período: <?= htmlspecialchars($fechaInicio); ?> al <?= htmlspecialchars($fechaFin); ?> | Generado el: <?= date('d/m/Y H:i:s'); ?></div>
                  <table>
                    <thead>
                      <tr>
                        <th>ID Pago</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Correo Electrónico</th>
                        <th>Plan Suscrito</th>
                        <th class="number">Monto ($USD)</th>
                        <th>Método</th>
                        <th>N° Transacción</th>
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($transacciones as $t): ?>
                        <tr>
                          <td>#<?= (int)$t['id_pago']; ?></td>
                          <td><?= htmlspecialchars($t['creado_en']); ?></td>
                          <td><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']); ?></td>
                          <td><?= htmlspecialchars($t['email']); ?></td>
                          <td><?= htmlspecialchars($t['nombre_plan'] ?? 'Sin Plan'); ?></td>
                          <td class="number">$<?= number_format((float)$t['monto'], 2); ?></td>
                          <td><?= htmlspecialchars(ucfirst($t['metodo'])); ?></td>
                          <td><?= htmlspecialchars(!empty($t['nro_transaccion']) ? $t['nro_transaccion'] : 'N/D'); ?></td>
                          <td><?= htmlspecialchars(ucfirst($t['estado'])); ?></td>
                        </tr>
                      <?php endforeach; ?>
                      <tr class="total-row">
                        <td colspan="5">TOTAL INGRESOS DEL PERÍODO (<?= count($transacciones); ?> transacciones)</td>
                        <td class="number">$<?= number_format($totalMonto, 2); ?></td>
                        <td colspan="3">USD</td>
                      </tr>
                    </tbody>
                  </table>
                </body>
                </html>
                <?php
                exit;
            }
        } elseif ($tipoReporte === 'usuarios') {
            $usuarios = $usuarioModel->obtenerReporteCompletoUsuarios();

            if ($formato === 'csv') {
                header('Content-Type: text/csv; charset=UTF-8');
                header("Content-Disposition: attachment; filename=\"{$filenameBase}.csv\"");
                header('Pragma: no-cache');
                header('Expires: 0');

                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Reporte de Usuarios y Membresias - FormAI', "Generado el: " . date('d/m/Y H:i:s')], ';');
                fputcsv($out, [], ';');
                fputcsv($out, ['ID', 'Nombre', 'Apellido', 'Email', 'Rol', 'Estado Cuenta', 'Plan Activo', 'Vencimiento', 'Total Pagos', 'Total Pagado USD', 'Ultimo Pago'], ';');

                foreach ($usuarios as $u) {
                    fputcsv($out, [
                        $u['id_usuario'],
                        $u['nombre'],
                        $u['apellido'],
                        $u['email'],
                        ucfirst($u['nomb_rol'] ?? 'cliente'),
                        ucfirst($u['estado_usr'] ?? 'activo'),
                        $u['nombre_plan'] ?? 'Sin Plan',
                        $u['suscripcion_vence'] ?? 'N/A',
                        $u['total_pagos'] ?? 0,
                        number_format((float)($u['total_pagado'] ?? 0), 2, '.', ''),
                        $u['ultimo_pago'] ?? 'Sin pagos'
                    ], ';');
                }
                fclose($out);
                exit;
            } else {
                header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
                header("Content-Disposition: attachment; filename=\"{$filenameBase}.xls\"");
                header('Pragma: no-cache');
                header('Expires: 0');
                echo "\xEF\xBB\xBF";
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                  <meta charset="UTF-8">
                  <style>
                    body { font-family: Arial, sans-serif; }
                    .header-title { background-color: #1e143d; color: #ffffff; font-size: 16pt; font-weight: bold; padding: 10px; }
                    .meta-info { color: #555555; font-size: 10pt; }
                    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
                    th { background-color: #2c1e50; color: #ffffff; font-weight: bold; border: 1px solid #999; padding: 8px; text-align: left; }
                    td { border: 1px solid #cccccc; padding: 6px; font-size: 10pt; }
                    .number { text-align: right; }
                  </style>
                </head>
                <body>
                  <div class="header-title">FormAI - Reporte de Usuarios del Sistema y Membresías</div>
                  <div class="meta-info">Total registros: <?= count($usuarios); ?> | Generado el: <?= date('d/m/Y H:i:s'); ?></div>
                  <table>
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nombre y Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Plan Activo</th>
                        <th>Vencimiento</th>
                        <th class="number">Total Pagos</th>
                        <th class="number">Total Pagado ($USD)</th>
                        <th>Último Pago</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($usuarios as $u): ?>
                        <tr>
                          <td>#<?= (int)$u['id_usuario']; ?></td>
                          <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?></td>
                          <td><?= htmlspecialchars($u['email']); ?></td>
                          <td><?= htmlspecialchars(ucfirst($u['nomb_rol'] ?? 'cliente')); ?></td>
                          <td><?= htmlspecialchars(ucfirst($u['estado_usr'] ?? 'activo')); ?></td>
                          <td><?= htmlspecialchars($u['nombre_plan'] ?? 'Sin Plan'); ?></td>
                          <td><?= htmlspecialchars($u['suscripcion_vence'] ?? 'N/A'); ?></td>
                          <td class="number"><?= (int)$u['total_pagos']; ?></td>
                          <td class="number">$<?= number_format((float)$u['total_pagado'], 2); ?></td>
                          <td><?= htmlspecialchars($u['ultimo_pago'] ?? 'Sin pagos'); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </body>
                </html>
                <?php
                exit;
            }
        } elseif ($tipoReporte === 'proyectos') {
            $proyectos = $patronModel->obtenerReporteProyectos($fechaInicio, $fechaFin);

            if ($formato === 'csv') {
                header('Content-Type: text/csv; charset=UTF-8');
                header("Content-Disposition: attachment; filename=\"{$filenameBase}.csv\"");
                header('Pragma: no-cache');
                header('Expires: 0');

                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Reporte de Proyectos y Produccion Digital - FormAI', "Rango: {$fechaInicio} al {$fechaFin}"], ';');
                fputcsv($out, [], ';');
                fputcsv($out, ['ID', 'Nombre Proyecto', 'Tipo', 'Autor', 'Email', 'Estado', 'Fecha Creacion'], ';');

                foreach ($proyectos as $p) {
                    fputcsv($out, [
                        $p['id_patron'],
                        $p['nomb_patron'],
                        ucfirst($p['tipo_patron'] ?? 'patronaje'),
                        $p['nombre'] . ' ' . $p['apellido'],
                        $p['email'],
                        ucfirst($p['estado_patron'] ?? 'activo'),
                        $p['fecha_creacion']
                    ], ';');
                }
                fclose($out);
                exit;
            } else {
                header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
                header("Content-Disposition: attachment; filename=\"{$filenameBase}.xls\"");
                header('Pragma: no-cache');
                header('Expires: 0');
                echo "\xEF\xBB\xBF";
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                  <meta charset="UTF-8">
                  <style>
                    body { font-family: Arial, sans-serif; }
                    .header-title { background-color: #1e143d; color: #ffffff; font-size: 16pt; font-weight: bold; padding: 10px; }
                    .meta-info { color: #555555; font-size: 10pt; }
                    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
                    th { background-color: #2c1e50; color: #ffffff; font-weight: bold; border: 1px solid #999; padding: 8px; text-align: left; }
                    td { border: 1px solid #cccccc; padding: 6px; font-size: 10pt; }
                  </style>
                </head>
                <body>
                  <div class="header-title">FormAI - Reporte de Proyectos y Patrones Procesados</div>
                  <div class="meta-info">Período: <?= htmlspecialchars($fechaInicio); ?> al <?= htmlspecialchars($fechaFin); ?> | Total proyectos: <?= count($proyectos); ?></div>
                  <table>
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nombre del Proyecto</th>
                        <th>Tipo</th>
                        <th>Autor / Cliente</th>
                        <th>Correo Electrónico</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($proyectos as $p): ?>
                        <tr>
                          <td>#<?= (int)$p['id_patron']; ?></td>
                          <td><?= htmlspecialchars($p['nomb_patron']); ?></td>
                          <td><?= htmlspecialchars(ucfirst($p['tipo_patron'] ?? 'patronaje')); ?></td>
                          <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?></td>
                          <td><?= htmlspecialchars($p['email']); ?></td>
                          <td><?= htmlspecialchars(ucfirst($p['estado_patron'] ?? 'activo')); ?></td>
                          <td><?= htmlspecialchars($p['fecha_creacion']); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </body>
                </html>
                <?php
                exit;
            }
        }
    }
}