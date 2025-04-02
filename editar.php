<?php
include 'db_config.php';

// Validar que se haya proporcionado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener los datos del personaje
$sql = "SELECT * FROM personajes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el personaje existe
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$personaje = $result->fetch_assoc();

// Si el formulario es enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiar y validar entrada
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $color = $conn->real_escape_string($_POST['color']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $nivel = intval($_POST['nivel']);
    $foto = $personaje['foto']; // Mantener la foto existente por defecto

    // Manejar la subida de la foto si se proporciona
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto_name = basename($_FILES['foto']['name']); // Nombre del archivo
        $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION)); // Extensión
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif'); // Extensiones permitidas

        if (in_array($foto_ext, $allowed_ext)) {
            $new_foto_name = uniqid() . '_' . $foto_name; // Nombre único para evitar duplicados
            $ruta = "imagenes/" . $new_foto_name; // Ruta relativa

            // Mover el archivo a la carpeta 'imagenes'
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
                $foto = $ruta; // Guardar la nueva ruta relativa
                // Opcional: eliminar la foto antigua si existe
                if (!empty($personaje['foto']) && file_exists($personaje['foto'])) {
                    @unlink($personaje['foto']); // Usar @ para suprimir advertencias
                    if (file_exists($personaje['foto'])) {
                        error_log("No se pudo eliminar la imagen antigua: " . $personaje['foto']);
                    }
                }
            } else {
                $error = "Error al subir la imagen.";
            }
        } else {
            $error = "Solo se permiten archivos JPG, JPEG, PNG o GIF.";
        }
    }

    if (empty($error)) {
        // Preparar y ejecutar consulta de actualización
        $update_sql = "UPDATE personajes SET 
                        nombre = ?, 
                        color = ?, 
                        tipo = ?, 
                        nivel = ?, 
                        foto = ? 
                        WHERE id = ?";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssisi", $nombre, $color, $tipo, $nivel, $foto, $id);

        if ($stmt->execute()) {
            // Redirigir con mensaje de éxito
            header("Location: index.php?mensaje=Personaje actualizado correctamente");
            exit();
        } else {
            $error = "Error al actualizar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Personaje Marvel</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .marvel-form {
            background-color: #1f1f1f;
            border: 2px solid #e23636;
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 0 20px rgba(226, 54, 54, 0.3);
        }
        .form-label {
            color: #e23636;
            font-weight: bold;
        }
        .form-control {
            background-color: #2f2f2f;
            border-color: #e23636;
            color: #ffffff;
        }
        .form-control:focus {
            background-color: #3f3f3f;
            border-color: #ff4444;
            box-shadow: 0 0 10px rgba(226, 54, 54, 0.3);
            color: #ffffff;
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
        .btn-secondary {
            background-color: #4f4f4f;
            border-color: #6f6f6f;
        }
        .marvel-header {
            background-color: #e23636;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Encabezado Marvel -->
    <header class="marvel-header">
        <div class="container">
            <h1>Editar Superhéroe</h1>
        </div>
    </header>

    <div class="container">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6 marvel-form">
                <form method="POST" action="" id="heroForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Personaje</label>
                        <input type="text" name="nombre" class="form-control" 
                               value="<?= htmlspecialchars($personaje['nombre']) ?>" 
                               required minlength="2" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color Representativo</label>
                        <div class="input-group">
                            <input type="color" name="color_picker" class="form-control form-control-color" 
                                   value="<?= $personaje['color'] ?>" 
                                   title="Elige el color del personaje">
                            <input type="text" name="color" class="form-control" 
                                   value="<?= htmlspecialchars($personaje['color']) ?>" 
                                   placeholder="Código de color o nombre">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo / Rol</label>
                        <select name="tipo" class="form-control" required>
                            <option value="">Selecciona un tipo</option>
                            <option value="Superhéroe" <?= $personaje['tipo'] == 'Superhéroe' ? 'selected' : '' ?>>Superhéroe</option>
                            <option value="Villano" <?= $personaje['tipo'] == 'Villano' ? 'selected' : '' ?>>Villano</option>
                            <option value="Antihéroe" <?= $personaje['tipo'] == 'Antihéroe' ? 'selected' : '' ?>>Antihéroe</option>
                            <option value="Equipo" <?= $personaje['tipo'] == 'Equipo' ? 'selected' : '' ?>>Equipo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel de Poder</label>
                        <input type="number" name="nivel" class="form-control" 
                               value="<?= $personaje['nivel'] ?>" 
                               min="1" max="10" 
                               placeholder="Nivel de 1 a 10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto del Personaje</label>
                        <input type="file" name="foto" class="form-control" 
                               accept="image/jpeg,image/png,image/gif" 
                               onchange="previewImage(event)">
                        <?php if (!empty($personaje['foto'])): ?>
                            <img src="<?= htmlspecialchars($personaje['foto']) ?>" 
                                 alt="Vista previa actual" class="preview-image mt-2">
                        <?php endif; ?>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-marvel">
                            Actualizar Personaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Validación de formulario con JavaScript
    document.getElementById('heroForm').addEventListener('submit', function(event) {
        const nombre = document.querySelector('input[name="nombre"]');
        
        // Validación de nombre
        if (nombre.value.trim().length < 2) {
            alert('Por favor, ingresa un nombre válido');
            event.preventDefault();
            return;
        }
    });

    // Previsualización de la imagen
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.createElement('img');
        preview.className = 'preview-image';
        preview.style.maxWidth = '200px';
        preview.style.maxHeight = '200px';
        preview.style.objectFit = 'cover';
        preview.style.borderRadius = '10px';
        preview.style.marginTop = '10px';

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            const container = event.target.parentElement;
            const existingPreview = container.querySelector('.preview-image');
            if (existingPreview) {
                existingPreview.remove();
            }
            container.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }
    </script>
</body>
</html>