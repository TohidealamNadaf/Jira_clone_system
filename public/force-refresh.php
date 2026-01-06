<?php
// Force clear all caches
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");

// Redirect to dashboard
header("Location: " . (isset($_GET['goto']) ? urldecode($_GET['goto']) : '/jira_clone_system/public/dashboard'));
exit;
?>
