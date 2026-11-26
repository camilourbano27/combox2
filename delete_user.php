<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ejecutar eliminación
    $sql = "DELETE FROM users WHERE id = $1";
    $result = pg_query_params($conn, $sql, array($id));
    
    // Volver a la lista
    header("Location: list_users.php");
    exit;
}

// Si no llega el id, regresar
header("Location: list_users.php");
exit;
?>
