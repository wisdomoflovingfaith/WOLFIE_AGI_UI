<?php
/**
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Enhanced Memory Management System for WOLFIE AGI UI
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 22:00:00 CDT
 * WHY: To manage short-term and long-term memory with comprehensive database integration, enhanced error handling, sophisticated scoring, and AGAPE alignment
 * HOW: PHP-based system with offline-first design, database persistence, and comprehensive memory operations
 * PURPOSE: Foundation for persistent memory management with AGAPE principles
 * ID: MEMORY_MANAGEMENT_SYSTEM_ENHANCED_001
 * KEY: MEMORY_MANAGEMENT_SYSTEM_ENHANCED
 * SUPERPOSITIONALLY: [MEMORY_MANAGEMENT_SYSTEM_ENHANCED_001, WOLFIE_AGI_UI_099]
 */

require_once 'database_config.php';

class MemoryManagementSystemEnhanced {
    private $db;
    private $workspacePath;
    private $shortTermMemory;
    private $longTermMemory;
    private $memoryThresholds;
    private $encryptionKey;
    private $agapeAnalyzer;
    private $memoryLogPath;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
        $this->workspacePath = __DIR__ . '/../workspace/memory/';
        $this->agapeAnalyzer = new AGAPEAnalyzer();
        
        $this->ensureDirectoriesExist();
        $this->initializeMemoryThresholds();
        $this->loadEncryptionKey();
        $this->loadPersistentMemory();
        
