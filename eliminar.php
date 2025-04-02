<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM personajes WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirigir inmediatamente después de la eliminación
        header("Location: index.php");
        exit();  // Asegúrate de llamar a exit después de header para evitar más ejecución
    } else {
        echo "Error al eliminar: " . $conn->error;
    }
}
?>
