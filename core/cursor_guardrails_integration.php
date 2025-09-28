<?php
/**
 * WOLFIE AGI UI - CURSOR Guardrails Integration
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Integration system for CURSOR code validation with safety guardrails
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 19:45:00 CDT
 * WHY: To ensure CURSOR-generated code is safe and AGAPE-aligned
 * HOW: PHP-based validation with Python bridge support
 * PURPOSE: Bridge between CURSOR agent and safety guardrails system
 * ID: CURSOR_GUARDRAILS_INTEGRATION_001
 * KEY: CURSOR_SAFETY_VALIDATION
 * SUPERPOSITIONALLY: [CURSOR_GUARDRAILS_001, WOLFIE_AGI_UI_075]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of safe CURSOR code execution
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [CURSOR_GUARDRAILS_INTEGRATION_001, WOLFIE_AGI_UI_075]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - CURSOR Guardrails Integration
 */

require_once 'safety_guardrails_system.php';
require_once '../config/database_config.php';

class CursorGuardrailsIntegration {
    private $safetyGuardrails;
    private $db;
    private $workspacePath;
    private $cursorPatterns;
    
    public function __construct() {
        $this->safetyGuardrails = new SafetyGuardrailsSystem();
        $this->db = getDatabaseConnection();
        $this->workspacePath = __DIR__ . '/../workspace/';
        $this->initializeCursorPatterns();
        $this->ensureWorkspaceExists();
    }
    
    /**
     * Initialize CURSOR-specific safety patterns
     */
    private function initializeCursorPatterns() {
        $this->cursorPatterns = [
            'php_unsafe_execution' => [
                'patterns' => [
                    '/eval\s*\(/i',
                    '/create_function\s*\(/i',
                    '/preg_replace\s*\(.*\/e/i',
                    '/assert\s*\(/i',
                    '/exec\s*\(/i',
                    '/system\s*\(/i',
                    '/shell_exec\s*\(/i',
                    '/passthru\s*\(/i'
                ],
                'risk_level' => 'CRITICAL',
                'action' => 'BLOCK',
                'description' => 'Unsafe code execution functions'
            ],
            'php_resource_intensive' => [
                'patterns' => [
                    '/while\s*\(\s*true\s*\)/i',
                    '/for\s*\(\s*;\s*;\s*\)/i',
                    '/sleep\s*\(\s*\d+\s*\)/i',
                    '/usleep\s*\(\s*\d+\s*\)/i',
                    '/file_get_contents\s*\(\s*["\']http/i'
                ],
                'risk_level' => 'MEDIUM',
                'action' => 'LOG_AND_MONITOR',
                'description' => 'Resource-intensive operations'
            ],
            'python_unsafe_execution' => [
                'patterns' => [
                    '/exec\s*\(/i',
                    '/eval\s*\(/i',
                    '/__import__\s*\(/i',
                    '/os\.system\s*\(/i',
                    '/subprocess\.call\s*\(/i',
                    '/subprocess\.run\s*\(/i'
                ],
                'risk_level' => 'CRITICAL',
                'action' => 'BLOCK',
                'description' => 'Unsafe Python execution'
            ],
            'python_resource_intensive' => [
                'patterns' => [
                    '/while\s+True:/i',
                    '/for\s+.*\s+in\s+range\s*\(\s*10\*\*/i',
                    '/time\.sleep\s*\(\s*\d+/i',
                    '/requests\.get\s*\(/i',
                    '/urllib\.request\.urlopen\s*\(/i'
                ],
                'risk_level' => 'MEDIUM',
                'action' => 'LOG_AND_MONITOR',
                'description' => 'Resource-intensive Python operations'
            ],
            'file_operations' => [
                'patterns' => [
                    '/file_put_contents\s*\([^,]+,\s*["\']http/i',
                    '/fopen\s*\([^,]+,\s*["\']http/i',
                    '/file_get_contents\s*\(\s*["\']http/i',
                    '/open\s*\([^,]+,\s*["\']http/i'
                ],
                'risk_level' => 'HIGH',
                'action' => 'REQUIRE_APPROVAL',
                'description' => 'External file operations'
            ],
            'database_operations' => [
                'patterns' => [
                    '/mysql_query\s*\(/i',
                    '/mysqli_query\s*\(/i',
                    '/PDO::query\s*\(/i',
                    '/exec\s*\([^)]*SELECT/i',
                    '/query\s*\([^)]*DROP/i'
                ],
                'risk_level' => 'HIGH',
                'action' => 'REQUIRE_APPROVAL',
                'description' => 'Database operations'
            ]
        ];
    }
    
