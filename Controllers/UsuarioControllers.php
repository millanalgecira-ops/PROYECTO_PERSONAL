<?php
session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/rutas.php';
require_once __DIR__ . '/../Models/usuario.php';

class UsuarioControllers {

    public function registrar() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URL_REGISTRO);
            exit;
        }

        $nombres            = trim($_POST['nombres']            ?? '');
        $apellidos          = trim($_POST['apellidos']          ?? '');
        $email              = trim($_POST['email']              ?? '');
        $password           = trim($_POST['password']           ?? '');
        $confirmar_password = trim($_POST['confirmar_password'] ?? '');

        $nombre_completo = $nombres . ' ' . $apellidos;

        if (empty($nombres) || empty($apellidos) || empty($email) || empty($password) || empty($confirmar_password)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Campos incompletos',
                'text'  => 'Debe completar todos los campos'
            ];
            header("Location: " . URL_REGISTRO);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Correo inválido',
                'text'  => 'Ingrese un correo válido'
            ];
            header("Location: " . URL_REGISTRO);
            exit;
        }

        if ($password !== $confirmar_password) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Las contraseñas no coinciden'
            ];
            header("Location: " . URL_REGISTRO);
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Contraseña inválida',
                'text'  => 'La contraseña debe tener al menos 6 caracteres'
            ];
            header("Location: " . URL_REGISTRO);
            exit;
        }

        $database = new Database();
        $db       = $database->conectar();

        // Los clientes se registran en la tabla 'clientes'
        $clienteModel = new Cliente($db);

        if ($clienteModel->existeCorreo($email)) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Correo existente',
                'text'  => 'Este correo ya está registrado'
            ];
            header("Location: " . URL_REGISTRO);
            exit;
        }

        $datos = [
            'nombre'   => $nombre_completo,
            'correo'   => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $resultado = $clienteModel->registrar($datos);

        if ($resultado === true) {
            // CL-001: Registro exitoso → iniciar sesión automáticamente y redirigir al catálogo
            $cliente = $clienteModel->obtenerPorEmail($email);
            session_regenerate_id(true);
            $_SESSION['usuario'] = [
                'id_usuario' => $cliente['id'],
                'nombre'     => $cliente['nombre'],
                'correo'     => $cliente['correo'],
                'id_rol'     => 3,
                'rol'        => 'cliente'
            ];
            header("Location: " . URL_VIEWS . "/dashboard/cliente.php");
            exit;
        } else {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => $resultado
            ];
            header("Location: " . URL_REGISTRO);
            exit;
        }
    }
}

$controller = new UsuarioControllers();
$controller->registrar();
?>
