<?php
$html = file_get_contents('http://localhost/Sillage/index.php');
// Look for sillage-mobile-actions
if (preg_match('/<div class="[^"]*sillage-mobile-actions[^"]*">(.*?)<\/div>/s', $html, $m)) {
    echo "MATCHED sillage-mobile-actions:\n" . $m[1] . "\n";
} else {
    echo "NOT MATCHED sillage-mobile-actions\n";
}
?>
