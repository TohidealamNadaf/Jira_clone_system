<?php
/**
 * This script systematically wraps all avatar variables with the avatar() helper function
 * to fix 404 errors across the entire system.
 */

// Define the replacements needed
$replacements = [
    // Replace assignee_avatar without avatar() wrapper
    '/src="<\?=\s*e\(\$issue\[\'assignee_avatar\'\]\)\s*\?>"/' => 'src="<?= e(avatar($issue[\'assignee_avatar\'])) ?>"',
    '/src="<\?=\s*e\(\$issue\["assignee_avatar"\]\)\s*\?>"/' => 'src="<?= e(avatar($issue["assignee_avatar"])) ?>"',
    
    // Replace reporter_avatar without avatar() wrapper
    '/src="<\?=\s*e\(\$issue\[\'reporter_avatar\'\]\)\s*\?>"/' => 'src="<?= e(avatar($issue[\'reporter_avatar\'])) ?>"',
    '/src="<\?=\s*e\(\$issue\["reporter_avatar"\]\)\s*\?>"/' => 'src="<?= e(avatar($issue["reporter_avatar"])) ?>"',
    
    // Replace $avatarUrl without avatar() wrapper (only when not already wrapped)
    '/src="<\?=\s*e\(\$avatarUrl\)\s*\?>"/' => 'src="<?= e(avatar($avatarUrl)) ?>"',
    
    // Replace $project['avatar'] with url() instead of avatar()
    '/src="<\?=\s*e\(url\(\$project\[\'avatar\'\]\)\)\s*\?>"/' => 'src="<?= e(avatar($project[\'avatar\'])) ?>"',
    '/src="<\?=\s*e\(url\(\$project\["avatar"\]\)\)\s*\?>"/' => 'src="<?= e(avatar($project["avatar"])) ?>"',
    \'/src="<\?= url\(\$project\[\\\'avatar\\\'\]\) \?>"/' => 'src="<?= avatar($project[\'avatar\']) ?>"',
    
    // Replace $project['lead']['avatar'] without avatar() wrapper
    '/src="<\?=\s*e\(\$project\[\'lead\'\]\[\'avatar\'\]\s*\??\?\s*\'[^\']*\'\)\s*\?>"/' => 'src="<?= e(avatar($project[\'lead\'][\'avatar\'] ?? null) ?: \'https://ui-avatars.com/api/?name=\' . urlencode($project[\'lead\'][\'display_name\'] ?? \'U\')) ?>"',
    
    // Replace $user['avatar'] without avatar() wrapper
   '/src="<\?=\s*e\(\$user\[\'avatar\'\]\s*\??\?\s*null\)\s*\?>"/' => 'src="<?= e(avatar($user[\'avatar\'] ?? null)) ?>"',
    
    // Replace $member['avatar'] without avatar() wrapper
    '/src="<\?=\s*e\(\$member\[\'avatar\'\]\)\s*\?>"/' => 'src="<?= e(avatar($member[\'avatar\'])) ?>"',
];

// Get all PHP view files
$viewsDir = __DIR__ . '/../views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$phpFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getPathname();
    }
}

echo "Found " . count($phpFiles) . " PHP files to process\n\n";

$filesModified = 0;
$totalReplacements = 0;

foreach ($phpFiles as $filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $fileReplacements = 0;
    
    foreach ($replacements as $pattern => $replacement) {
        $count = 0;
        $content = preg_replace($pattern, $replacement, $content, -1, $count);
        if ($count > 0) {
            $fileReplacements += $count;
            $totalReplacements += $count;
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        $relativePath = str_replace(__DIR__ . '/../', '', $filePath);
        echo "✓ Modified: $relativePath ($fileReplacements replacements)\n";
        $filesModified++;
    }
}

echo "\n======================\n";
echo "Summary:\n";
echo "Files modified: $filesModified\n";
echo "Total replacements: $totalReplacements\n";
echo "======================\n";
