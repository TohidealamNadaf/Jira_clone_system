<?php
require 'bootstrap/app.php';

$boardId = 2;

$board = \App\Core\Database::selectOne(
    "SELECT b.*, p.id as project_id, p.`key` as project_key
    FROM boards b
    JOIN projects p ON b.project_id = p.id
    WHERE b.id = ?",
    [$boardId]
);

if (!$board) {
    die("Board not found");
}

$statuses = \App\Core\Database::select(
    "SELECT id, name, color, category
    FROM statuses
    ORDER BY sort_order"
);

$days = 30;
$startDate = (new \DateTime())->modify("-{$days} days");
$endDate = new \DateTime();

$flowData = [];
$current = clone $startDate;

while ($current <= $endDate) {
    $dateStr = $current->format('Y-m-d');
    $dayData = ['date' => $dateStr];
    
    foreach ($statuses as $status) {
        $count = (int) \App\Core\Database::selectValue(
            "SELECT COUNT(*)
            FROM issues i
            JOIN issue_history h ON i.id = h.issue_id
            WHERE i.project_id = ?
            AND h.field = 'status_id'
            AND h.new_value = ?
            AND DATE(h.created_at) <= ?
            AND (
                NOT EXISTS (
                    SELECT 1 FROM issue_history h2
                    WHERE h2.issue_id = i.id
                    AND h2.field = 'status_id'
                    AND h2.created_at > h.created_at
                    AND DATE(h2.created_at) <= ?
                )
            )",
            [$board['project_id'], $status['id'], $dateStr, $dateStr]
        );
        
        if ($count === 0) {
            $count = (int) \App\Core\Database::selectValue(
                "SELECT COUNT(*)
                FROM issues
                WHERE project_id = ?
                AND status_id = ?
                AND DATE(created_at) <= ?",
                [$board['project_id'], $status['id'], $dateStr]
            );
        }
        
        $dayData[$status['name']] = $count;
    }
    
    $flowData[] = $dayData;
    $current->modify('+1 day');
}

// Set up minimal data for view
$user = null;
$errors = [];
$flash = ['success' => null, 'error' => null, 'warning' => null, 'info' => null];

// Extract just the script section from the view
ob_start();
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flowData = <?= json_encode($flowData ?? []) ?>;
    const statuses = <?= json_encode($statuses ?? []) ?>;
    
    console.log('flowData:', flowData);
    console.log('flowData.length:', flowData ? flowData.length : 'undefined');
    console.log('statuses:', statuses);
    
    if (!flowData || flowData.length === 0) {
        console.log('flowData is empty, hiding chart');
        document.getElementById('cumulativeFlowChart').style.display = 'none';
        return;
    }
    
    console.log('Creating chart...');
    console.log('First date:', flowData[0]['date']);
    console.log('Last date:', flowData[flowData.length - 1]['date']);
});
</script>
<?php
$script = ob_get_clean();
echo $script;
