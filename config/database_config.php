<?php
/**
 * WOLFIE AGI UI - Database Configuration
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Database connection and configuration
 * WHERE: C:\START\WOLFIE_AGI_UI\config\
 * WHEN: 2025-01-27 17:50:00 CDT
 * WHY: To provide secure database connectivity
 * HOW: SQLite-based offline-first database
 * PURPOSE: Database configuration for WOLFIE AGI UI
 * ID: WOLFIE_AGI_UI_DB_CONFIG_001
 * KEY: WOLFIE_AGI_UI_DATABASE
 * SUPERPOSITIONALLY: [WOLFIE_AGI_UI_DB_CONFIG_001, WOLFIE_AGI_UI_DATABASE]
 */

// Database configuration constants
define('DB_PATH', __DIR__ . '/../database/agape.db');
define('DB_TIMEOUT', 30);
define('DB_CHARSET', 'utf8mb4');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,
    PDO::ATTR_TIMEOUT => DB_TIMEOUT
]);

/**
 * Get database connection
 * @return PDO Database connection instance
 * @throws Exception If database connection fails
 */
function getDatabaseConnection() {
    try {
        // Ensure database directory exists
        $dbDir = dirname(DB_PATH);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        // Create SQLite connection
        $dsn = 'sqlite:' . DB_PATH;
        $pdo = new PDO($dsn, null, null, DB_OPTIONS);
        
        // Enable foreign key constraints
        $pdo->exec('PRAGMA foreign_keys = ON');
        
        // Set journal mode to WAL for better performance
        $pdo->exec('PRAGMA journal_mode = WAL');
        
        // Set synchronous mode for better performance
        $pdo->exec('PRAGMA synchronous = NORMAL');
        
        // Set cache size
        $pdo->exec('PRAGMA cache_size = 10000');
        
        // Set temp store to memory
        $pdo->exec('PRAGMA temp_store = MEMORY');
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Initialize database tables
 * @param PDO $pdo Database connection
 * @return bool Success status
 */
function initializeDatabase($pdo) {
    try {
        // Create exploration_results table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS exploration_results (
                exploration_id VARCHAR(50) PRIMARY KEY,
                area VARCHAR(100) NOT NULL,
                results TEXT,
                agape_score FLOAT DEFAULT 0,
                security_score FLOAT DEFAULT 0,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                status VARCHAR(20) DEFAULT 'pending',
                INDEX idx_area (area),
                INDEX idx_timestamp (timestamp),
                INDEX idx_status (status)
            )
        ");
        
        // Create prototype_metadata table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS prototype_metadata (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                exploration_id VARCHAR(50),
                prototype_type VARCHAR(50),
                file_path TEXT,
                metadata TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (exploration_id) REFERENCES exploration_results(exploration_id),
                INDEX idx_exploration_id (exploration_id),
                INDEX idx_prototype_type (prototype_type)
            )
        ");
        
        // Create exploration_logs table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS exploration_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                exploration_id VARCHAR(50),
                action VARCHAR(100),
                details TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_exploration_id (exploration_id),
                INDEX idx_action (action),
                INDEX idx_timestamp (timestamp)
            )
        ");
        
        // Create memory_entries table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS memory_entries (
                id VARCHAR(50) PRIMARY KEY,
                content TEXT NOT NULL,
                type ENUM('short_term', 'long_term') NOT NULL,
                metadata TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                encrypted BOOLEAN DEFAULT FALSE,
                access_count INTEGER DEFAULT 0,
                last_accessed DATETIME,
                importance_score FLOAT DEFAULT 0,
                agape_score FLOAT DEFAULT 0,
                INDEX idx_type (type),
                INDEX idx_timestamp (timestamp),
                INDEX idx_importance (importance_score),
                INDEX idx_agape (agape_score)
            )
        ");
        
        // Create memory_logs table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS memory_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                memory_id VARCHAR(50),
                action VARCHAR(100),
                details TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_memory_id (memory_id),
                INDEX idx_action (action),
                INDEX idx_timestamp (timestamp)
            )
        ");
        
        return true;
    } catch (PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Test database connection
 * @return array Test results
 */
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        return [
            'status' => 'success',
            'message' => 'Database connection successful',
            'test_result' => $result['test']
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ];
    }
}

// Auto-initialize database if this file is included
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    echo "=== WOLFIE AGI UI Database Configuration Test ===\n\n";
    
    // Test database connection
    $testResult = testDatabaseConnection();
    echo "Database Connection: " . $testResult['status'] . "\n";
    echo "Message: " . $testResult['message'] . "\n";
    
    if ($testResult['status'] === 'success') {
        // Initialize database
        $pdo = getDatabaseConnection();
        $initResult = initializeDatabase($pdo);
        echo "Database Initialization: " . ($initResult ? 'SUCCESS' : 'FAILED') . "\n";
    }
    
    echo "\n=== Database Configuration Complete ===\n";
}
?>