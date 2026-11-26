<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include __DIR__ . '/config/database.php';// tu conexión, deja la variable $conn
$sql = 'SELECT id, firstname, lastname, email, mobile_phone, photo_url
        FROM users ORDER BY id';
$result = pg_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
</head>
<body>
    <h1>Lista de usuarios</h1>

    <table border="1" cellpadding="8">
        <tr>
            <th>Foto</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>Celular</th>
            <th>Editar</th>
            <th>Eliminar</th>

        </tr>

        <?php while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td>
                <?php if ($row['photo_url'] != "") { ?>
                    <img src="<?php echo $row['photo_url']; ?>" width="60" height="60">
                <?php } else { ?>
                    Sin foto
                <?php } ?>
            </td>
            <td><?php echo $row['firstname']; ?></td>
            <td><?php echo $row['lastname']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['mobile_phone']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $row['id']; ?>">✏️</a>
            </td>
            <td>
                

    <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro?')">🗑️</a>
</td>
        </tr>

    
        <?php } ?>
    </table>
</body>
</html>