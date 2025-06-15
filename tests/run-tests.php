<?php
/**
 * Test Runner Script
 * Provides easy commands to run different test suites
 */

if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line');
}

// Change to tests directory
$testsDir = __DIR__;
chdir($testsDir);

// Available commands
$commands = [
    'all' => 'Run all tests',
    'unit' => 'Run unit tests only',
    'integration' => 'Run integration tests only', 
    'functional' => 'Run functional tests only',
    'maintenance' => 'Run maintenance tests only',
    'evolution' => 'Run evolution tests only',
    'security' => 'Run security maintenance tests only',
    'performance' => 'Run performance regression tests only',
    'coverage' => 'Run tests with coverage report',
    'help' => 'Show this help message'
];

// Get command from arguments
$command = $argv[1] ?? 'help';

// Display help if requested or invalid command
if ($command === 'help' || !array_key_exists($command, $commands)) {
    echo "E-Commerce Test Runner\n";
    echo "======================\n\n";
    echo "Usage: php run-tests.php [command]\n\n";
    echo "Available commands:\n";
    foreach ($commands as $cmd => $description) {
        echo sprintf("  %-12s %s\n", $cmd, $description);
    }
    echo "\nExamples:\n";
    echo "  php run-tests.php unit\n";
    echo "  php run-tests.php maintenance\n";
    echo "  php run-tests.php evolution\n";
    echo "  php run-tests.php coverage\n";
    exit(0);
}

// Check if PHPUnit is available
$phpunitPath = '..\vendor\bin\phpunit.bat';
if (!file_exists($phpunitPath)) {
    echo "Error: PHPUnit not found. Please run 'composer install' first.\n";
    exit(1);
}

// Build PHPUnit command based on selected test suite
switch ($command) {
    case 'all':
        $phpunitCommand = "$phpunitPath --testsuite all";
        break;
    
    case 'unit':
        $phpunitCommand = "$phpunitPath --testsuite unit";
        break;
    
    case 'integration':
        $phpunitCommand = "$phpunitPath --testsuite integration";
        break;
    
    case 'functional':
        $phpunitCommand = "$phpunitPath --testsuite functional";
        break;
    
    case 'maintenance':
        $phpunitCommand = "$phpunitPath --testsuite maintenance";
        break;
    
    case 'evolution':
        $phpunitCommand = "$phpunitPath --testsuite evolution";
        break;
    
    case 'security':
        $phpunitCommand = "$phpunitPath Maintenance/SecurityMaintenanceTest.php";
        break;
    
    case 'performance':
        $phpunitCommand = "$phpunitPath Maintenance/PerformanceRegressionTest.php";
        break;
    
    case 'coverage':
        $phpunitCommand = "$phpunitPath --coverage-html coverage-html --testsuite all";
        break;
    
    default:
        echo "Unknown command: $command\n";
        exit(1);
}

// Display what we're running
echo "Running: $commands[$command]\n";
echo "Command: $phpunitCommand\n";
echo str_repeat('-', 50) . "\n";

// Execute the command
$returnCode = 0;
passthru($phpunitCommand, $returnCode);

// Display results summary
echo "\n" . str_repeat('-', 50) . "\n";
if ($returnCode === 0) {
    echo "✅ Tests completed successfully!\n";
    
    if ($command === 'coverage') {
        echo "\n📊 Coverage report generated in: coverage-html/index.html\n";
    }
} else {
    echo "❌ Tests failed with return code: $returnCode\n";
}

// Show additional information for maintenance and evolution tests
if (in_array($command, ['maintenance', 'evolution', 'all'])) {
    echo "\n📝 Test Categories Run:\n";
    
    if (in_array($command, ['maintenance', 'all'])) {
        echo "  🔧 Maintenance Tests:\n";
        echo "     - Backward Compatibility\n";
        echo "     - Performance Regression\n";
        echo "     - Security Maintenance\n";
    }
    
    if (in_array($command, ['evolution', 'all'])) {
        echo "  🚀 Evolution Tests:\n";
        echo "     - Feature Evolution\n";
        echo "     - API Compatibility\n";
        echo "     - Data Migration\n";
    }
}

exit($returnCode);
