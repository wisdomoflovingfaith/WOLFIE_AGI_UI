<?php
/**
 * SUPERPOSITIONALLY MANAGER ENHANCED - WOLFIE AGI UI
 * 
 * WHO: WOLFIE (Captain Eric Robin Gerdes)
 * WHAT: Enhanced superpositionally header management system with SQLite backend
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 08:17:00 CDT
 * WHY: Scalable header management with enhanced search, relationships, and performance
 * HOW: PHP-based manager with SQLite database, caching, and advanced search algorithms
 * HELP: Contact WOLFIE for superpositionally manager questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for header management
 * GENESIS: Foundation of enhanced superpositionally header processing protocols
 * MD: Markdown documentation standard with .php implementation
 * 
 * FILE IDS: [SUPERPOSITIONALLY_MANAGER_ENHANCED_001, WOLFIE_AGI_UI_002, ENHANCED_HEADER_MANAGER_001]
 * 
 * VERSION: 2.0.0
 * STATUS: Active Development - Enhanced UI Integration
 */

// Include the base superpositionally manager
require_once 'superpositionally_manager.php';

class SuperpositionallyManagerEnhanced extends SuperpositionallyManager {
    
    // Enhanced properties
    private $db;
    private $dbPath;
    private $logPath;
    private $cacheSize = 100;
    private $searchCache = [];
    private $relationshipCache = [];
    private $indexCache = [];
    
    // Performance metrics
    private $searchCount = 0;
    private $cacheHits = 0;
    private $lastOptimization;
    
