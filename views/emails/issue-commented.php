<?php
/**
 * Email Template: Issue Commented
 * 
 * Variables available:
 * - $issueKey (string): Issue key (e.g., "PROJ-123")
 * - $issueSummary (string): Issue title
 * - $commentAuthor (string): Name of person who commented
 * - $commentText (string): Comment content
 * - $issueUrl (string): Link to issue
 * - $projectName (string): Project name
 */
$subject = "New comment on $issueKey - $issueSummary";
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
        .comment-box {
            background-color: #fafbfc;
            border: 1px solid #DFE1E6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .comment-author {
            font-weight: 600;
            color: #8B1956;
            margin-bottom: 10px;
        }
        .comment-text {
            color: #161B22;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
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
                <h1>New Comment</h1>
            </div>
            
            <div class="content">
                <p>Hi,</p>
                
                <p><?= htmlspecialchars($commentAuthor) ?> commented on an issue you're following in <?= htmlspecialchars($projectName) ?>:</p>
                
                <div class="issue-box">
                    <div class="issue-key"><?= htmlspecialchars($issueKey) ?></div>
                    <div class="issue-summary"><?= htmlspecialchars($issueSummary) ?></div>
                </div>
                
                <h3 style="margin-top: 25px; margin-bottom: 10px; font-size: 16px;">Comment:</h3>
                <div class="comment-box">
                    <div class="comment-author"><?= htmlspecialchars($commentAuthor) ?></div>
                    <div class="comment-text"><?= htmlspecialchars($commentText) ?></div>
                </div>
                
                <a href="<?= htmlspecialchars($issueUrl) ?>" class="cta-button">View Discussion</a>
            </div>
            
            <div class="footer">
                <p>You received this email because you're watching this issue. 
                <a href="<?= htmlspecialchars($notificationPreferencesUrl ?? '#') ?>">Manage email preferences</a></p>
            </div>
        </div>
    </div>
</body>
</html>
