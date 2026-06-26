<?php
$src_img = __DIR__ . '/assets/img/logo.png';
if (file_exists($src_img)) {
    $src = imagecreatefrompng($src_img);
    $w = imagesx($src);
    $h = imagesy($src);
    $size = max($w, $h);

    $padding = (int)($size * 0.1);
    $new_size = $size + ($padding * 2);

    $dst = imagecreatetruecolor($new_size, $new_size);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefill($dst, 0, 0, $transparent);

    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefilledellipse($dst, $new_size/2, $new_size/2, $new_size, $new_size, $white);

    imagealphablending($dst, true);
    $offset_x = ($new_size - $w) / 2;
    $offset_y = ($new_size - $h) / 2;
    
    imagecopy($dst, $src, $offset_x, $offset_y, 0, 0, $w, $h);
    
    imagepng($dst, __DIR__ . '/assets/img/favicon.png');
    imagedestroy($src);
    imagedestroy($dst);
}
?>