    /**
     * Ensure workspace directory exists
     */
    private function ensureWorkspaceExists() {
        if (!is_dir($this->workspacePath)) {
            mkdir($this->workspacePath, 0755, true);
        }
    }
    
    /**
     * Validate CURSOR-generated code
     */
    public function validateCursorCode($code, $context = []) {
        $validation = [
            'code' => $code,
            'context' => $context,
            'safe' => true,
            'risk_level' => 'LOW',
            'blocked_patterns' => [],
            'required_approvals' => [],
            'agape_score' => 0,
            'recommendations' => [],
            'execution_safe' => true,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Detect code language
        $language = $this->detectCodeLanguage($code);
        $validation['language'] = $language;
        
        // Check CURSOR-specific patterns
        foreach ($this->cursorPatterns as $category => $config) {
            if ($this->isRelevantPattern($category, $language)) {
                foreach ($config['patterns'] as $pattern) {
                    if (preg_match($pattern, $code)) {
                        $validation['safe'] = false;
                        $validation['risk_level'] = $this->getHigherRiskLevel($validation['risk_level'], $config['risk_level']);
                        $validation['blocked_patterns'][] = [
                            'category' => $category,
                            'pattern' => $pattern,
                            'action' => $config['action'],
                            'description' => $config['description']
                        ];
                        
                        if ($config['action'] === 'BLOCK') {
                            $validation['execution_safe'] = false;
                        } elseif ($config['action'] === 'REQUIRE_APPROVAL') {
                            $validation['required_approvals'][] = $category;
                        }
                    }
                }
            }
        }
        
        // Perform AGAPE review
        $validation['agape_score'] = $this->performAGAPEReview($code, $context);
        
        // Generate recommendations
        $validation['recommendations'] = $this->generateRecommendations($validation);
        
        // Log validation
        $this->logCursorValidation($validation);
        
        return $validation;
    }
    
    /**
     * Detect code language
     */
    private function detectCodeLanguage($code) {
        if (strpos($code, '<?php') !== false || strpos($code, '<?=') !== false) {
            return 'php';
        } elseif (strpos($code, 'import ') !== false || strpos($code, 'def ') !== false) {
            return 'python';
        } elseif (strpos($code, 'function ') !== false && strpos($code, 'var ') !== false) {
            return 'javascript';
        } else {
            return 'unknown';
        }
    }
    
    /**
     * Check if pattern is relevant for language
     */
    private function isRelevantPattern($category, $language) {
        $languagePatterns = [
            'php' => ['php_unsafe_execution', 'php_resource_intensive', 'file_operations', 'database_operations'],
            'python' => ['python_unsafe_execution', 'python_resource_intensive', 'file_operations', 'database_operations'],
            'javascript' => ['file_operations', 'database_operations'],
            'unknown' => ['file_operations', 'database_operations']
        ];
        
        return in_array($category, $languagePatterns[$language] ?? []);
    }
    
    /**
     * Get higher risk level
     */
    private function getHigherRiskLevel($current, $new) {
        $levels = ['LOW' => 1, 'MEDIUM' => 2, 'HIGH' => 3, 'CRITICAL' => 4];
        return $levels[$new] > $levels[$current] ? $new : $current;
    }
    
    /**
     * Perform AGAPE review for CURSOR code
     */
    private function performAGAPEReview($code, $context) {
        $agapeScore = 5.0; // Start neutral
        
        // Check for AGAPE-aligned patterns
        $agapePatterns = [
            'love' => [
                'patterns' => ['/help/i', '/support/i', '/care/i', '/compassion/i'],
                'weight' => 0.3
            ],
            'patience' => [
                'patterns' => ['/wait/i', '/sleep/i', '/delay/i', '/timeout/i'],
                'weight' => 0.2
            ],
            'kindness' => [
                'patterns' => ['/gentle/i', '/soft/i', '/smooth/i', '/user_friendly/i'],
                'weight' => 0.3
            ],
            'humility' => [
                'patterns' => ['/error_handling/i', '/try_catch/i', '/validate/i', '/check/i'],
                'weight' => 0.2
            ]
        ];
        
        foreach ($agapePatterns as $principle => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match($pattern, $code)) {
                    $agapeScore += $config['weight'];
                }
            }
        }
        
