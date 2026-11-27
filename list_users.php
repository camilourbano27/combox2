<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . '/config/database.php';

$mensaje = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$sql = "SELECT id, firstname, lastname, mobile_phone, email, photo_url, status
        FROM users
        ORDER BY lastname ASC";

$result = pg_query($conn, $sql);

if (!$result) {
    error_log(pg_last_error($conn));
    die("No se pudo cargar el listado de usuarios.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de usuarios</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid mt-4">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Listado de usuarios</h1>

        <a href="index.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($mensaje['type']) ?> alert-dismissible fade show">
            <?= htmlspecialchars($mensaje['message']) ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Usuarios registrados</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                            <th>Correo electrónico</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ($row = pg_fetch_assoc($result)): ?>

                        <?php
                        $foto = trim((string)($row['photo_url'] ?? ''));

                        if (filter_var($foto, FILTER_VALIDATE_URL)) {
                            $rutaFoto = $foto;
                        } else {
                            $nombreFoto = basename($foto);
                            $archivo = __DIR__ . '/photos/' . $nombreFoto;

                            $rutaFoto = (
                                $nombreFoto !== '' &&
                                is_file($archivo)
                            ) ? 'photos/' . rawurlencode($nombreFoto)
                              : 'img/undraw_profile.svg';
                        }

                        $activo = in_array(
                            strtolower((string)$row['status']),
                            ['t', 'true', '1'],
                            true
                        );
                        ?>

                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>

                            <td class="text-center align-middle">
                                <img
                                    src="<?= htmlspecialchars($rutaFoto, ENT_QUOTES, 'UTF-8') ?>"
                                    width="50" height="50"
                                    style="object-fit:cover;border-radius:50%;"
                                    alt="Foto de usuario">
                            </td>

                            <td><?= htmlspecialchars($row['firstname'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['lastname'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['mobile_phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>

                            <td class="text-center">
                                <?php if ($activo): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center text-nowrap">

    <a href="edit_user.php?id=<?= urlencode($row['id']) ?>"
       class="text-primary mx-1"
       title="Editar usuario">
        <i class="fas fa-edit"></i>
    </a>

    <form method="POST" action="process_user_action.php"
          class="d-inline"
          onsubmit="return confirm('¿Deseas <?= $activo ? 'desactivar' : 'activar' ?> este usuario?');">

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id"
               value="<?= htmlspecialchars($row['id']) ?>">
        <input type="hidden" name="action"
               value="<?= $activo ? 'deactivate' : 'activate' ?>">

        <button type="submit"
                class="btn btn-link btn-sm p-1 <?= $activo ? 'text-warning' : 'text-success' ?>"
                title="<?= $activo ? 'Desactivar usuario' : 'Activar usuario' ?>">
            <i class="fas <?= $activo ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
        </button>
    </form>

    <form method="POST" action="process_user_action.php"
          class="d-inline"
          onsubmit="return confirm('¿Eliminar definitivamente este usuario?');">

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id"
               value="<?= htmlspecialchars($row['id']) ?>">
        <input type="hidden" name="action" value="delete">

        <button type="submit" class="btn btn-link btn-sm p-1 text-danger"
                title="Eliminar usuario">
            <i class="fas fa-trash"></i>
        </button>
    </form>

</td>
                        </tr>

                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
