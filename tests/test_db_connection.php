<?php
/**
 * Test Database Connection
 */

echo "Testing database connection...\n";

try {
    $dbPath = __DIR__ . '/../data/channels.db';
    $dbDir = dirname($dbPath);
    echo "DB Path: " . $dbPath . "\n";
    echo "DB Dir: " . $dbDir . "\n";
    echo "DB Dir exists: " . (is_dir($dbDir) ? 'YES' : 'NO') . "\n";
    
    if (!is_dir($dbDir)) {
        echo "Creating directory...\n";
        mkdir($dbDir, 0777, true);
        echo "Directory created: " . (is_dir($dbDir) ? 'YES' : 'NO') . "\n";
    }
    
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n";
    
    // Test creating a table
    $db->exec("CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, name TEXT)");
    echo "Table creation successful!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
