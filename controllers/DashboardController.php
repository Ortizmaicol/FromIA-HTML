<?php
require_once __DIR__ . '/../models/PatronModel.php';

class DashboardController {
    private $patronModel;

    public function __construct() {
        $this->patronModel = new PatronModel();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $id_usuario = $_SESSION['user_id'];
        $nombre = $_SESSION['user_nombre'];
        $proyectos = $this->patronModel->obtenerPorUsuario($id_usuario);
        $totalActivos = $this->patronModel->contarActivosPorUsuario($id_usuario);

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
