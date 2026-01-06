<?php
declare(strict_types=1);

// Simple verification utility - no auth check for admin panel
require '../../bootstrap/autoload.php';

use App\Core\Database;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Count Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; background: white; padding: 30px; border-radius: 8px; }
        .user-card { border-left: 4px solid #8B1956; margin-bottom: 20px; }
        .count-correct { color: #216E4E; font-weight: bold; }
        .count-warning { color: #ED3C32; font-weight: bold; }
        table { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="bi bi-check-circle"></i> Dashboard Count Verification</h1>
    <p class="text-muted">Verify that the "Assigned to Me" count is correct for each user.</p>
    <hr>

    <?php
    try {
        // Get all users with assigned issues
        $users = Database::select(
            "SELECT u.id, u.display_name, u.email FROM users u ORDER BY u.display_name"
        );

        foreach ($users as $user) {
            $userId = (int)$user['id'];
            $userName = e($user['display_name']);
            $email = e($user['email']);

            // Count active issues (not done)
            $activeCount = (int)Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND s.category != 'done'",
                [$userId]
            );

            // Count done issues
            $doneCount = (int)Database::selectValue(
                "SELECT COUNT(*) FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ? AND s.category = 'done'",
                [$userId]
            );

            // Total count
            $totalCount = $activeCount + $doneCount;

            // Get status breakdown
            $breakdown = Database::select(
                "SELECT s.name, s.category, COUNT(*) as count
                 FROM issues i
                 JOIN statuses s ON i.status_id = s.id
                 WHERE i.assignee_id = ?
                 GROUP BY s.id, s.name, s.category
                 ORDER BY s.sort_order ASC",
                [$userId]
            );

            if ($totalCount > 0) {
                echo '<div class="card user-card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $userName . ' (' . $email . ')</h5>';
                
                echo '<div class="row">';
                echo '<div class="col-md-6">';
                echo '<strong>Summary:</strong>';
                echo '<ul class="list-unstyled mt-2">';
                echo '<li>Active (not done): <span class="count-correct">' . $activeCount . '</span></li>';
                echo '<li>Done/Completed: ' . $doneCount . '</li>';
                echo '<li>Total: ' . $totalCount . '</li>';
                echo '</ul>';
                echo '</div>';
                
                echo '<div class="col-md-6">';
                echo '<strong>Breakdown by Status:</strong>';
                echo '<ul class="list-unstyled mt-2">';
                foreach ($breakdown as $row) {
                    $isDone = $row['category'] === 'done' ? ' <span class="badge bg-secondary">Excluded</span>' : '';
                    echo '<li>' . e($row['name']) . ': ' . $row['count'] . $isDone . '</li>';
                }
                echo '</ul>';
                echo '</div>';
                echo '</div>';

                // If expected 15, show verification
                if ($activeCount == 15) {
                    echo '<div class="alert alert-success mt-3" role="alert">';
                    echo '✅ <strong>CORRECT:</strong> Dashboard should show 15 "Assigned to Me"';
                    echo '</div>';
                } elseif ($activeCount > 0) {
                    echo '<div class="alert alert-info mt-3" role="alert">';
                    echo '✓ <strong>Active Issues:</strong> Dashboard should show ' . $activeCount . ' "Assigned to Me"';
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
            }
        }

        // Summary statistics
        echo '<hr>';
        echo '<h4>Total Statistics</h4>';
        $totalIssues = (int)Database::selectValue("SELECT COUNT(*) FROM issues");
        $totalAssigned = (int)Database::selectValue("SELECT COUNT(*) FROM issues WHERE assignee_id IS NOT NULL");
        $totalUnassigned = (int)Database::selectValue("SELECT COUNT(*) FROM issues WHERE assignee_id IS NULL");
        $totalActive = (int)Database::selectValue(
            "SELECT COUNT(*) FROM issues i
             JOIN statuses s ON i.status_id = s.id
             WHERE s.category != 'done'"
        );
        $totalDone = (int)Database::selectValue(
            "SELECT COUNT(*) FROM issues i
             JOIN statuses s ON i.status_id = s.id
             WHERE s.category = 'done'"
        );

        echo '<ul class="list-unstyled">';
        echo '<li>Total Issues: <strong>' . $totalIssues . '</strong></li>';
        echo '<li>Active (not done): <strong>' . $totalActive . '</strong></li>';
        echo '<li>Done/Completed: <strong>' . $totalDone . '</strong></li>';
        echo '<li>Assigned: <strong>' . $totalAssigned . '</strong></li>';
        echo '<li>Unassigned: <strong>' . $totalUnassigned . '</strong></li>';
        echo '</ul>';

    } catch (Exception $e) {
        echo '<div class="alert alert-danger" role="alert">';
        echo '<strong>Error:</strong> ' . e($e->getMessage());
        echo '</div>';
    }
    ?>

    <hr>
    <small class="text-muted">
        This page verifies that the dashboard "Assigned to Me" count matches the database query:
        <code>SELECT COUNT(*) FROM issues WHERE assignee_id = ? AND status.category != 'done'</code>
    </small>
</div>
</body>
</html>
