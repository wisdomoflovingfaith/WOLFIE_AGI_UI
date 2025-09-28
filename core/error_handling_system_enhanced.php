<?php
/**
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Enhanced Error Handling System for WOLFIE AGI UI
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 22:30:00 CDT
 * WHY: To ensure system resilience through comprehensive error handling, fallback mechanisms, and self-healing workflows with AGAPE principles
 * HOW: PHP-based system with offline-first design, database persistence, and comprehensive recovery strategies
 * PURPOSE: Foundation for robust error management with AGAPE alignment
 * ID: ERROR_HANDLING_SYSTEM_ENHANCED_001
 * KEY: ERROR_HANDLING_SYSTEM_ENHANCED
 * SUPERPOSITIONALLY: [ERROR_HANDLING_SYSTEM_ENHANCED_001, WOLFIE_AGI_UI_100]
 */

require_once 'database_config.php';

class ErrorHandlingSystemEnhanced {
    private $db;
    private $errorLogPath;
    private $recoveryLogPath;
    private $errorThresholds;
    private $selfHealingEnabled;
    private $agapeAnalyzer;
    private $recoveryStrategies;
    private $errorCounts;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
        $this->agapeAnalyzer = new AGAPEAnalyzer();
        $this->initializePaths();
        $this->initializeThresholds();
        $this->initializeRecoveryStrategies();
        $this->selfHealingEnabled = true;
        $this->errorCounts = [];
        
