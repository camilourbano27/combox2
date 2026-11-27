<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . '/config/database.php';

$id = $_GET['id'] ?? '';

if (!ctype_digit((string)$id)) {
    die("ID de usuario no válido.");
}

$sql = "SELECT id, firstname, lastname, email, status
        FROM users WHERE id = $1";

$result = pg_query_params($conn, $sql, [$id]);

if (!$result || pg_num_rows($result) === 0) {
    die("Usuario no encontrado.");
}

$user = pg_fetch_assoc($result);

$activo = in_array(
    strtolower((string)$user['status']),
    ['t', 'true', '1'],
    true
);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acciones de usuario</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header py-3">
            <h5 class="font-weight-bold text-primary mb-0">
                ¿Qué deseas hacer?
            </h5>
        </div>

        <div class="card-body">
            <p>Usuario:
                <strong>
                    <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                </strong>
            </p>

            <p>Correo:
                <?= htmlspecialchars($user['email']) ?>
            </p>

            <p>Estado actual:
                <?php if ($activo): ?>
                    <span class="badge badge-success">Active</span>
                <?php else: ?>
                    <span class="badge badge-danger">Inactive</span>
                <?php endif; ?>
            </p>

            <form action="process_user_action.php" method="POST">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="id"
                       value="<?= htmlspecialchars($user['id']) ?>">

                <?php if ($activo): ?>
                    <button class="btn btn-warning btn-block mb-3"
                            type="submit" name="action" value="deactivate"
                            onclick="return confirm('¿Deseas desactivar este usuario?');">
                        <i class="fas fa-user-slash"></i>
                        Desactivar usuario
                    </button>
                <?php else: ?>
                    <button class="btn btn-success btn-block mb-3"
                            type="submit" name="action" value="activate"
                            onclick="return confirm('¿Deseas activar este usuario?');">
                        <i class="fas fa-user-check"></i>
                        Activar usuario
                    </button>
                <?php endif; ?>

                <button class="btn btn-danger btn-block mb-3"
                        type="submit" name="action" value="delete"
                        onclick="return confirm('¿Seguro que deseas eliminar definitivamente este usuario? Esta acción no se puede deshacer.');">
                    <i class="fas fa-trash"></i>
                    Eliminar definitivamente
                </button>
            </form>

            <a href="list_users.php" class="btn btn-secondary btn-block">
                Cancelar
            </a>
        </div>
    </div>
</div>
</body>
</html>
