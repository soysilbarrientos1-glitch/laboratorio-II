<?php
// optimizar-imagenes.php

$source_dir = 'Imagenes/';
$target_dir = 'Imagenes/optimizadas/';

// Crear la carpeta de destino si no existe
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Obtener todos los archivos de imagen
$images = glob($source_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

if (empty($images)) {
    echo "No se encontraron imágenes en la carpeta '$source_dir'.\n";
    exit;
}

echo "Optimizando " . count($images) . " imágenes...\n";

foreach ($images as $image_path) {
    $filename = basename($image_path);
    $target_path = $target_dir . $filename;

    // Obtener información de la imagen
    $info = getimagesize($image_path);
    if (!$info) {
        echo "⚠️ No se pudo obtener información de la imagen: $filename\n";
        continue;
    }

    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    // Dimensiones objetivo
    $max_width = 600;
    $max_height = 450;

    // Calcular nuevas dimensiones manteniendo la proporción
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = round($width * $ratio);
    $new_height = round($height * $ratio);

    // Crear una nueva imagen en blanco
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Cargar la imagen original según su tipo
    switch ($mime) {
        case 'image/jpeg':
        case 'image/jpg':
            $original = imagecreatefromjpeg($image_path);
            break;
        case 'image/png':
            $original = imagecreatefrompng($image_path);
            // Para PNG, activar transparencia
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            break;
        case 'image/gif':
            $original = imagecreatefromgif($image_path);
            break;
        default:
            echo "❌ Formato no soportado: $filename\n";
            continue;
    }

    if (!$original) {
        echo "❌ No se pudo cargar la imagen: $filename\n";
        continue;
    }

    // Redimensionar la imagen
    imagecopyresampled($new_image, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Guardar como JPG (si es PNG o GIF, convertir)
    if ($mime === 'image/png' || $mime === 'image/gif') {
        // Convertir a JPG
        $target_path = str_replace(['.png', '.gif'], '.jpg', $target_path);
        imagejpeg($new_image, $target_path, 85); // Calidad 85%
    } else {
        // Mantener como JPG
        imagejpeg($new_image, $target_path, 85);
    }

    // Liberar memoria
    imagedestroy($original);
    imagedestroy($new_image);

    echo "✅ Optimizada: $filename -> $target_path\n";
}

echo "\n🎉 Todas las imágenes han sido optimizadas.\n";
?>