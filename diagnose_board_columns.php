declare(strict_types=1);

// Bootstrap the application
require_once __DIR__ . '/bootstrap/app.php';


use App\Core\Database;

try {
Database::init();

echo "=== Scrum Board Column Analysis ===\n\n";

// Get all boards with their column counts
$query = "
SELECT
b.id as board_id,
b.name as board_name,
b.type as board_type,
p.id as project_id,
p.name as project_name,
p.key as project_key,
COUNT(bc.id) as column_count
FROM boards b
LEFT JOIN board_columns bc ON b.id = bc.board_id
LEFT JOIN projects p ON b.project_id = p.id
GROUP BY b.id
ORDER BY p.name, b.name
";

$boards = Database::select($query);

echo "Total Boards: " . count($boards) . "\n\n";

foreach ($boards as $board) {
$status = $board['column_count'] == 0 ? '⚠️ NO COLUMNS' : ($board['column_count'] < 5 ? '⚠️  FEW COLUMNS' : '✅ OK' );
    echo "Project: {$board['project_name']} ({$board['project_key']})\n" ;
    echo "  Board: {$board['board_name']} (ID: {$board['board_id']}, Type: {$board['board_type']})\n" ;
    echo "  Columns: {$board['column_count']} {$status}\n" ; if ($board['column_count']> 0) {
    $columns = Database::select("
    SELECT id, name, status_ids, sort_order
    FROM board_columns
    WHERE board_id = ?
    ORDER BY sort_order
    ", [$board['board_id']]);

    foreach ($columns as $col) {
    $statusArray = json_decode($col['status_ids'] ?? '[]', true) ?: [];
    $statusCount = count($statusArray);
    echo " - {$col['name']} (status_ids: {$col['status_ids']}, count: {$statusCount})\n";
    }
    }

    echo "\n";
    }

    // Check available statuses
    echo "\n=== Available Statuses ===\n";
    $statuses = Database::select("SELECT id, name, category, color FROM statuses ORDER BY category, sort_order");

    $byCategory = [];
    foreach ($statuses as $status) {
    $category = $status['category'] ?? 'unknown';
    if (!isset($byCategory[$category])) {
    $byCategory[$category] = [];
    }
    $byCategory[$category][] = $status;
    }

    foreach ($byCategory as $category => $items) {
    echo "\n{$category}:\n";
    foreach ($items as $status) {
    echo " - {$status['name']} (ID: {$status['id']}, Color: {$status['color']})\n";
    }
    }

    } catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    }