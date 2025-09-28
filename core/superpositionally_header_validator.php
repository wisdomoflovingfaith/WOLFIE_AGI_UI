<?php
/**
 * WOLFIE AGI UI - Superpositionally Header Validator
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Validates superpositionally headers in all project files
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 19:35:00 CDT
 * WHY: To ensure all files have complete superpositionally headers
 * HOW: PHP-based header validation with comprehensive checking
 * PURPOSE: Maintain consistency and metadata across all project files
 * ID: SUPERPOSITIONALLY_HEADER_VALIDATOR_001
 * KEY: HEADER_VALIDATION_SYSTEM
 * SUPERPOSITIONALLY: [HEADER_VALIDATOR_001, WOLFIE_AGI_UI_074]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of header validation for project consistency
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [SUPERPOSITIONALLY_HEADER_VALIDATOR_001, WOLFIE_AGI_UI_074]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - Superpositionally Header Validator
 */

require_once '../config/database_config.php';

class SuperpositionallyHeaderValidator {
    private $db;
    private $requiredHeaders = [
        'WHO', 'WHAT', 'WHERE', 'WHEN', 'WHY', 'HOW', 
        'PURPOSE', 'ID', 'KEY', 'SUPERPOSITIONALLY'
    ];
    private $optionalHeaders = [
        'AGAPE', 'GENESIS', 'MD', 'VERSION', 'STATUS', 'FILE_IDS'
    ];
    private $validationResults = [];
    
    public function __construct() {
        $this->db = getDatabaseConnection();
    }
    
    /**
     * Validate all files in project for superpositionally headers
     */
    public function validateAllFiles($projectRoot = '../') {
        $validationId = 'validation_' . uniqid();
        $startTime = microtime(true);
        
        $validation = [
            'id' => $validationId,
            'project_root' => $projectRoot,
            'status' => 'RUNNING',
            'started_at' => date('Y-m-d H:i:s'),
            'files_processed' => 0,
            'files_valid' => 0,
            'files_invalid' => 0,
            'missing_headers' => [],
            'validation_results' => []
        ];
        
        $this->validationResults[$validationId] = $validation;
        $this->logValidation($validation);
        
        // Scan all files
        $files = $this->scanProjectFiles($projectRoot);
        
        foreach ($files as $file) {
            $fileResult = $this->validateFileHeaders($file);
            $validation['validation_results'][] = $fileResult;
            $validation['files_processed']++;
            
            if ($fileResult['valid']) {
                $validation['files_valid']++;
            } else {
                $validation['files_invalid']++;
                $validation['missing_headers'] = array_merge(
                    $validation['missing_headers'], 
                    $fileResult['missing_headers']
                );
            }
        }
        
        $endTime = microtime(true);
        $validation['status'] = 'COMPLETED';
        $validation['completed_at'] = date('Y-m-d H:i:s');
        $validation['duration'] = round($endTime - $startTime, 2);
        $validation['completion_rate'] = $validation['files_processed'] > 0 ? 
            ($validation['files_valid'] / $validation['files_processed']) * 100 : 0;
        
        $this->validationResults[$validationId] = $validation;
        $this->logValidation($validation);
        
        return $validation;
    }
    
