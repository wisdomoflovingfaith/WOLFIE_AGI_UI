<?php
/**
 * WOLFIE AGI UI - CURSOR Header Validation Integration
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Integration system for CURSOR header validation and fixing
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 20:10:00 CDT
 * WHY: To ensure CURSOR-generated files have proper superpositionally headers
 * HOW: PHP-based integration with header validation and automated fixing
 * PURPOSE: Bridge between CURSOR agent and header validation system
 * ID: CURSOR_HEADER_VALIDATION_INTEGRATION_001
 * KEY: CURSOR_HEADER_VALIDATION_SYSTEM
 * SUPERPOSITIONALLY: [CURSOR_HEADER_VALIDATION_001, WOLFIE_AGI_UI_083]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of CURSOR header validation and fixing
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [CURSOR_HEADER_VALIDATION_INTEGRATION_001, WOLFIE_AGI_UI_083]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - CURSOR Header Validation Integration
 */

require_once 'superpositionally_header_validator.php';
require_once 'cursor_guardrails_integration.php';
require_once 'human_in_loop_system.php';
require_once 'co_agency_rituals_system.php';
require_once '../config/database_config.php';

class CursorHeaderValidationIntegration {
    private $headerValidator;
    private $cursorGuardrails;
    private $humanLoopSystem;
    private $coAgencyRituals;
    private $db;
    private $workspacePath;
    private $validationResults;
    
