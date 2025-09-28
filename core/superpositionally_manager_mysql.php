<?php
/**
 * WOLFIE AGI UI - Superpositionally Manager (MySQL Version)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Manages superpositionally headers using MySQL database for production
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 16:40:00 CDT
 * WHY: To use MySQL for production server instead of CSV
 * HOW: PHP-based MySQL operations with PDO for superpositionally header management
 * HELP: Contact Captain WOLFIE for superpositionally manager questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for data management
 * GENESIS: Foundation of MySQL-based superpositionally header management
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [SUPERPOSITIONALLY_MANAGER_MYSQL_001, WOLFIE_AGI_UI_037]
 * 
 * VERSION: 1.0.0 - The Captain's MySQL Version
 * STATUS: Active - Production MySQL Implementation
 */

require_once '../config/database_config.php';

class SuperpositionallyManagerMySQL {
    private $pdo;
    private $tableName = 'superpositionally_headers';
    
    public function __construct() {
        $this->pdo = getDatabaseConnection();
        $this->createTableIfNotExists();
    }
    
    /**
     * Create table if it doesn't exist
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id VARCHAR(100) NOT NULL UNIQUE,
            title VARCHAR(255) NOT NULL,
            who VARCHAR(100) NOT NULL,
            what TEXT,
            where_location VARCHAR(255),
            when_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            why_purpose TEXT,
            how_method TEXT,
            purpose_description TEXT,
            key_points TEXT,
            superpositionally_data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_file_id (file_id),
            INDEX idx_who (who),
            INDEX idx_title (title),
            INDEX idx_when_created (when_created)
        )";
        
        $this->pdo->exec($sql);
    }
    
    /**
     * Add a new superpositionally header
     */
    public function addHeader(array $headerData) {
        try {
            // Validate required fields
            $requiredFields = ['file_id', 'title', 'who'];
            foreach ($requiredFields as $field) {
                if (empty($headerData[$field])) {
                    throw new Exception("Required field missing: $field");
                }
            }
            
            // Prepare data
            $data = [
                'file_id' => $headerData['file_id'],
                'title' => $headerData['title'],
                'who' => $headerData['who'],
                'what' => $headerData['what'] ?? '',
                'where_location' => $headerData['where_location'] ?? '',
                'why_purpose' => $headerData['why_purpose'] ?? '',
                'how_method' => $headerData['how_method'] ?? '',
                'purpose_description' => $headerData['purpose_description'] ?? '',
                'key_points' => $headerData['key_points'] ?? '',
                'superpositionally_data' => json_encode($headerData['superpositionally_data'] ?? [])
            ];
            
            $sql = "INSERT INTO {$this->tableName} 
                    (file_id, title, who, what, where_location, why_purpose, how_method, purpose_description, key_points, superpositionally_data) 
                    VALUES (:file_id, :title, :who, :what, :where_location, :why_purpose, :how_method, :purpose_description, :key_points, :superpositionally_data)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    who = VALUES(who),
                    what = VALUES(what),
                    where_location = VALUES(where_location),
                    why_purpose = VALUES(why_purpose),
                    how_method = VALUES(how_method),
                    purpose_description = VALUES(purpose_description),
                    key_points = VALUES(key_points),
                    superpositionally_data = VALUES(superpositionally_data),
                    updated_at = CURRENT_TIMESTAMP";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($data);
            
            if ($result) {
                $this->log("Header added/updated: {$data['file_id']}");
                return true;
            } else {
                throw new Exception("Failed to add header");
            }
            
        } catch (Exception $e) {
            $this->log("Error adding header: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Get header by file ID
     */
    public function getHeaderById($fileId) {
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE file_id = :file_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['file_id' => $fileId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['superpositionally_data']) {
                $result['superpositionally_data'] = json_decode($result['superpositionally_data'], true);
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->log("Error getting header by ID: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Search headers with advanced filtering
     */
    public function searchHeaders($query, $field = 'all', $limit = 100, $offset = 0) {
        try {
            $whereClause = '';
            $params = [];
            
            if ($field === 'all') {
                $whereClause = "WHERE file_id LIKE :query 
                               OR title LIKE :query 
                               OR who LIKE :query 
                               OR what LIKE :query 
                               OR where_location LIKE :query 
                               OR why_purpose LIKE :query 
                               OR how_method LIKE :query 
                               OR purpose_description LIKE :query 
                               OR key_points LIKE :query";
                $params['query'] = "%$query%";
            } else {
                $whereClause = "WHERE $field LIKE :query";
                $params['query'] = "%$query%";
            }
            
            $sql = "SELECT * FROM {$this->tableName} 
                    $whereClause 
                    ORDER BY updated_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':query', $params['query'], PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode JSON data
            foreach ($results as &$result) {
                if ($result['superpositionally_data']) {
                    $result['superpositionally_data'] = json_decode($result['superpositionally_data'], true);
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $this->log("Error searching headers: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Get all headers with pagination
     */
    public function getAllHeaders($limit = 100, $offset = 0) {
        try {
            $sql = "SELECT * FROM {$this->tableName} 
                    ORDER BY updated_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode JSON data
            foreach ($results as &$result) {
                if ($result['superpositionally_data']) {
                    $result['superpositionally_data'] = json_decode($result['superpositionally_data'], true);
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $this->log("Error getting all headers: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Update header
     */
    public function updateHeader($fileId, array $newData) {
        try {
            $updateFields = [];
            $params = ['file_id' => $fileId];
            
            $allowedFields = [
                'title', 'who', 'what', 'where_location', 'why_purpose', 
                'how_method', 'purpose_description', 'key_points', 'superpositionally_data'
            ];
            
            foreach ($newData as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    $updateFields[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }
            
            if (empty($updateFields)) {
                throw new Exception("No valid fields to update");
            }
            
            $sql = "UPDATE {$this->tableName} 
                    SET " . implode(', ', $updateFields) . ", updated_at = CURRENT_TIMESTAMP 
                    WHERE file_id = :file_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                $this->log("Header updated: $fileId");
                return true;
            } else {
                throw new Exception("Failed to update header");
            }
            
        } catch (Exception $e) {
            $this->log("Error updating header: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Delete header
     */
    public function deleteHeader($fileId) {
        try {
            $sql = "DELETE FROM {$this->tableName} WHERE file_id = :file_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(['file_id' => $fileId]);
            
            if ($result) {
                $this->log("Header deleted: $fileId");
                return true;
            } else {
                throw new Exception("Failed to delete header");
            }
            
        } catch (Exception $e) {
            $this->log("Error deleting header: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Get statistics
     */
    public function getStatistics() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_headers,
                        COUNT(DISTINCT who) as unique_authors,
                        COUNT(DISTINCT DATE(created_at)) as active_days,
                        MAX(updated_at) as last_updated
                    FROM {$this->tableName}";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $this->log("Error getting statistics: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Search by multiple criteria
     */
    public function advancedSearch($criteria) {
        try {
            $whereClause = [];
            $params = [];
            
            if (!empty($criteria['who'])) {
                $whereClause[] = "who LIKE :who";
                $params['who'] = "%{$criteria['who']}%";
            }
            
            if (!empty($criteria['title'])) {
                $whereClause[] = "title LIKE :title";
                $params['title'] = "%{$criteria['title']}%";
            }
            
            if (!empty($criteria['date_from'])) {
                $whereClause[] = "created_at >= :date_from";
                $params['date_from'] = $criteria['date_from'];
            }
            
            if (!empty($criteria['date_to'])) {
                $whereClause[] = "created_at <= :date_to";
                $params['date_to'] = $criteria['date_to'];
            }
            
            if (!empty($criteria['keyword'])) {
                $whereClause[] = "(title LIKE :keyword OR what LIKE :keyword OR key_points LIKE :keyword)";
                $params['keyword'] = "%{$criteria['keyword']}%";
            }
            
            $whereSQL = empty($whereClause) ? '' : 'WHERE ' . implode(' AND ', $whereClause);
            
            $sql = "SELECT * FROM {$this->tableName} 
                    $whereSQL 
                    ORDER BY updated_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode JSON data
            foreach ($results as &$result) {
                if ($result['superpositionally_data']) {
                    $result['superpositionally_data'] = json_decode($result['superpositionally_data'], true);
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            $this->log("Error in advanced search: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Logging function
     */
    private function log($message, $level = 'INFO') {
        $logFile = __DIR__ . '/../logs/superpositionally_manager_mysql.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] SuperpositionallyManagerMySQL: $message\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($level === 'ERROR') {
            error_log("SuperpositionallyManagerMySQL: $message");
        }
    }
    
    /**
     * Close database connection
     */
    public function close() {
        $this->pdo = null;
    }
}
?>
