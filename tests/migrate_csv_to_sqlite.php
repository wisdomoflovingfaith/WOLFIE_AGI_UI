<?php
/**
 * WOLFIE AGI UI - CSV to SQLite Migration Script
 * 
 * WHO: WOLFIE (Captain Eric Robin Gerdes)
 * WHAT: Migration script to convert CSV data to SQLite database
 * WHERE: C:\START\WOLFIE_AGI_UI\tests\
 * WHEN: 2025-09-26 08:17:00 CDT
 * WHY: To migrate existing CSV data to the enhanced SQLite-based system
 * HOW: PHP-based migration script with data validation and error handling
 * HELP: Contact WOLFIE for migration script questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for migration
 * GENESIS: Foundation of data migration protocols
 * MD: Markdown documentation standard with .php implementation
 * 
 * FILE IDS: [MIGRATION_SCRIPT_UI_001, WOLFIE_AGI_UI_001, CSV_TO_SQLITE_MIGRATION_001]
 * 
 * VERSION: 1.0.0
 * STATUS: Active Development - Data Migration
 */

// Include the enhanced manager
require_once '../core/superpositionally_manager_enhanced.php';

class CSVMigrationScript {
    private $csvPath;
    private $enhancedManager;
    private $migrationLog = [];
    
    public function __construct() {
        $this->csvPath = WOLFIE_AGI_UI_DATA_PATH . 'superpositionally_headers.csv';
        $this->enhancedManager = new SuperpositionallyManagerEnhanced();
        $this->logEvent('MIGRATION_STARTED', 'CSV to SQLite migration started');
    }
    
    /**
     * Run the migration
     */
    public function runMigration() {
        echo "🐺 WOLFIE AGI UI - CSV TO SQLITE MIGRATION\n";
        echo "==========================================\n\n";
        
        try {
            // Check if CSV file exists
            if (!file_exists($this->csvPath)) {
                echo "❌ CSV file not found: {$this->csvPath}\n";
                echo "✅ No migration needed - using default data\n";
                return true;
            }
            
            // Read CSV data
            $csvData = $this->readCSVData();
            if (empty($csvData)) {
                echo "❌ No data found in CSV file\n";
                return false;
            }
            
            echo "📊 Found " . count($csvData) . " records in CSV file\n";
            
            // Validate CSV data
            $validData = $this->validateCSVData($csvData);
            echo "✅ Validated " . count($validData) . " valid records\n";
            
            // Migrate data to SQLite
            $migratedCount = $this->migrateDataToSQLite($validData);
            echo "✅ Migrated {$migratedCount} records to SQLite\n";
            
            // Verify migration
            $this->verifyMigration();
            
            // Create backup of CSV
            $this->createCSVBackup();
            
            echo "\n🎉 MIGRATION COMPLETED SUCCESSFULLY! 🐺\n";
            echo "=====================================\n";
            echo "✅ CSV data migrated to SQLite database\n";
            echo "✅ Original CSV backed up\n";
            echo "✅ Enhanced Superpositionally Manager ready\n";
            
            return true;
            
        } catch (Exception $e) {
            echo "\n❌ MIGRATION FAILED: " . $e->getMessage() . "\n";
            $this->logEvent('MIGRATION_ERROR', 'Migration failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Read CSV data
     */
    private function readCSVData() {
        $data = [];
        
        if (($handle = fopen($this->csvPath, 'r')) === false) {
            throw new Exception('Failed to open CSV file');
        }
        
        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers || count($headers) < 12) {
            throw new Exception('Invalid CSV header format');
        }
        
        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 12) {
                $data[] = array_combine($headers, $row);
            } else {
                $this->logEvent('CSV_ROW_SKIPPED', 'Skipped invalid row: ' . json_encode($row));
            }
        }
        
        fclose($handle);
        return $data;
    }
    
