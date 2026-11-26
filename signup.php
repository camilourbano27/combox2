<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/config/database.php';

// ✅ Verificar que lleguen todos los campos
if (!isset($_POST['fname'], $_POST['lname'], $_POST['fphone'], $_POST['email'], $_POST['pswd'])) {
    echo "⚠️ Por favor completa todos los campos del formulario.";
    echo '<br><a href="register.html">Volver al registro</a>';
    exit;
}

// Recibir y limpiar los datos
$f_name    = trim($_POST['fname']);
$l_name    = trim($_POST['lname']);
$m_phone   = trim($_POST['fphone']);
$e_mail    = trim($_POST['email']);
$p_assword = $_POST['pswd'];
$pass_enc  = md5($p_assword);

// Consulta segura
$sql = "
    INSERT INTO users (firstname, lastname, mobile_phone, email, password)
    VALUES ($1, $2, $3, $4, $5)
";

// ✅ Usamos $conn porque así se llama en database.php
$supa_res = pg_query_params($conn, $sql, array($f_name, $l_name, $m_phone, $e_mail, $pass_enc));

if ($supa_res) {
    echo "✅ Usuario creado exitosamente!!! Redirigiendo al inicio de sesión...<br>";
    header('Refresh: 2; url=login.html');
} else {
    echo "❌ Error al crear el usuario: " . pg_last_error($conn) . "<br>";
    echo '<a href="register.html">Intentar de nuevo</a>';
}
?>

