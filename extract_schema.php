<?php
$file = __DIR__ . '/database/jiira_clonee_system (2).sql';
$handle = fopen($file, "r");
if ($handle) {
    $buffer = '';
    $capturing = false;
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'CREATE TABLE `roadmap_item_issues`') !== false) {
            $capturing = true;
        }
        if ($capturing) {
            $buffer .= $line;
            if (strpos($line, ';') !== false) {
                break;
            }
        }
    }
    fclose($handle);
    echo $buffer;
} else {
    echo "Error opening file";
}
