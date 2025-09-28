<?php
/**
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Production-Ready Memory Management System for WOLFIE AGI UI
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 23:45:00 CDT
 * WHY: To provide production-ready memory management with comprehensive database integration, multi-modal support, and AGAPE alignment for Phase 5 readiness
 * HOW: PHP-based system with offline-first design, database persistence, sophisticated scoring, and multi-modal data handling
 * PURPOSE: Foundation for persistent memory with AGAPE principles and Phase 5 multi-modal support
 * ID: MEMORY_MANAGEMENT_SYSTEM_PRODUCTION_001
 * KEY: MEMORY_MANAGEMENT_SYSTEM_PRODUCTION
 * SUPERPOSITIONALLY: [MEMORY_MANAGEMENT_SYSTEM_PRODUCTION_001, WOLFIE_AGI_UI_102]
 */

require_once 'database_config.php';

class MemoryManagementSystemProduction {
    private $db;
    private $workspacePath;
    private $shortTermMemory;
    private $longTermMemory;
    private $encryptionKey;
    private $agapeAnalyzer;
    private $errorHandler;
    private $memoryThresholds;
    private $memoryLogPath;
    
    public function __construct($errorHandler = null) {
        $this->db = getDatabaseConnection();
        $this->createMemoryTables();
        $this->createMemoryLogsTable();
        
        $this->workspacePath = __DIR__ . '/../workspace/memory_management/';
        $this->shortTermMemory = [];
        $this->longTermMemory = [];
        $this->agapeAnalyzer = new AGAPEAnalyzer();
        $this->errorHandler = $errorHandler;
        $this->memoryLogPath = $this->workspacePath . 'logs/memory_operations.log';
        
        $this->ensureDirectoriesExist();
        $this->loadEncryptionKey();
        $this->initializeMemoryThresholds();
        $this->loadMemoriesFromDatabase();
    }
    
