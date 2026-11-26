<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/config/database.php';

if (!isset($_POST['email'], $_POST['password'])) {
    echo "⚠️ Por favor completa todos los campos.";
    echo '<br><a href="login.html">Volver al inicio de sesión</a>';
    exit;
}

$e_mail = trim($_POST['email']);
$p_assword = $_POST['password'];
$pass_enc = md5($p_assword);

$sql = "
    SELECT id, firstname, lastname, mobile_phone, email, password
    FROM users
    WHERE email = $1 AND password = $2
    LIMIT 1
";

// ✅ Usamos $conn aquí también
$supa_res = pg_query_params($conn, $sql, array($e_mail, $pass_enc));

if (!$supa_res) {
    echo "❌ Error en la consulta a la base de datos: " . pg_last_error($conn);
    exit;
}

if (pg_num_rows($supa_res) > 0) {
    $user = pg_fetch_assoc($supa_res);

    session_start();
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];

    header('Location: index.html');
    exit;
} else {
    echo "❌ Correo o contraseña incorrectos.<br>";
    echo '<a href="login.html">Intentar de nuevo</a>';
}
?>