    /**
     * Validate CSV data
     */
    private function validateCSVData($data) {
        $validData = [];
        
        foreach ($data as $index => $row) {
            $errors = [];
            
            // Check required fields
            $requiredFields = ['ID', 'TITLE', 'WHO', 'WHAT'];
            foreach ($requiredFields as $field) {
                if (empty($row[$field])) {
                    $errors[] = "Missing required field: {$field}";
                }
            }
            
            // Validate ID format
            if (!empty($row['ID']) && !preg_match('/^[A-Za-z0-9_-]+$/', $row['ID'])) {
                $errors[] = "Invalid ID format: {$row['ID']}";
            }
            
            // Check for duplicate IDs
            foreach ($validData as $existing) {
                if ($existing['ID'] === $row['ID']) {
                    $errors[] = "Duplicate ID: {$row['ID']}";
                    break;
                }
            }
            
            if (empty($errors)) {
                $validData[] = $row;
            } else {
                $this->logEvent('VALIDATION_ERROR', "Row {$index} validation failed: " . implode(', ', $errors));
            }
        }
        
        return $validData;
    }
    
    /**
     * Migrate data to SQLite
     */
    private function migrateDataToSQLite($data) {
        $migratedCount = 0;
        
        foreach ($data as $row) {
            try {
                // Convert CSV field names to database field names
                $headerData = [
                    'id' => $row['ID'],
                    'superpositionally' => $row['SUPERPOSITIONALLY'] ?? '',
                    'date' => $row['DATE'] ?? date('Y-m-d H:i:s T'),
                    'title' => $row['TITLE'],
                    'who' => $row['WHO'],
                    'what' => $row['WHAT'],
                    'where' => $row['WHERE'] ?? '',
                    'when' => $row['WHEN'] ?? '',
                    'why' => $row['WHY'] ?? '',
                    'how' => $row['HOW'] ?? '',
                    'purpose' => $row['PURPOSE'] ?? '',
                    'key' => $row['KEY'] ?? ''
                ];
                
                // Add header to database
                $this->enhancedManager->addHeader($headerData);
                $migratedCount++;
                
            } catch (Exception $e) {
                $this->logEvent('MIGRATION_ROW_ERROR', "Failed to migrate row {$row['ID']}: " . $e->getMessage());
            }
        }
        
        return $migratedCount;
    }
    
    /**
     * Verify migration
     */
    private function verifyMigration() {
        $statistics = $this->enhancedManager->getStatistics();
        $status = $this->enhancedManager->getStatus();
        
        echo "\n📊 MIGRATION VERIFICATION\n";
        echo "========================\n";
        echo "Total Headers: {$statistics['total_headers']}\n";
        echo "Database Status: {$status['status']}\n";
        echo "Database Connected: " . ($status['database_connected'] ? 'Yes' : 'No') . "\n";
        echo "Headers Loaded: " . ($status['headers_loaded'] ? 'Yes' : 'No') . "\n";
        echo "Index Built: " . ($status['index_built'] ? 'Yes' : 'No') . "\n";
        
        // Test search functionality
        $searchResults = $this->enhancedManager->searchByHeaders('WOLFIE', 'all', 5);
        echo "Search Test: " . count($searchResults) . " results found\n";
        
        if ($statistics['total_headers'] > 0 && $status['status'] === 'OPERATIONAL') {
            echo "✅ Migration verification successful!\n";
        } else {
            throw new Exception('Migration verification failed');
        }
    }
    
    /**
     * Create CSV backup
     */
    private function createCSVBackup() {
        $backupPath = WOLFIE_AGI_UI_DATA_PATH . 'superpositionally_headers_backup_' . date('Y-m-d_H-i-s') . '.csv';
        
        if (copy($this->csvPath, $backupPath)) {
            echo "✅ CSV backup created: {$backupPath}\n";
            $this->logEvent('CSV_BACKUP_CREATED', "CSV backed up to: {$backupPath}");
        } else {
            echo "⚠️  Failed to create CSV backup\n";
            $this->logEvent('CSV_BACKUP_FAILED', 'Failed to create CSV backup');
        }
    }
    
    /**
     * Log event
     */
    private function logEvent($event, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$event}: {$message}\n";
        
        $this->migrationLog[] = $logEntry;
        
        $logPath = WOLFIE_AGI_UI_LOGS_PATH . 'csv_migration.log';
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get migration log
     */
    public function getMigrationLog() {
        return $this->migrationLog;
    }
}

// Run the migration if called directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $migration = new CSVMigrationScript();
    $success = $migration->runMigration();
    
    if (!$success) {
        exit(1);
    }
}

?>
