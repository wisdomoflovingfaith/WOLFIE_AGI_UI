<?php
/**
 * WOLFIE AGI UI - File Search Engine
 * 
 * WHO: WOLFIE (Captain Eric Robin Gerdes)
 * WHAT: File search engine with superpositionally header integration for WOLFIE AGI UI
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 08:17:00 CDT
 * WHY: To provide advanced file search capabilities using superpositionally headers
 * HOW: PHP-based search engine with CSV integration and caching
 * HELP: Contact WOLFIE for file search engine questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for search
 * GENESIS: Foundation of file search engine protocols
 * MD: Markdown documentation standard with .php implementation
 * 
 * FILE IDS: [FILE_SEARCH_ENGINE_UI_001, WOLFIE_AGI_UI_001, SEARCH_ENGINE_001]
 * 
 * VERSION: 1.0.0
 * STATUS: Active Development - UI Integration
 */

class FileSearchEngine {
    private $superpositionallyManager;
    private $searchCache;
    private $fileSystemPath;
    private $supportedExtensions;
    private $searchHistory;
    
    public function __construct() {
        $this->superpositionallyManager = new SuperpositionallyManager();
        $this->searchCache = [];
        $this->fileSystemPath = 'C:\START\WOLFIE_AGI_UI\\';
        $this->supportedExtensions = ['.md', '.txt', '.php', '.html', '.css', '.js', '.json', '.csv'];
        $this->searchHistory = [];
        $this->initializeEngine();
    }
    
    /**
     * Initialize File Search Engine
     */
    private function initializeEngine() {
        $this->loadSearchHistory();
        $this->logEvent('FILE_SEARCH_ENGINE_INITIALIZED', 'File Search Engine UI online');
    }
    
    /**
     * Process search query
     */
    public function processSearchQuery($query, $options = []) {
        $startTime = microtime(true);
        
        // Default options
        $options = array_merge([
            'header_type' => 'all',
            'file_type' => 'all',
            'limit' => 25,
            'sort_by' => 'relevance',
            'include_content' => false,
            'fuzzy_search' => true
        ], $options);
        
        // Add to search history
        $this->addToSearchHistory($query, $options);
        
        // Perform search
        $results = $this->performSearch($query, $options);
        
        // Calculate search time
        $searchTime = microtime(true) - $startTime;
        
        // Log search
        $this->logEvent('SEARCH_EXECUTED', "Query: {$query}, Results: " . count($results) . ", Time: {$searchTime}s");
        
        return [
            'query' => $query,
            'results' => $results,
            'total_results' => count($results),
            'search_time' => $searchTime,
            'options' => $options
        ];
    }
    
    /**
     * Perform the actual search
     */
    private function performSearch($query, $options) {
        $cacheKey = $this->generateCacheKey($query, $options);
        
        // Check cache first
        if (isset($this->searchCache[$cacheKey])) {
            return $this->searchCache[$cacheKey];
        }
        
        $results = [];
        
        // Search superpositionally headers
        $headerResults = $this->searchSuperpositionallyHeaders($query, $options);
        
        // Search file system if content search is enabled
        if ($options['include_content']) {
            $fileSystemResults = $this->searchFileSystem($query, $options);
            $results = array_merge($headerResults, $fileSystemResults);
        } else {
            $results = $headerResults;
        }
        
        // Apply file type filter
        if ($options['file_type'] !== 'all') {
            $results = $this->filterByFileType($results, $options['file_type']);
        }
        
        // Sort results
        $results = $this->sortResults($results, $options['sort_by']);
        
        // Limit results
        $results = array_slice($results, 0, $options['limit']);
        
        // Cache results
        $this->searchCache[$cacheKey] = $results;
        
        return $results;
    }
    
