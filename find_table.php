<?php
$file = __DIR__ . '/database/jiira_clonee_system (2).sql';
$content = file_get_contents($file);
if ($content === false) {
    echo "Error reading file";
    exit;
}

// Simple heuristic for UTF-16 LE (BOM or null bytes)
if (substr($content, 0, 2) === "\xFF\xFE" || strpos($content, "\x00") !== false) {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}

if (preg_match('/CREATE TABLE [`"]?roadmap_item_issues[`"]?.*?;/si', $content, $matches)) {
    echo "FOUND_START\n";
    echo $matches[0];
    echo "\nFOUND_END";
} else {
    echo "Table definition not found.";
    // Print first 100 chars to debug
    echo "\nDEBUG: " . substr($content, 0, 100);
}
