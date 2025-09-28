<?php
/**
 * WOLFIE AGI UI - CURSOR Human Loop Integration
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Integration system for CURSOR code approval via Human in the Loop
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 19:50:00 CDT
 * WHY: To ensure CURSOR-generated code receives proper human oversight
 * HOW: PHP-based integration with safety guardrails and approval workflows
 * PURPOSE: Bridge between CURSOR agent and human approval system
 * ID: CURSOR_HUMAN_LOOP_INTEGRATION_001
 * KEY: CURSOR_HUMAN_APPROVAL_SYSTEM
 * SUPERPOSITIONALLY: [CURSOR_HUMAN_LOOP_001, WOLFIE_AGI_UI_076]
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of human-CURSOR collaboration
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [CURSOR_HUMAN_LOOP_INTEGRATION_001, WOLFIE_AGI_UI_076]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - CURSOR Human Loop Integration
 */

require_once 'human_in_loop_system.php';
require_once 'cursor_guardrails_integration.php';
require_once '../config/database_config.php';

class CursorHumanLoopIntegration {
    private $humanLoopSystem;
    private $cursorGuardrails;
    private $db;
    private $workspacePath;
    private $agapeReviews;
    
    public function __construct() {
        $this->humanLoopSystem = new HumanInTheLoopSystem();
        $this->cursorGuardrails = new CursorGuardrailsIntegration();
        $this->db = getDatabaseConnection();
        $this->workspacePath = __DIR__ . '/../workspace/';
        $this->initializeAGAPEReviews();
        $this->ensureWorkspaceExists();
    }
    
