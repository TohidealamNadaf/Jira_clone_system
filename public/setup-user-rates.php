<?php
/**
 * User Rates Setup Page
 * Quick fix for "User rate not configured" error
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Core\Database;
use App\Core\Request;

header('Content-Type: text/html; charset=utf-8');

// Check if form submitted
$action = Request::post('action');
$results = [];
$message = '';
$success = false;

if ($action === 'setup_rates') {
    try {
        $rateAmount = (float)Request::post('rate_amount', 50.00);
        $rateType = Request::post('rate_type', 'hourly');
        
        // Get all users
        $users = Database::select("SELECT id FROM users WHERE id > 1");
        
        if (empty($users)) {
            throw new Exception("No users found in database");
        }
        
        $count = 0;
        foreach ($users as $user) {
            $userId = $user['id'];
            
            // Check for existing active rate
            $existing = Database::selectOne(
                "SELECT id FROM user_rates WHERE user_id = ? AND is_active = 1",
                [$userId]
            );
            
            if (!$existing) {
                // Deactivate old rates
                Database::execute(
                    "UPDATE user_rates SET is_active = 0 WHERE user_id = ?",
                    [$userId]
                );
                
                // Insert new rate
                Database::execute(
                    "INSERT INTO user_rates (user_id, rate_type, rate_amount, currency, is_active, effective_from, created_at) 
                     VALUES (?, ?, ?, 'USD', 1, CURDATE(), NOW())",
                    [$userId, $rateType, $rateAmount]
                );
                $count++;
            }
        }
        
        $success = true;
        $message = "✓ Successfully configured rates for $count user(s)!";
        
        // Get results to display
        $results = Database::select(
            "SELECT u.id, u.email, u.display_name, ur.rate_type, ur.rate_amount, ur.currency
             FROM users u
             LEFT JOIN user_rates ur ON u.id = ur.user_id AND ur.is_active = 1
             WHERE u.id > 1
             ORDER BY u.id"
        );
        
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Rates Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        button:hover {
            background: #5568d3;
        }
        .results {
            margin-top: 30px;
            border-top: 2px solid #eee;
            padding-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏱️ User Rates Setup</h1>
            <p>Configure hourly rates for all users to enable time tracking</p>
        </div>
        
        <div class="content">
            <div class="info">
                <strong>About this page:</strong> Each user needs a configured hourly rate before they can start a timer. 
                This tool quickly sets up rates for all users. You can adjust rates later in individual user profiles.
            </div>
            
            <?php if ($message): ?>
                <div class="message <?= $success ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="setup_rates">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rate_type">Rate Type</label>
                        <select id="rate_type" name="rate_type">
                            <option value="hourly" selected>Hourly ($X/hour)</option>
                            <option value="minutely">Minutely ($X/minute)</option>
                            <option value="secondly">Secondly ($X/second)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="rate_amount">Rate Amount (USD)</label>
                        <input type="number" id="rate_amount" name="rate_amount" value="50.00" step="0.01" min="0.01">
                    </div>
                </div>
                
                <button type="submit">Setup User Rates</button>
            </form>
            
            <?php if (!empty($results)): ?>
                <div class="results">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">User Rates Status</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Display Name</th>
                                <th>Rate Type</th>
                                <th>Rate Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['display_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['rate_type'] ?? '-') ?></td>
                                    <td>$<?= $row['rate_amount'] ? number_format($row['rate_amount'], 2) : '-' ?></td>
                                    <td>
                                        <?php if ($row['rate_amount']): ?>
                                            <span class="badge badge-success">✓ Configured</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">⚠ Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
