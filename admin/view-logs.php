<?php
include_once('./includes/headerNav.php');
include_once('./includes/restriction.php');

// Check if user is logged in as admin
if (!(isset($_SESSION['logged-in']))) {
    header("Location:login.php?unauthorizedAccess");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Audit Logs</title>
    <style>
        .log-container {
            margin: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .log-entry {
            background-color: white;
            margin: 10px 0;
            padding: 15px;
            border-left: 4px solid #007bff;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }
        .log-entry.success {
            border-left-color: #28a745;
        }
        .log-entry.failure {
            border-left-color: #dc3545;
        }
        .log-header {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }
        .log-filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 4px;
        }
        .filter-group {
            display: inline-block;
            margin-right: 20px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .filter-group select, .filter-group input {
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .btn-filter {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-filter:hover {
            background-color: #0056b3;
        }
        .no-logs {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="log-container">
    <h1>Security Audit Logs</h1>
    
    <!-- Filters -->
    <div class="log-filters">
        <form method="GET" action="">
            <div class="filter-group">
                <label for="event_type">Event Type:</label>
                <select name="event_type" id="event_type">
                    <option value="">All Events</option>
                    <option value="LOGIN" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'LOGIN') ? 'selected' : ''; ?>>Login</option>
                    <option value="ADMIN_LOGIN" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'ADMIN_LOGIN') ? 'selected' : ''; ?>>Admin Login</option>
                    <option value="USER_REGISTRATION" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'USER_REGISTRATION') ? 'selected' : ''; ?>>User Registration</option>
                    <option value="PRODUCT_ADD" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'PRODUCT_ADD') ? 'selected' : ''; ?>>Product Add</option>
                    <option value="PRODUCT_UPDATE" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'PRODUCT_UPDATE') ? 'selected' : ''; ?>>Product Update</option>
                    <option value="PRODUCT_DELETE" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'PRODUCT_DELETE') ? 'selected' : ''; ?>>Product Delete</option>
                    <option value="UNAUTHORIZED_ACCESS" <?php echo (isset($_GET['event_type']) && $_GET['event_type'] == 'UNAUTHORIZED_ACCESS') ? 'selected' : ''; ?>>Unauthorized Access</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="event_status">Status:</label>
                <select name="event_status" id="event_status">
                    <option value="">All Status</option>
                    <option value="SUCCESS" <?php echo (isset($_GET['event_status']) && $_GET['event_status'] == 'SUCCESS') ? 'selected' : ''; ?>>Success</option>
                    <option value="FAILURE" <?php echo (isset($_GET['event_status']) && $_GET['event_status'] == 'FAILURE') ? 'selected' : ''; ?>>Failure</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_from">From Date:</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
            </div>
            
            <div class="filter-group">
                <label for="date_to">To Date:</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
            </div>
            
            <div class="filter-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn-filter">Filter Logs</button>
                <a href="view-logs.php" class="btn-filter" style="text-decoration: none; background-color: #6c757d;">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Log Entries -->
    <div class="log-entries">
        <?php
        $log_file = __DIR__ . '/../logs/security_audit.log';
        
        if (file_exists($log_file)) {
            $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logs = array_reverse($logs); // Show newest first
            
            $filtered_logs = [];
            
            // Apply filters
            foreach ($logs as $log) {
                $show_log = true;
                
                // Filter by event type
                if (isset($_GET['event_type']) && !empty($_GET['event_type'])) {
                    if (strpos($log, '[' . $_GET['event_type'] . ']') === false) {
                        $show_log = false;
                    }
                }
                
                // Filter by status
                if (isset($_GET['event_status']) && !empty($_GET['event_status'])) {
                    if (strpos($log, '[' . $_GET['event_status'] . ']') === false) {
                        $show_log = false;
                    }
                }
                
                // Filter by date range
                if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                    preg_match('/\[(.*?)\]/', $log, $matches);
                    if (isset($matches[1])) {
                        $log_date = date('Y-m-d', strtotime($matches[1]));
                        if ($log_date < $_GET['date_from']) {
                            $show_log = false;
                        }
                    }
                }
                
                if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                    preg_match('/\[(.*?)\]/', $log, $matches);
                    if (isset($matches[1])) {
                        $log_date = date('Y-m-d', strtotime($matches[1]));
                        if ($log_date > $_GET['date_to']) {
                            $show_log = false;
                        }
                    }
                }
                
                if ($show_log) {
                    $filtered_logs[] = $log;
                }
            }
            
            if (count($filtered_logs) > 0) {
                foreach ($filtered_logs as $log) {
                    $status_class = (strpos($log, '[SUCCESS]') !== false) ? 'success' : 'failure';
                    echo '<div class="log-entry ' . $status_class . '">' . htmlspecialchars($log) . '</div>';
                }
            } else {
                echo '<div class="no-logs">No logs found matching the selected criteria.</div>';
            }
        } else {
            echo '<div class="no-logs">No security audit logs found. The log file will be created when the first security event occurs.</div>';
        }
        ?>
    </div>
</div>

</body>
</html>