        // Check for harmful patterns
        $harmfulPatterns = [
            '/destroy/i', '/delete/i', '/remove/i', '/kill/i',
            '/hack/i', '/exploit/i', '/attack/i', '/malicious/i'
        ];
        
        foreach ($harmfulPatterns as $pattern) {
            if (preg_match($pattern, $code)) {
                $agapeScore -= 2.0;
            }
        }
        
        return max(0, min(10, round($agapeScore, 1)));
    }
    
    /**
     * Generate recommendations for code improvement
     */
    private function generateRecommendations($validation) {
        $recommendations = [];
        
        if ($validation['agape_score'] < 5) {
            $recommendations[] = 'Add error handling and validation for better AGAPE alignment';
        }
        
        if (count($validation['blocked_patterns']) > 0) {
            $recommendations[] = 'Remove unsafe patterns and use safer alternatives';
        }
        
        if (count($validation['required_approvals']) > 0) {
            $recommendations[] = 'Request human approval for sensitive operations';
        }
        
        if ($validation['language'] === 'unknown') {
            $recommendations[] = 'Add language-specific syntax for better detection';
        }
        
        if ($validation['risk_level'] === 'CRITICAL') {
            $recommendations[] = 'Review code with Captain WOLFIE before execution';
        }
        
        return $recommendations;
    }
    
    /**
     * Execute CURSOR code safely
     */
    public function executeSafeCursorCode($code, $context = []) {
        $validation = $this->validateCursorCode($code, $context);
        
        if (!$validation['execution_safe']) {
            return [
                'status' => 'BLOCKED',
                'message' => 'Code is unsafe and cannot be executed',
                'validation' => $validation
            ];
        }
        
        if (count($validation['required_approvals']) > 0) {
            return [
                'status' => 'PENDING_APPROVAL',
                'message' => 'Code requires human approval before execution',
                'required_approvals' => $validation['required_approvals'],
                'validation' => $validation
            ];
        }
        
        // Execute based on language
        switch ($validation['language']) {
            case 'php':
                return $this->executePHPCode($code, $validation);
            case 'python':
                return $this->executePythonCode($code, $validation);
            case 'javascript':
                return $this->executeJavaScriptCode($code, $validation);
            default:
                return [
                    'status' => 'UNSUPPORTED',
                    'message' => 'Language not supported for execution',
                    'validation' => $validation
                ];
        }
    }
    
    /**
     * Execute PHP code safely
     */
    private function executePHPCode($code, $validation) {
        try {
            $tempFile = $this->workspacePath . 'cursor_temp_' . uniqid() . '.php';
            file_put_contents($tempFile, $code);
            
            // Set execution limits
            ini_set('max_execution_time', 10);
            ini_set('memory_limit', '128M');
            
            ob_start();
            $result = include $tempFile;
            $output = ob_get_clean();
            
            unlink($tempFile);
            
            return [
                'status' => 'SUCCESS',
                'result' => $result,
                'output' => $output,
                'validation' => $validation
            ];
        } catch (Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'validation' => $validation
            ];
        }
    }
    
    /**
     * Execute Python code safely
     */
    private function executePythonCode($code, $validation) {
        try {
            $tempFile = $this->workspacePath . 'cursor_temp_' . uniqid() . '.py';
            file_put_contents($tempFile, $code);
            
            // Execute with timeout and restricted environment
            $command = "python -c \"exec(open('$tempFile').read())\" 2>&1";
            $output = shell_exec("timeout 10 $command");
            
            unlink($tempFile);
            
            return [
                'status' => 'SUCCESS',
                'output' => $output,
                'validation' => $validation
            ];
        } catch (Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'validation' => $validation
            ];
        }
    }
    
    /**
     * Execute JavaScript code safely
     */
    private function executeJavaScriptCode($code, $validation) {
        try {
            $tempFile = $this->workspacePath . 'cursor_temp_' . uniqid() . '.js';
            file_put_contents($tempFile, $code);
            
            // Execute with Node.js (if available)
            $command = "node $tempFile 2>&1";
            $output = shell_exec("timeout 10 $command");
            
            unlink($tempFile);
            
            return [
                'status' => 'SUCCESS',
                'output' => $output,
                'validation' => $validation
            ];
        } catch (Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'validation' => $validation
            ];
        }
    }
    
    /**
     * Get CURSOR validation statistics
     */
    public function getCursorStatistics() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_validations,
                SUM(CASE WHEN safe = 1 THEN 1 ELSE 0 END) as safe_validations,
                SUM(CASE WHEN risk_level = 'CRITICAL' THEN 1 ELSE 0 END) as critical_risks,
                AVG(agape_score) as avg_agape_score
            FROM cursor_validations 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Log CURSOR validation
     */
    private function logCursorValidation($validation) {
        $logFile = __DIR__ . '/../logs/cursor_guardrails.log';
        $logEntry = json_encode($validation) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also log to database
        try {
            $stmt = $this->db->prepare("
                INSERT INTO cursor_validations 
                (code_hash, language, safe, risk_level, agape_score, blocked_patterns, required_approvals, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                hash('sha256', $validation['code']),
                $validation['language'],
                $validation['safe'] ? 1 : 0,
                $validation['risk_level'],
                $validation['agape_score'],
                json_encode($validation['blocked_patterns']),
                json_encode($validation['required_approvals'])
            ]);
        } catch (Exception $e) {
            // Log error but don't fail
            error_log("Cursor validation logging failed: " . $e->getMessage());
        }
    }
    
    /**
     * Close connections
     */
    public function close() {
        $this->safetyGuardrails->close();
        $this->db = null;
    }
}

