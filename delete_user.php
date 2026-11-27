<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

$id = $_GET['id'] ?? '';

if (!ctype_digit((string)$id)) {
    header("Location: list_users.php");
    exit;
}

header("Location: user_action.php?id=" . urlencode($id));
exit;
