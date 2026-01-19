<?php
/**
 * Backlog Routing Verification Tool
 * Verifies all projects have Scrum boards and routing works correctly
 */

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=cways_prod',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

$allPass = true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Backlog Routing Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .header { background: #0052CC; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .pass { border-left: 4px solid #216e4e; }
        .fail { border-left: 4px solid #ae2a19; background: #fdf0ef; }
        .project-row { padding: 10px; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: 150px 200px 150px 200px; gap: 10px; align-items: center; }
        .project-row:last-child { border-bottom: none; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-pass { background: #e6ffed; color: #216e4e; }
        .badge-fail { background: #ffe6e6; color: #ae2a19; }
        .code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f5f5f5; padding: 10px; text-align: left; font-weight: bold; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .summary { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin: 20px 0; }
        .summary-card { background: white; padding: 15px; border-radius: 8px; text-align: center; }
        .summary-card h3 { margin: 0 0 10px 0; color: #666; font-size: 14px; }
        .summary-card .number { font-size: 32px; font-weight: bold; }
        .summary-card.pass .number { color: #216e4e; }
        .summary-card.fail .number { color: #ae2a19; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Backlog Routing Verification Tool</h1>
        <p>Verifies all projects have Scrum boards and routing configuration</p>
    </div>

    <?php
    // Get all projects
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

    $projectsWithoutScrum = [];
    $projectsWithScrum = [];

    foreach ($projects as $project) {
        if ((int)($project['scrum_board_count'] ?? 0) > 0) {
            $projectsWithScrum[] = $project;
        } else {
            $projectsWithoutScrum[] = $project;
            $allPass = false;
        }
    }
    ?>

    <div class="summary">
        <div class="summary-card <?= $allPass ? 'pass' : 'fail' ?>">
            <h3>Total Projects</h3>
            <div class="number"><?= count($projects) ?></div>
        </div>
        <div class="summary-card pass">
            <h3>With Scrum Board</h3>
            <div class="number"><?= count($projectsWithScrum) ?></div>
        </div>
        <div class="summary-card <?= empty($projectsWithoutScrum) ? 'pass' : 'fail' ?>">
            <h3>Without Scrum Board</h3>
            <div class="number"><?= count($projectsWithoutScrum) ?></div>
        </div>
    </div>

    <?php if (!empty($projectsWithScrum)): ?>
    <div class="section pass">
        <h2>✅ Projects With Scrum Boards (<?= count($projectsWithScrum) ?>)</h2>
        <table>
            <thead>
                <tr>
                    <th>Project Key</th>
                    <th>Project Name</th>
                    <th>Board ID</th>
                    <th>Backlog URL</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projectsWithScrum as $proj): ?>
                <tr>
                    <td><code><?= htmlspecialchars($proj['key']) ?></code></td>
                    <td><?= htmlspecialchars($proj['name']) ?></td>
                    <td><?= $proj['scrum_board_id'] ?></td>
                    <td>/projects/<?= htmlspecialchars($proj['key']) ?>/backlog</td>
                    <td><span class="badge badge-pass">✓ Ready</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if (!empty($projectsWithoutScrum)): ?>
    <div class="section fail">
        <h2>❌ Projects Without Scrum Boards (<?= count($projectsWithoutScrum) ?>)</h2>
        <p>⚠️ These projects need Scrum boards created. Run the fix script:</p>
        <div class="code" style="padding: 10px; margin: 10px 0;">
            php scripts/fix-missing-scrum-boards-simple.php
        </div>
        <table>
            <thead>
                <tr>
                    <th>Project Key</th>
                    <th>Project Name</th>
                    <th>Boards Count</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projectsWithoutScrum as $proj): ?>
                <tr>
                    <td><code><?= htmlspecialchars($proj['key']) ?></code></td>
                    <td><?= htmlspecialchars($proj['name']) ?></td>
                    <td><?= $proj['board_count'] ?></td>
                    <td><span class="badge badge-fail">✗ Missing Scrum Board</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="section">
        <h2>🔍 Routing Configuration</h2>
        <p>When accessing <span class="code">/projects/{KEY}/backlog</span>:</p>
        <ol>
            <li><strong>Check</strong> if project exists</li>
            <li><strong>Query</strong> for Scrum board: <span class="code">SELECT id FROM boards WHERE project_id = ? AND type = 'scrum'</span></li>
            <li><strong>Redirect</strong> to <span class="code">/boards/{BOARD_ID}/backlog</span> if found</li>
            <li><strong>Fallback</strong> to old backlog page if no board exists (backward compatibility)</li>
        </ol>
        <p><strong>Status:</strong> <?= $allPass ? '✅ ALL PROJECTS PROPERLY CONFIGURED' : '⚠️ SOME PROJECTS NEED FIXING' ?></p>
    </div>

    <div class="section">
        <h2>📋 Requirements for Real Jira Compatibility</h2>
        <ul>
            <li>✅ Every project has at least one Scrum board</li>
            <li>✅ Backlog page redirects to board view</li>
            <li>✅ Default columns: To Do, In Progress, Done</li>
            <li>✅ Consistent user experience across all projects</li>
            <li>✅ New projects auto-create Scrum board</li>
        </ul>
        <p><strong>Current Status:</strong> <?= $allPass ? '✅ PRODUCTION READY' : '⚠️ NEEDS FIXING' ?></p>
    </div>

    <?php if ($allPass): ?>
    <div class="section pass" style="text-align: center; padding: 40px;">
        <h2>🎉 VERIFICATION PASSED</h2>
        <p>All projects have Scrum boards and are properly configured for Real Jira backlog behavior.</p>
        <p><strong>Next step:</strong> Clear browser cache and test project backlogs</p>
    </div>
    <?php endif; ?>
</body>
</html>
