<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Step 1: Loading bootstrap...\n";
require_once __DIR__ . '/bootstrap/autoload.php';
echo "✓ Autoload loaded\n";

require_once __DIR__ . '/bootstrap/app.php';
echo "✓ App loaded\n\n";

echo "Step 2: Checking database connection...\n";
use App\Core\Database;
try {
    $count = Database::selectValue("SELECT COUNT(*) FROM boards");
    echo "✓ Database OK. Boards: $count\n\n";
} catch (\Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit;
}

echo "Step 3: Checking velocity.php view file...\n";
$viewFile = __DIR__ . '/views/reports/velocity.php';
if (file_exists($viewFile)) {
    $size = filesize($viewFile);
    echo "✓ View file exists ($size bytes)\n";
    
    $content = file_get_contents($viewFile);
    if (strpos($content, 'velocityChart') !== false) {
        echo "✓ Canvas element found in view\n";
    } else {
        echo "✗ Canvas element NOT found\n";
    }
    
    if (strpos($content, 'VELOCITY SCRIPT LOADED') !== false) {
        echo "✓ Script console.log found\n\n";
    } else {
        echo "✗ Script console.log NOT found\n\n";
    }
} else {
    echo "✗ View file NOT found\n\n";
    exit;
}

echo "Step 4: Checking ReportController...\n";
$controllerFile = __DIR__ . '/src/Controllers/ReportController.php';
$content = file_get_contents($controllerFile);

// Check for return type
if (strpos($content, 'public function velocity(Request $request): string') !== false) {
    echo "✓ Controller has correct return type (string)\n";
} elseif (strpos($content, 'public function velocity(Request $request): void') !== false) {
    echo "✗ Controller still has void return type\n";
} else {
    echo "? Cannot determine controller return type\n";
}

// Check for return view
if (strpos($content, "return \$this->view('reports.velocity'") !== false) {
    echo "✓ Controller returns view\n\n";
} else {
    echo "✗ Controller does NOT return view\n\n";
}

echo "Step 5: Testing data retrieval...\n";
try {
    $board = Database::selectOne("SELECT id, name FROM boards LIMIT 1");
    if ($board) {
        echo "✓ Board found: " . $board['name'] . "\n";
        
        $sprints = Database::select(
            "SELECT id, name FROM sprints WHERE board_id = ? AND status = 'closed'",
            [$board['id']]
        );
        echo "✓ Closed sprints: " . count($sprints) . "\n";
        
        if (count($sprints) > 0) {
            echo "✓ Board has closed sprints - chart should display\n";
        } else {
            echo "⚠ Board has NO closed sprints - empty state will show\n";
        }
    } else {
        echo "✗ No boards in database\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY:\n";
echo "========\n";
echo "✓ If all steps passed, velocity chart SHOULD WORK\n";
echo "✓ Go to: http://localhost/jira_clone_system/public/reports/velocity/1\n";
echo "✓ Press F12 → Console and look for 'VELOCITY SCRIPT LOADED'\n";
?>
