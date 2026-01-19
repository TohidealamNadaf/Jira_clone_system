<?php
$pdo = new PDO(
    'mysql:host=localhost;dbname=cways_prod',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$stmt = $pdo->query("
    SELECT 
        p.id, 
        p.key, 
        p.name,
        COUNT(b.id) as board_count,
        SUM(CASE WHEN b.type = 'scrum' THEN 1 ELSE 0 END) as scrum_board_count,
        MAX(CASE WHEN b.type = 'scrum' THEN b.id ELSE NULL END) as scrum_board_id
    FROM projects p
    LEFT JOIN boards b ON p.id = b.project_id
    WHERE p.is_archived = 0
    GROUP BY p.id
    ORDER BY p.name
");

$projects = $stmt->fetchAll();

echo "\n════════════════════════════════════════════════════════════════\n";
echo "              BACKLOG ROUTING VERIFICATION RESULTS\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$allPass = true;
foreach ($projects as $proj) {
    $hasScrumBoard = !empty($proj['scrum_board_id']);
    $status = $hasScrumBoard ? '✅' : '❌';
    $redirect = $hasScrumBoard 
        ? "/boards/{$proj['scrum_board_id']}/backlog"
        : "/projects/{$proj['key']}/backlog";
    
    printf(
        "  %s  %-12s  %-28s  Board:%3s  →  %s\n",
        $status,
        $proj['key'],
        $proj['name'],
        $proj['scrum_board_id'] ?: 'X',
        $redirect
    );
    
    if (!$hasScrumBoard) {
        $allPass = false;
    }
}

echo "\n════════════════════════════════════════════════════════════════\n";
if ($allPass) {
    echo "✅ ALL PROJECTS PASS - Consistent routing ready!\n";
} else {
    echo "⚠️  Some projects still need Scrum boards\n";
}
echo "════════════════════════════════════════════════════════════════\n\n";