    /**
     * Search superpositionally headers
     */
    private function searchSuperpositionallyHeaders($query, $options) {
        $headerResults = $this->superpositionallyManager->searchByHeaders($query, $options['header_type'], $options['limit']);
        
        $results = [];
        foreach ($headerResults as $result) {
            $header = $result['header'];
            $results[] = [
                'type' => 'header',
                'id' => $header['id'],
                'title' => $header['title'],
                'who' => $header['who'],
                'what' => $header['what'],
                'where' => $header['where'],
                'when' => $header['when'],
                'why' => $header['why'],
                'how' => $header['how'],
                'purpose' => $header['purpose'],
                'key' => $header['key'],
                'score' => $result['score'],
                'matched_fields' => $result['matched_fields'],
                'file_path' => $this->constructFilePath($header),
                'file_type' => $this->getFileTypeFromPath($this->constructFilePath($header)),
                'last_modified' => $header['date'],
                'size' => $this->getFileSize($this->constructFilePath($header))
            ];
        }
        
        return $results;
    }
    
    /**
     * Search file system
     */
    private function searchFileSystem($query, $options) {
        $results = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->fileSystemPath));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array(strtolower($file->getExtension()), $this->supportedExtensions)) {
                $filePath = $file->getPathname();
                $fileType = '.' . strtolower($file->getExtension());
                
                // Skip if file type filter is active
                if ($options['file_type'] !== 'all' && $fileType !== $options['file_type']) {
                    continue;
                }
                
                // Search file content
                $content = file_get_contents($filePath);
                if (stripos($content, $query) !== false) {
                    $results[] = [
                        'type' => 'file',
                        'file_path' => $filePath,
                        'file_name' => $file->getFilename(),
                        'file_type' => $fileType,
                        'size' => $file->getSize(),
                        'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'score' => $this->calculateContentScore($content, $query),
                        'content_snippet' => $this->extractContentSnippet($content, $query)
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Calculate content score
     */
    private function calculateContentScore($content, $query) {
        $score = 0;
        $queryWords = explode(' ', strtolower($query));
        $contentLower = strtolower($content);
        
        foreach ($queryWords as $word) {
            $word = trim($word, '.,;:!?()[]{}"\'');
            if (strlen($word) < 3) continue;
            
            $count = substr_count($contentLower, $word);
            $score += $count * 2;
            
            // Bonus for exact phrase match
            if (stripos($content, $query) !== false) {
                $score += 10;
            }
        }
        
        return $score;
    }
    
    /**
     * Extract content snippet
     */
    private function extractContentSnippet($content, $query, $length = 200) {
        $pos = stripos($content, $query);
        if ($pos === false) {
            return substr($content, 0, $length) . '...';
        }
        
        $start = max(0, $pos - $length / 2);
        $snippet = substr($content, $start, $length);
        
        if ($start > 0) {
            $snippet = '...' . $snippet;
        }
        if ($start + $length < strlen($content)) {
            $snippet .= '...';
        }
        
        return $snippet;
    }
    
    /**
     * Filter results by file type
     */
    private function filterByFileType($results, $fileType) {
        return array_filter($results, function($result) use ($fileType) {
            return $result['file_type'] === $fileType;
        });
    }
    
    /**
     * Sort results
     */
    private function sortResults($results, $sortBy) {
        switch ($sortBy) {
            case 'relevance':
                usort($results, function($a, $b) {
                    return $b['score'] - $a['score'];
                });
                break;
            case 'title':
                usort($results, function($a, $b) {
                    return strcmp($a['title'] ?? $a['file_name'], $b['title'] ?? $b['file_name']);
                });
                break;
            case 'date':
                usort($results, function($a, $b) {
                    return strcmp($b['last_modified'], $a['last_modified']);
                });
                break;
            case 'size':
                usort($results, function($a, $b) {
                    return $b['size'] - $a['size'];
                });
                break;
        }
        
        return $results;
    }
    
    /**
     * Construct file path from header
     */
    private function constructFilePath($header) {
        // Try to construct path from WHERE field
        if (isset($header['where']) && !empty($header['where'])) {
            $where = $header['where'];
            if (strpos($where, 'C:\\') === 0) {
                return $where;
            }
        }
        
        // Fallback to title-based path
        $title = $header['title'] ?? 'untitled';
        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
        return $this->fileSystemPath . $safeTitle . '.md';
    }
    
    /**
     * Get file type from path
     */
    private function getFileTypeFromPath($path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return '.' . $extension;
    }
    
    /**
     * Get file size
     */
    private function getFileSize($path) {
        if (file_exists($path)) {
            $bytes = filesize($path);
            return $this->formatFileSize($bytes);
        }
        return '0 B';
    }
    
    /**
     * Format file size
     */
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Generate cache key
     */
    private function generateCacheKey($query, $options) {
        return md5($query . serialize($options));
    }
    
    /**
     * Add to search history
     */
    private function addToSearchHistory($query, $options) {
        $this->searchHistory[] = [
            'query' => $query,
            'options' => $options,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Keep only last 100 searches
        if (count($this->searchHistory) > 100) {
            array_shift($this->searchHistory);
        }
        
        $this->saveSearchHistory();
    }
    
    /**
     * Load search history
     */
    private function loadSearchHistory() {
        $historyFile = 'C:\START\WOLFIE_AGI_UI\data\search_history.json';
        if (file_exists($historyFile)) {
            $this->searchHistory = json_decode(file_get_contents($historyFile), true) ?: [];
        }
    }
    
    /**
     * Save search history
     */
    private function saveSearchHistory() {
        $historyFile = 'C:\START\WOLFIE_AGI_UI\data\search_history.json';
        $historyDir = dirname($historyFile);
        if (!is_dir($historyDir)) {
            mkdir($historyDir, 0777, true);
        }
        
        file_put_contents($historyFile, json_encode($this->searchHistory, JSON_PRETTY_PRINT));
    }
    
    /**
     * Get search suggestions
     */
    public function getSearchSuggestions($partialQuery, $limit = 10) {
        $suggestions = [];
        $partialQuery = strtolower($partialQuery);
        
        // Get suggestions from search history
        foreach ($this->searchHistory as $search) {
            if (stripos($search['query'], $partialQuery) !== false) {
                $suggestions[] = $search['query'];
            }
        }
        
        // Get suggestions from headers
        foreach ($this->superpositionallyManager->getHeaders() as $header) {
            foreach (['title', 'who', 'what', 'key'] as $field) {
                if (stripos(strtolower($header[$field]), $partialQuery) !== false) {
                    $suggestions[] = $header[$field];
                }
            }
        }
        
        // Remove duplicates and limit
        $suggestions = array_unique($suggestions);
        $suggestions = array_slice($suggestions, 0, $limit);
        
        return $suggestions;
    }
    
    /**
     * Clear search cache
     */
    public function clearSearchCache() {
        $this->searchCache = [];
        $this->logEvent('SEARCH_CACHE_CLEARED', 'Search cache cleared');
    }
    
    /**
     * Get search statistics
     */
    public function getSearchStatistics() {
        return [
            'total_searches' => count($this->searchHistory),
            'cache_size' => count($this->searchCache),
            'supported_extensions' => $this->supportedExtensions,
            'file_system_path' => $this->fileSystemPath,
            'last_search' => !empty($this->searchHistory) ? end($this->searchHistory)['timestamp'] : null
        ];
    }
    
    /**
     * Get status
     */
    public function getStatus() {
        return [
            'status' => 'OPERATIONAL',
            'superpositionally_manager' => $this->superpositionallyManager->getStatus(),
            'search_cache_active' => !empty($this->searchCache),
            'search_history_loaded' => !empty($this->searchHistory),
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Log event
     */
    private function logEvent($event, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$event}: {$message}\n";
        
        $logPath = 'C:\START\WOLFIE_AGI_UI\logs\file_search_engine.log';
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Initialize File Search Engine
$fileSearchEngine = new FileSearchEngine();

?>
