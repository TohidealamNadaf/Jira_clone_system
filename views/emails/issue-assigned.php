<?php
/**
 * Email Template: Issue Assigned
 * 
 * Variables available:
 * - $userName (string): Name of assigned user
 * - $issueKey (string): Issue key (e.g., "PROJ-123")
 * - $issueSummary (string): Issue title
 * - $issueDescription (string): Issue description
 * - $priority (string): Priority level
 * - $dueDate (string): Due date if set
 * - $assignedBy (string): Name of user who assigned
 * - $projectName (string): Project name
 * - $issueUrl (string): Link to issue
 */

$subject = "Issue $issueKey assigned to you";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($subject) ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #161B22;
            line-height: 1.6;
            background-color: #f6f8fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
            overflow: hidden;
        }
        .header {
            background-color: #8B1956;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        .issue-box {
            background-color: #f6f8fa;
            border-left: 4px solid #8B1956;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .issue-key {
            font-weight: 600;
            color: #8B1956;
            font-size: 16px;
        }
        .issue-summary {
            font-size: 18px;
            font-weight: 600;
            color: #161B22;
            margin: 10px 0;
        }
        .issue-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
            font-size: 14px;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            color: #626F86;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .meta-value {
            color: #161B22;
        }
        .priority-critical {
            background-color: #AE2A19;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }
        .priority-high {
            background-color: #CD5C5C;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }
        .priority-medium {
            background-color: #974F0C;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }
        .priority-low {
            background-color: #216E4E;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }
        .cta-button {
            display: inline-block;
            background-color: #8B1956;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: background-color 0.2s;
        }
        .cta-button:hover {
            background-color: #0043A5;
        }
        .footer {
            border-top: 1px solid #DFE1E6;
            padding: 20px 30px;
            background-color: #f6f8fa;
            font-size: 12px;
            color: #626F86;
        }
        .footer a {
            color: #8B1956;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>Issue Assigned</h1>
            </div>
            
            <div class="content">
                <p>Hi <?= htmlspecialchars($userName) ?>,</p>
                
                <p><?= htmlspecialchars($assignedBy) ?> assigned an issue to you in <?= htmlspecialchars($projectName) ?>:</p>
                
                <div class="issue-box">
                    <div class="issue-key"><?= htmlspecialchars($issueKey) ?></div>
                    <div class="issue-summary"><?= htmlspecialchars($issueSummary) ?></div>
                    
                    <div class="issue-meta">
                        <div class="meta-item">
                            <span class="meta-label">Priority</span>
                            <span class="meta-value">
                                <?php
                                $priorityClass = 'priority-' . strtolower($priority);
                                echo "<span class=\"$priorityClass\">" . htmlspecialchars(ucfirst($priority)) . "</span>";
                                ?>
                            </span>
                        </div>
                        <?php if (!empty($dueDate)): ?>
                        <div class="meta-item">
                            <span class="meta-label">Due Date</span>
                            <span class="meta-value"><?= htmlspecialchars($dueDate) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <p><?= htmlspecialchars($issueDescription) ?></p>
                
                <a href="<?= htmlspecialchars($issueUrl) ?>" class="cta-button">View Issue</a>
            </div>
            
            <div class="footer">
                <p>You received this email because you were assigned to an issue. 
                <a href="<?= htmlspecialchars($notificationPreferencesUrl ?? '#') ?>">Manage email preferences</a></p>
            </div>
        </div>
    </div>
</body>
</html>