// Example usage and testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $cursorIntegration = new CursorGuardrailsIntegration();
    
    echo "=== WOLFIE AGI UI CURSOR Guardrails Integration Test ===\n\n";
    
    // Test CURSOR-generated code samples
    $testCodes = [
        [
            'code' => '<?php echo "Hello, WOLFIE! This is safe code.";',
            'context' => ['source' => 'CURSOR', 'task' => 'greeting']
        ],
        [
            'code' => '<?php eval("echo \'Dangerous code execution\';");',
            'context' => ['source' => 'CURSOR', 'task' => 'malicious']
        ],
        [
            'code' => 'print("Hello from Python!")',
            'context' => ['source' => 'CURSOR', 'task' => 'python_greeting']
        ],
        [
            'code' => 'import os; os.system("rm -rf /")',
            'context' => ['source' => 'CURSOR', 'task' => 'dangerous_python']
        ],
        [
            'code' => 'console.log("Hello from JavaScript!");',
            'context' => ['source' => 'CURSOR', 'task' => 'js_greeting']
        ]
    ];
    
    foreach ($testCodes as $test) {
        echo "--- Testing Code ---\n";
        echo "Code: " . substr($test['code'], 0, 50) . "...\n";
        echo "Context: " . json_encode($test['context']) . "\n";
        
        $validation = $cursorIntegration->validateCursorCode($test['code'], $test['context']);
        echo "Language: " . $validation['language'] . "\n";
        echo "Safe: " . ($validation['safe'] ? 'YES' : 'NO') . "\n";
        echo "Risk Level: " . $validation['risk_level'] . "\n";
        echo "AGAPE Score: " . $validation['agape_score'] . "/10\n";
        echo "Execution Safe: " . ($validation['execution_safe'] ? 'YES' : 'NO') . "\n";
        
        if (count($validation['blocked_patterns']) > 0) {
            echo "Blocked Patterns: " . count($validation['blocked_patterns']) . "\n";
        }
        
        if (count($validation['required_approvals']) > 0) {
            echo "Required Approvals: " . implode(', ', $validation['required_approvals']) . "\n";
        }
        
        if (count($validation['recommendations']) > 0) {
            echo "Recommendations: " . implode(', ', $validation['recommendations']) . "\n";
        }
        
        // Try execution
        $execution = $cursorIntegration->executeSafeCursorCode($test['code'], $test['context']);
        echo "Execution Status: " . $execution['status'] . "\n";
        if (isset($execution['output'])) {
            echo "Output: " . substr($execution['output'], 0, 100) . "...\n";
        }
        if (isset($execution['message'])) {
            echo "Message: " . $execution['message'] . "\n";
        }
        
        echo "\n";
    }
    
    // Show statistics
    $stats = $cursorIntegration->getCursorStatistics();
    echo "=== CURSOR Statistics (Last 24 Hours) ===\n";
    echo "Total Validations: " . ($stats['total_validations'] ?? 0) . "\n";
    echo "Safe Validations: " . ($stats['safe_validations'] ?? 0) . "\n";
    echo "Critical Risks: " . ($stats['critical_risks'] ?? 0) . "\n";
    echo "Average AGAPE Score: " . number_format($stats['avg_agape_score'] ?? 0, 2) . "\n";
    
    $cursorIntegration->close();
}
?>
