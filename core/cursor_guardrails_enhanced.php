<?php
/**
 * WOLFIE AGI UI - Enhanced CURSOR Guardrails Integration
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Enhanced integration system for CURSOR code validation with secure execution
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 20:15:00 CDT
 * WHY: To provide secure, AGAPE-aligned CURSOR code execution
 * HOW: PHP-based integration with restricted Python execution and secure subprocess
 * PURPOSE: Bridge between CURSOR agent and secure code execution
 * ID: CURSOR_GUARDRAILS_ENHANCED_001
 * KEY: CURSOR_SECURE_EXECUTION_SYSTEM
 * SUPERPOSITIONALLY: [CURSOR_GUARDRAILS_ENHANCED_001, WOLFIE_AGI_UI_086]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of secure CURSOR code execution
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [CURSOR_GUARDRAILS_ENHANCED_001, WOLFIE_AGI_UI_086]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - Enhanced CURSOR Guardrails Integration
 */

require_once 'cursor_guardrails_integration.php';
require_once '../config/database_config.php';

class CursorGuardrailsEnhanced extends CursorGuardrailsIntegration {
    private $restrictedPythonScript;
    private $nodeScript;
    private $secureExecutionEnabled;
    
    public function __construct() {
        parent::__construct();
        $this->restrictedPythonScript = __DIR__ . '/../scripts/restricted_exec.py';
        $this->nodeScript = __DIR__ . '/../scripts/restricted_exec.js';
        $this->secureExecutionEnabled = $this->checkSecureExecutionSupport();
    }
    
    /**
     * Check if secure execution is supported
     */
    private function checkSecureExecutionSupport() {
        $pythonSupported = file_exists($this->restrictedPythonScript) && 
                          $this->isPythonAvailable();
        $nodeSupported = $this->isNodeAvailable();
        
        return [
            'python' => $pythonSupported,
            'node' => $nodeSupported,
            'overall' => $pythonSupported || $nodeSupported
        ];
    }
    
    /**
     * Check if Python is available
     */
    private function isPythonAvailable() {
        $output = [];
        $returnCode = 0;
        exec('python --version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Check if Node.js is available
     */
    private function isNodeAvailable() {
        $output = [];
        $returnCode = 0;
        exec('node --version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Enhanced Python code execution with restrictedpython
     */
    protected function executePythonCode($code, $validation) {
        if (!$this->secureExecutionEnabled['python']) {
            return [
                'status' => 'ERROR',
                'message' => 'Secure Python execution not available',
                'validation' => $validation
            ];
        }
        
        try {
            $tempCodeFile = $this->workspacePath . 'cursor_secure_' . uniqid() . '.py';
            file_put_contents($tempCodeFile, $code);
            
            // Use subprocess for secure execution
            $command = [
                'python',
                $this->restrictedPythonScript,
                $tempCodeFile
            ];
            
            $process = proc_open(
                $command,
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w']
                ],
                $pipes,
                $this->workspacePath,
                null,
                ['timeout' => 10]
            );
            
            if (!is_resource($process)) {
                throw new Exception('Failed to create subprocess');
            }
            
            // Close input pipe
            fclose($pipes[0]);
            
            // Read output
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            
            // Close pipes
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            // Get exit code
            $exitCode = proc_close($process);
            
            // Clean up
            unlink($tempCodeFile);
            
            if ($exitCode !== 0) {
                return [
                    'status' => 'ERROR',
                    'message' => 'Python execution failed: ' . trim($error),
                    'validation' => $validation
                ];
            }
            
            // Parse JSON output from restricted script
            $result = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'status' => 'SUCCESS',
                    'output' => trim($output),
                    'validation' => $validation
                ];
            }
            
            return [
                'status' => $result['status'],
                'output' => $result['output'] ?? '',
                'message' => $result['message'] ?? '',
                'validation' => $validation
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Python execution error: ' . $e->getMessage(),
                'validation' => $validation
            ];
        }
    }
    
    /**
     * Enhanced JavaScript code execution with Node.js
     */
    protected function executeJavaScriptCode($code, $validation) {
        if (!$this->secureExecutionEnabled['node']) {
            return [
                'status' => 'ERROR',
                'message' => 'Secure JavaScript execution not available',
                'validation' => $validation
            ];
        }
        
        try {
            $tempCodeFile = $this->workspacePath . 'cursor_secure_' . uniqid() . '.js';
            file_put_contents($tempCodeFile, $code);
            
            // Use subprocess for secure execution
            $command = [
                'node',
                '--max-old-space-size=128',
                '--max-execution-time=10000',
                $tempCodeFile
            ];
            
            $process = proc_open(
                $command,
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w']
                ],
                $pipes,
                $this->workspacePath,
                null,
                ['timeout' => 10]
            );
            
            if (!is_resource($process)) {
                throw new Exception('Failed to create subprocess');
            }
            
            // Close input pipe
            fclose($pipes[0]);
            
            // Read output
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            
            // Close pipes
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            // Get exit code
            $exitCode = proc_close($process);
            
            // Clean up
            unlink($tempCodeFile);
            
            if ($exitCode !== 0) {
                return [
                    'status' => 'ERROR',
                    'message' => 'JavaScript execution failed: ' . trim($error),
                    'validation' => $validation
                ];
            }
            
            return [
                'status' => 'SUCCESS',
                'output' => trim($output),
                'validation' => $validation
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => 'JavaScript execution error: ' . $e->getMessage(),
                'validation' => $validation
            ];
        }
    }
    
