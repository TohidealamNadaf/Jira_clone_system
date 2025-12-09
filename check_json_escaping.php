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

// Test the json_encode as it would appear in the view
echo "=== JSON ENCODING TEST ===\n\n";

$json_flowData = json_encode($flowData ?? []);
$json_statuses = json_encode($statuses ?? []);

echo "FlowData length: " . strlen($json_flowData) . " characters\n";
echo "FlowData is valid JSON: " . (json_decode($json_flowData) ? "YES" : "NO") . "\n\n";

echo "Statuses length: " . strlen($json_statuses) . " characters\n";
echo "Statuses is valid JSON: " . (json_decode($json_statuses) ? "YES" : "NO") . "\n\n";

// Check for problematic characters
echo "=== CHECKING FOR PROBLEMATIC CHARACTERS ===\n";
echo "Contains quotes: " . (strpos($json_flowData, '"') !== false ? "YES" : "NO") . "\n";
echo "Contains newlines: " . (strpos($json_flowData, "\n") !== false ? "YES" : "NO") . "\n";
echo "Contains < or >: " . (preg_match('/<|>/', $json_flowData) ? "YES" : "NO") . "\n\n";

// Simulate the view output
echo "=== SIMULATED VIEW OUTPUT ===\n";
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flowData = <?= json_encode($flowData ?? []) ?>;
    const statuses = <?= json_encode($statuses ?? []) ?>;
    
    console.log('flowData type:', typeof flowData);
    console.log('flowData is array:', Array.isArray(flowData));
    console.log('flowData length:', flowData.length);
    console.log('flowData first:', flowData[0]);
});
</script>
<?php