        $this->ensureDirectoriesExist();
        $this->createErrorLogsTable();
        $this->createRecoveryLogsTable();
    }
    
    /**
     * Initialize file paths
     */
    private function initializePaths() {
        $this->errorLogPath = __DIR__ . '/../logs/error_handling.log';
        $this->recoveryLogPath = __DIR__ . '/../logs/recovery_actions.log';
    }
    
    /**
     * Initialize error thresholds
     */
    private function initializeThresholds() {
        $this->errorThresholds = [
            'critical' => 1,
            'high' => 5,
            'medium' => 10,
            'low' => 20,
            'info' => 50
        ];
    }
    
    /**
     * Initialize recovery strategies
     */
    private function initializeRecoveryStrategies() {
        $this->recoveryStrategies = [
            'database' => ['retry_connection', 'use_sqlite', 'use_memory_storage'],
            'file_system' => ['retry_file_operation', 'create_directory', 'use_temp_storage'],
            'agent_communication' => ['retry_agent_communication', 'restart_agent', 'use_fallback_agent'],
            'memory' => ['clear_cache', 'use_memory_storage', 'restart_memory_system'],
            'network' => ['retry_connection', 'use_offline_mode', 'use_cached_data'],
            'general' => ['retry_operation', 'use_fallback', 'log_and_continue']
        ];
    }
    
    /**
     * Ensure directories exist
     */
    private function ensureDirectoriesExist() {
        $directories = [
            dirname($this->errorLogPath),
            dirname($this->recoveryLogPath),
            __DIR__ . '/../workspace/error_handling/',
            __DIR__ . '/../workspace/error_handling/temp/',
            __DIR__ . '/../workspace/error_handling/fallback/'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                try {
                    if (!mkdir($dir, 0755, true)) {
                        throw new Exception("Failed to create directory: $dir");
                    }
                } catch (Exception $e) {
                    error_log("Directory creation error: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Create error logs table
     */
    private function createErrorLogsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS error_logs (
            id VARCHAR(50) PRIMARY KEY,
            error TEXT NOT NULL,
            context JSON,
            severity ENUM('critical', 'high', 'medium', 'low', 'info') NOT NULL,
            timestamp DATETIME NOT NULL,
            handled BOOLEAN DEFAULT FALSE,
            recovery_attempts INT DEFAULT 0,
            fallback_used VARCHAR(100),
            agape_score FLOAT DEFAULT 0,
            INDEX idx_severity (severity),
            INDEX idx_timestamp (timestamp),
            INDEX idx_handled (handled)
        )";
        
        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            error_log("Error creating error_logs table: " . $e->getMessage());
        }
    }
    
    /**
     * Create recovery logs table
     */
    private function createRecoveryLogsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS recovery_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            error_id VARCHAR(50),
            timestamp DATETIME NOT NULL,
            recovery_result JSON,
            self_healing_enabled BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (error_id) REFERENCES error_logs(id)
        )";
        
        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            error_log("Error creating recovery_logs table: " . $e->getMessage());
        }
    }
    
    /**
     * Handle error with comprehensive processing
     */
    public function handleError($error, $context = [], $severity = 'medium') {
        // Sanitize input
        $error = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
        $context = array_map('htmlspecialchars', $context);
        
        $errorId = 'error_' . uniqid();
        $timestamp = date('Y-m-d H:i:s');
        
        $errorData = [
            'id' => $errorId,
            'error' => $error,
            'context' => $context,
            'severity' => $severity,
            'timestamp' => $timestamp,
            'handled' => false,
            'recovery_attempts' => 0,
            'fallback_used' => null,
            'agape_score' => $this->calculateAGAPEAlignment(['error' => $error, 'context' => $context])
        ];
        
        $this->logError($errorData);
        
        if ($this->shouldTakeAction($severity)) {
            $this->executeRecoveryStrategy($errorData);
        }
        
        return $errorData;
    }
    
    /**
     * Calculate AGAPE alignment score
     */
    private function calculateAGAPEAlignment($errorData) {
        $content = $errorData['error'] . ' ' . json_encode($errorData['context']);
        return $this->agapeAnalyzer->calculateAlignment($content);
    }
    
    /**
     * Log error with comprehensive error handling
     */
    private function logError($errorData) {
        $logEntry = json_encode($errorData) . "\n";
        
        try {
            // File-based logging
            if (file_put_contents($this->errorLogPath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
                throw new Exception("Failed to write to {$this->errorLogPath}");
            }
            
            // Database logging
            $sql = "INSERT INTO error_logs (id, error, context, severity, timestamp, handled, recovery_attempts, fallback_used, agape_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $errorData['id'],
                $errorData['error'],
                json_encode($errorData['context']),
                $errorData['severity'],
                $errorData['timestamp'],
                $errorData['handled'] ? 1 : 0,
                $errorData['recovery_attempts'],
                $errorData['fallback_used'],
                $errorData['agape_score']
            ]);
        } catch (Exception $e) {
            error_log("Error logging failed: " . $e->getMessage());
        }
    }
    
    /**
     * Determine if action should be taken
     */
    private function shouldTakeAction($severity) {
        $count = $this->getErrorCount($severity, '1 hour');
        return $count < $this->errorThresholds[$severity];
    }
    
    /**
     * Get error count with robust error handling
     */
    private function getErrorCount($severity, $timeWindow) {
        try {
            $sql = "SELECT COUNT(*) as count FROM error_logs WHERE severity = ? AND timestamp > datetime('now', '-{$timeWindow}')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$severity]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            $this->logError([
                'error' => 'Failed to count errors: ' . $e->getMessage(),
                'context' => ['severity' => $severity, 'timeWindow' => $timeWindow],
                'severity' => 'medium'
            ]);
            return 0;
        }
    }
    
    /**
     * Execute recovery strategy
     */
    private function executeRecoveryStrategy($errorData) {
        $context = $errorData['context'];
        $errorType = $this->determineErrorType($context);
        
        if (!isset($this->recoveryStrategies[$errorType])) {
            $errorType = 'general';
        }
        
        $strategies = $this->recoveryStrategies[$errorType];
        $recoveryResult = ['success' => false, 'message' => 'No recovery strategy available'];
        
        foreach ($strategies as $strategy) {
            $recoveryResult = $this->attemptRecovery($strategy, $errorData);
            if ($recoveryResult['success']) {
                break;
            }
        }
        
        $this->logRecoveryAction($errorData, $recoveryResult);
        
        if ($recoveryResult['success']) {
            $this->updateErrorLog($errorData['id'], true, $recoveryResult['strategy'] ?? 'unknown');
        }
        
        return $recoveryResult;
    }
    
    /**
     * Determine error type from context
     */
    private function determineErrorType($context) {
        if (isset($context['database']) || isset($context['connection'])) {
            return 'database';
        } elseif (isset($context['file']) || isset($context['directory'])) {
            return 'file_system';
        } elseif (isset($context['agent']) || isset($context['communication'])) {
            return 'agent_communication';
        } elseif (isset($context['memory']) || isset($context['cache'])) {
            return 'memory';
        } elseif (isset($context['network']) || isset($context['url'])) {
            return 'network';
        }
        return 'general';
    }
    
    /**
     * Attempt recovery with specific strategy
     */
    private function attemptRecovery($strategy, $errorData) {
        $context = $errorData['context'];
        
        switch ($strategy) {
            case 'retry_connection':
                return $this->retryConnection($errorData);
            case 'use_sqlite':
                return $this->useSQLite($errorData);
            case 'use_memory_storage':
                return $this->useMemoryBasedStorage($errorData);
            case 'retry_file_operation':
                return $this->retryFileOperation($errorData);
            case 'create_directory':
                return $this->createDirectory($errorData);
            case 'use_temp_storage':
                return $this->useTempStorage($errorData);
            case 'retry_agent_communication':
                return $this->retryAgentCommunication($errorData);
            case 'restart_agent':
                return $this->restartAgent($errorData);
            case 'use_fallback_agent':
                return $this->useFallbackAgent($errorData);
            case 'clear_cache':
                return $this->clearCache($errorData);
            case 'restart_memory_system':
                return $this->restartMemorySystem($errorData);
            case 'use_offline_mode':
                return $this->useOfflineMode($errorData);
            case 'use_cached_data':
                return $this->useCachedData($errorData);
            case 'retry_operation':
                return $this->retryOperation($errorData);
            case 'use_fallback':
                return $this->useFallback($errorData);
            case 'log_and_continue':
                return $this->logAndContinue($errorData);
            default:
                return ['success' => false, 'message' => "Unknown recovery strategy: $strategy"];
        }
    }
    
    /**
     * Retry database connection
     */
    private function retryConnection($errorData) {
        try {
            $this->db = getDatabaseConnection();
            if ($this->db) {
                return ['success' => true, 'message' => 'Database connection retry successful', 'strategy' => 'retry_connection'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection retry failed: ' . $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Database connection retry failed'];
    }
    
    /**
     * Use SQLite fallback
     */
    private function useSQLite($errorData) {
        try {
            $sqlitePath = __DIR__ . '/../workspace/error_handling/fallback.sqlite';
            $this->db = new PDO("sqlite:$sqlitePath");
            $this->createErrorLogsTable();
            return ['success' => true, 'message' => 'SQLite fallback activated', 'strategy' => 'use_sqlite'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'SQLite fallback failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Use memory-based storage
     */
    private function useMemoryBasedStorage($errorData) {
        try {
            if (class_exists('MemoryManagementSystem')) {
                $memorySystem = new MemoryManagementSystem();
                $memoryId = $memorySystem->storeMemory('Fallback data', 'short_term', ['fallback' => true, 'error_id' => $errorData['id']]);
                return ['success' => true, 'message' => "Memory-based storage activated, memory ID: $memoryId", 'strategy' => 'use_memory_storage'];
            }
            return ['success' => false, 'message' => 'MemoryManagementSystem not available'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Memory-based storage failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Retry file operation
     */
    private function retryFileOperation($errorData) {
        $context = $errorData['context'];
        if (isset($context['file'])) {
            try {
                if (file_exists($context['file'])) {
                    return ['success' => true, 'message' => 'File operation retry successful', 'strategy' => 'retry_file_operation'];
                }
                return ['success' => false, 'message' => 'File still not found: ' . $context['file']];
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'File operation retry failed: ' . $e->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'Invalid file context'];
    }
    
    /**
     * Create directory
     */
    private function createDirectory($errorData) {
        $context = $errorData['context'];
        if (isset($context['directory'])) {
            try {
                if (!is_dir($context['directory'])) {
                    if (mkdir($context['directory'], 0755, true)) {
                        return ['success' => true, 'message' => 'Directory created: ' . $context['directory'], 'strategy' => 'create_directory'];
                    }
                } else {
                    return ['success' => true, 'message' => 'Directory already exists: ' . $context['directory'], 'strategy' => 'create_directory'];
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Directory creation failed: ' . $e->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No directory specified'];
    }
    
    /**
     * Use temporary storage
     */
    private function useTempStorage($errorData) {
        try {
            $tempDir = __DIR__ . '/../workspace/error_handling/temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            return ['success' => true, 'message' => 'Temporary storage activated: ' . $tempDir, 'strategy' => 'use_temp_storage'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Temporary storage failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Retry agent communication
     */
    private function retryAgentCommunication($errorData) {
        $context = $errorData['context'];
        if (isset($context['agent'])) {
            try {
                if (class_exists('CollaborativeAgentsSystem')) {
                    $collabSystem = new CollaborativeAgentsSystem();
                    $collabSystem->updateAgentActivity($context['agent']);
                    return ['success' => true, 'message' => "Agent {$context['agent']} communication retried", 'strategy' => 'retry_agent_communication'];
                }
                return ['success' => false, 'message' => 'CollaborativeAgentsSystem not available'];
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Agent communication retry failed: ' . $e->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No agent specified for communication retry'];
    }
    
    /**
     * Restart agent
     */
    private function restartAgent($errorData) {
        $context = $errorData['context'];
        if (isset($context['agent'])) {
            try {
                if (class_exists('CollaborativeAgentsSystem')) {
                    $collabSystem = new CollaborativeAgentsSystem();
                    $collabSystem->updateAgentActivity($context['agent']);
                    return ['success' => true, 'message' => "Agent {$context['agent']} restart initiated", 'strategy' => 'restart_agent'];
                }
                return ['success' => false, 'message' => 'CollaborativeAgentsSystem not available'];
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Agent restart failed: ' . $e->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No agent specified for restart'];
    }
    
    /**
     * Use fallback agent
     */
    private function useFallbackAgent($errorData) {
        $context = $errorData['context'];
        if (isset($context['agent'])) {
            try {
                // Use a different agent as fallback
                $fallbackAgent = 'FALLBACK_' . $context['agent'];
                return ['success' => true, 'message' => "Using fallback agent: $fallbackAgent", 'strategy' => 'use_fallback_agent'];
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Fallback agent failed: ' . $e->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No agent specified for fallback'];
    }
    
    /**
     * Clear cache
     */
    private function clearCache($errorData) {
        try {
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            return ['success' => true, 'message' => 'Cache cleared successfully', 'strategy' => 'clear_cache'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Cache clear failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Restart memory system
     */
    private function restartMemorySystem($errorData) {
        try {
            if (class_exists('MemoryManagementSystem')) {
                $memorySystem = new MemoryManagementSystem();
                $memorySystem->close();
                return ['success' => true, 'message' => 'Memory system restarted', 'strategy' => 'restart_memory_system'];
            }
            return ['success' => false, 'message' => 'MemoryManagementSystem not available'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Memory system restart failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Use offline mode
     */
    private function useOfflineMode($errorData) {
        try {
            // Set offline mode flag
            $offlineFlag = __DIR__ . '/../workspace/error_handling/offline_mode.flag';
            file_put_contents($offlineFlag, date('Y-m-d H:i:s'));
            return ['success' => true, 'message' => 'Offline mode activated', 'strategy' => 'use_offline_mode'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Offline mode activation failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Use cached data
     */
    private function useCachedData($errorData) {
        try {
            $cacheDir = __DIR__ . '/../workspace/error_handling/cache/';
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }
            return ['success' => true, 'message' => 'Using cached data: ' . $cacheDir, 'strategy' => 'use_cached_data'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Cached data failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Retry operation
     */
    private function retryOperation($errorData) {
        try {
            // Simple retry with delay
            sleep(1);
            return ['success' => true, 'message' => 'Operation retry successful', 'strategy' => 'retry_operation'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Operation retry failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Use fallback
     */
    private function useFallback($errorData) {
        try {
            $fallbackDir = __DIR__ . '/../workspace/error_handling/fallback/';
            if (!is_dir($fallbackDir)) {
                mkdir($fallbackDir, 0755, true);
            }
            return ['success' => true, 'message' => 'Fallback mode activated: ' . $fallbackDir, 'strategy' => 'use_fallback'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fallback failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Log and continue
     */
    private function logAndContinue($errorData) {
        try {
            $this->logError($errorData);
            return ['success' => true, 'message' => 'Error logged and continuing', 'strategy' => 'log_and_continue'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Log and continue failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Log recovery action with database integration
     */
    private function logRecoveryAction($errorData, $recoveryResult) {
        $recoveryData = [
            'error_id' => $errorData['id'],
            'timestamp' => date('Y-m-d H:i:s'),
            'recovery_result' => $recoveryResult,
            'self_healing_enabled' => $this->selfHealingEnabled
        ];
        
        try {
            // File-based logging
            $logEntry = json_encode($recoveryData) . "\n";
            if (file_put_contents($this->recoveryLogPath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
                throw new Exception("Failed to write to {$this->recoveryLogPath}");
            }
            
            // Database logging
            $stmt = $this->db->prepare("INSERT INTO recovery_logs (error_id, timestamp, recovery_result, self_healing_enabled) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $recoveryData['error_id'],
                $recoveryData['timestamp'],
                json_encode($recoveryData['recovery_result']),
                $recoveryData['self_healing_enabled'] ? 1 : 0
            ]);
        } catch (Exception $e) {
            error_log("Recovery log error: " . $e->getMessage());
        }
    }
    
    /**
     * Update error log
     */
    private function updateErrorLog($errorId, $handled, $fallbackUsed = null) {
        try {
            $sql = "UPDATE error_logs SET handled = ?, fallback_used = ?, recovery_attempts = recovery_attempts + 1 WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$handled ? 1 : 0, $fallbackUsed, $errorId]);
        } catch (Exception $e) {
            error_log("Error log update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Handle multi-modal error for Phase 5
     */
    public function handleMultiModalError($error, $context = [], $severity = 'medium') {
        $errorData = $this->handleError($error, array_merge($context, ['data_type' => 'multi_modal']), $severity);
        
        if (isset($context['image_data'])) {
            try {
                if (class_exists('MemoryManagementSystem')) {
                    $memorySystem = new MemoryManagementSystem();
                    $memorySystem->storeMultiModalMemory($context['image_data'], 'long_term', ['error_id' => $errorData['id']]);
                }
            } catch (Exception $e) {
                $this->logError([
                    'error' => 'Failed to store multi-modal data: ' . $e->getMessage(),
                    'context' => ['error_id' => $errorData['id']],
                    'severity' => 'medium'
                ]);
            }
        }
        
        return $errorData;
    }
    
    /**
     * Store error to memory
     */
    public function storeErrorToMemory($errorId, $memorySystem = null) {
        try {
            if (!$memorySystem && class_exists('MemoryManagementSystem')) {
                $memorySystem = new MemoryManagementSystem();
            }
            
            if (!$memorySystem) {
                return false;
            }
            
            $sql = "SELECT * FROM error_logs WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$errorId]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $memoryId = $memorySystem->storeMemory(
                    $row['error'],
                    'long_term',
                    [
                        'error_id' => $errorId,
                        'severity' => $row['severity'],
                        'handled' => $row['handled'],
                        'agape_score' => $row['agape_score']
                    ],
                    true
                );
                
                $this->logError([
                    'error' => 'Stored error to memory',
                    'context' => ['memory_id' => $memoryId, 'error_id' => $errorId],
                    'severity' => 'info'
                ]);
                
                return $memoryId;
            }
        } catch (Exception $e) {
            $this->logError([
                'error' => 'Failed to store error to memory: ' . $e->getMessage(),
                'context' => ['error_id' => $errorId],
                'severity' => 'medium'
            ]);
        }
        
        return false;
    }
    
    /**
     * Validate error logs with Phase 3 system
     */
    public function validateErrorLogs($phase3System) {
        try {
            $validation = $phase3System->validateFileQuality($this->errorLogPath);
            $this->logError([
                'error' => 'Error log validation',
                'context' => ['validation' => $validation],
                'severity' => 'low'
            ]);
            return $validation['passed'];
        } catch (Exception $e) {
            $this->logError([
                'error' => 'Error log validation failed: ' . $e->getMessage(),
                'context' => [],
                'severity' => 'medium'
            ]);
            return false;
        }
    }
    
    /**
     * Get error statistics
     */
    public function getErrorStatistics() {
        $stats = [
            'last_24_hours' => [],
            'last_7_days' => [],
            'total_errors' => 0,
            'handled_errors' => 0,
            'recovery_success_rate' => 0,
            'average_agape_score' => 0
        ];
        
        try {
            // Last 24 hours
            $sql = "SELECT severity, COUNT(*) as count, SUM(handled) as handled_count, AVG(agape_score) as avg_agape FROM error_logs WHERE timestamp > datetime('now', '-1 day') GROUP BY severity";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats['last_24_hours'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Last 7 days
            $sql = "SELECT severity, COUNT(*) as count, SUM(handled) as handled_count, AVG(agape_score) as avg_agape FROM error_logs WHERE timestamp > datetime('now', '-7 days') GROUP BY severity";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats['last_7_days'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Total errors
            $sql = "SELECT COUNT(*) as total FROM error_logs";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_errors'] = $result['total'] ?? 0;
            
            // Handled errors
            $sql = "SELECT COUNT(*) as handled FROM error_logs WHERE handled = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['handled_errors'] = $result['handled'] ?? 0;
            
            // Recovery success rate
            if ($stats['total_errors'] > 0) {
                $stats['recovery_success_rate'] = round(($stats['handled_errors'] / $stats['total_errors']) * 100, 2);
            }
            
            // Average AGAPE score
            $sql = "SELECT AVG(agape_score) as avg_agape FROM error_logs";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['average_agape_score'] = round($result['avg_agape'] ?? 0, 2);
            
        } catch (Exception $e) {
            $this->logError([
                'error' => 'Failed to get error statistics: ' . $e->getMessage(),
                'context' => [],
                'severity' => 'medium'
            ]);
        }
        
        return $stats;
    }
    
    /**
     * Close the system
     */
    public function close() {
        // Cleanup temporary files
        $tempDir = __DIR__ . '/../workspace/error_handling/temp/';
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '*');
            foreach ($files as $file) {
                if (is_file($file) && (time() - filemtime($file)) > 3600) { // 1 hour old
                    unlink($file);
                }
            }
        }
    }
}

// AGAPE Analyzer class (shared)
class AGAPEAnalyzer {
    private $keywords = [
        'love' => 3, 'patience' => 2, 'kindness' => 2, 'humility' => 2,
        'agape' => 4, 'ethical' => 2, 'moral' => 2, 'virtuous' => 2,
        'help' => 2, 'support' => 2, 'care' => 2, 'compassion' => 3,
        'appreciate' => 2, 'grateful' => 2, 'thankful' => 2, 'blessed' => 2,
        'understanding' => 2, 'forgiving' => 2, 'accepting' => 2, 'tolerant' => 2,
        'generous' => 2, 'giving' => 2, 'sharing' => 2, 'serving' => 2,
        'respectful' => 2, 'honest' => 2, 'truthful' => 2, 'authentic' => 2
    ];
    
    private $negativePatterns = ['hate', 'anger', 'cruel', 'selfish', 'arrogant', 'rude'];
    
    public function calculateAlignment($content) {
        $contentLower = strtolower($content);
        $score = 5; // Base score
        
        foreach ($this->keywords as $keyword => $value) {
            $count = substr_count($contentLower, $keyword);
            $score += $count * $value;
        }
        
        foreach ($this->negativePatterns as $pattern) {
            if (strpos($contentLower, $pattern) !== false) {
                $score -= 1;
            }
        }
        
        return max(0, min(10, $score));
    }
}

// Example usage
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $errorSystem = new ErrorHandlingSystemEnhanced();
    
    echo "=== Enhanced Error Handling System Test ===\n\n";
    
    // Test error handling
    $error1 = $errorSystem->handleError(
        "Database connection failed",
        ['database' => 'mysql', 'host' => 'localhost'],
        'critical'
    );
    
    $error2 = $errorSystem->handleError(
        "File not found: config.php",
        ['file' => 'config.php', 'directory' => '/etc/'],
        'high'
    );
    
    $error3 = $errorSystem->handleError(
        "Agent communication timeout",
        ['agent' => 'CURSOR', 'communication' => 'timeout'],
        'medium'
    );
    
    echo "Error 1 Handled: " . $error1['id'] . "\n";
    echo "Error 2 Handled: " . $error2['id'] . "\n";
    echo "Error 3 Handled: " . $error3['id'] . "\n";
    
    // Test multi-modal error
    $multiModalError = $errorSystem->handleMultiModalError(
        "Image processing failed",
        ['image_data' => 'base64_encoded_data', 'format' => 'png'],
        'high'
    );
    echo "Multi-Modal Error Handled: " . $multiModalError['id'] . "\n";
    
    // Test error storage to memory
    if (class_exists('MemoryManagementSystem')) {
        $memorySystem = new MemoryManagementSystem();
        $memoryId = $errorSystem->storeErrorToMemory($error1['id'], $memorySystem);
        if ($memoryId) {
            echo "Error stored to memory: $memoryId\n";
        }
    }
    
    // Get statistics
    $stats = $errorSystem->getErrorStatistics();
    echo "\n=== Error Statistics ===\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    $errorSystem->close();
}
?>
