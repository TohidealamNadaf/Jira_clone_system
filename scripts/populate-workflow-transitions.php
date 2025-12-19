<?php
/**
 * Populate Workflow Transitions
 * 
 * This script populates the workflow_transitions table with standard transitions
 * for Jira-like workflow management. Run this after setting up the database.
 * 
 * Usage: php scripts/populate-workflow-transitions.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;

class WorkflowTransitionSeeder
{
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function run(): int
    {
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║         Populating Workflow Transitions                         ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n\n";

        try {
            // Check if transitions already exist
            $count = $this->pdo->query("SELECT COUNT(*) FROM workflow_transitions")->fetchColumn();
            
            if ($count > 0) {
                echo "✅ Workflow transitions already populated ({$count} rows)\n";
                return 0;
            }

            echo "Status: No workflow transitions found. Creating...\n\n";

            // Get the default workflow
            $workflow = Database::selectOne("SELECT id FROM workflows WHERE is_default = 1");
            if (!$workflow) {
                throw new \Exception("No default workflow found");
            }

            $workflowId = $workflow['id'];
            echo "Using workflow ID: {$workflowId}\n\n";

            // Get all statuses
            $statuses = Database::select("SELECT id, name FROM statuses ORDER BY sort_order ASC");
            
            echo "Available statuses:\n";
            $statusMap = [];
            foreach ($statuses as $status) {
                $statusMap[$status['name']] = $status['id'];
                echo "  • {$status['name']} (ID: {$status['id']})\n";
            }
            echo "\n";

            // Define standard Jira-like transitions
            $transitions = $this->getStandardTransitions();

            echo "Creating workflow transitions:\n\n";

            $inserted = 0;
            $skipped = 0;

            foreach ($transitions as $from => $tos) {
                if (!isset($statusMap[$from])) {
                    echo "  ⚠️  Skipping unknown status: {$from}\n";
                    $skipped++;
                    continue;
                }

                $fromStatusId = $statusMap[$from];

                foreach ($tos as $to) {
                    if (!isset($statusMap[$to])) {
                        echo "  ⚠️  Skipping unknown target: {$to}\n";
                        $skipped++;
                        continue;
                    }

                    $toStatusId = $statusMap[$to];

                    $stmt = $this->pdo->prepare(
                        "INSERT INTO workflow_transitions (workflow_id, name, from_status_id, to_status_id) 
                         VALUES (?, ?, ?, ?)"
                    );

                    $stmt->execute([
                        $workflowId,
                        "{$from} → {$to}",
                        $fromStatusId,
                        $toStatusId
                    ]);

                    echo "  ✅ {$from} → {$to}\n";
                    $inserted++;
                }
            }

            echo "\n";
            echo "╔════════════════════════════════════════════════════════════════╗\n";
            echo "║  ✅ Successfully populated {$inserted} workflow transitions      ║\n";
            if ($skipped > 0) {
                echo "║  ⚠️  Skipped {$skipped} invalid transitions                       ║\n";
            }
            echo "║  🚀 Board drag-and-drop is now fully functional               ║\n";
            echo "╚════════════════════════════════════════════════════════════════╝\n";

            return 0;

        } catch (\Exception $e) {
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            return 1;
        }
    }

    /**
     * Get standard Jira-like workflow transitions
     */
    private function getStandardTransitions(): array
    {
        return [
            // From Open
            'Open' => [
                'To Do',
                'Closed'
            ],

            // From To Do
            'To Do' => [
                'In Progress',
                'Open',
                'Closed'
            ],

            // From In Progress
            'In Progress' => [
                'In Review',
                'Testing',
                'To Do',
                'Closed'
            ],

            // From In Review
            'In Review' => [
                'In Progress',
                'Testing',
                'To Do',
                'Closed'
            ],

            // From Testing
            'Testing' => [
                'In Progress',
                'Done',
                'In Review',
                'Closed'
            ],

            // From Done
            'Done' => [
                'Closed',
                'In Progress',
                'Testing'
            ],

            // From Closed
            'Closed' => [
                'To Do',
                'In Progress'
            ]
        ];
    }
}

// Run the seeder
$seeder = new WorkflowTransitionSeeder();
exit($seeder->run());
