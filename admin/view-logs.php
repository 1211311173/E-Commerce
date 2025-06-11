<?php
// Simple log viewer for testing purposes
include_once('./includes/restriction.php');
if (!(isset($_SESSION['logged-in']))) {
  header("Location:login.php?unauthorizedAccess");
}

$log_file = __DIR__ . '/../logs/security_audit.log';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Security Audit Logs</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        .log-entry { margin: 5px 0; padding: 5px; background: #f5f5f5; }
        .success { border-left: 3px solid green; }
        .failure { border-left: 3px solid red; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Security Audit Logs</h1>
    <p><a href="post.php">← Back to Admin Panel</a></p>
    
    <?php if (file_exists($log_file)): ?>
        <div>
            <?php
            $logs = file($log_file, FILE_IGNORE_NEW_LINES);
            $logs = array_reverse($logs); // Show newest first
            
            foreach ($logs as $log) {
                $class = strpos($log, '[SUCCESS]') !== false ? 'success' : 'failure';
                echo "<div class='log-entry $class'>" . htmlspecialchars($log) . "</div>";
            }
            ?>
        </div>
    <?php else: ?>
        <p>No log file found yet. Perform some actions to generate logs.</p>
    <?php endif; ?>
</body>
</html>
