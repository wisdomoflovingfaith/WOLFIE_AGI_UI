<?php
/**
 * WOLFIE AGI UI - Superpositionally Manager (CSV Version)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: CSV-based superpositionally header management system
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 14:45:00 CDT
 * WHY: Fast CSV-based storage as requested by Captain WOLFIE
 * HOW: PHP-based CSV management with .lock files
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of CSV-based header management
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [SUPERPOSITIONALLY_MANAGER_CSV_001, WOLFIE_AGI_UI_017]
 * 
 * VERSION: 1.0.0 - The Captain's CSV Version
 * STATUS: Active - Fast CSV Implementation
 */

class SuperpositionallyManagerCSV {
    private $csvFile;
    private $lockFile;
    private $data;
    private $headers;
    
    public function __construct($csvPath = null) {
        $this->csvFile = $csvPath ?: __DIR__ . '/../data/superpositionally_headers.csv';
        $this->lockFile = $this->csvFile . '.lock';
        $this->data = [];
        $this->headers = [
            'path', 'title', 'who', 'what', 'where', 'when', 
            'why', 'how', 'purpose', 'key', 'superpositionally', 'date'
        ];
        $this->initCSVStorage();
        $this->loadDataFromCSV();
    }
    
    /**
     * Initialize CSV Storage
     */
    private function initCSVStorage() {
        $dataDir = dirname($this->csvFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }
        
        if (!file_exists($this->csvFile)) {
            $this->createCSVFile();
        }
    }
    
    /**
     * Create CSV File with Headers
     */
    private function createCSVFile() {
        $handle = fopen($this->csvFile, 'w');
        if ($handle) {
            fputcsv($handle, $this->headers);
            fclose($handle);
        }
    }
    
    /**
     * Load Data from CSV
     */
    private function loadDataFromCSV() {
        if (!file_exists($this->csvFile)) {
            return;
        }
        
        $handle = fopen($this->csvFile, 'r');
        if ($handle === false) {
            return;
        }
        
        $header = fgetcsv($handle);
        $this->data = [];
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= count($this->headers)) {
                $fileData = [];
                foreach ($this->headers as $i => $header) {
                    $fileData[$header] = $row[$i] ?? '';
                }
                $this->data[] = $fileData;
            }
        }
        
        fclose($handle);
    }
    
    /**
     * Save Data to CSV
     */
    private function saveDataToCSV() {
        $this->acquireLock();
        
        $handle = fopen($this->csvFile, 'w');
        if ($handle) {
            fputcsv($handle, $this->headers);
            foreach ($this->data as $row) {
                $csvRow = [];
                foreach ($this->headers as $header) {
                    $csvRow[] = $row[$header] ?? '';
                }
                fputcsv($handle, $csvRow);
            }
            fclose($handle);
        }
        
        $this->releaseLock();
    }
    
    /**
     * Acquire Lock
     */
    private function acquireLock() {
        $timeout = 30; // 30 seconds timeout
        $start = time();
        
        while (file_exists($this->lockFile) && (time() - $start) < $timeout) {
            usleep(100000); // Wait 100ms
        }
        
        if (file_exists($this->lockFile)) {
            throw new Exception('Could not acquire lock for CSV file');
        }
        
        file_put_contents($this->lockFile, getmypid());
    }
    
    /**
     * Release Lock
     */
    private function releaseLock() {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }
    
    /**
     * Add File
     */
    public function addFile($fileData) {
        $this->data[] = $fileData;
        $this->saveDataToCSV();
        return true;
    }
    
    /**
     * Search Files
     */
    public function searchFiles($query, $field = 'all') {
        $results = [];
        
        foreach ($this->data as $file) {
            if ($field === 'all') {
                foreach ($file as $value) {
                    if (stripos($value, $query) !== false) {
                        $results[] = $file;
                        break;
                    }
                }
            } else {
                if (isset($file[$field]) && stripos($file[$field], $query) !== false) {
                    $results[] = $file;
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Get All Files
     */
    public function getAllFiles() {
        return $this->data;
    }
    
    /**
     * Get File by Path
     */
    public function getFileByPath($path) {
        foreach ($this->data as $file) {
            if ($file['path'] === $path) {
                return $file;
            }
        }
        return null;
    }
    
    /**
     * Update File
     */
    public function updateFile($path, $newData) {
        foreach ($this->data as $i => $file) {
            if ($file['path'] === $path) {
                $this->data[$i] = array_merge($file, $newData);
                $this->saveDataToCSV();
                return true;
            }
        }
        return false;
    }
    
    /**
     * Delete File
     */
    public function deleteFile($path) {
        foreach ($this->data as $i => $file) {
            if ($file['path'] === $path) {
                unset($this->data[$i]);
                $this->data = array_values($this->data);
                $this->saveDataToCSV();
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get Statistics
     */
    public function getStatistics() {
        return [
            'total_files' => count($this->data),
            'csv_file' => $this->csvFile,
            'lock_file' => $this->lockFile,
            'status' => 'ACTIVE'
        ];
    }
    
    /**
     * Log Event
     */
    private function logEvent($event, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$event}: {$message}\n";
        $logFile = __DIR__ . '/../logs/superpositionally_manager_csv.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Initialize for testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $manager = new SuperpositionallyManagerCSV();
    echo "✅ SuperpositionallyManagerCSV initialized successfully\n";
    echo "📊 Statistics: " . json_encode($manager->getStatistics()) . "\n";
}
?>
