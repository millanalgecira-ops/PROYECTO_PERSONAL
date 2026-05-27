<?php
session_start();

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/rutas.php';
require_once __DIR__ . '/../Models/usuario.php';

class AuthController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URL_LOGIN);
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Campos incompletos',
                'text'  => 'Debe ingresar correo y contraseña'
            ];
            header("Location: " . URL_LOGIN);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Correo inválido',
                'text'  => 'Ingrese un correo electrónico válido'
            ];
            header("Location: " . URL_LOGIN);
            exit;
        }

        $database = new Database();
        $db       = $database->conectar();

        // ── Buscar en tabla usuarios (Administrador / Cocina) ──
        $usuarioModel = new Usuario($db);
        $usuario      = $usuarioModel->obtenerPorEmail($email);

        if ($usuario) {
            if (!password_verify($password, $usuario['password_hash'])) {
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Credenciales incorrectas',
                    'text'  => 'Correo o contraseña incorrectos'
                ];
                header("Location: " . URL_LOGIN);
                exit;
            }

            session_regenerate_id(true);

            // Obtener nombre del rol desde la BD
            $stmt = $db->prepare("SELECT nombre FROM roles WHERE id = :id LIMIT 1");
            $stmt->bindParam(":id", $usuario['rol_id'], PDO::PARAM_INT);
            $stmt->execute();
            $rol = strtolower($stmt->fetchColumn());

            $_SESSION['usuario'] = [
                'id_usuario' => $usuario['id'],
                'nombre'     => $usuario['nombre'],
                'correo'     => $usuario['correo'],
                'id_rol'     => $usuario['rol_id'],
                'rol'        => $rol
            ];

            switch ($rol) {
                case 'administrador':
                    header("Location: " . URL_VIEWS . "/dashboard/admin.php");
                    exit;
                case 'cocina':
                    header("Location: " . URL_VIEWS . "/dashboard/cocina.php");
                    exit;
                default:
                    $_SESSION['alert'] = [
                        'icon'  => 'error',
                        'title' => 'Rol no válido',
                        'text'  => 'No se pudo determinar el acceso del usuario'
                    ];
                    header("Location: " . URL_LOGIN);
                    exit;
            }
        }

        // ── Buscar en tabla clientes ──
        $clienteModel = new Cliente($db);
        $cliente      = $clienteModel->obtenerPorEmail($email);

        if (!$cliente) {
            // Verificar si existe pero está inactivo
            $stmtInactivo = $db->prepare("SELECT activo FROM clientes WHERE correo = :correo LIMIT 1");
            $stmtInactivo->bindParam(':correo', $email);
            $stmtInactivo->execute();
            $inactivo = $stmtInactivo->fetch(PDO::FETCH_ASSOC);

            if ($inactivo && $inactivo['activo'] == 0) {
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Cuenta inactiva',
                    'text'  => 'Tu cuenta está inactiva. Contacta al administrador del asadero.'
                ];
            } else {
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Usuario no encontrado',
                    'text'  => 'Correo o contraseña incorrectos'
                ];
            }
            header("Location: " . URL_LOGIN);
            exit;
        }

        if (!password_verify($password, $cliente['password_hash'])) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Contraseña incorrecta',
                'text'  => 'Verifique sus credenciales'
            ];
            header("Location: " . URL_LOGIN);
            exit;
        }

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
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: " . URL_LOGIN);
        exit;
    }
}

$controller = new AuthController();
$accion = $_GET['accion'] ?? 'login';

if ($accion === 'logout') {
    $controller->logout();
} else {
    $controller->login();
}
?>
