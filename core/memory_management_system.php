<?php
/**
 * WOLFIE AGI UI - Memory Management System
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Memory management system for short/long-term memory organization
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 20:25:00 CDT
 * WHY: To organize and manage memory for Quantum Mirror Chat and entangled personas
 * HOW: PHP-based memory management with local storage security and memory optimization
 * PURPOSE: Foundation of memory organization and retrieval
 * ID: MEMORY_MANAGEMENT_SYSTEM_001
 * KEY: MEMORY_MANAGEMENT_ORGANIZATION_SYSTEM
 * SUPERPOSITIONALLY: [MEMORY_MANAGEMENT_SYSTEM_001, WOLFIE_AGI_UI_089]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of memory organization and retrieval
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [MEMORY_MANAGEMENT_SYSTEM_001, WOLFIE_AGI_UI_089]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - Memory Management System
 */

require_once '../config/database_config.php';

class MemoryManagementSystem {
    private $db;
    private $workspacePath;
    private $memoryLogPath;
    private $shortTermMemory;
    private $longTermMemory;
    private $memoryThresholds;
    private $encryptionKey;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
        $this->workspacePath = __DIR__ . '/../workspace/memory/';
        $this->memoryLogPath = __DIR__ . '/../logs/memory_management.log';
        $this->shortTermMemory = [];
        $this->longTermMemory = [];
        $this->initializeMemoryThresholds();
        $this->encryptionKey = $this->generateEncryptionKey();
        $this->ensureDirectoriesExist();
    }
    
    /**
     * Initialize memory thresholds
     */
    private function initializeMemoryThresholds() {
        $this->memoryThresholds = [
            'short_term_max_size' => 1000,      // Maximum short-term memory entries
            'long_term_max_size' => 10000,      // Maximum long-term memory entries
            'memory_cleanup_threshold' => 0.8,  // Cleanup when 80% full
            'compression_threshold' => 0.9,     // Compress when 90% full
            'max_memory_usage' => '512M'        // Maximum memory usage
        ];
    }
    
    /**
     * Generate encryption key for sensitive memory
     */
    private function generateEncryptionKey() {
        $keyFile = $this->workspacePath . '.encryption_key';
        if (file_exists($keyFile)) {
            return file_get_contents($keyFile);
        } else {
            $key = bin2hex(random_bytes(32));
            file_put_contents($keyFile, $key);
            return $key;
        }
    }
    
    /**
     * Ensure required directories exist
     */
    private function ensureDirectoriesExist() {
        $directories = [
            $this->workspacePath,
            $this->workspacePath . 'short_term/',
            $this->workspacePath . 'long_term/',
            $this->workspacePath . 'compressed/',
            dirname($this->memoryLogPath)
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Store memory entry
     */
    public function storeMemory($content, $type = 'short_term', $metadata = [], $encrypt = false) {
        $memoryId = 'memory_' . uniqid();
        $timestamp = date('Y-m-d H:i:s');
        
        $memoryEntry = [
            'id' => $memoryId,
            'content' => $content,
            'type' => $type,
            'metadata' => $metadata,
            'timestamp' => $timestamp,
            'encrypted' => $encrypt,
            'access_count' => 0,
            'last_accessed' => $timestamp,
            'importance_score' => $this->calculateImportanceScore($content, $metadata)
        ];
        
        // Encrypt if requested
        if ($encrypt) {
            $memoryEntry['content'] = $this->encryptContent($content);
        }
        
        // Store in appropriate memory type
        if ($type === 'short_term') {
            $this->storeShortTermMemory($memoryEntry);
        } else {
            $this->storeLongTermMemory($memoryEntry);
        }
        
        // Log memory operation
        $this->logMemoryOperation('store', $memoryEntry);
        
        return $memoryId;
    }
    
    /**
     * Store short-term memory
     */
    private function storeShortTermMemory($memoryEntry) {
        $this->shortTermMemory[$memoryEntry['id']] = $memoryEntry;
        
        // Check if cleanup is needed
        if (count($this->shortTermMemory) > $this->memoryThresholds['short_term_max_size']) {
            $this->cleanupShortTermMemory();
        }
        
        // Persist to file
        $this->persistShortTermMemory();
    }
    
    /**
     * Store long-term memory
     */
    private function storeLongTermMemory($memoryEntry) {
        $this->longTermMemory[$memoryEntry['id']] = $memoryEntry;
        
        // Check if cleanup is needed
        if (count($this->longTermMemory) > $this->memoryThresholds['long_term_max_size']) {
            $this->cleanupLongTermMemory();
        }
        
        // Persist to file
        $this->persistLongTermMemory();
    }
    
    /**
     * Retrieve memory entry
     */
    public function retrieveMemory($memoryId, $type = null) {
        $memoryEntry = null;
        
        if ($type === 'short_term' || $type === null) {
            $memoryEntry = $this->shortTermMemory[$memoryId] ?? null;
        }
        
        if (!$memoryEntry && ($type === 'long_term' || $type === null)) {
            $memoryEntry = $this->longTermMemory[$memoryId] ?? null;
        }
        
        if (!$memoryEntry) {
            // Try to load from file
            $memoryEntry = $this->loadMemoryFromFile($memoryId, $type);
        }
        
        if ($memoryEntry) {
            // Update access statistics
            $memoryEntry['access_count']++;
            $memoryEntry['last_accessed'] = date('Y-m-d H:i:s');
            
            // Decrypt if needed
            if ($memoryEntry['encrypted']) {
                $memoryEntry['content'] = $this->decryptContent($memoryEntry['content']);
            }
            
            // Update in memory
            if ($memoryEntry['type'] === 'short_term') {
                $this->shortTermMemory[$memoryId] = $memoryEntry;
            } else {
                $this->longTermMemory[$memoryId] = $memoryEntry;
            }
            
            $this->logMemoryOperation('retrieve', $memoryEntry);
        }
        
        return $memoryEntry;
    }
    
    /**
     * Search memories
     */
    public function searchMemories($query, $type = null, $limit = 10) {
        $results = [];
        $searchTerms = $this->extractSearchTerms($query);
        
        // Search short-term memory
        if ($type === 'short_term' || $type === null) {
            foreach ($this->shortTermMemory as $memory) {
                $score = $this->calculateSearchScore($memory, $searchTerms);
                if ($score > 0) {
                    $results[] = ['memory' => $memory, 'score' => $score, 'type' => 'short_term'];
                }
            }
        }
        
        // Search long-term memory
        if ($type === 'long_term' || $type === null) {
            foreach ($this->longTermMemory as $memory) {
                $score = $this->calculateSearchScore($memory, $searchTerms);
                if ($score > 0) {
                    $results[] = ['memory' => $memory, 'score' => $score, 'type' => 'long_term'];
                }
            }
        }
        
        // Sort by score and limit results
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($results, 0, $limit);
    }
    
    /**
     * Calculate importance score
     */
    private function calculateImportanceScore($content, $metadata) {
        $score = 0;
        
        // Base score from content length
        $score += min(strlen($content) / 100, 10);
        
        // Boost for specific metadata
        if (isset($metadata['importance'])) {
            $score += $metadata['importance'] * 5;
        }
        
        if (isset($metadata['agent_id'])) {
            $score += 2; // Agent-related memories are important
        }
        
        if (isset($metadata['task_type'])) {
            $score += 1; // Task-related memories are important
        }
        
        // Boost for recent memories
        $score += 1;
        
        return min($score, 100); // Cap at 100
    }
    
    /**
     * Calculate search score
     */
    private function calculateSearchScore($memory, $searchTerms) {
        $score = 0;
        $content = strtolower($memory['content']);
        $metadata = json_encode($memory['metadata']);
        
        foreach ($searchTerms as $term) {
            $term = strtolower($term);
            
            // Content matches
            if (strpos($content, $term) !== false) {
                $score += 2;
            }
            
            // Metadata matches
            if (strpos($metadata, $term) !== false) {
                $score += 1;
            }
            
            // Exact matches get higher score
            if ($content === $term) {
                $score += 5;
            }
        }
        
        // Boost for frequently accessed memories
        $score += $memory['access_count'] * 0.1;
        
        // Boost for important memories
        $score += $memory['importance_score'] * 0.01;
        
        return $score;
    }
    
    /**
     * Extract search terms from query
     */
    private function extractSearchTerms($query) {
        // Simple term extraction - can be enhanced with NLP
        $terms = preg_split('/\s+/', trim($query));
        return array_filter($terms, function($term) {
            return strlen($term) > 2; // Ignore very short terms
        });
    }
    
    /**
     * Cleanup short-term memory
     */
    private function cleanupShortTermMemory() {
        // Sort by importance and access count
        uasort($this->shortTermMemory, function($a, $b) {
            $scoreA = $a['importance_score'] + ($a['access_count'] * 0.1);
            $scoreB = $b['importance_score'] + ($b['access_count'] * 0.1);
            return $scoreB <=> $scoreA;
        });
        
        // Keep only the most important memories
        $keepCount = intval($this->memoryThresholds['short_term_max_size'] * 0.8);
        $this->shortTermMemory = array_slice($this->shortTermMemory, 0, $keepCount, true);
        
        $this->logMemoryOperation('cleanup', ['type' => 'short_term', 'count' => $keepCount]);
    }
    
    /**
     * Cleanup long-term memory
     */
    private function cleanupLongTermMemory() {
        // Sort by importance and access count
        uasort($this->longTermMemory, function($a, $b) {
            $scoreA = $a['importance_score'] + ($a['access_count'] * 0.1);
            $scoreB = $b['importance_score'] + ($b['access_count'] * 0.1);
            return $scoreB <=> $scoreA;
        });
        
        // Keep only the most important memories
        $keepCount = intval($this->memoryThresholds['long_term_max_size'] * 0.8);
        $this->longTermMemory = array_slice($this->longTermMemory, 0, $keepCount, true);
        
        $this->logMemoryOperation('cleanup', ['type' => 'long_term', 'count' => $keepCount]);
    }
    
    /**
     * Persist short-term memory to file
     */
    private function persistShortTermMemory() {
        $filePath = $this->workspacePath . 'short_term/memory.json';
        file_put_contents($filePath, json_encode($this->shortTermMemory, JSON_PRETTY_PRINT));
    }
    
    /**
     * Persist long-term memory to file
     */
    private function persistLongTermMemory() {
        $filePath = $this->workspacePath . 'long_term/memory.json';
        file_put_contents($filePath, json_encode($this->longTermMemory, JSON_PRETTY_PRINT));
    }
    
    /**
     * Load memory from file
     */
    private function loadMemoryFromFile($memoryId, $type) {
        $filePath = $this->workspacePath . $type . '/memory.json';
        
        if (file_exists($filePath)) {
            $memories = json_decode(file_get_contents($filePath), true);
            return $memories[$memoryId] ?? null;
        }
        
        return null;
    }
    
    /**
     * Encrypt content
     */
    private function encryptContent($content) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($content, 'AES-256-CBC', $this->encryptionKey, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt content
     */
    private function decryptContent($encryptedContent) {
        $data = base64_decode($encryptedContent);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryptionKey, 0, $iv);
    }
    
    /**
     * Log memory operation
     */
    private function logMemoryOperation($operation, $data) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'operation' => $operation,
            'data' => $data
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->memoryLogPath, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get memory statistics
     */
    public function getMemoryStatistics() {
        return [
            'short_term_count' => count($this->shortTermMemory),
            'long_term_count' => count($this->longTermMemory),
            'total_memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
            'memory_thresholds' => $this->memoryThresholds,
            'encryption_enabled' => !empty($this->encryptionKey)
        ];
    }
    
    /**
     * Create memory tables
     */
    public function createMemoryTables() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS memory_entries (
                id TEXT PRIMARY KEY,
                content TEXT NOT NULL,
                type TEXT NOT NULL,
                metadata TEXT,
                timestamp DATETIME NOT NULL,
                encrypted INTEGER DEFAULT 0,
                access_count INTEGER DEFAULT 0,
                last_accessed DATETIME,
                importance_score INTEGER DEFAULT 0
            )";
            
            $this->db->exec($sql);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Close connections
     */
    public function close() {
        // Persist all memories before closing
        $this->persistShortTermMemory();
        $this->persistLongTermMemory();
        $this->db = null;
    }
}

// Example usage and testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $memoryManager = new MemoryManagementSystem();
    
    echo "=== WOLFIE AGI UI Memory Management System Test ===\n\n";
    
    // Create memory tables
    $tableCreated = $memoryManager->createMemoryTables();
    echo "Memory tables created: " . ($tableCreated ? 'YES' : 'NO') . "\n\n";
    
    // Test storing memories
    echo "--- Testing Memory Storage ---\n";
    
    $memories = [
        [
            'content' => 'Captain WOLFIE is working on AGI patterns integration',
            'type' => 'short_term',
            'metadata' => ['agent_id' => 'CURSOR', 'task_type' => 'development'],
            'encrypt' => false
        ],
        [
            'content' => 'AGAPE principles: Love, Patience, Kindness, Humility',
            'type' => 'long_term',
            'metadata' => ['importance' => 10, 'category' => 'principles'],
            'encrypt' => true
        ],
        [
            'content' => 'Quantum Mirror Chat for entangled personas',
            'type' => 'long_term',
            'metadata' => ['importance' => 8, 'category' => 'technology'],
            'encrypt' => false
        ],
        [
            'content' => 'Error handling system implemented successfully',
            'type' => 'short_term',
            'metadata' => ['agent_id' => 'ARA', 'task_type' => 'completion'],
            'encrypt' => false
        ]
    ];
    
    $memoryIds = [];
    foreach ($memories as $index => $memory) {
        $id = $memoryManager->storeMemory(
            $memory['content'],
            $memory['type'],
            $memory['metadata'],
            $memory['encrypt']
        );
        $memoryIds[] = $id;
        echo "Stored memory " . ($index + 1) . ": $id\n";
    }
    
    echo "\n--- Testing Memory Retrieval ---\n";
    
    // Test retrieving memories
    foreach ($memoryIds as $index => $id) {
        $memory = $memoryManager->retrieveMemory($id);
        if ($memory) {
            echo "Retrieved memory " . ($index + 1) . ": " . substr($memory['content'], 0, 50) . "...\n";
            echo "  Type: " . $memory['type'] . "\n";
            echo "  Importance: " . $memory['importance_score'] . "\n";
            echo "  Encrypted: " . ($memory['encrypted'] ? 'YES' : 'NO') . "\n";
        } else {
            echo "Failed to retrieve memory " . ($index + 1) . "\n";
        }
    }
    
    echo "\n--- Testing Memory Search ---\n";
    
    // Test searching memories
    $searchQueries = ['AGI', 'AGAPE', 'Quantum', 'Error handling'];
    
    foreach ($searchQueries as $query) {
        echo "Searching for: '$query'\n";
        $results = $memoryManager->searchMemories($query, null, 3);
        
        foreach ($results as $result) {
            echo "  Found: " . substr($result['memory']['content'], 0, 50) . "... (Score: " . $result['score'] . ")\n";
        }
        echo "\n";
    }
    
    // Show statistics
    $stats = $memoryManager->getMemoryStatistics();
    echo "=== Memory Statistics ===\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    $memoryManager->close();
}
?>
