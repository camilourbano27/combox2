
<?php

// get data base connection
require_once __DIR__ . '/../combox/config/database.php';

// get data from html form (client)
$e_mail = $_POST['email'];
$p_assword = $_POST['password'];
$pass_enc = md5($p_assword);

// prepare query
$sql = "
    SELECT firstname, lastname, mobile_phone, email, password
    FROM users
    WHERE email = $1 AND password = $2
    LIMIT 1
";

// check local database
$local_res = pg_query_params($local_conn, $sql, array($e_mail, $pass_enc));

// check Supabase database
$supa_res = pg_query_params($supa_conn, $sql, array($e_mail, $pass_enc));

if ($local_res && pg_num_rows($local_res) > 0) {

    $user = pg_fetch_assoc($local_res);

    session_start();
    $_SESSION['email'] = $user['email'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];

    header('Location: index.html');
    exit;

} elseif ($supa_res && pg_num_rows($supa_res) > 0) {

    $user = pg_fetch_assoc($supa_res);

    session_start();
    $_SESSION['email'] = $user['email'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];

   HEADER('refresh:0;url=login.html');
    exit;

} else {
    echo "Incorrect email or password.<br>";
    echo '<a href="logi.html">Try again</a>';
}

?>