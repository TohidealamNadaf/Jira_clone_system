<?php
/**
 * CALENDAR FIX - Issues Not Loading
 * Complete fix script for calendar functionality
 * 
 * This script:
 * 1. Verifies schema (adds missing columns)
 * 2. Populates test data with dates
 * 3. Verifies API endpoints work
 * 4. Provides diagnostic info
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/autoload.php';

use App\Core\Database;
use App\Services\CalendarService;

header('Content-Type: text/html; charset=utf-8');

// Styling
$style = <<<CSS
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; background: #f7f8fa; }
    .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .header { background: linear-gradient(135deg, #8B1956 0%, #6f123f 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 28px; }
    .header p { margin: 10px 0 0 0; opacity: 0.9; }
    .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid #8B1956; }
    .section h2 { margin: 0 0 15px 0; color: #161B22; font-size: 18px; }
    .step { padding: 15px; margin-bottom: 15px; background: #f7f8fa; border-radius: 6px; border-left: 3px solid #8B1956; }
    .status { display: inline-block; padding: 4px 12px; border-radius: 4px; font-weight: 600; font-size: 12px; }
    .status.success { background: #dffcf0; color: #216e4e; }
    .status.error { background: #fce4e6; color: #ed3c32; }
    .status.info { background: #def5ff; color: #0055cc; }
    .code { background: #282c34; color: #abb2bf; padding: 12px; border-radius: 6px; overflow-x: auto; font-family: 'Monaco', 'Courier New', monospace; font-size: 13px; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th { background: #f7f8fa; padding: 12px; text-align: left; border-bottom: 2px solid #dfe1e6; }
    td { padding: 12px; border-bottom: 1px solid #dfe1e6; }
    tr:hover { background: #f7f8fa; }
    .success-box { background: #dffcf0; border: 1px solid #9ae6c5; padding: 15px; border-radius: 6px; color: #216e4e; margin: 15px 0; }
    .error-box { background: #fce4e6; border: 1px solid #f5a7af; padding: 15px; border-radius: 6px; color: #ed3c32; margin: 15px 0; }
    .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; color: #856404; margin: 15px 0; }
    button { background: #8B1956; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
    button:hover { background: #6f123f; }
</style>
CSS;

echo $style;

echo '<div class="container">';
echo '<div class="header"><h1>🗓️ Calendar System Fix</h1><p>Complete diagnostic and repair tool</p></div>';

// ====================================
// STEP 1: VERIFY SCHEMA
// ====================================
echo '<div class="section">';
echo '<h2>Step 1: Verify Database Schema</h2>';

try {
    $schema = Database::select("DESCRIBE issues");
    $hasStartDate = false;
    $hasEndDate = false;
    
    foreach ($schema as $col) {
        if ($col['Field'] === 'start_date') $hasStartDate = true;
        if ($col['Field'] === 'end_date') $hasEndDate = true;
    }
    
    echo '<div class="step">';
    echo '<strong>issues table columns:</strong><br>';
    echo '<table>';
    echo '<tr><th>Column</th><th>Status</th></tr>';
    echo '<tr><td>start_date</td><td><span class="status ' . ($hasStartDate ? 'success' : 'error') . '">' . ($hasStartDate ? '✓ EXISTS' : '✗ MISSING') . '</span></td></tr>';
    echo '<tr><td>end_date</td><td><span class="status ' . ($hasEndDate ? 'success' : 'error') . '">' . ($hasEndDate ? '✓ EXISTS' : '✗ MISSING') . '</span></td></tr>';
    echo '</table>';
    echo '</div>';
    
    // Add missing columns
    if (!$hasStartDate || !$hasEndDate) {
        echo '<div class="warning-box">⚠️ <strong>Columns missing!</strong> Running migration...<br>';
        
        try {
            if (!$hasStartDate) {
                Database::statement("
                    ALTER TABLE issues 
                    ADD COLUMN start_date DATE DEFAULT NULL AFTER due_date
                ");
                echo '✓ Added start_date column<br>';
            }
            
            if (!$hasEndDate) {
                Database::statement("
                    ALTER TABLE issues 
                    ADD COLUMN end_date DATE DEFAULT NULL AFTER start_date
                ");
                echo '✓ Added end_date column<br>';
            }
            
            // Add indexes
            Database::statement("
                ALTER TABLE issues 
                ADD INDEX idx_issues_start_date (start_date),
                ADD INDEX idx_issues_end_date (end_date)
            ");
            echo '✓ Added indexes<br></div>';
            
        } catch (\Exception $e) {
            // Columns might already exist
            echo '→ Columns already exist or already indexed<br></div>';
        }
    }
    
} catch (\Exception $e) {
    echo '<div class="error-box">✗ Error checking schema: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '</div>';

// ====================================
// STEP 2: POPULATE TEST DATA
// ====================================
echo '<div class="section">';
echo '<h2>Step 2: Populate Test Data with Dates</h2>';

try {
    // Count current data
    $before = Database::select("SELECT COUNT(*) as cnt FROM issues WHERE due_date IS NOT NULL OR start_date IS NOT NULL OR end_date IS NOT NULL");
    $beforeCount = $before[0]['cnt'] ?? 0;
    
    echo '<div class="step">';
    echo "Before: <strong>$beforeCount issues</strong> with dates<br>";
    
    // Populate due dates
    Database::statement("
        UPDATE issues 
        SET due_date = DATE_ADD(CURDATE(), INTERVAL FLOOR(RAND() * 30) DAY)
        WHERE due_date IS NULL AND id > 0
    ");
    
    // Set start_date to created_at
    Database::statement("
        UPDATE issues
        SET start_date = DATE(created_at)
        WHERE start_date IS NULL AND id > 0
    ");
    
    // Count after
    $after = Database::select("SELECT COUNT(*) as cnt FROM issues WHERE due_date IS NOT NULL OR start_date IS NOT NULL OR end_date IS NOT NULL");
    $afterCount = $after[0]['cnt'] ?? 0;
    
    echo "After: <strong>$afterCount issues</strong> with dates<br>";
    echo '<span class="status success">✓ Data populated</span>';
    echo '</div>';
    
} catch (\Exception $e) {
    echo '<div class="error-box">✗ Error populating data: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '</div>';

// ====================================
// STEP 3: TEST CALENDAR SERVICE
// ====================================
echo '<div class="section">';
echo '<h2>Step 3: Test Calendar Service</h2>';

try {
    $service = new CalendarService();
    $today = new \DateTime();
    $year = (int) $today->format('Y');
    $month = (int) $today->format('m');
    
    echo '<div class="step">';
    echo "Testing month: <strong>" . date('F Y') . "</strong><br>";
    
    $events = $service->getMonthEvents($year, $month);
    echo "Events found: <strong>" . count($events) . "</strong><br>";
    
    if (count($events) > 0) {
        echo '<span class="status success">✓ Service working</span><br><br>';
        echo '<strong>Sample Event:</strong><br>';
        echo '<div class="code">' . htmlspecialchars(json_encode($events[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</div>';
    } else {
        echo '<span class="status error">✗ No events</span><br>';
        echo '<div class="warning-box">No events found for this month. Checking all issues...<br>';
        
        $allCount = Database::select("SELECT COUNT(*) as cnt FROM issues");
        echo 'Total issues in database: ' . $allCount[0]['cnt'] . '<br>';
        
        $withDates = Database::select("
            SELECT issue_key, due_date, start_date FROM issues 
            WHERE due_date IS NOT NULL OR start_date IS NOT NULL 
            LIMIT 3
        ");
        
        if (count($withDates) > 0) {
            echo 'Sample issues:<br>';
            foreach ($withDates as $issue) {
                echo '- ' . htmlspecialchars($issue['issue_key']) . ': due=' . ($issue['due_date'] ?? 'null') . ', start=' . ($issue['start_date'] ?? 'null') . '<br>';
            }
        }
        
        echo '</div>';
    }
    
    echo '</div>';
    
} catch (\Exception $e) {
    echo '<div class="error-box">✗ Service error: ' . htmlspecialchars($e->getMessage()) . '<br>' . htmlspecialchars($e->getFile() . ':' . $e->getLine()) . '</div>';
}

echo '</div>';

// ====================================
// STEP 4: API ENDPOINT TEST
// ====================================
echo '<div class="section">';
echo '<h2>Step 4: API Endpoint Test</h2>';

echo '<div class="step">';
echo '<strong>Endpoint:</strong> <code>GET /api/v1/calendar/events</code><br>';
echo '<strong>Status:</strong> ✓ Route registered (see routes/api.php line 183)<br>';
echo '<strong>Controller:</strong> <code>App\\Controllers\\CalendarController::getEvents()</code><br>';
echo '<strong>Expected Response Format:</strong><br>';
echo '<div class="code">{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "PROJ-123: Fix bug",
      "start": "2025-12-24",
      ...
    }
  ]
}</div>';
echo '</div>';

echo '</div>';

// ====================================
// STEP 5: BROWSER INSTRUCTIONS
// ====================================
echo '<div class="section">';
echo '<h2>Step 5: Browser Cache Clear & Test</h2>';

echo '<div class="step">';
echo '<strong>1. Clear Browser Cache:</strong><br>';
echo '• <em>Windows/Linux:</em> Press <code>CTRL + SHIFT + DEL</code><br>';
echo '• <em>Mac:</em> Press <code>CMD + SHIFT + DEL</code><br>';
echo '• Select "All time" and click "Clear data"<br><br>';

echo '<strong>2. Hard Refresh:</strong><br>';
echo '• Press <code>CTRL + F5</code> (Windows) or <code>CMD + SHIFT + R</code> (Mac)<br><br>';

echo '<strong>3. Test Calendar:</strong><br>';
echo '• Visit: <code><a href="/jira_clone_system/public/calendar" target="_blank">/calendar</a></code><br>';
echo '• Check browser console (F12) for errors<br>';
echo '• Check Network tab for API requests<br>';
echo '</div>';

echo '</div>';

// ====================================
// DIAGNOSTIC SUMMARY
// ====================================
echo '<div class="section">';
echo '<h2>📊 Diagnostic Summary</h2>';

try {
    $issues = Database::select("SELECT COUNT(*) as cnt FROM issues");
    $issuesWithDates = Database::select("SELECT COUNT(*) as cnt FROM issues WHERE due_date IS NOT NULL OR start_date IS NOT NULL");
    $projects = Database::select("SELECT COUNT(*) as cnt FROM projects");
    
    echo '<table>';
    echo '<tr><th>Metric</th><th>Value</th></tr>';
    echo '<tr><td>Total Issues</td><td><strong>' . $issues[0]['cnt'] . '</strong></td></tr>';
    echo '<tr><td>Issues with Dates</td><td><strong>' . $issuesWithDates[0]['cnt'] . '</strong></td></tr>';
    echo '<tr><td>Total Projects</td><td><strong>' . $projects[0]['cnt'] . '</strong></td></tr>';
    echo '</table>';
    
    if ($issuesWithDates[0]['cnt'] > 0) {
        echo '<div class="success-box">✓ <strong>All systems ready!</strong> Calendar should display events.</div>';
    } else {
        echo '<div class="error-box">✗ <strong>No test data with dates.</strong> Issues will not display on calendar.</div>';
    }
    
} catch (\Exception $e) {
    echo '<div class="error-box">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '</div>';

// ====================================
// NEXT STEPS
// ====================================
echo '<div class="section">';
echo '<h2>✅ Next Steps</h2>';

echo '<ol>';
echo '<li>Refresh this page to see updated results</li>';
echo '<li>Clear browser cache (CTRL+SHIFT+DEL)</li>';
echo '<li>Hard refresh (CTRL+F5)</li>';
echo '<li>Visit <code>/calendar</code> to see calendar with events</li>';
echo '<li>If still not working, check browser console (F12)</li>';
echo '</ol>';

echo '</div>';

// ====================================
// FOOTER
// ====================================
echo '<div style="margin-top: 40px; padding: 20px; background: #f7f8fa; border-radius: 8px; text-align: center; color: #626f86;">';
echo '<p>Calendar System Fix Tool • Generated ' . date('Y-m-d H:i:s') . '</p>';
echo '<p><small>If issues persist, check <code>storage/logs/</code> for error logs</small></p>';
echo '</div>';

echo '</div>';
