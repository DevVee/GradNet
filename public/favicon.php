<?php
/**
 * GradNet favicon — serves the GradNet logo as a 64×64 PNG
 * with rounded corners (radius 14 px). Cached by browser via
 * Cache-Control header. No framework boot needed.
 */
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

// Try root GradNet.png first, fall back to public/images copy
$srcPath = file_exists(__DIR__ . '/../GradNet.png')
    ? __DIR__ . '/../GradNet.png'
    : __DIR__ . '/images/gradnet-logo.png';
$src  = imagecreatefrompng($srcPath);
$size = 64;
$r    = 14;   // corner radius

// Destination canvas — transparent background
$dst = imagecreatetruecolor($size, $size);
imagealphablending($dst, false);
imagesavealpha($dst, true);
$clear = imagecolorallocatealpha($dst, 0, 0, 0, 127);
imagefill($dst, 0, 0, $clear);

// Resize the logo into the 64×64 canvas
imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size,
                   imagesx($src), imagesy($src));

// Punch out the four corners to create the rounded effect
for ($x = 0; $x < $size; $x++) {
    for ($y = 0; $y < $size; $y++) {
        $inCorner =
            ($x <  $r        && $y <  $r        && ($x-$r)**2        + ($y-$r)**2        > $r**2) ||
            ($x >= $size-$r  && $y <  $r        && ($x-($size-$r))**2+ ($y-$r)**2        > $r**2) ||
            ($x <  $r        && $y >= $size-$r  && ($x-$r)**2        + ($y-($size-$r))**2> $r**2) ||
            ($x >= $size-$r  && $y >= $size-$r  && ($x-($size-$r))**2+ ($y-($size-$r))**2> $r**2);
        if ($inCorner) imagesetpixel($dst, $x, $y, $clear);
    }
}

imagepng($dst);
imagedestroy($src);
imagedestroy($dst);
