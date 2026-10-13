<?php

//get data base connection
require_once __DIR__ . '/../combox/config/database.php';
//get data from html form (client)
$f_name = $_POST['fname'];
$l_name = $_POST['lname'];
$m_phone = $_POST['fphone'];
$e_mail = $_POST['email'];
$p_assword = $_POST['pswd'];
$pass_enc = md5($p_assword);

//prepare query
$sql="
 INSERT INTO users(
 firstname, lastname, mobile_phone, email, password
 )
 VALUES(
  '$f_name','$l_name','$m_phone','$e_mail','$pass_enc'
  )
  ";
  $local_res = pg_query($local_conn, $sql);
  $supa_res = pg_query($supa_conn, $sql);
  
if ($local_res) {
    echo "User has been created successfully into local database!!!<br>";
} else {
    echo "Error in local database: " . pg_last_error($local_conn) . "<br>";
}

if ($supa_res) {
    echo "User has been created successfully into Supabase!!!<br>";
} else {
    echo "Error in Supabase: " . pg_last_error($supa_conn) . "<br>";
}
HEADER('refresh:0;url=login.html');
?>

