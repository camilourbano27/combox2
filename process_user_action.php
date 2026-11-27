<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list_users.php");
    exit;
}

$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Solicitud no válida. Vuelve a intentarlo.'
    ];
    header("Location: list_users.php");
    exit;
}

$id = $_POST['id'] ?? '';
$action = $_POST['action'] ?? '';

if (!ctype_digit((string)$id)) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'ID de usuario no válido.'
    ];
    header("Location: list_users.php");
    exit;
}

require_once __DIR__ . '/config/database.php';

$operations = [
    'delete' => [
        'sql' => 'DELETE FROM users WHERE id = $1 RETURNING id',
        'success' => 'Usuario eliminado definitivamente.'
    ],
    'deactivate' => [
        'sql' => 'UPDATE users SET status = FALSE WHERE id = $1 RETURNING id',
        'success' => 'Usuario desactivado correctamente.'
    ],
    'activate' => [
        'sql' => 'UPDATE users SET status = TRUE WHERE id = $1 RETURNING id',
        'success' => 'Usuario activado correctamente.'
    ]
];

if (!isset($operations[$action])) {
    header("Location: list_users.php");
    exit;
}

$result = pg_query_params($conn, $operations[$action]['sql'], [$id]);

if (!$result) {
    error_log(pg_last_error($conn));

    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => $action === 'delete'
            ? 'No se pudo eliminar. Puede tener registros relacionados.'
            : 'No se pudo actualizar el estado del usuario.'
    ];
} elseif (pg_num_rows($result) === 0) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'message' => 'No se encontró el usuario.'
    ];
} else {
    $_SESSION['flash_message'] = [
        'type' => 'success',
        'message' => $operations[$action]['success']
    ];
}

header("Location: list_users.php");
exit;
