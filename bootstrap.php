<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_DIR', __DIR__);

echo "Starting Comfort Travel API setup...\n";

function run_sql_file($pdo, $file) {
    if (!file_exists($file)) {
        die("SQL file not found: $file\n");
    }
    
    $sql = file_get_contents($file);
    
    try {
        $pdo->exec($sql);
        echo "Database schema imported successfully\n";
    } catch (PDOException $e) {
        die("Error importing database schema: " . $e->getMessage() . "\n");
    }
}

$config = [
    'host' => '127.0.0.1:3306',
    'dbname' => 'comfort_otdyh',
    'username' => 'root',
    'password' => '88888888'
];

try {
    $dsn = "mysql:host={$config['host']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL server\n";
    
    // Check if database exists
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$config['dbname']}'");
    $dbExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$dbExists) {
        echo "Creating database '{$config['dbname']}'...\n";
        $pdo->exec("CREATE DATABASE `{$config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$config['dbname']}`");
        echo "Database created successfully\n";
        
        // Import schema
        run_sql_file($pdo, ROOT_DIR . '/database.sql');
    } else {
        echo "Using existing database '{$config['dbname']}'\n";
        $pdo->exec("USE `{$config['dbname']}`");
        
        // Check if tables exist and are empty
        $tables = ['countries', 'clients', 'tours', 'bookings'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                // Table doesn't exist, import schema
                run_sql_file($pdo, ROOT_DIR . '/database.sql');
                break;
            }
        }
    }
    
    echo "Starting PHP development server...\n";
    echo "API will be available at: http://localhost:8000\n";
    echo "Partner site will be available at: http://localhost:3000\n";
    
    $api_cmd = 'start "API Server" /MIN cmd /c php -S localhost:8000 -t ' . ROOT_DIR . '/comfort-travel-api';
    $partner_cmd = 'start "Partner Site" /MIN cmd /c php -S localhost:3000 -t ' . ROOT_DIR . '/partner-site';
    
    pclose(popen($api_cmd, 'r'));
    pclose(popen($partner_cmd, 'r'));
    
    echo "Servers started. Press Ctrl+C to stop.\n";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}