<?php
/**
 * Email Template: Issue Status Changed
 * 
 * Variables available:
 * - $issueKey (string): Issue key (e.g., "PROJ-123")
 * - $issueSummary (string): Issue title
 * - $oldStatus (string): Previous status
 * - $newStatus (string): New status
 * - $changedBy (string): Name of user who changed status
 * - $issueUrl (string): Link to issue
 * - $projectName (string): Project name
 */
$subject = "Issue $issueKey status changed to $newStatus";
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
        }
        .issue-summary {
            font-size: 16px;
            font-weight: 600;
            color: #161B22;
            margin: 5px 0 0 0;
        }
        .status-change {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 25px 0;
            padding: 15px;
            background-color: #f6f8fa;
            border-radius: 6px;
        }
        .status-badge {
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            color: #ffffff;
            text-align: center;
            min-width: 100px;
        }
        .status-badge.old {
            background-color: #CD5C5C;
        }
        .status-badge.new {
            background-color: #216E4E;
        }
        .arrow {
            font-size: 24px;
            color: #626F86;
        }
        .changed-by {
            font-size: 14px;
            color: #626F86;
            margin-top: 15px;
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
                <h1>Status Update</h1>
            </div>
            
            <div class="content">
                <p>Hi,</p>
                
                <p>An issue status has been updated in <?= htmlspecialchars($projectName) ?>:</p>
                
                <div class="issue-box">
                    <div class="issue-key"><?= htmlspecialchars($issueKey) ?></div>
                    <div class="issue-summary"><?= htmlspecialchars($issueSummary) ?></div>
                </div>
                
                <div class="status-change">
                    <div class="status-badge old"><?= htmlspecialchars($oldStatus) ?></div>
                    <div class="arrow">→</div>
                    <div class="status-badge new"><?= htmlspecialchars($newStatus) ?></div>
                </div>
                
                <div class="changed-by">
                    Changed by <strong><?= htmlspecialchars($changedBy) ?></strong>
                </div>
                
                <a href="<?= htmlspecialchars($issueUrl) ?>" class="cta-button">View Issue</a>
            </div>
            
            <div class="footer">
                <p>You received this email because you're watching this issue. 
                <a href="<?= htmlspecialchars($notificationPreferencesUrl ?? '#') ?>">Manage email preferences</a></p>
            </div>
        </div>
    </div>
</body>
</html>
