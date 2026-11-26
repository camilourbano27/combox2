<?php

//cloud connection

//supabase connection connection
$supa_host = "aws-0-us-east-1.pooler.supabase.com"; 
$supa_port = "6543";
$supa_user = "postgres.bbllqswkabxbdxlndjrh";
$supa_dbname = "postgres";
$supa_password = "unicesmag@@";



$conn = pg_connect("
 host=$supa_host
 port=$supa_port
 dbname=$supa_dbname
 user=$supa_user
 password=$supa_password
");


 
 if (!$conn){
    die("Error supa connection !!!");
 } else{
    echo "<br>supa Connection successs!!!";
 }


?>