    /**
     * Scan project files recursively
     */
    private function scanProjectFiles($directory) {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, ['php', 'md', 'js', 'html', 'htm', 'css', 'json', 'txt', 'sql'])) {
                    $files[] = $file->getPathname();
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Validate headers in individual file
     */
    private function validateFileHeaders($filePath) {
        $result = [
            'file' => $filePath,
            'valid' => true,
            'missing_headers' => [],
            'present_headers' => [],
            'optional_headers' => [],
            'header_quality_score' => 0,
            'errors' => [],
            'warnings' => []
        ];
        
        // Read file content
        $content = file_get_contents($filePath);
        if ($content === false) {
            $result['valid'] = false;
            $result['errors'][] = 'Could not read file';
            return $result;
        }
        
        // Check required headers
        foreach ($this->requiredHeaders as $header) {
            if ($this->hasHeader($content, $header)) {
                $result['present_headers'][] = $header;
            } else {
                $result['missing_headers'][] = $header;
                $result['valid'] = false;
            }
        }
        
        // Check optional headers
        foreach ($this->optionalHeaders as $header) {
            if ($this->hasHeader($content, $header)) {
                $result['optional_headers'][] = $header;
            }
        }
        
        // Calculate header quality score
        $result['header_quality_score'] = $this->calculateHeaderQualityScore($content, $result);
        
        // Check for AGAPE principles
        if ($this->hasAGAPEHeaders($content)) {
            $result['agape_present'] = true;
        } else {
            $result['warnings'][] = 'AGAPE principles not found in headers';
        }
        
        // Check for superpositionally array
        if ($this->hasSuperpositionallyArray($content)) {
            $result['superpositionally_present'] = true;
        } else {
            $result['warnings'][] = 'SUPERPOSITIONALLY array not found or malformed';
        }
        
        return $result;
    }
    
    /**
     * Check if file has specific header
     */
    private function hasHeader($content, $header) {
        $patterns = [
            "/^\s*\*\*\s*$header\s*:\s*/m",
            "/^\s*$header\s*:\s*/m",
            "/^\s*\/\/\s*$header\s*:\s*/m",
            "/^\s*#\s*$header\s*:\s*/m",
            "/^\s*<!--\s*$header\s*:\s*/m"
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Calculate header quality score
     */
    private function calculateHeaderQualityScore($content, $result) {
        $score = 0;
        $maxScore = 100;
        
        // Required headers (60 points)
        $requiredScore = (count($result['present_headers']) / count($this->requiredHeaders)) * 60;
        $score += $requiredScore;
        
        // Optional headers (20 points)
        $optionalScore = (count($result['optional_headers']) / count($this->optionalHeaders)) * 20;
        $score += $optionalScore;
        
        // AGAPE presence (10 points)
        if ($result['agape_present'] ?? false) {
            $score += 10;
        }
        
        // Superpositionally array (10 points)
        if ($result['superpositionally_present'] ?? false) {
            $score += 10;
        }
        
        return min($maxScore, round($score));
    }
    
    /**
     * Check for AGAPE headers
     */
    private function hasAGAPEHeaders($content) {
        $agapePatterns = [
            '/AGAPE:\s*Love,\s*Patience,\s*Kindness,\s*Humility/i',
            '/AGAPE\s*principles/i',
            '/Love,\s*Patience,\s*Kindness,\s*Humility/i'
        ];
        
        foreach ($agapePatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for superpositionally array
     */
    private function hasSuperpositionallyArray($content) {
        $patterns = [
            '/SUPERPOSITIONALLY:\s*\[[^\]]+\]/i',
            '/SUPERPOSITIONALLY\s*:\s*\[[^\]]+\]/i',
            '/FILE\s*IDS:\s*\[[^\]]+\]/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate header template for file
     */
    public function generateHeaderTemplate($fileType = 'php') {
        $templates = [
            'php' => '<?php
/**
 * WOLFIE AGI UI - [FILE_NAME]
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: [DESCRIPTION]
 * WHERE: [FILE_PATH]
 * WHEN: [CURRENT_DATE_TIME]
 * WHY: [REASON]
 * HOW: [IMPLEMENTATION_METHOD]
 * PURPOSE: [PURPOSE_DESCRIPTION]
 * ID: [UNIQUE_ID]
 * KEY: [KEY_WORDS]
 * SUPERPOSITIONALLY: [SUPERPOSITIONALLY_ARRAY]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: [GENESIS_DESCRIPTION]
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [FILE_IDS_ARRAY]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - [STATUS_DESCRIPTION]
 */',
            'markdown' => '# WOLFIE AGI UI - [FILE_NAME]

**WHO:** Captain WOLFIE (Eric Robin Gerdes)  
**WHAT:** [DESCRIPTION]  
**WHERE:** [FILE_PATH]  
**WHEN:** [CURRENT_DATE_TIME]  
**WHY:** [REASON]  
**HOW:** [IMPLEMENTATION_METHOD]  
**PURPOSE:** [PURPOSE_DESCRIPTION]  
**ID:** [UNIQUE_ID]  
**KEY:** [KEY_WORDS]  
**SUPERPOSITIONALLY:** [SUPERPOSITIONALLY_ARRAY]  

## AGAPE: Love, Patience, Kindness, Humility
## GENESIS: [GENESIS_DESCRIPTION]
## MD: Markdown documentation with [FILE_TYPE] implementation

**FILE IDS:** [FILE_IDS_ARRAY]

**VERSION:** 1.0.0 - [VERSION_DESCRIPTION]  
**STATUS:** Active - [STATUS_DESCRIPTION]',
            'javascript' => '/**
 * WOLFIE AGI UI - [FILE_NAME]
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: [DESCRIPTION]
 * WHERE: [FILE_PATH]
 * WHEN: [CURRENT_DATE_TIME]
 * WHY: [REASON]
 * HOW: [IMPLEMENTATION_METHOD]
 * PURPOSE: [PURPOSE_DESCRIPTION]
 * ID: [UNIQUE_ID]
 * KEY: [KEY_WORDS]
 * SUPERPOSITIONALLY: [SUPERPOSITIONALLY_ARRAY]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: [GENESIS_DESCRIPTION]
 * MD: Markdown documentation with .js implementation
 * 
 * FILE IDS: [FILE_IDS_ARRAY]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - [STATUS_DESCRIPTION]
 */'
        ];
        
        return $templates[$fileType] ?? $templates['php'];
    }
    
    /**
     * Fix missing headers in file
     */
    public function fixFileHeaders($filePath, $fileType = 'php') {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }
        
        $validation = $this->validateFileHeaders($filePath);
        if ($validation['valid']) {
            return true; // No fixes needed
        }
        
        // Generate header template
        $template = $this->generateHeaderTemplate($fileType);
        
        // Replace placeholders with actual values
        $fileName = basename($filePath);
        $filePath = str_replace('\\', '/', $filePath);
        $currentDateTime = date('Y-m-d H:i:s T');
        $uniqueId = strtoupper($fileName) . '_' . uniqid();
        
        $template = str_replace([
            '[FILE_NAME]',
            '[DESCRIPTION]',
            '[FILE_PATH]',
            '[CURRENT_DATE_TIME]',
            '[REASON]',
            '[IMPLEMENTATION_METHOD]',
            '[PURPOSE_DESCRIPTION]',
            '[UNIQUE_ID]',
            '[KEY_WORDS]',
            '[SUPERPOSITIONALLY_ARRAY]',
            '[GENESIS_DESCRIPTION]',
            '[FILE_IDS_ARRAY]',
            '[STATUS_DESCRIPTION]'
        ], [
            $fileName,
            'Auto-generated description',
            $filePath,
            $currentDateTime,
            'Auto-generated reason',
            'Auto-generated method',
            'Auto-generated purpose',
            $uniqueId,
            'AUTO_GENERATED',
            '[' . $uniqueId . ', WOLFIE_AGI_UI_AUTO]',
            'Auto-generated genesis',
            '[' . $uniqueId . ', WOLFIE_AGI_UI_AUTO]',
            'Auto-generated status'
        ], $template);
        
        // Add header to file
        $newContent = $template . "\n\n" . $content;
        
        return file_put_contents($filePath, $newContent) !== false;
    }
    
    /**
     * Get validation results
     */
    public function getValidationResults($validationId = null) {
        if ($validationId) {
            return isset($this->validationResults[$validationId]) ? 
                $this->validationResults[$validationId] : null;
        }
        return $this->validationResults;
    }
    
    /**
     * Get validation statistics
     */
    public function getValidationStatistics() {
        $totalValidations = count($this->validationResults);
        $completedValidations = count(array_filter($this->validationResults, function($v) {
            return $v['status'] === 'COMPLETED';
        }));
        
        $totalFiles = 0;
        $totalValidFiles = 0;
        
        foreach ($this->validationResults as $validation) {
            if ($validation['status'] === 'COMPLETED') {
                $totalFiles += $validation['files_processed'];
                $totalValidFiles += $validation['files_valid'];
            }
        }
        
        return [
            'total_validations' => $totalValidations,
            'completed_validations' => $completedValidations,
            'total_files_processed' => $totalFiles,
            'total_valid_files' => $totalValidFiles,
            'overall_completion_rate' => $totalFiles > 0 ? ($totalValidFiles / $totalFiles) * 100 : 0
        ];
    }
    
    /**
     * Log validation
     */
    private function logValidation($validation) {
        $logFile = __DIR__ . '/../logs/superpositionally_header_validation.log';
        $logEntry = json_encode($validation) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Close database connection
     */
    public function close() {
        $this->db = null;
    }
}

// Example usage and testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $validator = new SuperpositionallyHeaderValidator();
    
    echo "=== WOLFIE AGI UI Superpositionally Header Validator Test ===\n\n";
    
    // Validate all files
    $validation = $validator->validateAllFiles('../');
    
    echo "Validation ID: " . $validation['id'] . "\n";
    echo "Status: " . $validation['status'] . "\n";
    echo "Files Processed: " . $validation['files_processed'] . "\n";
    echo "Files Valid: " . $validation['files_valid'] . "\n";
    echo "Files Invalid: " . $validation['files_invalid'] . "\n";
    echo "Completion Rate: " . number_format($validation['completion_rate'], 2) . "%\n";
    echo "Duration: " . $validation['duration'] . " seconds\n";
    
    // Show statistics
    $stats = $validator->getValidationStatistics();
    echo "\n=== Validation Statistics ===\n";
    echo "Total Validations: " . $stats['total_validations'] . "\n";
    echo "Completed Validations: " . $stats['completed_validations'] . "\n";
    echo "Total Files Processed: " . $stats['total_files_processed'] . "\n";
    echo "Total Valid Files: " . $stats['total_valid_files'] . "\n";
    echo "Overall Completion Rate: " . number_format($stats['overall_completion_rate'], 2) . "%\n";
    
    $validator->close();
}
?>
