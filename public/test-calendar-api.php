<?php
/**
 * Test Calendar API Endpoint
 * Direct test without going through routing
 */

header('Content-Type: application/json');

// Check if we can access the issues table
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=jiira_clonee_system;charset=utf8mb4',
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_THROW);
    
    // Get sample issues
    $stmt = $pdo->query("
        SELECT 
            i.id,
            i.issue_key,
            i.summary,
            i.start_date,
            i.end_date,
            i.due_date,
            s.name as status,
            p.key as project_key,
            p.name as project_name
        FROM issues i
        LEFT JOIN statuses s ON i.status_id = s.id
        LEFT JOIN projects p ON i.project_id = p.id
        LIMIT 5
    ");
    
    $issues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'count' => count($issues),
        'issues' => $issues,
        'message' => 'Database connected successfully'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Database connection failed'
    ], JSON_PRETTY_PRINT);
}
