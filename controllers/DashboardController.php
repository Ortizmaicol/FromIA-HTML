<?php
require_once __DIR__ . '/../models/PatronModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class DashboardController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Validar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FromIA/login.php');
            exit;
        }

        $idUsuario = $_SESSION['user_id'];
        $rol       = strtolower($_SESSION['user_rol'] ?? 'cliente'); // 'administrador' o 'cliente'
        $nombre    = $_SESSION['user_nombre'] ?? 'Usuario';

        // 2. Despachar según el Rol
        if ($rol === 'administrador' || $rol === 'admin') {
            $this->cargarDashboardAdmin($nombre);
        } else {
            $this->cargarDashboardCliente($idUsuario, $nombre);
        }
    }

    private function cargarDashboardCliente($idUsuario, $nombre) {
        $patronModel  = new PatronModel();
        $proyectos    = $patronModel->obtenerPorUsuario($idUsuario);
        $totalActivos = count($proyectos);

        // Variables disponibles para la vista del cliente
        $extraCss  = "dashboard.css";
        $pageTitle = "FormAI - Mi Espacio";

        require_once __DIR__ . '/../views/dashboard/cliente/index.php';
    }

    private function cargarDashboardAdmin($nombre) {
        // Consultas exclusivas para el Administrador (ej. total de usuarios, ventas, etc.)
        $extraCss  = "dashboard.css"; // Puedes crear también un dashboard-admin.css si requiere estilos propios
        $pageTitle = "FormAI - Panel de Administración";

        require_once __DIR__ . '/../views/dashboard/administrador/index.php';
    }
}