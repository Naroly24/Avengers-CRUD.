<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'serie_db');

// Función para manejar errores de conexión de manera más amigable
function handleDatabaseError($message) {
    // Log del error (puedes implementar un sistema de logs más robusto)
    error_log("Error de base de datos: " . $message);

    // Página de error personalizada
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error de Conexión</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                text-align: center;
            }
            .error-container {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                padding: 30px;
                max-width: 500px;
            }
            .error-icon {
                color: #e23636;
                font-size: 64px;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">❌</div>
            <h1>Error de Conexión a la Base de Datos</h1>
            <p><?= htmlspecialchars($message) ?></p>
            <p>Por favor, contacta al administrador o verifica la configuración de la base de datos.</p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Intentar establecer conexión
try {
    // Configurar conexión con opciones adicionales de seguridad
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    // Configurar juego de caracteres
    $conn->set_charset("utf8mb4");

    // Función para verificar si la tabla existe, si no, crearla
    function verificarTablaPersonajes($conn) {
        $tabla = "personajes";
        $check_table = $conn->query("SHOW TABLES LIKE '$tabla'");
        
        if ($check_table->num_rows == 0) {
            // Script para crear la tabla si no existe
            $create_table = "CREATE TABLE `$tabla` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nombre` VARCHAR(100) NOT NULL,
                `color` VARCHAR(50) NULL,
                `tipo` VARCHAR(50) NULL,
                `nivel` INT NULL,
                `foto` VARCHAR(255) NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            if ($conn->query($create_table) === TRUE) {
                echo "Tabla $tabla creada exitosamente.";
            } else {
                handleDatabaseError("No se pudo crear la tabla: " . $conn->error);
            }
        }
    }

    // Llamar a la función de verificación de tabla
    verificarTablaPersonajes($conn);

} catch (Exception $e) {
    // Manejar cualquier error de conexión
    handleDatabaseError($e->getMessage());
}
?>