    /**
     * Ensure memory management directories exist with comprehensive error handling
     */
    private function ensureDirectoriesExist() {
        $directories = [
            $this->workspacePath,
            $this->workspacePath . 'short_term/',
            $this->workspacePath . 'long_term/',
            $this->workspacePath . 'logs/',
            $this->workspacePath . 'temp/',
            $this->workspacePath . 'multimodal/',
            $this->workspacePath . 'agent_coordination/',
            $this->workspacePath . 'cache/'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                try {
                    if (!mkdir($dir, 0755, true)) {
                        throw new Exception("Failed to create directory: $dir");
                    }
                } catch (Exception $e) {
                    $this->logMemoryOperation('directory_creation_error', ['error' => $e->getMessage(), 'directory' => $dir]);
                    if ($this->errorHandler) {
                        $this->errorHandler->handleError("Directory creation failed: {$e->getMessage()}", ['directory' => $dir], 'medium');
                    }
                }
            }
        }
    }
    
    /**
     * Create memory tables with comprehensive error handling
     */
    private function createMemoryTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS memory_entries (
            id VARCHAR(50) PRIMARY KEY,
            content TEXT NOT NULL,
            type ENUM('short_term', 'long_term') NOT NULL,
            metadata JSON,
            timestamp DATETIME NOT NULL,
            encrypted BOOLEAN DEFAULT FALSE,
            access_count INT DEFAULT 0,
            last_accessed DATETIME,
            importance_score FLOAT DEFAULT 0,
            agape_score FLOAT DEFAULT 0,
            INDEX idx_type (type),
            INDEX idx_timestamp (timestamp),
            INDEX idx_importance (importance_score),
            INDEX idx_agape (agape_score),
            INDEX idx_metadata_category (metadata('$.category'))
        )";
        
        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            $this->logMemoryOperation('table_creation_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory table creation failed: {$e->getMessage()}", ['component' => 'memory_management'], 'critical');
            }
        }
    }
    
    /**
     * Create memory logs table with comprehensive error handling
     */
    private function createMemoryLogsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS memory_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp DATETIME NOT NULL,
            action VARCHAR(100),
            data JSON
        )";
        
        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            $this->logMemoryOperation('log_table_creation_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory log table creation failed: {$e->getMessage()}", ['component' => 'memory_management'], 'critical');
            }
        }
    }
    
    /**
     * Enhanced encryption key loading with comprehensive error handling
     */
    private function loadEncryptionKey() {
        $keyFile = $this->workspacePath . '.encryption_key';
        $envKey = getenv('WOLFIE_MEMORY_KEY');
        
        if ($envKey && strlen($envKey) === 64 && ctype_xdigit($envKey)) {
            $this->encryptionKey = $envKey;
            $this->logMemoryOperation('key_loaded_from_env', ['source' => 'environment']);
            return;
        }
        
        try {
            if (file_exists($keyFile)) {
                $this->encryptionKey = file_get_contents($keyFile);
                if (strlen($this->encryptionKey) !== 64 || !ctype_xdigit($this->encryptionKey)) {
                    throw new Exception("Invalid encryption key format");
                }
                $this->logMemoryOperation('key_loaded_from_file', ['source' => 'file']);
            } else {
                $this->encryptionKey = $this->generateEncryptionKey();
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('key_load_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Encryption key load failed: {$e->getMessage()}", ['component' => 'memory_management'], 'critical');
            }
            $this->encryptionKey = $this->generateEncryptionKey();
        }
    }
    
    /**
     * Enhanced encryption key generation with comprehensive error handling
     */
    private function generateEncryptionKey() {
        $keyFile = $this->workspacePath . '.encryption_key';
        $key = bin2hex(random_bytes(32));
        
        try {
            if (file_put_contents($keyFile, $key) === false) {
                throw new Exception("Failed to write encryption key to $keyFile");
            }
            chmod($keyFile, 0600);
            $this->logMemoryOperation('key_generated', ['key_file' => $keyFile]);
        } catch (Exception $e) {
            $this->logMemoryOperation('key_generate_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Encryption key generation failed: {$e->getMessage()}", ['component' => 'memory_management'], 'critical');
            }
        }
        
        return $key;
    }
    
    /**
     * Initialize memory thresholds
     */
    private function initializeMemoryThresholds() {
        $this->memoryThresholds = [
            'short_term_max_size' => 100,
            'long_term_max_size' => 1000,
            'multimodal_max_size' => 10240, // 10MB
            'agent_coordination_max_size' => 1024, // 1MB
            'cleanup_threshold' => 0.8
        ];
    }
    
    /**
     * Load memories from database
     */
    private function loadMemoriesFromDatabase() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM memory_entries ORDER BY timestamp DESC LIMIT 1000");
            $stmt->execute();
            $memories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($memories as $memory) {
                $memoryEntry = $this->formatMemoryEntry($memory);
                if ($memory['type'] === 'short_term') {
                    $this->shortTermMemory[$memory['id']] = $memoryEntry;
                } else {
                    $this->longTermMemory[$memory['id']] = $memoryEntry;
                }
            }
            
            $this->logMemoryOperation('load_from_database', ['count' => count($memories)]);
        } catch (Exception $e) {
            $this->logMemoryOperation('db_load_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory load from database failed: {$e->getMessage()}", ['component' => 'memory_management'], 'high');
            }
        }
    }
    
    /**
     * Store memory with comprehensive error handling and size validation
     */
    public function storeMemory($content, $type = 'long_term', $metadata = [], $encrypt = true) {
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $metadata = array_map('htmlspecialchars', $metadata);
        
        if (strlen($content) > $this->memoryThresholds[$type . '_max_size'] * 1024) {
            $this->logMemoryOperation('oversized_memory', ['size' => strlen($content), 'type' => $type]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory content exceeds size limit: " . strlen($content), ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
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
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory storage failed: {$e->getMessage()}", ['memory_id' => $memoryId], 'high');
            }
            return false;
        }
        
        if ($type === 'short_term') {
            $this->shortTermMemory[$memoryId] = $memoryEntry;
            $this->storeShortTermMemory($memoryEntry);
        } else {
            $this->longTermMemory[$memoryId] = $memoryEntry;
            $this->storeLongTermMemory($memoryEntry);
        }
        
        $this->logMemoryOperation('store_memory', $memoryEntry);
        return $memoryId;
    }
    
    /**
     * Store multi-modal memory with comprehensive validation and size limits
     */
    public function storeMultiModalMemory($content, $type = 'long_term', $metadata = [], $encrypt = true) {
        $metadata = array_map('htmlspecialchars', $metadata);
        $dataType = $metadata['data_type'] ?? 'unknown';
        $validTypes = ['image', 'audio', 'video', 'text', 'data'];
        
        if (!in_array($dataType, $validTypes)) {
            $this->logMemoryOperation('invalid_multi_modal_type', ['data_type' => $dataType]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Invalid multi-modal data type: $dataType", ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
        if ($dataType === 'image' && isset($metadata['format'])) {
            $validFormats = ['png', 'jpeg', 'jpg', 'gif', 'bmp', 'webp'];
            if (!in_array(strtolower($metadata['format']), $validFormats)) {
                $this->logMemoryOperation('invalid_image_format', ['format' => $metadata['format']]);
                if ($this->errorHandler) {
                    $this->errorHandler->handleError("Invalid image format: {$metadata['format']}", ['component' => 'memory_management'], 'medium');
                }
                return false;
            }
        } elseif ($dataType === 'audio' && isset($metadata['format'])) {
            $validFormats = ['mp3', 'wav', 'ogg', 'flac', 'aac'];
            if (!in_array(strtolower($metadata['format']), $validFormats)) {
                $this->logMemoryOperation('invalid_audio_format', ['format' => $metadata['format']]);
                if ($this->errorHandler) {
                    $this->errorHandler->handleError("Invalid audio format: {$metadata['format']}", ['component' => 'memory_management'], 'medium');
                }
                return false;
            }
        } elseif ($dataType === 'video' && isset($metadata['format'])) {
            $validFormats = ['mp4', 'avi', 'mkv', 'mov', 'wmv'];
            if (!in_array(strtolower($metadata['format']), $validFormats)) {
                $this->logMemoryOperation('invalid_video_format', ['format' => $metadata['format']]);
                if ($this->errorHandler) {
                    $this->errorHandler->handleError("Invalid video format: {$metadata['format']}", ['component' => 'memory_management'], 'medium');
                }
                return false;
            }
        }
        
        $encodedContent = is_string($content) ? $content : base64_encode($content);
        
        // Format-specific size limits
        $sizeLimits = [
            'image' => 5 * 1024 * 1024, // 5MB
            'audio' => 10 * 1024 * 1024, // 10MB
            'video' => 50 * 1024 * 1024, // 50MB
            'text' => 1 * 1024 * 1024, // 1MB
            'data' => 1 * 1024 * 1024 // 1MB
        ];
        $maxSize = $sizeLimits[$dataType] ?? $this->memoryThresholds['multimodal_max_size'] * 1024;
        
        if (strlen($encodedContent) > $maxSize) {
            $this->logMemoryOperation('oversized_multi_modal', ['size' => strlen($encodedContent), 'data_type' => $dataType, 'max_size' => $maxSize]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Multi-modal data exceeds size limit: " . strlen($encodedContent) . " (max: $maxSize)", ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
        // OpenCV validation for images
        if ($dataType === 'image' && isset($metadata['format'])) {
            try {
                $tempFile = $this->workspacePath . 'temp/' . uniqid() . '.' . strtolower($metadata['format']);
                file_put_contents($tempFile, base64_decode($encodedContent));
                $output = [];
                exec("python -c \"import cv2; img = cv2.imread('" . escapeshellarg($tempFile) . "'); print(img.shape if img is not None else 'Invalid')\" 2>&1", $output);
                unlink($tempFile);
                if (implode('', $output) === 'Invalid') {
                    $this->logMemoryOperation('invalid_image_data', ['format' => $metadata['format']]);
                    if ($this->errorHandler) {
                        $this->errorHandler->handleError("Invalid image data", ['component' => 'memory_management'], 'medium');
                    }
                    return false;
                }
            } catch (Exception $e) {
                $this->logMemoryOperation('image_validation_error', ['error' => $e->getMessage()]);
                if ($this->errorHandler) {
                    $this->errorHandler->handleError("Image validation failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
                }
                return false;
            }
        } elseif ($dataType === 'audio' && isset($metadata['format'])) {
            try {
                $tempFile = $this->workspacePath . 'temp/' . uniqid() . '.' . strtolower($metadata['format']);
                file_put_contents($tempFile, base64_decode($encodedContent));
                $output = [];
                exec("ffmpeg -i " . escapeshellarg($tempFile) . " -f null - 2>&1", $output);
                unlink($tempFile);
                $outputStr = implode('', $output);
                if (strpos($outputStr, 'Invalid data') !== false || !strpos($outputStr, 'Duration')) {
                    $this->logMemoryOperation('invalid_audio_data', ['format' => $metadata['format']]);
                    if ($this->errorHandler) {
                        $this->errorHandler->handleError("Invalid audio data", ['component' => 'memory_management'], 'medium');
                    }
                    return false;
                }
                // Extract duration metadata
                if (preg_match('/Duration: (\d{2}:\d{2}:\d{2}\.\d+)/', $outputStr, $matches)) {
                    $metadata['duration'] = $matches[1];
                }
            } catch (Exception $e) {
                $this->logMemoryOperation('audio_validation_error', ['error' => $e->getMessage()]);
                if ($this->errorHandler) {
                    $this->errorHandler->handleError("Audio validation failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
                }
                return false;
            }
        } elseif ($dataType === 'video' && isset($metadata['format'])) {
            try {
                $tempFile = $this->workspacePath . 'temp/' . uniqid() . '.' . strtolower($metadata['format']);
                file_put_contents($tempFile, base64_decode($encodedContent));
                $output = [];
                exec("ffmpeg -i " . escapeshellarg($tempFile) . " -f null - 2>&1", $output);
                unlink($tempFile);
                $outputStr = implode('', $output);
                if (strpos($outputStr, 'Invalid data') !== false || !strpos($outputStr, 'Duration')) {
                    $this->logMemoryOperation('invalid_video_data', ['format' => $metadata['format']]);
                    if ($this->errorHandler) {
                        $this->errorHandler->handleError("Invalid video data", ['component' => 'memory_management'], 'medium');
                    }
                    return false;
                }
                // Extract duration and resolution metadata
                if (preg_match('/Duration: (\d{2}:\d{2}:\d{2}\.\d+)/', $outputStr, $matches)) {
                    $metadata['duration'] = $matches[1];
                }
                if (preg_match('/(\d+)x(\d+)/', $outputStr, $matches)) {
                    $metadata['resolution'] = $matches[1] . 'x' . $matches[2];
                }
            } catch (Exception $e) {
                $this->logMemoryOperation('video_validation_error', ['error' => $e->getMessage()]);
                if ($this->errorHandler) {
                    $this->errorHandler->handleError("Video validation failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
                }
                return false;
            }
        }
        
        $memoryId = 'memory_' . uniqid();
        $timestamp = date('Y-m-d H:i:s');
        $memoryEntry = [
            'id' => $memoryId,
            'content' => $encrypt ? $this->encryptContent($encodedContent) : $encodedContent,
            'type' => $type,
            'metadata' => array_merge($metadata, ['data_type' => $dataType]),
            'timestamp' => $timestamp,
            'encrypted' => $encrypt,
            'access_count' => 0,
            'last_accessed' => $timestamp,
            'importance_score' => $this->calculateImportanceScore($encodedContent, $metadata),
            'agape_score' => $this->calculateAGAPEAlignment($encodedContent)
        ];
        
        try {
            $stmt = $this->db->prepare("INSERT INTO memory_entries (id, content, type, metadata, timestamp, encrypted, access_count, last_accessed, importance_score, agape_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $memoryId, $memoryEntry['content'], $type, json_encode($memoryEntry['metadata']), $timestamp,
                $encrypt ? 1 : 0, 0, $timestamp, $memoryEntry['importance_score'], $memoryEntry['agape_score']
            ]);
        } catch (Exception $e) {
            $this->logMemoryOperation('db_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Multi-modal memory storage failed: {$e->getMessage()}", ['memory_id' => $memoryId], 'high');
            }
            return false;
        }
        
        if ($type === 'short_term') {
            $this->shortTermMemory[$memoryId] = $memoryEntry;
            $this->storeShortTermMemory($memoryEntry);
        } else {
            $this->longTermMemory[$memoryId] = $memoryEntry;
            $this->storeLongTermMemory($memoryEntry);
        }
        
        $this->logMemoryOperation('store_multi_modal', $memoryEntry);
        return $memoryId;
    }
    
    /**
     * Store agent coordination with comprehensive validation and size limits
     */
    public function storeAgentCoordination($agentId, $coordinationData, $type = 'long_term') {
        $validAgents = ['CURSOR', 'ARA', 'GEMINI', 'COPILOT', 'WOLFIE', 'HUMAN_STEWARD'];
        
        if (!in_array($agentId, $validAgents)) {
            $this->logMemoryOperation('invalid_agent_id', ['agent_id' => $agentId]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Invalid agent ID: $agentId", ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
        // Validate required coordination data fields
        if (!isset($coordinationData['task']) || !isset($coordinationData['status'])) {
            $this->logMemoryOperation('invalid_coordination_data', ['agent_id' => $agentId]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Missing required coordination data fields", ['agent_id' => $agentId], 'medium');
            }
            return false;
        }
        
        // Validate workflow engine
        $validWorkflowEngines = ['prefect', 'temporal', 'none'];
        if (isset($coordinationData['workflow_engine']) && !in_array($coordinationData['workflow_engine'], $validWorkflowEngines)) {
            $this->logMemoryOperation('invalid_workflow_engine', ['workflow_engine' => $coordinationData['workflow_engine']]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Invalid workflow engine: {$coordinationData['workflow_engine']}", ['agent_id' => $agentId], 'medium');
            }
            return false;
        }
        
        $encodedData = json_encode($coordinationData);
        
        if (strlen($encodedData) > $this->memoryThresholds['agent_coordination_max_size'] * 1024) {
            $this->logMemoryOperation('oversized_coordination_data', ['size' => strlen($encodedData)]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Agent coordination data exceeds size limit: " . strlen($encodedData), ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
        $metadata = [
            'agent_id' => htmlspecialchars($agentId, ENT_QUOTES, 'UTF-8'),
            'category' => 'coordination',
            'importance' => 9,
            'workflow_engine' => $coordinationData['workflow_engine'] ?? 'none',
            'task' => htmlspecialchars($coordinationData['task'], ENT_QUOTES, 'UTF-8')
        ];
        
        return $this->storeMemory($encodedData, $type, $metadata, true);
    }
    
    /**
     * Store knowledge graph data for NetworkX/RDFLib integration
     */
    public function storeKnowledgeGraph($graphData, $type = 'long_term', $metadata = []) {
        $metadata = array_map('htmlspecialchars', $metadata);
        $encodedData = json_encode($graphData);
        
        if (strlen($encodedData) > $this->memoryThresholds['multimodal_max_size'] * 1024) {
            $this->logMemoryOperation('oversized_graph_data', ['size' => strlen($encodedData)]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Knowledge graph data exceeds size limit: " . strlen($encodedData), ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
        if (!isset($graphData['nodes']) || !isset($graphData['edges'])) {
            $this->logMemoryOperation('invalid_graph_data', ['metadata' => $metadata]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Invalid knowledge graph data", ['component' => 'memory_management'], 'medium');
            }
            return false;
        }
        
        $metadata = array_merge($metadata, ['data_type' => 'graph', 'category' => 'knowledge_graph']);
        return $this->storeMemory($encodedData, $type, $metadata, true);
    }
    
    /**
     * Retrieve memory with comprehensive error handling and cache optimization
     */
    public function retrieveMemory($memoryId) {
        $cacheFile = $this->workspacePath . 'cache/' . $memoryId . '.json';
        
        // Check file cache first (1 hour cache)
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
            $cached = $this->loadMemoryFromFile($cacheFile);
            if ($cached) {
                $cached['access_count']++;
                $cached['last_accessed'] = date('Y-m-d H:i:s');
                $this->logMemoryOperation('retrieve_memory_from_cache', ['memory_id' => $memoryId, 'source' => 'file_cache']);
                file_put_contents($cacheFile, json_encode($cached, JSON_PRETTY_PRINT));
                if ($cached['type'] === 'short_term') {
                    $this->shortTermMemory[$memoryId] = $cached;
                } else {
                    $this->longTermMemory[$memoryId] = $cached;
                }
                return $cached;
            }
        }
        
        // Check in-memory cache
        if (isset($this->shortTermMemory[$memoryId])) {
            $this->shortTermMemory[$memoryId]['access_count']++;
            $this->shortTermMemory[$memoryId]['last_accessed'] = date('Y-m-d H:i:s');
            $this->logMemoryOperation('retrieve_memory_from_cache', ['memory_id' => $memoryId, 'type' => 'short_term']);
            file_put_contents($cacheFile, json_encode($this->shortTermMemory[$memoryId], JSON_PRETTY_PRINT));
            return $this->shortTermMemory[$memoryId];
        }
        
        if (isset($this->longTermMemory[$memoryId])) {
            $this->longTermMemory[$memoryId]['access_count']++;
            $this->longTermMemory[$memoryId]['last_accessed'] = date('Y-m-d H:i:s');
            $this->logMemoryOperation('retrieve_memory_from_cache', ['memory_id' => $memoryId, 'type' => 'long_term']);
            file_put_contents($cacheFile, json_encode($this->longTermMemory[$memoryId], JSON_PRETTY_PRINT));
            return $this->longTermMemory[$memoryId];
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM memory_entries WHERE id = ?");
            $stmt->execute([$memoryId]);
            
            if ($memory = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $memoryEntry = $this->formatMemoryEntry($memory);
                
                // Update access count
                $stmt = $this->db->prepare("UPDATE memory_entries SET access_count = access_count + 1, last_accessed = ? WHERE id = ?");
                $stmt->execute([date('Y-m-d H:i:s'), $memoryId]);
                
                // Add to appropriate cache
                if ($memory['type'] === 'short_term') {
                    $this->shortTermMemory[$memoryId] = $memoryEntry;
                } else {
                    $this->longTermMemory[$memoryId] = $memoryEntry;
                }
                
                // Cache to file
                file_put_contents($cacheFile, json_encode($memoryEntry, JSON_PRETTY_PRINT));
                
                $this->logMemoryOperation('retrieve_memory', ['memory_id' => $memoryId]);
                return $memoryEntry;
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('retrieve_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory retrieval failed: {$e->getMessage()}", ['memory_id' => $memoryId], 'high');
            }
        }
        
        return false;
    }
    
    /**
     * Search memories with comprehensive error handling
     */
    public function searchMemories($searchTerms, $type = null, $limit = 10) {
        if (is_string($searchTerms)) {
            $searchTerms = preg_split('/\s+/', trim($searchTerms));
        }
        $searchTerms = array_map('htmlspecialchars', array_map('strtolower', (array)$searchTerms));
        $results = [];
        
        try {
            $query = "SELECT * FROM memory_entries WHERE 1=1";
            $params = [];
            
            if ($type) {
                $query .= " AND type = ?";
                $params[] = $type;
            }
            
            if (!empty($searchTerms)) {
                $query .= " AND (";
                $conditions = [];
                foreach ($searchTerms as $term) {
                    $conditions[] = "LOWER(content) LIKE ? OR JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.category')) LIKE ?";
                    $params[] = "%$term%";
                    $params[] = "%$term%";
                }
                $query .= implode(" OR ", $conditions) . ")";
            }
            
            $query .= " ORDER BY importance_score DESC, access_count DESC, agape_score DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $memories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($memories as $memory) {
                $memoryEntry = $this->formatMemoryEntry($memory);
                $searchScore = $this->calculateSearchScore($memoryEntry, $searchTerms);
                if ($searchScore > 0) {
                    $results[] = ['memory' => $memoryEntry, 'search_score' => $searchScore];
                }
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('search_db_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory search failed: {$e->getMessage()}", ['component' => 'memory_management'], 'high');
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
     * Optimized memory cleanup with direct database deletion
     */
    private function cleanupShortTermMemory() {
        uasort($this->shortTermMemory, function($a, $b) {
            $scoreA = $a['importance_score'] + ($a['access_count'] * 0.1) + ($a['agape_score'] * 0.1);
            $scoreB = $b['importance_score'] + ($b['access_count'] * 0.1) + ($b['agape_score'] * 0.1);
            return $scoreB <=> $scoreA;
        });
        
        $keepCount = intval($this->memoryThresholds['short_term_max_size'] * $this->memoryThresholds['cleanup_threshold']);
        $this->shortTermMemory = array_slice($this->shortTermMemory, 0, $keepCount, true);
        
        try {
            $ids = array_keys($this->shortTermMemory);
            if (!empty($ids)) {
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $this->db->prepare("DELETE FROM memory_entries WHERE type = 'short_term' AND id NOT IN ($placeholders)");
                $stmt->execute($ids);
            } else {
                $stmt = $this->db->prepare("DELETE FROM memory_entries WHERE type = 'short_term'");
                $stmt->execute();
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('cleanup_db_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory cleanup failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
            }
        }
        
        $this->persistShortTermMemory();
        $this->logMemoryOperation('cleanup', [
            'type' => 'short_term',
            'kept_count' => count($this->shortTermMemory)
        ]);
    }
    
    /**
     * Optimized long-term memory cleanup
     */
    private function cleanupLongTermMemory() {
        uasort($this->longTermMemory, function($a, $b) {
            $scoreA = $a['importance_score'] + ($a['access_count'] * 0.1) + ($a['agape_score'] * 0.1);
            $scoreB = $b['importance_score'] + ($b['access_count'] * 0.1) + ($b['agape_score'] * 0.1);
            return $scoreB <=> $scoreA;
        });
        
        $keepCount = intval($this->memoryThresholds['long_term_max_size'] * $this->memoryThresholds['cleanup_threshold']);
        $this->longTermMemory = array_slice($this->longTermMemory, 0, $keepCount, true);
        
        try {
            $ids = array_keys($this->longTermMemory);
            if (!empty($ids)) {
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $this->db->prepare("DELETE FROM memory_entries WHERE type = 'long_term' AND id NOT IN ($placeholders)");
                $stmt->execute($ids);
            } else {
                $stmt = $this->db->prepare("DELETE FROM memory_entries WHERE type = 'long_term'");
                $stmt->execute();
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('cleanup_db_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory cleanup failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
            }
        }
        
        $this->persistLongTermMemory();
        $this->logMemoryOperation('cleanup', [
            'type' => 'long_term',
            'kept_count' => count($this->longTermMemory)
        ]);
    }
    
    /**
     * Enhanced memory operation logging with database persistence
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
            
            $stmt = $this->db->prepare("INSERT INTO memory_logs (timestamp, action, data) VALUES (?, ?, ?)");
            $stmt->execute([$logEntry['timestamp'], $action, json_encode($data)]);
        } catch (Exception $e) {
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory log failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
            }
        }
    }
    
    /**
     * Calculate importance score with sophisticated metrics
     */
    private function calculateImportanceScore($content, $metadata) {
        $score = 5; // Base score
        
        // Content length factor
        $score += min(3, strlen($content) / 1000);
        
        // Technical terms boost
        $technicalTerms = ['agape', 'wolfie', 'agi', 'ai', 'system', 'protocol', 'algorithm', 'data', 'analysis'];
        foreach ($technicalTerms as $term) {
            if (stripos($content, $term) !== false) {
                $score += 0.5;
            }
        }
        
        // AGAPE terms boost
        $agapeTerms = ['love', 'patience', 'kindness', 'humility', 'ethical', 'moral', 'virtuous'];
        foreach ($agapeTerms as $term) {
            if (stripos($content, $term) !== false) {
                $score += 0.3;
            }
        }
        
        // Metadata importance
        if (isset($metadata['importance'])) {
            $score += min(2, $metadata['importance']);
        }
        
        if (isset($metadata['priority'])) {
            $score += min(1, $metadata['priority']);
        }
        
        if (isset($metadata['category']) && in_array($metadata['category'], ['coordination', 'multimodal', 'critical'])) {
            $score += 1;
        }
        
        return min(10, max(0, $score));
    }
    
    /**
     * Calculate search score with TF-IDF-like scoring
     */
    private function calculateSearchScore($memory, $searchTerms) {
        $content = strtolower($memory['content']);
        $score = 0;
        
        foreach ($searchTerms as $term) {
            $termCount = substr_count($content, $term);
            if ($termCount > 0) {
                $score += $termCount * 0.1;
            }
        }
        
        // Boost by access count and importance
        $score += $memory['access_count'] * 0.05;
        $score += $memory['importance_score'] * 0.1;
        $score += $memory['agape_score'] * 0.05;
        
        return $score;
    }
    
    /**
     * Calculate AGAPE alignment using shared analyzer
     */
    private function calculateAGAPEAlignment($content) {
        return $this->agapeAnalyzer->calculateAlignment($content);
    }
    
    /**
     * Encrypt content with secure IV
     */
    private function encryptContent($content) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($content, 'AES-256-CBC', hex2bin($this->encryptionKey), 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt content with secure IV
     */
    private function decryptContent($encryptedContent) {
        $data = base64_decode($encryptedContent);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', hex2bin($this->encryptionKey), 0, $iv);
    }
    
    /**
     * Format memory entry
     */
    private function formatMemoryEntry($memory) {
        $memoryEntry = [
            'id' => $memory['id'],
            'content' => $memory['encrypted'] ? $this->decryptContent($memory['content']) : $memory['content'],
            'type' => $memory['type'],
            'metadata' => json_decode($memory['metadata'], true),
            'timestamp' => $memory['timestamp'],
            'encrypted' => (bool)$memory['encrypted'],
            'access_count' => (int)$memory['access_count'],
            'last_accessed' => $memory['last_accessed'],
            'importance_score' => (float)$memory['importance_score'],
            'agape_score' => (float)$memory['agape_score']
        ];
        
        return $memoryEntry;
    }
    
    /**
     * Persist short-term memory to file with optimized single entry support
     */
    private function persistShortTermMemory($memoryEntry = null) {
        try {
            $filePath = $this->workspacePath . 'short_term/memory.json';
            
            if ($memoryEntry) {
                // Optimize to append single entry
                $existingData = $this->loadMemoryFromFile($filePath);
                $existingData[$memoryEntry['id']] = $memoryEntry;
                $data = $existingData;
            } else {
                $data = $this->shortTermMemory;
            }
            
            if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
                throw new Exception("Failed to write to $filePath");
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('persist_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Short-term memory persistence failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
            }
        }
    }
    
    /**
     * Persist long-term memory to file with optimized single entry support
     */
    private function persistLongTermMemory($memoryEntry = null) {
        try {
            $filePath = $this->workspacePath . 'long_term/memory.json';
            
            if ($memoryEntry) {
                // Optimize to append single entry
                $existingData = $this->loadMemoryFromFile($filePath);
                $existingData[$memoryEntry['id']] = $memoryEntry;
                $data = $existingData;
            } else {
                $data = $this->longTermMemory;
            }
            
            if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
                throw new Exception("Failed to write to $filePath");
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('persist_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Long-term memory persistence failed: {$e->getMessage()}", ['component' => 'memory_management'], 'medium');
            }
        }
    }
    
    /**
     * Load memory from file
     */
    private function loadMemoryFromFile($filePath) {
        try {
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                if ($content !== false) {
                    return json_decode($content, true);
                }
            }
        } catch (Exception $e) {
            $this->logMemoryOperation('load_error', ['error' => $e->getMessage()]);
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory load from file failed: {$e->getMessage()}", ['file_path' => $filePath], 'medium');
            }
        }
        
        return [];
    }
    
    /**
     * Validate memory using Phase3IntegrationTestingSystem
     */
    public function validateMemory($memoryId, $phase3System) {
        $memory = $this->retrieveMemory($memoryId);
        if (!$memory) {
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory $memoryId not found", ['component' => 'memory_management'], 'high');
            }
            return false;
        }
        
        $filePath = $this->workspacePath . 'temp/' . $memoryId . '.txt';
        try {
            if (file_put_contents($filePath, $memory['content']) === false) {
                throw new Exception("Failed to write to $filePath");
            }
            
            $validation = $phase3System->validateFileQuality($filePath);
            unlink($filePath);
            
            $this->logMemoryOperation('validate_memory', ['memory_id' => $memoryId, 'validation' => $validation]);
            return $validation['passed'];
        } catch (Exception $e) {
            if ($this->errorHandler) {
                $this->errorHandler->handleError("Memory validation failed: {$e->getMessage()}", ['memory_id' => $memoryId], 'high');
            }
            $this->logMemoryOperation('validation_error', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Get comprehensive memory statistics
     */
    public function getMemoryStatistics() {
        $stats = [
            'short_term_count' => count($this->shortTermMemory),
            'long_term_count' => count($this->longTermMemory),
            'total_memories' => count($this->shortTermMemory) + count($this->longTermMemory),
            'average_importance_score' => $this->calculateAverageImportanceScore(),
            'average_agape_score' => $this->calculateAverageAGAPEScore(),
            'database_connected' => $this->db !== null,
            'encryption_enabled' => !empty($this->encryptionKey),
            'error_handler_available' => $this->errorHandler !== null
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
     * Close the system
     */
    public function close() {
        $this->cleanupShortTermMemory();
        $this->cleanupLongTermMemory();
        
        // Cleanup temporary files
        $tempDir = $this->workspacePath . 'temp/';
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
    $errorHandler = new ErrorHandlingSystemProduction();
    $phase3System = new Phase3IntegrationTestingSystem();
    $memorySystem = new MemoryManagementSystemProduction($errorHandler);
    
    echo "=== Production Memory Management System Test ===\n\n";
    
    // Test basic memory storage
    $memoryId1 = $memorySystem->storeMemory(
        "This is a test memory with AGAPE principles of love and patience",
        'short_term',
        ['category' => 'test', 'importance' => 8],
        true
    );
    
    $memoryId2 = $memorySystem->storeMemory(
        "This is a long-term memory about WOLFIE AGI system development",
        'long_term',
        ['category' => 'development', 'importance' => 9],
        true
    );
    
    echo "Short-term Memory ID: $memoryId1\n";
    echo "Long-term Memory ID: $memoryId2\n";
    
    // Test multi-modal memory
    $multiModalId = $memorySystem->storeMultiModalMemory(
        base64_encode("fake_image_data"),
        'long_term',
        ['data_type' => 'image', 'format' => 'png'],
        true
    );
    echo "Multi-Modal Memory ID: $multiModalId\n";
    
    // Test agent coordination
    $coordinationId = $memorySystem->storeAgentCoordination(
        'CURSOR',
        ['task' => 'code_generation', 'status' => 'completed', 'workflow_engine' => 'prefect'],
        'long_term'
    );
    echo "Agent Coordination ID: $coordinationId\n";
    
    // Test knowledge graph storage
    $graphId = $memorySystem->storeKnowledgeGraph(
        ['nodes' => ['WOLFIE', 'AGAPE'], 'edges' => [['WOLFIE', 'AGAPE', 'aligned_with']]],
        'long_term',
        ['category' => 'knowledge_graph']
    );
    echo "Knowledge Graph Memory ID: $graphId\n";
    
    // Test memory retrieval
    $memory = $memorySystem->retrieveMemory($memoryId1);
    if ($memory) {
        echo "Retrieved Memory: " . substr($memory['content'], 0, 50) . "...\n";
        echo "Importance Score: " . $memory['importance_score'] . "\n";
        echo "AGAPE Score: " . $memory['agape_score'] . "\n";
    }
    
    // Test memory validation
    if ($memoryId1) {
        $validationResult = $memorySystem->validateMemory($memoryId1, $phase3System);
        echo "Memory Validation: " . ($validationResult ? 'SUCCESS' : 'FAILED') . "\n";
    }
    
    // Test memory search
    $searchResults = $memorySystem->searchMemories(['agape', 'love'], 'short_term', 5);
    echo "Search Results: " . count($searchResults) . " found\n";
    
    // Test memory cleanup
    $memorySystem->cleanupShortTermMemory();
    $memorySystem->cleanupLongTermMemory();
    echo "Memory Cleanup: COMPLETED\n";
    
    // Get statistics
    $stats = $memorySystem->getMemoryStatistics();
    echo "\n=== Memory Statistics ===\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    $memorySystem->close();
}
?>