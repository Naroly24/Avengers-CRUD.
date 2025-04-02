<?php
session_start();

// Verificar si ya existe la configuración de la base de datos
if (file_exists('config/db_config.php')) {
    header('Location: index.php'); // Si ya está configurado, redirigir a la página principal
    exit();
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $host = $_POST['host'];
    $user = $_POST['user'];
    $password = $_POST['password'];
    $dbname = $_POST['dbname'];

    // Intentar conectar a la base de datos
    try {
        $conn = new mysqli($host, $user, $password);

        // Verificar si la conexión es exitosa
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }

        // Crear la base de datos si no existe
        $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");

        // Seleccionar la base de datos
        $conn->select_db($dbname);

        // Crear la tabla personajes si no existe
        $createTableQuery = "CREATE TABLE IF NOT EXISTS personajes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            color VARCHAR(50),
            tipo VARCHAR(50),
            nivel INT,
            foto VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if ($conn->query($createTableQuery) === TRUE) {
            // Guardar la configuración en un archivo
            $configContent = "<?php
define('DB_HOST', '$host');
define('DB_USER', '$user');
define('DB_PASS', '$password');
define('DB_NAME', '$dbname');
?>";

            // Crear el archivo de configuración
            file_put_contents('config/db_config.php', $configContent);

            $_SESSION['install_success'] = "Instalación completada exitosamente.";
            header('Location: index.php');
            exit();
        } else {
            throw new Exception("Error al crear la tabla 'personajes': " . $conn->error);
        }
    } catch (Exception $e) {
        $_SESSION['install_error'] = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistente de Instalación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            margin: 10px 0;
        }
        .success-message {
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<h1>Asistente de Instalación de Base de Datos</h1>

<?php
if (isset($_SESSION['install_error'])) {
    echo "<p class='error-message'>" . $_SESSION['install_error'] . "</p>";
    unset($_SESSION['install_error']);
}
?>

<?php
if (isset($_SESSION['install_success'])) {
    echo "<p class='success-message'>" . $_SESSION['install_success'] . "</p>";
    unset($_SESSION['install_success']);
}
?>

<div class="form-container">
    <form action="install.php" method="POST">
        <label for="host">Servidor</label>
        <input type="text" id="host" name="host" required placeholder="Por ejemplo: localhost">

        <label for="user">Usuario</label>
        <input type="text" id="user" name="user" required placeholder="Por ejemplo: root">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Contraseña de la base de datos">

        <label for="dbname">Nombre de la Base de Datos</label>
        <input type="text" id="dbname" name="dbname" required placeholder="Nombre de la base de datos">

        <button type="submit">Instalar</button>
    </form>
</div>

</body>
</html>
