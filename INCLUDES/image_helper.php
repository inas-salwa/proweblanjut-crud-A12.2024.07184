<?php
function uploadFotoWithThumb($file) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024;

    if (!in_array($file['type'], $allowed)) return ['error' => 'Format foto tidak valid.'];
    if ($file['size'] > $max_size) return ['error' => 'Ukuran foto maks 2 MB.'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nama_file = uniqid('brg_') . '.' . $ext;

    $path_original = 'uploads/original/' . $nama_file;
    $path_thumb = 'uploads/thumbs/thumb_' . $nama_file;

    $dir_original = 'C:/xampp/htdocs/INVENTORY/uploads/original/';
    $dir_thumb    = 'C:/xampp/htdocs/INVENTORY/uploads/thumbs/';

    switch ($file['type']) {
        case 'image/jpeg': $source = imagecreatefromjpeg($file['tmp_name']); break;
        case 'image/png': $source = imagecreatefrompng($file['tmp_name']); break;
        case 'image/gif': $source = imagecreatefromgif($file['tmp_name']); break;
        case 'image/webp': $source = imagecreatefromwepb($file['tmp_name']); break;
        default: return ['error' => 'Format tidak didukung.'];
    }

    if ($width > $max_w || $height > $max_h) {
        $scale = min($max_w / $width, $max_h / $height);
        $new_w = floor($width * $scale);
        $new_h = floor($height* $scale);
        $resize = imagecreatetruecolor ($new_w, $new_h);

        if ($file['type'] === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $new_w, $new_h, $width, $height);
        imagejpeg($resized, $dir_original, $nama_file, 90);
        imagedestroy($resized);
        $width = $new_w; $height = $new_h;
    } else {
        move_uploaded_file($file['tmp_name'], $dir_original . $nama_file);
        switch ($file['type']) {
            case 'image/jpeg': $source = imagecreatefromjpeg($dir_original . $nama_file); break;
            case 'image/png':  $source = imagecreatefrompng($dir_original . $nama_file);  break;
            case 'image/gif':  $source = imagecreatefromgif($dir_original . $nama_file);  break;
            case 'image/webp': $source = imagecreatefromwebp($dir_original . $nama_file); break;
        }
    }


    $thumb_size = 200;
    $thumb = imagecreatetruecolor($thumb_size, $thumb_size);

    if ($width > $height) {
        $crop_x = ($width - $height) / 2;
        $crop_y = 0;
        $crop_s = $height;
    } else {
        $crop_x = 0;
        $crop_y = ($height - $width) / 2;
        $crop_s = $width;
    }

    imagecopyresampled($thumb, $source, 0, 0, $crop_x, $crop_y, $thumb_size, $thumb_size, $crop_s);
    imagejpeg($thumb, $dir_thumb . 'thumb_' . $nama_file, 90);

    imagedestroy($source);
    imagedestroy($thumb);

    return [
        'original' => $path_original,
        'thumb' => $path_thumb,
        'nama' => $nama_file,
    ];
}