    public function __construct() {
        $this->headerValidator = new SuperpositionallyHeaderValidator();
        $this->cursorGuardrails = new CursorGuardrailsIntegration();
        $this->humanLoopSystem = new HumanInTheLoopSystem();
        $this->coAgencyRituals = new CoAgencyRitualsSystem();
        $this->db = getDatabaseConnection();
        $this->workspacePath = __DIR__ . '/../workspace/';
        $this->validationResults = [];
        $this->ensureWorkspaceExists();
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
     * Validate and fix CURSOR-generated file content
     */
    public function validateCursorHeaders($cursorContent, $context = []) {
        $validationId = 'cursor_header_validation_' . uniqid();
        $startTime = microtime(true);
        
        $validation = [
            'id' => $validationId,
            'type' => 'CURSOR_HEADER_VALIDATION',
            'cursor_content' => $cursorContent,
            'context' => $context,
            'status' => 'RUNNING',
            'started_at' => date('Y-m-d H:i:s'),
            'files_processed' => 0,
            'files_valid' => 0,
            'files_fixed' => 0,
            'files_requiring_approval' => 0,
            'files_requiring_ritual' => 0,
            'approval_ids' => [],
            'ritual_ids' => [],
            'fixed_contents' => [],
            'errors' => [],
            'warnings' => []
        ];
        
        $this->validationResults[$validationId] = $validation;
        $this->logValidation($validation);
        
        foreach ($cursorContent as $index => $item) {
            $content = $item['content'];
            $fileType = $item['fileType'] ?? 'php';
            $tempPath = $this->saveTempFile($content, $fileType, $index);
            
            try {
                // Safety check first
                $safetyValidation = $this->cursorGuardrails->validateCursorCode($content, $context);
                if (!$safetyValidation['safe']) {
                    $validation['errors'][] = "Safety issue in file $index: " . implode(', ', $safetyValidation['recommendations']);
                    $validation['files_processed']++;
                    $this->cleanupTempFile($tempPath);
                    continue;
                }
                
                // Validate headers
                $fileResult = $this->headerValidator->validateFileHeaders($tempPath);
                $validation['files_processed']++;
                
                if ($fileResult['valid']) {
                    $validation['files_valid']++;
                    $validation['fixed_contents'][] = $content; // Already valid
                } else {
                    // Determine if human approval is needed
                    $requiresApproval = $this->determineApprovalRequirement($fileResult, $context);
                    if ($requiresApproval) {
                        $validation['files_requiring_approval']++;
                        $approvalId = $this->requestHumanApproval($tempPath, $fileResult, $context);
                        if ($approvalId) {
                            $validation['approval_ids'][] = $approvalId;
                        }
                    }
                    
                    // Determine if co-agency ritual is needed
                    $requiresRitual = $this->determineRitualRequirement($fileResult, $context);
                    if ($requiresRitual) {
                        $validation['files_requiring_ritual']++;
                        $solutions = $this->generateHeaderFixSolutions($fileResult, $fileType);
                        $ritualId = $this->initiateCoAgencyRitual($solutions, $fileResult, $context);
                        if ($ritualId) {
                            $validation['ritual_ids'][] = $ritualId;
                        }
                    }
                    
                    // Apply auto-fix if possible
                    if ($this->headerValidator->fixFileHeaders($tempPath, $fileType)) {
                        $fixedContent = file_get_contents($tempPath);
                        $validation['fixed_contents'][] = $fixedContent;
                        $validation['files_fixed']++;
                    } else {
                        $validation['errors'][] = "Failed to fix headers in file $index";
                        $validation['fixed_contents'][] = $content; // Return original if fix failed
                    }
                }
                
                // Add warnings if any
                if (!empty($fileResult['warnings'])) {
                    $validation['warnings'] = array_merge($validation['warnings'], $fileResult['warnings']);
                }
                
            } catch (Exception $e) {
                $validation['errors'][] = "Error processing file $index: " . $e->getMessage();
                $validation['files_processed']++;
            } finally {
                $this->cleanupTempFile($tempPath);
            }
        }
        
        $endTime = microtime(true);
        $validation['status'] = 'COMPLETED';
        $validation['completed_at'] = date('Y-m-d H:i:s');
        $validation['duration'] = round($endTime - $startTime, 2);
        $validation['completion_rate'] = $validation['files_processed'] > 0 ? 
            (($validation['files_valid'] + $validation['files_fixed']) / $validation['files_processed']) * 100 : 0;
        
        $this->validationResults[$validationId] = $validation;
        $this->logValidation($validation);
        
        return $validation;
    }
    
    /**
     * Save temporary file for CURSOR content
     */
    private function saveTempFile($content, $fileType, $index = 0) {
        $extensions = [
            'php' => '.php',
            'markdown' => '.md',
            'javascript' => '.js',
            'html' => '.html',
            'css' => '.css',
            'json' => '.json',
            'txt' => '.txt',
            'sql' => '.sql'
        ];
        
        $ext = $extensions[$fileType] ?? '.php';
        $tempFile = $this->workspacePath . 'cursor_header_' . $index . '_' . uniqid() . $ext;
        file_put_contents($tempFile, $content);
        return $tempFile;
    }
    
    /**
     * Clean up temporary file
     */
    private function cleanupTempFile($tempPath) {
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }
    
    /**
     * Determine if file requires human approval
     */
    private function determineApprovalRequirement($fileResult, $context) {
        // Require approval for files with many missing headers
        if (count($fileResult['missing_headers']) > 5) {
            return true;
        }
        
        // Require approval for low header quality scores
        if ($fileResult['header_quality_score'] < 50) {
            return true;
        }
        
        // Require approval for critical context
        if (isset($context['urgency']) && $context['urgency'] === 'CRITICAL') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if file requires co-agency ritual
     */
    private function determineRitualRequirement($fileResult, $context) {
        // Require ritual for files with complex header issues
        if (count($fileResult['missing_headers']) > 3) {
            return true;
        }
        
        // Require ritual for files with low AGAPE scores
        if (isset($fileResult['agape_present']) && !$fileResult['agape_present']) {
            return true;
        }
        
        // Require ritual for complex context
        if (isset($context['complexity']) && $context['complexity'] === 'HIGH') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Request human approval for file
     */
    private function requestHumanApproval($filePath, $fileResult, $context) {
        $approvalContext = array_merge($context, [
            'file_path' => $filePath,
            'header_result' => $fileResult,
            'source' => 'CURSOR_HEADER_VALIDATION'
        ]);
        
        $urgency = $fileResult['header_quality_score'] < 30 ? 'HIGH' : 'NORMAL';
        
        return $this->humanLoopSystem->requestApproval(
            "Fix headers in CURSOR file: " . basename($filePath),
            $approvalContext,
            $urgency
        );
    }
    
    /**
     * Initiate co-agency ritual for header fixes
     */
    private function initiateCoAgencyRitual($solutions, $fileResult, $context) {
        $ritualContext = array_merge($context, [
            'header_result' => $fileResult,
            'source' => 'CURSOR_HEADER_VALIDATION'
        ]);
        
        $urgency = $fileResult['header_quality_score'] < 40 ? 'HIGH' : 'NORMAL';
        
        return $this->coAgencyRituals->initiateCursorCodeRitual(
            $solutions,
            $ritualContext,
            $urgency
        );
    }
    
    /**
     * Generate alternative header fix solutions
     */
    private function generateHeaderFixSolutions($fileResult, $fileType) {
        $solutions = [];
        
        // Generate solutions based on missing headers
        foreach ($fileResult['missing_headers'] as $header) {
            $solutions[] = "Add missing $header header to file using template for $fileType";
        }
        
        // Generate solutions based on warnings
        if (!empty($fileResult['warnings'])) {
            foreach ($fileResult['warnings'] as $warning) {
                $solutions[] = "Address warning: $warning";
            }
        }
        
        // Generate solutions based on AGAPE issues
        if (isset($fileResult['agape_present']) && !$fileResult['agape_present']) {
            $solutions[] = "Add AGAPE principles section to file";
        }
        
        // Generate solutions based on superpositionally issues
        if (isset($fileResult['superpositionally_present']) && !$fileResult['superpositionally_present']) {
            $solutions[] = "Add SUPERPOSITIONALLY array to file headers";
        }
        
        // Add generic solutions if none generated
        if (empty($solutions)) {
            $solutions[] = "Review and fix header issues in $fileType file";
            $solutions[] = "Regenerate file with complete superpositionally headers";
        }
        
        return $solutions;
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
     * Get CURSOR header validation statistics
     */
    public function getCursorHeaderStatistics() {
        $headerStats = $this->headerValidator->getValidationStatistics();
        $cursorStats = $this->cursorGuardrails->getCursorStatistics();
        
        return [
            'header_validation_stats' => $headerStats,
            'cursor_guardrails_stats' => $cursorStats,
            'integration_stats' => [
                'total_validations' => count($this->validationResults),
                'completed_validations' => count(array_filter($this->validationResults, function($v) {
                    return $v['status'] === 'COMPLETED';
                })),
                'total_files_processed' => array_sum(array_column($this->validationResults, 'files_processed')),
                'total_files_valid' => array_sum(array_column($this->validationResults, 'files_valid')),
                'total_files_fixed' => array_sum(array_column($this->validationResults, 'files_fixed')),
                'total_approvals_triggered' => array_sum(array_map(function($v) {
                    return count($v['approval_ids']);
                }, $this->validationResults)),
                'total_rituals_triggered' => array_sum(array_map(function($v) {
                    return count($v['ritual_ids']);
                }, $this->validationResults))
            ]
        ];
    }
    
    /**
     * Log validation
     */
    private function logValidation($validation) {
        $logFile = __DIR__ . '/../logs/cursor_header_validation.log';
        $logEntry = json_encode($validation) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Close connections
     */
    public function close() {
        $this->headerValidator->close();
        $this->cursorGuardrails->close();
        $this->humanLoopSystem->close();
        $this->coAgencyRituals->close();
        $this->db = null;
    }
}

// Example usage and testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $cursorHeaderValidation = new CursorHeaderValidationIntegration();
    
    echo "=== WOLFIE AGI UI CURSOR Header Validation Integration Test ===\n\n";
    
    // Test CURSOR content samples
    $testCursorContent = [
        [
            'content' => "<?php\necho 'Hello, WOLFIE! This is a test file.';",
            'fileType' => 'php'
        ],
        [
            'content' => "<?php\n/**\n * WHO: Captain WOLFIE\n * WHAT: Test file with partial headers\n */\necho 'Partial headers test';",
            'fileType' => 'php'
        ],
        [
            'content' => "# Markdown test\nContent here without proper headers",
            'fileType' => 'markdown'
        ],
        [
            'content' => "console.log('JavaScript test');\n/* WHO: Test */",
            'fileType' => 'javascript'
        ],
        [
            'content' => "<?php\n/**\n * WHO: Captain WOLFIE (Eric Robin Gerdes)\n * WHAT: Complete test file\n * WHERE: C:\\START\\WOLFIE_AGI_UI\\core\\\n * WHEN: 2025-09-26 20:10:00 CDT\n * WHY: To test complete header validation\n * HOW: PHP-based validation\n * PURPOSE: Test file for validation\n * ID: COMPLETE_TEST_001\n * KEY: COMPLETE_TEST\n * SUPERPOSITIONALLY: [COMPLETE_TEST_001, WOLFIE_AGI_UI_084]\n * \n * AGAPE: Love, Patience, Kindness, Humility\n * GENESIS: Foundation of testing\n * MD: Markdown documentation with .php implementation\n */\n\necho 'Complete headers test';",
            'fileType' => 'php'
        ]
    ];
    
    $context = [
        'agent_id' => 'CURSOR',
        'task' => 'Code generation with header validation',
        'urgency' => 'NORMAL',
        'complexity' => 'MEDIUM'
    ];
    
    echo "--- Testing CURSOR Header Validation ---\n";
    $validation = $cursorHeaderValidation->validateCursorHeaders($testCursorContent, $context);
    
    echo "Validation ID: " . $validation['id'] . "\n";
    echo "Status: " . $validation['status'] . "\n";
    echo "Files Processed: " . $validation['files_processed'] . "\n";
    echo "Files Valid: " . $validation['files_valid'] . "\n";
    echo "Files Fixed: " . $validation['files_fixed'] . "\n";
    echo "Files Requiring Approval: " . $validation['files_requiring_approval'] . "\n";
    echo "Files Requiring Ritual: " . $validation['files_requiring_ritual'] . "\n";
    echo "Approvals Triggered: " . count($validation['approval_ids']) . "\n";
    echo "Rituals Triggered: " . count($validation['ritual_ids']) . "\n";
    echo "Duration: " . $validation['duration'] . " seconds\n";
    echo "Completion Rate: " . number_format($validation['completion_rate'], 2) . "%\n";
    
    if (!empty($validation['errors'])) {
        echo "\n--- Errors ---\n";
        foreach ($validation['errors'] as $error) {
            echo "Error: $error\n";
        }
    }
    
    if (!empty($validation['warnings'])) {
        echo "\n--- Warnings ---\n";
        foreach ($validation['warnings'] as $warning) {
            echo "Warning: $warning\n";
        }
    }
    
    // Show statistics
    $stats = $cursorHeaderValidation->getCursorHeaderStatistics();
    echo "\n=== CURSOR Header Validation Statistics ===\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    $cursorHeaderValidation->close();
}
?>