    public function __construct() {
        // Define BASE_PATH for portability
        define('BASE_PATH', 'C:\\START\\WOLFIE_AGI_UI\\');
        
        $this->dbPath = BASE_PATH . 'data\\superpositionally_headers.db';
        $this->logPath = BASE_PATH . 'logs\\superpositionally_manager_enhanced.log';
        
        try {
            // Initialize SQLite database
            $this->initializeDatabase();
            
            // Initialize parent class
            parent::__construct();
            
            // Load data from database
            $this->loadHeadersFromDatabase();
            
            // Build enhanced indexes
            $this->buildEnhancedIndexes();
            
            $this->logEvent('ENHANCED_MANAGER_INITIALIZED', 'SuperpositionallyManagerEnhanced initialized with SQLite backend');
            
        } catch (Exception $e) {
            $this->logEvent('INITIALIZATION_ERROR', 'Failed to initialize enhanced manager: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Initialize SQLite database
     */
    private function initializeDatabase() {
        try {
            // Ensure data directory exists
            $dataDir = dirname($this->dbPath);
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0777, true);
            }
            
            // Connect to SQLite database
            $this->db = new PDO('sqlite:' . $this->dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables
            $this->createTables();
            
        } catch (PDOException $e) {
            $this->logEvent('DB_CONNECTION_ERROR', 'Failed to connect to SQLite: ' . $e->getMessage());
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Create database tables
     */
    private function createTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS headers (
                id TEXT PRIMARY KEY,
                superpositionally TEXT,
                date TEXT,
                title TEXT,
                who TEXT,
                what TEXT,
                where TEXT,
                when TEXT,
                why TEXT,
                how TEXT,
                purpose TEXT,
                key TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $this->db->exec($sql);
        
        // Create indexes for better performance
        $indexes = [
            'CREATE INDEX IF NOT EXISTS idx_title ON headers(title)',
            'CREATE INDEX IF NOT EXISTS idx_who ON headers(who)',
            'CREATE INDEX IF NOT EXISTS idx_what ON headers(what)',
            'CREATE INDEX IF NOT EXISTS idx_where ON headers(where)',
            'CREATE INDEX IF NOT EXISTS idx_when ON headers(when)',
            'CREATE INDEX IF NOT EXISTS idx_why ON headers(why)',
            'CREATE INDEX IF NOT EXISTS idx_how ON headers(how)',
            'CREATE INDEX IF NOT EXISTS idx_purpose ON headers(purpose)',
            'CREATE INDEX IF NOT EXISTS idx_key ON headers(key)',
            'CREATE INDEX IF NOT EXISTS idx_superpositionally ON headers(superpositionally)',
            'CREATE INDEX IF NOT EXISTS idx_created_at ON headers(created_at)'
        ];
        
        foreach ($indexes as $indexSql) {
            $this->db->exec($indexSql);
        }
    }
    
    /**
     * Load headers from database
     */
    private function loadHeadersFromDatabase() {
        try {
            $stmt = $this->db->query('SELECT * FROM headers ORDER BY created_at DESC');
            $this->headers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->logEvent('HEADERS_LOADED', 'Loaded ' . count($this->headers) . ' headers from database');
            
        } catch (PDOException $e) {
            $this->logEvent('HEADERS_LOAD_ERROR', 'Failed to load headers from database: ' . $e->getMessage());
            $this->headers = [];
        }
    }
    
    /**
     * Build enhanced indexes
     */
    private function buildEnhancedIndexes() {
        $this->fileIndex = [];
        $this->relationshipMap = [];
        
        foreach ($this->headers as $index => $header) {
            // Build word-based index for each field
            $fields = ['title', 'who', 'what', 'where', 'when', 'why', 'how', 'purpose', 'key'];
            
            foreach ($fields as $field) {
                if (isset($header[$field]) && !empty($header[$field])) {
                    $words = $this->extractWords($header[$field]);
                    foreach ($words as $word) {
                        if (!isset($this->fileIndex[$word])) {
                            $this->fileIndex[$word] = [];
                        }
                        $this->fileIndex[$word][] = $index;
                    }
                }
            }
            
            // Build relationship map
            if (isset($header['superpositionally']) && !empty($header['superpositionally'])) {
                $relatedIds = $this->parseSuperpositionally($header['superpositionally']);
                $this->relationshipMap[$header['id']] = $relatedIds;
            }
        }
        
        $this->logEvent('ENHANCED_INDEXES_BUILT', 'Built enhanced indexes for ' . count($this->headers) . ' headers');
    }
    
    /**
     * Extract words from text
     */
    private function extractWords($text) {
        // Remove special characters and split into words
        $words = preg_split('/[\s\W]+/', strtolower($text));
        return array_filter($words, function($word) {
            return strlen($word) > 2; // Only words longer than 2 characters
        });
    }
    
    /**
     * Parse superpositionally field for relationships
     */
    private function parseSuperpositionally($superpositionally) {
        // Extract IDs from superpositionally field
        preg_match_all('/\[([^\]]+)\]/', $superpositionally, $matches);
        return $matches[1] ?? [];
    }
    
    /**
     * Enhanced search with SQLite backend
     */
    public function searchByHeaders($query, $headerType = 'all', $limit = 25) {
        $this->searchCount++;
        
        // Check cache first
        $cacheKey = md5($query . $headerType . $limit);
        if (isset($this->searchCache[$cacheKey])) {
            $this->cacheHits++;
            return $this->searchCache[$cacheKey];
        }
        
        try {
            $results = $this->performDatabaseSearch($query, $headerType, $limit);
            
            // Apply relevance scoring
            $scoredResults = $this->calculateRelevanceScores($results, $query, $headerType);
            
            // Sort by relevance
            usort($scoredResults, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            // Cache results
            $this->searchCache[$cacheKey] = $scoredResults;
            
            // Manage cache size
            if (count($this->searchCache) > $this->cacheSize) {
                array_shift($this->searchCache);
            }
            
            $this->logEvent('ENHANCED_SEARCH_EXECUTED', "Query: {$query}, Type: {$headerType}, Results: " . count($scoredResults));
            
            return $scoredResults;
            
        } catch (Exception $e) {
            $this->logEvent('ENHANCED_SEARCH_ERROR', 'Search failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Perform database search
     */
    private function performDatabaseSearch($query, $headerType, $limit) {
        $sql = 'SELECT * FROM headers WHERE ';
        $params = [];
        
        if ($headerType === 'all') {
            $conditions = [];
            $searchFields = ['title', 'who', 'what', 'where', 'when', 'why', 'how', 'purpose', 'key'];
            
            foreach ($searchFields as $field) {
                $conditions[] = "{$field} LIKE ?";
                $params[] = "%{$query}%";
            }
            
            $sql .= '(' . implode(' OR ', $conditions) . ')';
        } else {
            $sql .= "{$headerType} LIKE ?";
            $params[] = "%{$query}%";
        }
        
        $sql .= ' ORDER BY updated_at DESC LIMIT ?';
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Calculate relevance scores
     */
    private function calculateRelevanceScores($results, $query, $headerType) {
        $scoredResults = [];
        $queryLower = strtolower($query);
        
        foreach ($results as $result) {
            $score = 0;
            
            if ($headerType === 'all') {
                // Weighted scoring for all fields
                $fieldWeights = [
                    'title' => 10,
                    'who' => 8,
                    'what' => 9,
                    'where' => 6,
                    'when' => 5,
                    'why' => 7,
                    'how' => 7,
                    'purpose' => 8,
                    'key' => 9
                ];
                
                foreach ($fieldWeights as $field => $weight) {
                    if (isset($result[$field]) && !empty($result[$field])) {
                        $fieldValue = strtolower($result[$field]);
                        if (strpos($fieldValue, $queryLower) !== false) {
                            $score += $weight;
                            
                            // Bonus for exact matches
                            if ($fieldValue === $queryLower) {
                                $score += $weight * 2;
                            }
                            
                            // Bonus for word boundary matches
                            if (preg_match('/\b' . preg_quote($queryLower, '/') . '\b/', $fieldValue)) {
                                $score += $weight;
                            }
                        }
                    }
                }
            } else {
                // Single field scoring
                if (isset($result[$headerType]) && !empty($result[$headerType])) {
                    $fieldValue = strtolower($result[$headerType]);
                    if (strpos($fieldValue, $queryLower) !== false) {
                        $score = 10;
                        
                        if ($fieldValue === $queryLower) {
                            $score = 20;
                        }
                    }
                }
            }
            
            $scoredResults[] = [
                'header' => $result,
                'score' => $score
            ];
        }
        
        return $scoredResults;
    }
    
    /**
     * Enhanced file relationships with full data
     */
    public function getFileRelationships($fileId) {
        // Check cache first
        if (isset($this->relationshipCache[$fileId])) {
            return $this->relationshipCache[$fileId];
        }
        
        try {
            if (!isset($this->relationshipMap[$fileId])) {
                return [];
            }
            
            $relatedIds = $this->relationshipMap[$fileId];
            $relatedFiles = [];
            
            foreach ($relatedIds as $relatedId) {
                $relatedFile = $this->getHeaderById($relatedId);
                if ($relatedFile) {
                    $relatedFiles[] = $relatedFile;
                }
            }
            
            // Cache results
            $this->relationshipCache[$fileId] = $relatedFiles;
            
            $this->logEvent('RELATIONSHIPS_RETRIEVED', "Retrieved relationships for {$fileId}: " . count($relatedFiles) . " files");
            
            return $relatedFiles;
            
        } catch (Exception $e) {
            $this->logEvent('RELATIONSHIPS_ERROR', 'Failed to get relationships: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get header by ID
     */
    private function getHeaderById($id) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM headers WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logEvent('HEADER_BY_ID_ERROR', 'Failed to get header by ID: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Add header with enhanced validation
     */
    public function addHeader($headerData) {
        try {
            // Validate required fields
            $requiredFields = ['id', 'title', 'who', 'what', 'where', 'when', 'why', 'how', 'purpose', 'key'];
            foreach ($requiredFields as $field) {
                if (!isset($headerData[$field]) || empty($headerData[$field])) {
                    throw new Exception("Required field '{$field}' is missing");
                }
            }
            
            // Sanitize data
            $sanitizedData = $this->sanitizeHeaderData($headerData);
            
            // Insert into database
            $sql = 'INSERT OR REPLACE INTO headers (id, superpositionally, date, title, who, what, where, when, why, how, purpose, key) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $params = [
                $sanitizedData['id'],
                $sanitizedData['superpositionally'] ?? '',
                $sanitizedData['date'] ?? date('Y-m-d H:i:s'),
                $sanitizedData['title'],
                $sanitizedData['who'],
                $sanitizedData['what'],
                $sanitizedData['where'],
                $sanitizedData['when'],
                $sanitizedData['why'],
                $sanitizedData['how'],
                $sanitizedData['purpose'],
                $sanitizedData['key']
            ];
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Update local data
            $this->loadHeadersFromDatabase();
            $this->buildEnhancedIndexes();
            
            $this->logEvent('HEADER_ADDED', "Header added: {$sanitizedData['id']}");
            
            return $sanitizedData['id'];
            
        } catch (Exception $e) {
            $this->logEvent('HEADER_ADD_ERROR', 'Failed to add header: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Sanitize header data
     */
    private function sanitizeHeaderData($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            // Remove control characters and sanitize
            $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $sanitized[$key] = trim($value);
        }
        
        return $sanitized;
    }
    
    /**
     * Get enhanced statistics
     */
    public function getEnhancedStatistics() {
        try {
            $stats = [
                'total_headers' => count($this->headers),
                'search_count' => $this->searchCount,
                'cache_hits' => $this->cacheHits,
                'cache_hit_rate' => $this->searchCount > 0 ? round(($this->cacheHits / $this->searchCount) * 100, 2) : 0,
                'cache_size' => count($this->searchCache),
                'relationship_count' => count($this->relationshipMap),
                'index_size' => count($this->fileIndex),
                'last_optimization' => $this->lastOptimization,
                'database_size' => file_exists($this->dbPath) ? filesize($this->dbPath) : 0
            ];
            
            // Get field distribution
            $fieldStats = [];
            $fields = ['title', 'who', 'what', 'where', 'when', 'why', 'how', 'purpose', 'key'];
            
            foreach ($fields as $field) {
                $count = 0;
                foreach ($this->headers as $header) {
                    if (!empty($header[$field])) {
                        $count++;
                    }
                }
                $fieldStats[$field] = $count;
            }
            
            $stats['field_distribution'] = $fieldStats;
            
            return $stats;
            
        } catch (Exception $e) {
            $this->logEvent('STATISTICS_ERROR', 'Failed to get statistics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Optimize database
     */
    public function optimizeDatabase() {
        try {
            $this->db->exec('VACUUM');
            $this->db->exec('ANALYZE');
            
            $this->lastOptimization = time();
            
            $this->logEvent('DATABASE_OPTIMIZED', 'Database optimized successfully');
            
        } catch (Exception $e) {
            $this->logEvent('OPTIMIZATION_ERROR', 'Failed to optimize database: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear caches
     */
    public function clearCaches() {
        $this->searchCache = [];
        $this->relationshipCache = [];
        $this->indexCache = [];
        
        $this->logEvent('CACHES_CLEARED', 'All caches cleared');
    }
    
    /**
     * Get status for UI integration
     */
    public function getStatus() {
        return [
            'status' => 'ACTIVE',
            'version' => '2.0.0',
            'database_connected' => $this->db !== null,
            'total_headers' => count($this->headers),
            'search_count' => $this->searchCount,
            'cache_hit_rate' => $this->searchCount > 0 ? round(($this->cacheHits / $this->searchCount) * 100, 2) : 0,
            'last_activity' => time()
        ];
    }
    
    /**
     * Enhanced logging
     */
    private function logEvent($event, $message) {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $logEntry = "[{$timestamp}] [ENHANCED] {$event}: {$message}\n";
            
            // Ensure log directory exists
            $logDir = dirname($this->logPath);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
            
        } catch (Exception $e) {
            error_log("Enhanced logging error: " . $e->getMessage());
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        if ($this->db) {
            $this->db = null;
        }
    }
}

?>