        $this->memoryLogPath = $this->workspacePath . 'logs/memory_operations.log';
    }
    
    /**
     * Ensure memory directories exist
     */
    private function ensureDirectoriesExist() {
        $directories = [
            $this->workspacePath,
            $this->workspacePath . 'short_term/',
            $this->workspacePath . 'long_term/',
            $this->workspacePath . 'logs/',
            $this->workspacePath . 'temp/',
            $this->workspacePath . 'encrypted/'
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
     * Initialize memory thresholds
     */
    private function initializeMemoryThresholds() {
        $this->memoryThresholds = [
            'short_term_max_size' => 100,
            'long_term_max_size' => 1000,
            'cleanup_threshold' => 0.8,
            'importance_threshold' => 5.0
        ];
    }
    
    /**
     * Load encryption key securely
     */
    private function loadEncryptionKey() {
        $keyFile = $this->workspacePath . '.encryption_key';
        
        try {
            if (file_exists($keyFile)) {
                $this->encryptionKey = file_get_contents($keyFile);
                if (strlen($this->encryptionKey) !== 64) {
                    throw new Exception("Invalid encryption key length");
                }
            } else {
                $this->encryptionKey = $this->generateEncryptionKey();
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('key_load_error', ['error' => $e->getMessage()]);
            $this->encryptionKey = $this->generateEncryptionKey();
        }
    }
    
    /**
     * Generate encryption key securely
     */
    private function generateEncryptionKey() {
        $keyFile = $this->workspacePath . '.encryption_key';
        $key = bin2hex(random_bytes(32));
        
        try {
            if (file_put_contents($keyFile, $key) === false) {
                throw new Exception("Failed to write encryption key to $keyFile");
            }
            chmod($keyFile, 0600); // Restrict permissions
            $this->logMemoryOperation('key_generated', ['key_file' => $keyFile]);
        } catch (Exception $e) {
            $this->logMemoryOperation('key_generate_error', ['error' => $e->getMessage()]);
        }
        
        return $key;
    }
    
    /**
     * Load persistent memory from database
     */
    private function loadPersistentMemory() {
        try {
            // Load short-term memory
            $stmt = $this->db->prepare("SELECT * FROM memory_entries WHERE type = 'short_term' ORDER BY last_accessed DESC LIMIT ?");
            $stmt->execute([$this->memoryThresholds['short_term_max_size']]);
            $shortTermMemories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($shortTermMemories as $memory) {
                $this->shortTermMemory[$memory['id']] = [
                    'id' => $memory['id'],
                    'content' => $memory['encrypted'] ? $this->decryptContent($memory['content']) : $memory['content'],
                    'type' => $memory['type'],
                    'metadata' => json_decode($memory['metadata'], true) ?: [],
                    'timestamp' => $memory['timestamp'],
                    'encrypted' => (bool)$memory['encrypted'],
                    'access_count' => (int)$memory['access_count'],
                    'last_accessed' => $memory['last_accessed'],
                    'importance_score' => (float)$memory['importance_score'],
                    'agape_score' => (float)$memory['agape_score']
                ];
            }
            
            // Load long-term memory
            $stmt = $this->db->prepare("SELECT * FROM memory_entries WHERE type = 'long_term' ORDER BY last_accessed DESC LIMIT ?");
            $stmt->execute([$this->memoryThresholds['long_term_max_size']]);
            $longTermMemories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($longTermMemories as $memory) {
                $this->longTermMemory[$memory['id']] = [
                    'id' => $memory['id'],
                    'content' => $memory['encrypted'] ? $this->decryptContent($memory['content']) : $memory['content'],
                    'type' => $memory['type'],
                    'metadata' => json_decode($memory['metadata'], true) ?: [],
                    'timestamp' => $memory['timestamp'],
                    'encrypted' => (bool)$memory['encrypted'],
                    'access_count' => (int)$memory['access_count'],
                    'last_accessed' => $memory['last_accessed'],
                    'importance_score' => (float)$memory['importance_score'],
                    'agape_score' => (float)$memory['agape_score']
                ];
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('db_load_error', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Store memory with comprehensive database integration
     */
    public function storeMemory($content, $type = 'short_term', $metadata = [], $encrypt = false) {
        // Sanitize input
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $metadata = array_map('htmlspecialchars', $metadata);
        
        $memoryId = 'memory_' . uniqid();
        $timestamp = date('Y-m-d H:i:s');
        
        $memoryEntry = [
            'id' => $memoryId,
            'content' => $encrypt ? $this->encryptContent($content) : $content,
            'type' => $type,
            'metadata' => $metadata,
            'timestamp' => $timestamp,
            'encrypted' => $encrypt,
            'access_count' => 0,
            'last_accessed' => $timestamp,
            'importance_score' => $this->calculateImportanceScore($content, $metadata),
            'agape_score' => $this->calculateAGAPEAlignment($content)
        ];
        
        try {
            $stmt = $this->db->prepare("INSERT INTO memory_entries (id, content, type, metadata, timestamp, encrypted, access_count, last_accessed, importance_score, agape_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $memoryId, $memoryEntry['content'], $type, json_encode($metadata), $timestamp,
                $encrypt ? 1 : 0, 0, $timestamp, $memoryEntry['importance_score'], $memoryEntry['agape_score']
            ]);
        } catch (Exception $e) {
            $this->logMemoryOperation('db_error', ['error' => $e->getMessage()]);
        }
        
        if ($type === 'short_term') {
            $this->storeShortTermMemory($memoryEntry);
        } else {
            $this->storeLongTermMemory($memoryEntry);
        }
        
        $this->logMemoryOperation('store', $memoryEntry);
        return $memoryId;
    }
    
    /**
     * Store multi-modal memory for Phase 5
     */
    public function storeMultiModalMemory($content, $type = 'long_term', $metadata = [], $encrypt = true) {
        // Sanitize input
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $metadata = array_map('htmlspecialchars', $metadata);
        
        $memoryId = 'memory_' . uniqid();
        $timestamp = date('Y-m-d H:i:s');
        
        $memoryEntry = [
            'id' => $memoryId,
            'content' => $encrypt ? $this->encryptContent(base64_encode($content)) : base64_encode($content),
            'type' => $type,
            'metadata' => array_merge($metadata, ['data_type' => 'multi_modal']),
            'timestamp' => $timestamp,
            'encrypted' => $encrypt,
            'access_count' => 0,
            'last_accessed' => $timestamp,
            'importance_score' => $this->calculateImportanceScore($content, $metadata),
            'agape_score' => $this->calculateAGAPEAlignment($content)
        ];
        
        try {
            $stmt = $this->db->prepare("INSERT INTO memory_entries (id, content, type, metadata, timestamp, encrypted, access_count, last_accessed, importance_score, agape_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $memoryId, $memoryEntry['content'], $type, json_encode($memoryEntry['metadata']), $timestamp,
                $encrypt ? 1 : 0, 0, $timestamp, $memoryEntry['importance_score'], $memoryEntry['agape_score']
            ]);
        } catch (Exception $e) {
            $this->logMemoryOperation('db_error', ['error' => $e->getMessage()]);
        }
        
        if ($type === 'short_term') {
            $this->storeShortTermMemory($memoryEntry);
        } else {
            $this->storeLongTermMemory($memoryEntry);
        }
        
        $this->logMemoryOperation('store_multi_modal', $memoryEntry);
        return $memoryId;
    }
    
    /**
     * Store agent coordination memory
     */
    public function storeAgentCoordination($agentId, $coordinationData, $type = 'long_term') {
        return $this->storeMemory(
            json_encode($coordinationData),
            $type,
            ['agent_id' => $agentId, 'category' => 'coordination', 'importance' => 9],
            true
        );
    }
    
    /**
     * Retrieve memory with database integration
     */
    public function retrieveMemory($memoryId, $type = null) {
        try {
            $query = "SELECT * FROM memory_entries WHERE id = ?";
            if ($type) {
                $query .= " AND type = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$memoryId, $type]);
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute([$memoryId]);
            }
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $memoryEntry = [
                    'id' => $row['id'],
                    'content' => $row['encrypted'] ? $this->decryptContent($row['content']) : $row['content'],
                    'type' => $row['type'],
                    'metadata' => json_decode($row['metadata'], true) ?: [],
                    'timestamp' => $row['timestamp'],
                    'encrypted' => (bool)$row['encrypted'],
                    'access_count' => (int)$row['access_count'] + 1,
                    'last_accessed' => date('Y-m-d H:i:s'),
                    'importance_score' => (float)$row['importance_score'],
                    'agape_score' => (float)$row['agape_score']
                ];
                
                // Update access count in database
                $stmt = $this->db->prepare("UPDATE memory_entries SET access_count = ?, last_accessed = ? WHERE id = ?");
                $stmt->execute([$memoryEntry['access_count'], $memoryEntry['last_accessed'], $memoryId]);
                
                // Update in-memory cache
                if ($memoryEntry['type'] === 'short_term') {
                    $this->shortTermMemory[$memoryId] = $memoryEntry;
                } else {
                    $this->longTermMemory[$memoryId] = $memoryEntry;
                }
                
                $this->logMemoryOperation('retrieve', $memoryEntry);
                return $memoryEntry;
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('db_error', ['error' => $e->getMessage()]);
        }
        
        // Fallback to file-based retrieval
        return $this->loadMemoryFromFile($memoryId, $type);
    }
    
    /**
     * Search memories with sophisticated scoring
     */
    public function searchMemories($searchTerms, $type = null, $limit = 10) {
        $searchTerms = array_map('strtolower', $searchTerms);
        $results = [];
        
        $memories = $type === 'short_term' ? $this->shortTermMemory : 
                   ($type === 'long_term' ? $this->longTermMemory : 
                   array_merge($this->shortTermMemory, $this->longTermMemory));
        
        foreach ($memories as $memory) {
            $searchScore = $this->calculateSearchScore($memory, $searchTerms);
            if ($searchScore > 0) {
                $results[] = [
                    'memory' => $memory,
                    'search_score' => $searchScore
                ];
            }
        }
        
        usort($results, function($a, $b) {
            return $b['search_score'] <=> $a['search_score'];
        });
        
        $this->logMemoryOperation('search', [
            'search_terms' => $searchTerms,
            'type' => $type,
            'results_count' => count($results)
        ]);
        
        return array_slice($results, 0, $limit);
    }
    
    /**
     * Calculate sophisticated search score
     */
    private function calculateSearchScore($memory, $searchTerms) {
        $score = 0;
        $content = strtolower($memory['content']);
        $metadata = json_encode($memory['metadata']);
        
        $termFrequencies = [];
        foreach ($searchTerms as $term) {
            $term = strtolower($term);
            $termFrequencies[$term] = substr_count($content, $term) + substr_count($metadata, $term);
            $score += $termFrequencies[$term] * 2;
        }
        
        // Access count bonus
        $score += $memory['access_count'] * 0.1;
        
        // Importance score bonus
        $score += $memory['importance_score'] * 0.01;
        
        // AGAPE score bonus
        $score += $memory['agape_score'] * 0.1;
        
        return $score;
    }
    
    /**
     * Calculate sophisticated importance score
     */
    private function calculateImportanceScore($content, $metadata) {
        $score = 5; // Base score
        
        // Length factor
        $length = strlen($content);
        if ($length > 1000) {
            $score += 2;
        } elseif ($length > 500) {
            $score += 1;
        }
        
        // Technical terms increase importance
        $technicalTerms = ['function', 'class', 'method', 'algorithm', 'pattern', 'architecture', 'design', 'implementation'];
        foreach ($technicalTerms as $term) {
            if (stripos($content, $term) !== false) {
                $score += 1;
            }
        }
        
        // AGAPE terms increase importance
        $agapeTerms = ['love', 'patience', 'kindness', 'humility', 'agape', 'ethical', 'moral', 'virtuous'];
        foreach ($agapeTerms as $term) {
            if (stripos($content, $term) !== false) {
                $score += 1;
            }
        }
        
        // Metadata importance
        if (isset($metadata['importance'])) {
            $score += (int)$metadata['importance'];
        }
        
        if (isset($metadata['priority']) && $metadata['priority'] === 'high') {
            $score += 2;
        }
        
        return min($score, 10);
    }
    
    /**
     * Calculate AGAPE alignment using shared analyzer
     */
    private function calculateAGAPEAlignment($content) {
        return $this->agapeAnalyzer->calculateAlignment($content);
    }
    
    /**
     * Store short-term memory
     */
    private function storeShortTermMemory($memoryEntry) {
        $this->shortTermMemory[$memoryEntry['id']] = $memoryEntry;
        
        if (count($this->shortTermMemory) > $this->memoryThresholds['short_term_max_size']) {
            $this->cleanupShortTermMemory();
        }
        
        $this->persistShortTermMemory();
    }
    
    /**
     * Store long-term memory
     */
    private function storeLongTermMemory($memoryEntry) {
        $this->longTermMemory[$memoryEntry['id']] = $memoryEntry;
        
        if (count($this->longTermMemory) > $this->memoryThresholds['long_term_max_size']) {
            $this->cleanupLongTermMemory();
        }
        
        $this->persistLongTermMemory();
    }
    
    /**
     * Cleanup short-term memory with database sync
     */
    private function cleanupShortTermMemory() {
        uasort($this->shortTermMemory, function($a, $b) {
            $scoreA = $a['importance_score'] + ($a['access_count'] * 0.1) + ($a['agape_score'] * 0.1);
            $scoreB = $b['importance_score'] + ($b['access_count'] * 0.1) + ($b['agape_score'] * 0.1);
            return $scoreB <=> $scoreA;
        });
        
        $keepCount = intval($this->memoryThresholds['short_term_max_size'] * $this->memoryThresholds['cleanup_threshold']);
        $toKeep = array_slice($this->shortTermMemory, 0, $keepCount, true);
        $toRemove = array_diff_key($this->shortTermMemory, $toKeep);
        
        try {
            if (!empty($toRemove)) {
                $placeholders = str_repeat('?,', count($toRemove) - 1) . '?';
                $stmt = $this->db->prepare("DELETE FROM memory_entries WHERE type = 'short_term' AND id IN ($placeholders)");
                $stmt->execute(array_keys($toRemove));
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('cleanup_db_error', ['error' => $e->getMessage()]);
        }
        
        $this->shortTermMemory = $toKeep;
        $this->persistShortTermMemory();
        
        $this->logMemoryOperation('cleanup', [
            'type' => 'short_term',
            'kept_count' => count($toKeep),
            'removed_count' => count($toRemove)
        ]);
    }
    
    /**
     * Cleanup long-term memory with database sync
     */
    private function cleanupLongTermMemory() {
        uasort($this->longTermMemory, function($a, $b) {
            $scoreA = $a['importance_score'] + ($a['access_count'] * 0.1) + ($a['agape_score'] * 0.1);
            $scoreB = $b['importance_score'] + ($b['access_count'] * 0.1) + ($b['agape_score'] * 0.1);
            return $scoreB <=> $scoreA;
        });
        
        $keepCount = intval($this->memoryThresholds['long_term_max_size'] * $this->memoryThresholds['cleanup_threshold']);
        $toKeep = array_slice($this->longTermMemory, 0, $keepCount, true);
        $toRemove = array_diff_key($this->longTermMemory, $toKeep);
        
        try {
            if (!empty($toRemove)) {
                $placeholders = str_repeat('?,', count($toRemove) - 1) . '?';
                $stmt = $this->db->prepare("DELETE FROM memory_entries WHERE type = 'long_term' AND id IN ($placeholders)");
                $stmt->execute(array_keys($toRemove));
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('cleanup_db_error', ['error' => $e->getMessage()]);
        }
        
        $this->longTermMemory = $toKeep;
        $this->persistLongTermMemory();
        
        $this->logMemoryOperation('cleanup', [
            'type' => 'long_term',
            'kept_count' => count($toKeep),
            'removed_count' => count($toRemove)
        ]);
    }
    
    /**
     * Persist short-term memory with error handling
     */
    private function persistShortTermMemory() {
        $filePath = $this->workspacePath . 'short_term/memory.json';
        try {
            if (file_put_contents($filePath, json_encode($this->shortTermMemory, JSON_PRETTY_PRINT)) === false) {
                throw new Exception("Failed to write to $filePath");
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('persist_error', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Persist long-term memory with error handling
     */
    private function persistLongTermMemory() {
        $filePath = $this->workspacePath . 'long_term/memory.json';
        try {
            if (file_put_contents($filePath, json_encode($this->longTermMemory, JSON_PRETTY_PRINT)) === false) {
                throw new Exception("Failed to write to $filePath");
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('persist_error', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Load memory from file with error handling
     */
    private function loadMemoryFromFile($memoryId, $type) {
        $filePath = $this->workspacePath . ($type ?? 'short_term') . '/memory.json';
        try {
            if (file_exists($filePath)) {
                $memories = json_decode(file_get_contents($filePath), true);
                return $memories[$memoryId] ?? null;
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('load_error', ['error' => $e->getMessage()]);
        }
        return null;
    }
    
    /**
     * Encrypt content
     */
    private function encryptContent($content) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($content, 'AES-256-CBC', hex2bin($this->encryptionKey), 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt content
     */
    private function decryptContent($encryptedContent) {
        $data = base64_decode($encryptedContent);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', hex2bin($this->encryptionKey), 0, $iv);
    }
    
    /**
     * Validate memory with Phase 3 system
     */
    public function validateMemory($memoryId, $phase3System) {
        $memory = $this->retrieveMemory($memoryId);
        if (!$memory) {
            return false;
        }
        
        $filePath = $this->workspacePath . 'temp/' . $memoryId . '.txt';
        try {
            file_put_contents($filePath, $memory['content']);
            $validation = $phase3System->validateFileQuality($filePath);
            unlink($filePath);
            
            $this->logMemoryOperation('validate_memory', [
                'memory_id' => $memoryId,
                'validation' => $validation
            ]);
            
            return $validation['passed'];
        } catch (Exception $e) {
            $this->logMemoryOperation('validation_error', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Log memory operations with error handling
     */
    private function logMemoryOperation($action, $data) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'data' => $data
        ];
        
        try {
            $logLine = json_encode($logEntry) . "\n";
            if (file_put_contents($this->memoryLogPath, $logLine, FILE_APPEND | LOCK_EX) === false) {
                throw new Exception("Failed to write to {$this->memoryLogPath}");
            }
        } catch (Exception $e) {
            error_log("Memory log error: " . $e->getMessage());
        }
    }
    
    /**
     * Get memory statistics
     */
    public function getMemoryStatistics() {
        $stats = [
            'short_term_count' => count($this->shortTermMemory),
            'long_term_count' => count($this->longTermMemory),
            'total_memories' => count($this->shortTermMemory) + count($this->longTermMemory),
            'average_importance_score' => $this->calculateAverageImportanceScore(),
            'average_agape_score' => $this->calculateAverageAGAPEScore(),
            'memory_usage' => memory_get_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'peak_usage' => memory_get_peak_usage(true)
        ];
        
        return $stats;
    }
    
    /**
     * Calculate average importance score
     */
    private function calculateAverageImportanceScore() {
        $allMemories = array_merge($this->shortTermMemory, $this->longTermMemory);
        if (empty($allMemories)) {
            return 0;
        }
        
        $totalScore = array_sum(array_column($allMemories, 'importance_score'));
        return round($totalScore / count($allMemories), 2);
    }
    
    /**
     * Calculate average AGAPE score
     */
    private function calculateAverageAGAPEScore() {
        $allMemories = array_merge($this->shortTermMemory, $this->longTermMemory);
        if (empty($allMemories)) {
            return 0;
        }
        
        $totalScore = array_sum(array_column($allMemories, 'agape_score'));
        return round($totalScore / count($allMemories), 2);
    }
    
    /**
     * Create memory tables
     */
    public function createMemoryTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS memory_entries (
            id VARCHAR(50) PRIMARY KEY,
            content TEXT,
            type ENUM('short_term', 'long_term') NOT NULL,
            metadata JSON,
            timestamp DATETIME NOT NULL,
            encrypted BOOLEAN DEFAULT FALSE,
            access_count INT DEFAULT 0,
            last_accessed DATETIME NOT NULL,
            importance_score FLOAT DEFAULT 0,
            agape_score FLOAT DEFAULT 0,
            INDEX idx_type (type),
            INDEX idx_importance (importance_score),
            INDEX idx_agape (agape_score),
            INDEX idx_last_accessed (last_accessed)
        )";
        
        try {
            $this->db->exec($sql);
            $this->logMemoryOperation('create_tables', ['status' => 'success']);
        } catch (Exception $e) {
            $this->logMemoryOperation('create_tables_error', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Close the system
     */
    public function close() {
        $this->persistShortTermMemory();
        $this->persistLongTermMemory();
    }
}

// AGAPE Analyzer class
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
    $memorySystem = new MemoryManagementSystemEnhanced();
    
    echo "=== Enhanced Memory Management System Test ===\n\n";
    
    // Create tables
    $memorySystem->createMemoryTables();
    
    // Test memory storage
    $memoryId1 = $memorySystem->storeMemory(
        "AGAPE principles guide all our actions with love, patience, kindness, and humility",
        'short_term',
        ['category' => 'agape', 'importance' => 9],
        false
    );
    
    $memoryId2 = $memorySystem->storeMemory(
        "Implement comprehensive error handling with try-catch blocks",
        'long_term',
        ['category' => 'technical', 'priority' => 'high'],
        true
    );
    
    echo "Memory 1 Stored: $memoryId1\n";
    echo "Memory 2 Stored: $memoryId2\n";
    
    // Test memory retrieval
    $memory1 = $memorySystem->retrieveMemory($memoryId1);
    $memory2 = $memorySystem->retrieveMemory($memoryId2);
    
    echo "Retrieved Memory 1: " . substr($memory1['content'], 0, 50) . "...\n";
    echo "Retrieved Memory 2: " . substr($memory2['content'], 0, 50) . "...\n";
    
    // Test search
    $searchResults = $memorySystem->searchMemories(['agape', 'principles'], null, 5);
    echo "Search Results: " . count($searchResults) . " found\n";
    
    // Test multi-modal memory
    $multiModalId = $memorySystem->storeMultiModalMemory(
        "Base64 encoded image data",
        'long_term',
        ['data_type' => 'image', 'format' => 'png'],
        true
    );
    echo "Multi-Modal Memory Stored: $multiModalId\n";
    
    // Test agent coordination
    $coordinationId = $memorySystem->storeAgentCoordination(
        'CURSOR',
        ['task' => 'code_generation', 'status' => 'completed'],
        'long_term'
    );
    echo "Agent Coordination Stored: $coordinationId\n";
    
    // Get statistics
    $stats = $memorySystem->getMemoryStatistics();
    echo "\n=== Memory Statistics ===\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    $memorySystem->close();
}
?>
