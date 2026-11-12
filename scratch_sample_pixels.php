<?php
$size = getimagesize('screenshot_se_dpi1.png');
echo "Image dimensions: {$size[0]} x {$size[1]}\n";
$im = imagecreatefrompng('screenshot_se_dpi1.png');
// Sample colors in the header row at y=25 for x from 200 to 360 in steps of 10
echo "Header color samples at y=25:\n";
for ($x = 180; $x <= 360; $x += 15) {
    $rgb = imagecolorat($im, $x, 25);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    echo "x={$x}: RGB({$r},{$g},{$b})\n";
}
?>