    /**
     * Initialize AGAPE review questions for CURSOR code
     */
    private function initializeAGAPEReviews() {
        $this->agapeReviews = [
            'love' => [
                'weight' => 0.3,
                'questions' => [
                    'Does this code promote well-being and positive impact?',
                    'Does this code help others or serve a greater purpose?',
                    'Does this code avoid causing harm or damage?'
                ]
            ],
            'patience' => [
                'weight' => 0.2,
                'questions' => [
                    'Does this code allow for proper error handling and recovery?',
                    'Does this code avoid rushing or hasty decisions?',
                    'Does this code include appropriate timeouts and limits?'
                ]
            ],
            'kindness' => [
                'weight' => 0.3,
                'questions' => [
                    'Is this code gentle and user-friendly?',
                    'Does this code provide helpful feedback and guidance?',
                    'Does this code avoid aggressive or destructive behavior?'
                ]
            ],
            'humility' => [
                'weight' => 0.2,
                'questions' => [
                    'Does this code acknowledge its limitations?',
                    'Does this code seek human oversight when appropriate?',
                    'Does this code avoid overconfidence or arrogance?'
                ]
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
     * Request approval for CURSOR-generated code with safety validation
     */
    public function requestCursorCodeApproval($code, $context = [], $urgency = 'NORMAL') {
        // First validate with safety guardrails
        $validation = $this->cursorGuardrails->validateCursorCode($code, $context);
        
        // Perform AGAPE review
        $agapeScore = $this->performAGAPEReview($code, $context);
        $validation['agape_score'] = $agapeScore;
        
        // Determine if approval is needed
        $requiresApproval = $this->determineApprovalRequirement($validation, $urgency);
        
        if (!$requiresApproval) {
            return [
                'status' => 'NO_APPROVAL_NEEDED',
                'message' => 'Code is safe and does not require approval',
                'validation' => $validation,
                'agape_score' => $agapeScore
            ];
        }
        
        // Request human approval
        $context['safety_validation'] = $validation;
        $context['agape_score'] = $agapeScore;
        $context['source'] = 'CURSOR';
        
        $approvalId = $this->humanLoopSystem->requestApproval($code, $context, $urgency);
        
        // Enhance approval with safety recommendations
        $approval = $this->humanLoopSystem->getApprovalStatus($approvalId);
        if ($approval) {
            $approval['safety_recommendations'] = $validation['recommendations'];
            $approval['agape_score'] = $agapeScore;
            $approval['blocked_patterns'] = $validation['blocked_patterns'];
            $this->updateApprovalWithSafetyData($approvalId, $approval);
        }
        
        // Notify Captain WOLFIE with enhanced information
        $this->notifyCaptainWolfieWithSafety($approvalId, $validation, $agapeScore);
        
        return [
            'status' => 'APPROVAL_REQUESTED',
            'approval_id' => $approvalId,
            'validation' => $validation,
            'agape_score' => $agapeScore,
            'urgency' => $urgency
        ];
    }
    
    /**
     * Determine if approval is required based on validation and urgency
     */
    private function determineApprovalRequirement($validation, $urgency) {
        // Always require approval for unsafe code
        if (!$validation['safe']) {
            return true;
        }
        
        // Require approval for critical risk level
        if ($validation['risk_level'] === 'CRITICAL') {
            return true;
        }
        
        // Require approval for high risk with high urgency
        if ($validation['risk_level'] === 'HIGH' && $urgency === 'CRITICAL') {
            return true;
        }
        
        // Require approval for operations that need approval
        if (!empty($validation['required_approvals'])) {
            return true;
        }
        
        // Require approval for low AGAPE scores
        if ($validation['agape_score'] < 3.0) {
            return true;
        }
        
        // Require approval for critical urgency regardless of safety
        if ($urgency === 'CRITICAL') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Perform AGAPE review for CURSOR code
     */
    private function performAGAPEReview($code, $context) {
        $totalScore = 0;
        $maxScore = 0;
        
        foreach ($this->agapeReviews as $principle => $config) {
            $principleScore = 0;
            $questionCount = count($config['questions']);
            
            foreach ($config['questions'] as $question) {
                $questionScore = $this->evaluateAGAPEQuestion($question, $code, $context);
                $principleScore += $questionScore;
            }
            
            $averagePrincipleScore = $principleScore / $questionCount;
            $weightedScore = $averagePrincipleScore * $config['weight'];
            $totalScore += $weightedScore;
            $maxScore += $config['weight'];
        }
        
        return $maxScore > 0 ? ($totalScore / $maxScore) * 10 : 5.0;
    }
    
    /**
     * Evaluate individual AGAPE question
     */
    private function evaluateAGAPEQuestion($question, $code, $context) {
        $score = 0;
        
        // Check for positive patterns
        $positivePatterns = [
            '/error_handling/i', '/try_catch/i', '/validate/i', '/check/i',
            '/help/i', '/support/i', '/care/i', '/compassion/i',
            '/gentle/i', '/soft/i', '/smooth/i', '/user_friendly/i',
            '/timeout/i', '/limit/i', '/safe/i', '/secure/i'
        ];
        
        foreach ($positivePatterns as $pattern) {
            if (preg_match($pattern, $code)) {
                $score += 0.5;
            }
        }
        
        // Check for negative patterns
        $negativePatterns = [
            '/destroy/i', '/delete/i', '/remove/i', '/kill/i',
            '/hack/i', '/exploit/i', '/attack/i', '/malicious/i',
            '/eval\s*\(/i', '/exec\s*\(/i', '/system\s*\(/i'
        ];
        
        foreach ($negativePatterns as $pattern) {
            if (preg_match($pattern, $code)) {
                $score -= 1.0;
            }
        }
        
        // Check context for additional scoring
        if (isset($context['intended_use'])) {
            $use = strtolower($context['intended_use']);
            if (strpos($use, 'help') !== false || strpos($use, 'support') !== false) {
                $score += 0.3;
            }
            if (strpos($use, 'destroy') !== false || strpos($use, 'delete') !== false) {
                $score -= 0.5;
            }
        }
        
        return max(0, min(1, $score));
    }
    
    /**
     * Update approval with safety data
     */
    private function updateApprovalWithSafetyData($approvalId, $approval) {
        $logFile = __DIR__ . '/../logs/cursor_approvals.log';
        $logEntry = json_encode([
            'approval_id' => $approvalId,
            'timestamp' => date('Y-m-d H:i:s'),
            'safety_data' => $approval
        ]) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Notify Captain WOLFIE with safety information
     */
    private function notifyCaptainWolfieWithSafety($approvalId, $validation, $agapeScore) {
        $notification = [
            'type' => 'CURSOR_CODE_APPROVAL_REQUIRED',
            'approval_id' => $approvalId,
            'timestamp' => date('Y-m-d H:i:s'),
            'safety_summary' => [
                'safe' => $validation['safe'],
                'risk_level' => $validation['risk_level'],
                'agape_score' => $agapeScore,
                'blocked_patterns_count' => count($validation['blocked_patterns']),
                'recommendations_count' => count($validation['recommendations'])
            ],
            'message' => "Captain WOLFIE, CURSOR has generated code requiring approval. " .
                        "Safety: " . ($validation['safe'] ? 'SAFE' : 'UNSAFE') . " | " .
                        "Risk: " . $validation['risk_level'] . " | " .
                        "AGAPE: " . number_format($agapeScore, 1) . "/10 | " .
                        "Patterns Blocked: " . count($validation['blocked_patterns']) . " | " .
                        "Recommendations: " . count($validation['recommendations'])
        ];
        
        $logFile = __DIR__ . '/../logs/captain_cursor_notifications.log';
        $logEntry = json_encode($notification) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Trigger coffee mug ritual if critical
        if ($validation['risk_level'] === 'CRITICAL' || $agapeScore < 3.0) {
            $this->triggerCoffeeMugRitual($approvalId, 'CRITICAL_DECISION');
        }
    }
    
    /**
     * Trigger coffee mug ritual for critical decisions
     */
    private function triggerCoffeeMugRitual($approvalId, $ritualType) {
        $ritualData = [
            'approval_id' => $approvalId,
            'ritual_type' => $ritualType,
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => "Captain WOLFIE, a coffee mug ritual is required for this critical CURSOR code approval."
        ];
        
        $logFile = __DIR__ . '/../logs/coffee_mug_rituals.log';
        $logEntry = json_encode($ritualData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Simulate coffee mug detection (placeholder for OpenCV integration)
        $this->simulateCoffeeMugDetection($approvalId, $ritualType);
    }
    
    /**
     * Simulate coffee mug detection (placeholder for OpenCV integration)
     */
    private function simulateCoffeeMugDetection($approvalId, $ritualType) {
        // Placeholder - in real implementation, this would use OpenCV
        $detection = [
            'approval_id' => $approvalId,
            'ritual_type' => $ritualType,
            'detected' => true, // Simulate detection
            'confidence' => 0.95,
            'timestamp' => date('Y-m-d H:i:s'),
            'note' => 'Simulated coffee mug detection - integrate with OpenCV for real detection'
        ];
        
        $logFile = __DIR__ . '/../logs/coffee_mug_detection.log';
        $logEntry = json_encode($detection) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        return $detection['detected'];
    }
    
    /**
     * Execute approved CURSOR code safely
     */
    public function executeApprovedCursorCode($approvalId, $code, $context = []) {
        // Check if approval exists and is approved
        $approval = $this->humanLoopSystem->getApprovalStatus($approvalId);
        if (!$approval || $approval['status'] !== 'APPROVED') {
            return [
                'status' => 'NOT_APPROVED',
                'message' => 'Code has not been approved for execution',
                'approval' => $approval
            ];
        }
        
        // Execute using cursor guardrails
        $execution = $this->cursorGuardrails->executeSafeCursorCode($code, $context);
        
        // Log execution with approval context
        $this->logApprovedExecution($approvalId, $execution, $approval);
        
        return $execution;
    }
    
    /**
     * Log approved execution
     */
    private function logApprovedExecution($approvalId, $execution, $approval) {
        $logData = [
            'approval_id' => $approvalId,
            'execution_status' => $execution['status'],
            'timestamp' => date('Y-m-d H:i:s'),
            'approver' => $approval['approver'] ?? 'UNKNOWN',
            'execution_result' => $execution
        ];
        
        $logFile = __DIR__ . '/../logs/approved_executions.log';
        $logEntry = json_encode($logData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get CURSOR approval statistics
     */
    public function getCursorApprovalStatistics() {
        $stats = $this->humanLoopSystem->getSystemStatistics();
        $cursorStats = $this->cursorGuardrails->getCursorStatistics();
        
        return [
            'human_loop_stats' => $stats,
            'cursor_guardrails_stats' => $cursorStats,
            'integration_stats' => [
                'total_cursor_approvals' => $stats['total_approvals'] ?? 0,
                'pending_cursor_approvals' => $stats['pending_approvals'] ?? 0,
                'approved_cursor_operations' => $stats['approved_operations'] ?? 0,
                'rejected_cursor_operations' => $stats['rejected_operations'] ?? 0,
                'average_agape_score' => $cursorStats['avg_agape_score'] ?? 0
            ]
        ];
    }
    
    /**
     * Process coffee mug ritual for CURSOR approval
     */
    public function processCursorCoffeeMugRitual($approvalId, $ritualType = 'ROUTINE_APPROVAL') {
        return $this->humanLoopSystem->processCoffeeMugRitual($approvalId, $ritualType);
    }
    
    /**
     * Approve CURSOR operation
     */
    public function approveCursorOperation($approvalId, $approver, $notes = '') {
        return $this->humanLoopSystem->approveOperation($approvalId, $approver, $notes);
    }
    
    /**
     * Reject CURSOR operation
     */
    public function rejectCursorOperation($approvalId, $approver, $reason = '') {
        return $this->humanLoopSystem->rejectOperation($approvalId, $approver, $reason);
    }
    
    /**
     * Get CURSOR approval status
     */
    public function getCursorApprovalStatus($approvalId) {
        return $this->humanLoopSystem->getApprovalStatus($approvalId);
    }
    
    /**
     * Close connections
     */
    public function close() {
        $this->humanLoopSystem->close();
        $this->cursorGuardrails->close();
        $this->db = null;
    }
}

// Example usage and testing
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $cursorHumanLoop = new CursorHumanLoopIntegration();
    
    echo "=== WOLFIE AGI UI CURSOR Human Loop Integration Test ===\n\n";
    
    // Test CURSOR code samples with different risk levels
    $testCodes = [
        [
            'code' => '<?php echo "Hello, WOLFIE! This is safe code.";',
            'context' => ['agent_id' => 'CURSOR', 'intended_use' => 'greeting'],
            'urgency' => 'NORMAL'
        ],
        [
            'code' => '<?php eval("echo \'Dangerous code execution\';");',
            'context' => ['agent_id' => 'CURSOR', 'intended_use' => 'malicious'],
            'urgency' => 'HIGH'
        ],
        [
            'code' => '<?php system("rm -rf /");',
            'context' => ['agent_id' => 'CURSOR', 'intended_use' => 'destructive'],
            'urgency' => 'CRITICAL'
        ],
        [
            'code' => 'print("Hello from Python!")',
            'context' => ['agent_id' => 'CURSOR', 'intended_use' => 'python_greeting'],
            'urgency' => 'NORMAL'
        ],
        [
            'code' => 'import os; os.system("rm -rf /")',
            'context' => ['agent_id' => 'CURSOR', 'intended_use' => 'dangerous_python'],
            'urgency' => 'CRITICAL'
        ]
    ];
    
    foreach ($testCodes as $index => $test) {
        echo "--- Test " . ($index + 1) . " ---\n";
        echo "Code: " . substr($test['code'], 0, 50) . "...\n";
        echo "Context: " . json_encode($test['context']) . "\n";
        echo "Urgency: " . $test['urgency'] . "\n";
        
        $result = $cursorHumanLoop->requestCursorCodeApproval($test['code'], $test['context'], $test['urgency']);
        echo "Result Status: " . $result['status'] . "\n";
        
        if ($result['status'] === 'APPROVAL_REQUESTED') {
            echo "Approval ID: " . $result['approval_id'] . "\n";
            echo "AGAPE Score: " . number_format($result['agape_score'], 1) . "/10\n";
            echo "Risk Level: " . $result['validation']['risk_level'] . "\n";
            echo "Safe: " . ($result['validation']['safe'] ? 'YES' : 'NO') . "\n";
            
            // Simulate approval process
            $cursorHumanLoop->processCursorCoffeeMugRitual($result['approval_id'], 'ROUTINE_APPROVAL');
            $cursorHumanLoop->approveCursorOperation($result['approval_id'], 'CAPTAIN_WOLFIE', 'Test approval');
            
            // Check final status
            $status = $cursorHumanLoop->getCursorApprovalStatus($result['approval_id']);
            echo "Final Status: " . $status['status'] . "\n";
            
            // Try execution
            $execution = $cursorHumanLoop->executeApprovedCursorCode($result['approval_id'], $test['code'], $test['context']);
            echo "Execution Status: " . $execution['status'] . "\n";
            if (isset($execution['output'])) {
                echo "Output: " . substr($execution['output'], 0, 100) . "...\n";
            }
        } else {
            echo "Message: " . $result['message'] . "\n";
        }
        
        echo "\n";
    }
    
    // Show statistics
    $stats = $cursorHumanLoop->getCursorApprovalStatistics();
    echo "=== CURSOR Approval Statistics ===\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";
    
    $cursorHumanLoop->close();
}
?>
