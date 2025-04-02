<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir la configuración de la base de datos
include 'config/db_config.php';

// Establecer la conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar si hubo un error de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Realizar la consulta para obtener todos los personajes
$sql = "SELECT * FROM personajes";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personajes Marvel - Avengers</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Estilos personalizados Marvel -->
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .marvel-header {
            background-color: #e23636;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .table-marvel {
            background-color: #1f1f1f;
            color: #ffffff;
            border: 2px solid #e23636;
        }
        .table-marvel thead {
            background-color: #e23636;
            color: white;
        }
        .table-marvel tbody tr {
            transition: all 0.3s ease;
        }
        .table-marvel tbody tr:hover {
            background-color: #2f2f2f;
            transform: scale(1.01);
        }
        .character-img {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        .character-img:hover {
            transform: scale(1.1);
        }
        .btn-marvel {
            background-color: #e23636;
            border-color: #ffffff;
            color: #ffffff;
            transition: all 0.3s ease;
        }
        .btn-marvel:hover {
            background-color: #ff4444;
            transform: scale(1.05);
        }
        .actions-column {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .color-badge {
            display: inline-block;
            width: 80px;
            height: 30px;
            border: 1px solid #ffffff;
            border-radius: 5px;
        }
        .color-text {
            display: block;
            text-align: center;
            font-weight: bold;
            padding: 2px;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.7);
        }
    </style>
</head>
<body>
    <!-- Encabezado Marvel -->
    <header class="marvel-header text-center">
        <div class="container">
            <h1>Registro de Superhéroes Marvel</h1>
            <p>Explora y gestiona los personajes del Universo Marvel</p>
        </div>
    </header>

    <div class="container">
        <!-- Botón para agregar un personaje -->
        <div class="mb-4">
            <a href="agregar.php" class="btn btn-marvel">
                <i class="fas fa-plus"></i> Agregar Nuevo Personaje
            </a>
        </div>

        <!-- Tabla de Personajes -->
        <div class="table-responsive">
            <table class="table table-marvel table-hover">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Color</th>
                        <th>Tipo</th>
                        <th>Nivel</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['foto'])): ?>
                                <img src="<?= $row['foto'] ?>" alt="Foto de <?= $row['nombre'] ?>" class="character-img">
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['nombre'] ?></td>
                        <td>
                            <div class="color-badge" style="background-color: <?= $row['color'] ?>;">
                                <span class="color-text"><?= $row['color'] ?></span>
                            </div>
                        </td>
                        <td><?= $row['tipo'] ?></td>
                        <td><?= $row['nivel'] ?></td>
                        <td class="actions-column">
                            <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                Editar
                            </a>
                            <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar a <?= $row['nombre'] ?>?')">
                                Eliminar
                            </a>
                            <a href="generar_pdf.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                Descargar PDF
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Font Awesome para iconos (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
