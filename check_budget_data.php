<?php
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "--- PROJECTS TABLE (ID=1) ---\n";
    $stmt = $pdo->query("SELECT id, name, budget, budget_currency FROM projects WHERE id = 1");
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($project);

    echo "\n--- PROJECT_BUDGETS TABLE (Project ID=1) ---\n";
    try {
        $stmt = $pdo->query("SELECT * FROM project_budgets WHERE project_id = 1");
        $budget = $stmt->fetch(PDO::FETCH_ASSOC);
        print_r($budget);
    } catch (Exception $e) {
        echo "Table project_budgets might not exist or error: " . $e->getMessage();
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
