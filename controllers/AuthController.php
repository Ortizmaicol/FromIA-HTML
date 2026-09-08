<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard.php');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'login') {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';

                $usuario = $this->usuarioModel->obtenerPorEmail($email);

                if ($usuario && $usuario['estado_usr'] === 'activo' && password_verify($password, $usuario['password_hash'])) {
                    $_SESSION['user_id'] = $usuario['id_usuario'];
                    $_SESSION['user_nombre'] = $usuario['nombre'];
                    $_SESSION['user_apellido'] = $usuario['apellido'];
                    $_SESSION['user_email'] = $usuario['email'];
                    $_SESSION['user_rol'] = $usuario['rol'];

                    $this->usuarioModel->actualizarUltimoLogin($usuario['id_usuario']);

                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = "El correo o la contraseÃ±a son incorrectos.";
                }
            } elseif ($accion === 'registro') {
                $nombre = trim($_POST['nombre'] ?? '');
                $apellido = trim($_POST['apellido'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';

                if (!empty($nombre) && !empty($apellido) && !empty($telefono) && !empty($email) && !empty($password)) {
                    $existe = $this->usuarioModel->obtenerPorEmail($email);
                    if ($existe) {
                        $error = "Este correo electrÃ³nico ya se encuentra registrado.";
                    } else {
                        $ok = $this->usuarioModel->registrar($nombre, $apellido, $telefono, $email, $password);
                        if ($ok) {
                            $success = "Â¡Cuenta creada con Ã©xito! Ahora puedes iniciar sesiÃ³n.";
                        } else {
                            $error = "Hubo un error al registrar la cuenta.";
                        }
                    }
                } else {
                    $error = "Por favor completa todos los campos requeridos.";
                }
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
