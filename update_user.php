<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
include __DIR__ . '/config/database.php';


// Datos que llegan del formulario
$id           = $_POST['id'];
$firstname    = $_POST['firstname'];
$lastname     = $_POST['lastname'];
$email        = $_POST['email'];
$mobile_phone = $_POST['mobile_phone'];

// ---------- PARTE A: actualizar los datos ----------
$sql = 'UPDATE users
        SET firstname = $1, lastname = $2, email = $3, mobile_phone = $4
        WHERE id = $5';

$result = pg_query_params(
    $conn,
    $sql,
    array($firstname, $lastname, $email, $mobile_phone, $id)
);

if (!$result) {
    echo "Error al actualizar los datos: " . pg_last_error($conn);
    exit;
}

// ---------- PARTE B: la foto (solo si eligieron una) ----------
if (isset($_FILES['photo']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE) {

    if ($_FILES['photo']['error'] != UPLOAD_ERR_OK) {
        echo "Error al subir la foto.";
        exit;
    }

    // Extensión del archivo en minúsculas
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if ($ext == "jpg" || $ext == "jpeg" || $ext == "png") {

        // Nombre de la foto usando el ID del usuario
        $photo_url = "photos/user_" . $id . "." . $ext;

        // Ruta completa donde se guardará la foto
        $ruta_destino = __DIR__ . "/" . $photo_url;

        // Verificar que exista la carpeta photos
        if (!is_dir(__DIR__ . "/photos")) {
            echo "No existe la carpeta photos.";
            exit;
        }

        // Mover la foto a la carpeta photos
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {

            // Guardar la ruta de la foto en la base de datos
            $sql = 'UPDATE users SET photo_url = $1 WHERE id = $2';

            $result = pg_query_params(
                $conn,
                $sql,
                array($photo_url, $id)
            );

            if (!$result) {
                echo "Error al actualizar la foto en la base de datos.";
                exit;
            }

        } else {
            echo "No se pudo guardar la foto. Revisa la carpeta photos.";
            exit;
        }

    } else {
        echo "Solo se permiten fotos JPG o PNG.";
        exit;
    }
}

// ---------- PARTE C: volver a la página principal ----------
header("Location: list_users.php");
exit;

?>