    /**
     * Get secure execution status
     */
    public function getSecureExecutionStatus() {
        return $this->secureExecutionEnabled;
    }
    
    /**
     * Test secure execution capabilities
     */
    public function testSecureExecution() {
        $tests = [
            'python' => [
                'code' => 'print("Hello, WOLFIE! This is a secure Python test.")',
                'expected' => 'SUCCESS'
            ],
            'javascript' => [
                'code' => 'console.log("Hello, WOLFIE! This is a secure JavaScript test.");',
                'expected' => 'SUCCESS'
            ]
        ];
        
        $results = [];
        
        foreach ($tests as $language => $test) {
            $validation = $this->validateCursorCode($test['code'], ['language' => $language]);
            $execution = $this->executeSafeCursorCode($test['code'], ['language' => $language]);
            
            $results[$language] = [
                'validation' => $validation,
                'execution' => $execution,
                'secure_execution_available' => $this->secureExecutionEnabled[$language],
                'test_passed' => $execution['status'] === $test['expected']
            ];
        }
        
        return $results;
    }
    
    /**
     * Enhanced statistics with secure execution info
     */
    public function getEnhancedStatistics() {
        $baseStats = $this->getCursorStatistics();
        $secureStats = $this->getSecureExecutionStatus();
        
        return [
            'base_statistics' => $baseStats,
            'secure_execution' => $secureStats,
            'enhancement_status' => [
                'secure_python' => $secureStats['python'],
                'secure_javascript' => $secureStats['node'],
                'overall_secure' => $secureStats['overall']
            ]
        ];
    }
    
    /**
     * Create database table for cursor validations
     */
    public function createDatabaseTable() {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS cursor_validations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    code_hash TEXT NOT NULL,
                    language TEXT NOT NULL,
                    safe INTEGER NOT NULL DEFAULT 0,
                    risk_level TEXT NOT NULL DEFAULT 'LOW',
                    agape_score REAL NOT NULL DEFAULT 0.0,
                    blocked_patterns TEXT,
                    required_approvals TEXT,
                    execution_status TEXT,
                    execution_output TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ";
            
            $this->db->exec($sql);
            
            // Create index for performance
            $this->db->exec("CREATE INDEX IF NOT EXISTS idx_cursor_validations_code_hash ON cursor_validations(code_hash)");
            $this->db->exec("CREATE INDEX IF NOT EXISTS idx_cursor_validations_created_at ON cursor_validations(created_at)");
            
            return true;
        } catch (Exception $e) {
            error_log("Failed to create cursor_validations table: " . $e->getMessage());
            return false;
        }
    }
}

// Example usage and testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $enhancedGuardrails = new CursorGuardrailsEnhanced();
    
    echo "=== WOLFIE AGI UI Enhanced CURSOR Guardrails Integration Test ===\n\n";
    
    // Check secure execution status
    $secureStatus = $enhancedGuardrails->getSecureExecutionStatus();
    echo "--- Secure Execution Status ---\n";
    echo "Python: " . ($secureStatus['python'] ? 'Available' : 'Not Available') . "\n";
    echo "JavaScript: " . ($secureStatus['node'] ? 'Available' : 'Not Available') . "\n";
    echo "Overall: " . ($secureStatus['overall'] ? 'Available' : 'Not Available') . "\n\n";
    
    // Test secure execution
    echo "--- Testing Secure Execution ---\n";
    $testResults = $enhancedGuardrails->testSecureExecution();
    
    foreach ($testResults as $language => $result) {
        echo "Language: $language\n";
        echo "Secure Execution Available: " . ($result['secure_execution_available'] ? 'YES' : 'NO') . "\n";
        echo "Test Passed: " . ($result['test_passed'] ? 'YES' : 'NO') . "\n";
        echo "Execution Status: " . $result['execution']['status'] . "\n";
        if (isset($result['execution']['output'])) {
            echo "Output: " . substr($result['execution']['output'], 0, 100) . "...\n";
        }
        echo "\n";
    }
    
    // Test unsafe code
    echo "--- Testing Unsafe Code Detection ---\n";
    $unsafeCode = 'import os; os.system("rm -rf /")';
    $unsafeValidation = $enhancedGuardrails->validateCursorCode($unsafeCode, ['language' => 'python']);
    echo "Unsafe Code Safe: " . ($unsafeValidation['safe'] ? 'YES' : 'NO') . "\n";
    echo "Risk Level: " . $unsafeValidation['risk_level'] . "\n";
    echo "AGAPE Score: " . $unsafeValidation['agape_score'] . "/10\n";
    
    // Show enhanced statistics
    $enhancedStats = $enhancedGuardrails->getEnhancedStatistics();
    echo "\n=== Enhanced Statistics ===\n";
    echo json_encode($enhancedStats, JSON_PRETTY_PRINT) . "\n";
    
    // Create database table
    echo "\n--- Creating Database Table ---\n";
    $tableCreated = $enhancedGuardrails->createDatabaseTable();
    echo "Database Table Created: " . ($tableCreated ? 'YES' : 'NO') . "\n";
    
    $enhancedGuardrails->close();
}
?>
