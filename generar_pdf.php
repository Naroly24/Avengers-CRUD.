<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start(); // Iniciar el buffer de salida para evitar cualquier salida no deseada

header('Content-Type: text/html; charset=utf-8');

// Para instalación manual de DomPDF
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include 'db_config.php';  // Conexión a la base de datos

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  // Asegurarse de que el ID sea un número entero

    // Obtener los datos del personaje desde la base de datos
    $sql = "SELECT * FROM personajes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $personaje = $result->fetch_assoc();

    if (!$personaje) {
        die("No se encontró el personaje con ID: " . $id);
    }

    // Configurar opciones para DomPDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);  // Habilitar imágenes remotas
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultMediaType', 'all');
    $options->set('enable_remote', true);
    $options->set('tempDir', sys_get_temp_dir()); // Directorio temporal del sistema
    $options->set('chroot', dirname(__FILE__)); // Usa la ruta actual del script
    $dompdf = new Dompdf($options);

    // Limpiar cualquier salida acumulada en el buffer
    ob_end_clean();

    // Manejo mejorado de imágenes - CONVERTIR A BASE64
    $image_path = '';
    
    // Verificar si la foto existe y preparar la ruta de imagen
    if (!empty($personaje['foto'])) {
        // Ruta completa al archivo de imagen
        $img_file = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($personaje['foto'], '/');
        
        // Verificar si el archivo existe
        if (file_exists($img_file)) {
            // Convertir imagen a base64
            $type = pathinfo($img_file, PATHINFO_EXTENSION);
            $data = file_get_contents($img_file);
            $image_path = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            // Probar con rutas alternativas
            $alternative_paths = [
                $personaje['foto'],
                './' . ltrim($personaje['foto'], '/'),
                dirname(__FILE__) . '/' . ltrim($personaje['foto'], '/')
            ];
            
            foreach ($alternative_paths as $path) {
                if (file_exists($path)) {
                    // Convertir imagen a base64
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $image_path = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    break;
                }
            }
        }
    }

    // Crear el contenido HTML para el PDF
    $html = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { 
                font-family: 'Arial', sans-serif; 
                color: #333; 
                background-color: #f4f4f4;
            }
            .container {
                width: 90%;
                margin: auto;
                background-color: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border-radius: 10px;
                position: relative;
            }
            .header {
                background-color: #e23636;  /* Color Marvel */
                color: white;
                text-align: center;
                padding: 15px;
                border-radius: 10px 10px 0 0;
            }
            .header h1 {
                margin: 0;
                text-transform: uppercase;
                letter-spacing: 2px;
            }
            .character-photo {
                max-width: 200px;
                max-height: 200px;
                border-radius: 50%;
                border: 5px solid #e23636;
                display: block;
                margin: 20px auto;
            }
            .details {
                background-color: #f9f9f9;
                border-radius: 10px;
                padding: 20px;
                margin-top: 20px;
            }
            .detail-row {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #ddd;
                padding: 10px 0;
            }
            .detail-row:last-child {
                border-bottom: none;
            }
            .detail-label {
                font-weight: bold;
                color: #e23636;  /* Color Marvel */
            }
            .color-sample {
                display: inline-block;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                margin-left: 10px;
                border: 1px solid #333;
            }
            .superhero-watermark {
                position: absolute;
                opacity: 0.1;
                font-size: 120px;
                color: #e23636;
                z-index: -1;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-30deg);
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>{$personaje['nombre']}</h1>
            </div>";

    // Solo incluir la imagen si se encontró una ruta válida
    if (!empty($image_path)) {
        $html .= "<img src='{$image_path}' alt='{$personaje['nombre']}' class='character-photo'>";
    } else {
        $html .= "<div style='text-align: center; margin: 20px; color: #e23636;'>
                    <p>Imagen no disponible</p>
                  </div>";
    }

    $html .= "
            <div class='details'>
                <div class='detail-row'>
                    <span class='detail-label'>Nombre</span>
                    <span>{$personaje['nombre']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Color Representativo</span>
                    <span>
                        {$personaje['color']}
                        <span class='color-sample' style='background-color: {$personaje['color']};'></span>
                    </span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Tipo</span>
                    <span>{$personaje['tipo']}</span>
                </div>
                <div class='detail-row'>
                    <span class='detail-label'>Nivel</span>
                    <span>{$personaje['nivel']}</span>
                </div>
            </div>
            <div class='superhero-watermark'>MARVEL</div>
        </div>
    </body>
    </html>";

    // Cargar el HTML al generador de PDF
    $dompdf->loadHtml($html);

    // Establecer el tamaño y la orientación del papel
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar el PDF
    $dompdf->render();

    // Forzar descarga del PDF
    $dompdf->stream("perfil_{$personaje['nombre']}.pdf", array("Attachment" => 1));

    // Terminar el script
    exit();
}
?>