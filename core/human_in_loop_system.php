<?php
/**
 * WOLFIE AGI UI - Human in the Loop System
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Human oversight system for edge cases and high-stakes decisions
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 19:20:00 CDT
 * WHY: To add human oversight for edge cases and implement coffee mug rituals
 * HOW: PHP-based human oversight with approval workflows and Sanctuary UI integration
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of human oversight for AGI operations
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [HUMAN_IN_LOOP_SYSTEM_001, WOLFIE_AGI_UI_071]
 * 
 * VERSION: 1.0.0
 * STATUS: Active - Human in the Loop System
 */

require_once '../config/database_config.php';

class HumanInTheLoopSystem {
    private $db;
    private $pendingApprovals = [];
    private $coffeeMugRituals = [];
    private $approvalWorkflows = [];
    
    public function __construct() {
        $this->db = getDatabaseConnection();
        $this->initializeApprovalWorkflows();
        $this->initializeCoffeeMugRituals();
    }
    
    /**
     * Initialize approval workflows for different operation types
     */
    private function initializeApprovalWorkflows() {
        $this->approvalWorkflows = [
            'file_operations' => [
                'critical_files' => ['config', 'database', 'core', 'api'],
                'approval_required' => true,
                'approval_level' => 'CAPTAIN_WOLFIE',
                'timeout' => 300 // 5 minutes
            ],
            'database_operations' => [
                'critical_tables' => ['users', 'channels', 'messages', 'system'],
                'approval_required' => true,
                'approval_level' => 'CAPTAIN_WOLFIE',
                'timeout' => 600 // 10 minutes
            ],
            'system_operations' => [
                'critical_commands' => ['system', 'exec', 'shell_exec'],
                'approval_required' => true,
                'approval_level' => 'CAPTAIN_WOLFIE',
                'timeout' => 900 // 15 minutes
            ],
            'network_operations' => [
                'external_requests' => true,
                'approval_required' => true,
                'approval_level' => 'CAPTAIN_WOLFIE',
                'timeout' => 300 // 5 minutes
            ],
            'agent_operations' => [
                'critical_agents' => ['CAPTAIN_WOLFIE', 'CURSOR', 'ARA', 'COPILOT'],
                'approval_required' => true,
                'approval_level' => 'CAPTAIN_WOLFIE',
                'timeout' => 180 // 3 minutes
            ]
        ];
    }
    
    /**
     * Initialize coffee mug rituals for high-stakes decisions
     */
    private function initializeCoffeeMugRituals() {
        $this->coffeeMugRituals = [
            'CRITICAL_DECISION' => [
                'description' => 'Critical system decision requiring coffee mug ritual',
                'steps' => [
                    '1. Pause all automated operations',
                    '2. Display decision details on Sanctuary UI',
                    '3. Wait for Captain WOLFIE to place coffee mug on desk',
                    '4. Record timestamp of coffee mug placement',
                    '5. Proceed with decision after 30-second contemplation period',
                    '6. Log the ritual completion'
                ],
                'timeout' => 1800, // 30 minutes
                'required' => true
            ],
            'HIGH_STAKES_DECISION' => [
                'description' => 'High-stakes decision requiring coffee mug ritual',
                'steps' => [
                    '1. Display decision details on Sanctuary UI',
                    '2. Wait for Captain WOLFIE to place coffee mug on desk',
                    '3. Record timestamp of coffee mug placement',
                    '4. Proceed with decision after 15-second contemplation period',
                    '5. Log the ritual completion'
                ],
                'timeout' => 900, // 15 minutes
                'required' => true
            ],
            'ROUTINE_APPROVAL' => [
                'description' => 'Routine approval with coffee mug ritual',
                'steps' => [
                    '1. Display approval request on Sanctuary UI',
                    '2. Wait for Captain WOLFIE to place coffee mug on desk',
                    '3. Record timestamp of coffee mug placement',
                    '4. Proceed with approval after 5-second contemplation period',
                    '5. Log the ritual completion'
                ],
                'timeout' => 300, // 5 minutes
                'required' => false
            ]
        ];
    }
    
    /**
     * Request human approval for an operation
     */
    public function requestApproval($operation, $context = [], $urgency = 'NORMAL') {
        $approvalId = 'approval_' . uniqid();
        $workflow = $this->determineWorkflow($operation, $context);
        
        $approval = [
            'id' => $approvalId,
            'operation' => $operation,
            'context' => $context,
            'urgency' => $urgency,
            'workflow' => $workflow,
            'status' => 'PENDING',
            'created_at' => date('Y-m-d H:i:s'),
            'timeout' => $this->approvalWorkflows[$workflow]['timeout'],
            'coffee_mug_ritual' => $this->determineCoffeeMugRitual($urgency, $workflow)
        ];
        
        $this->pendingApprovals[$approvalId] = $approval;
        $this->logApprovalRequest($approval);
        $this->displayOnSanctuaryUI($approval);
        
        return $approvalId;
    }
    
    /**
     * Determine which workflow applies to the operation
     */
    private function determineWorkflow($operation, $context) {
        // Check for file operations
        if (preg_match('/file_|fopen|fwrite|fread|unlink|mkdir|rmdir/', $operation)) {
            return 'file_operations';
        }
        
        // Check for database operations
        if (preg_match('/SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER/', strtoupper($operation))) {
            return 'database_operations';
        }
        
        // Check for system operations
        if (preg_match('/system|exec|shell_exec|passthru|proc_open/', $operation)) {
            return 'system_operations';
        }
        
        // Check for network operations
        if (preg_match('/curl|http|ftp|file_get_contents|fopen.*http/', $operation)) {
            return 'network_operations';
        }
        
        // Check for agent operations
        if (isset($context['agent_id']) && in_array($context['agent_id'], ['CAPTAIN_WOLFIE', 'CURSOR', 'ARA', 'COPILOT'])) {
            return 'agent_operations';
        }
        
        return 'file_operations'; // Default
    }
    
    /**
     * Determine which coffee mug ritual to use
     */
    private function determineCoffeeMugRitual($urgency, $workflow) {
        if ($urgency === 'CRITICAL' || $workflow === 'system_operations') {
            return 'CRITICAL_DECISION';
        } elseif ($urgency === 'HIGH' || $workflow === 'database_operations') {
            return 'HIGH_STAKES_DECISION';
        } else {
            return 'ROUTINE_APPROVAL';
        }
    }
    
    /**
     * Display approval request on Sanctuary UI via Support Meeting system
     */
    private function displayOnSanctuaryUI($approval) {
        $uiData = [
            'type' => 'APPROVAL_REQUEST',
            'approval_id' => $approval['id'],
            'operation' => $approval['operation'],
            'context' => $approval['context'],
            'urgency' => $approval['urgency'],
            'workflow' => $approval['workflow'],
            'coffee_mug_ritual' => $approval['coffee_mug_ritual'],
            'timeout' => $approval['timeout'],
            'timestamp' => $approval['created_at'],
            'support_meeting_triggered' => true,
            'meeting_type' => $this->determineMeetingType($approval['urgency']),
            'meeting_priority' => $this->determineMeetingPriority($approval['urgency'])
        ];
        
        // Trigger support meeting for human approval
        $this->triggerSupportMeeting($uiData);
        
        // Log the approval request
        $logFile = __DIR__ . '/../logs/sanctuary_ui.log';
        $logEntry = json_encode($uiData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Determine meeting type based on urgency
     */
    private function determineMeetingType($urgency) {
        switch ($urgency) {
            case 'CRITICAL':
                return 'EMERGENCY_APPROVAL_MEETING';
            case 'HIGH':
                return 'HIGH_PRIORITY_APPROVAL_MEETING';
            case 'NORMAL':
                return 'ROUTINE_APPROVAL_MEETING';
            default:
                return 'STANDARD_APPROVAL_MEETING';
        }
    }
    
    /**
     * Determine meeting priority based on urgency
     */
    private function determineMeetingPriority($urgency) {
        switch ($urgency) {
            case 'CRITICAL':
                return 'IMMEDIATE';
            case 'HIGH':
                return 'HIGH';
            case 'NORMAL':
                return 'MEDIUM';
            default:
                return 'LOW';
        }
    }
    
    /**
     * Trigger support meeting for human approval
     */
    private function triggerSupportMeeting($uiData) {
        $meetingData = [
            'meeting_id' => 'meeting_' . uniqid(),
            'type' => $uiData['meeting_type'],
            'priority' => $uiData['meeting_priority'],
            'triggered_by' => 'HUMAN_IN_LOOP_SYSTEM',
            'approval_id' => $uiData['approval_id'],
            'operation' => $uiData['operation'],
            'context' => $uiData['context'],
            'urgency' => $uiData['urgency'],
            'coffee_mug_ritual' => $uiData['coffee_mug_ritual'],
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'PENDING_CAPTAIN_WOLFIE',
            'participants' => ['CAPTAIN_WOLFIE'],
            'agenda' => [
                'Review operation requiring approval',
                'Perform coffee mug ritual if required',
                'Make approval decision',
                'Log decision and reasoning'
            ]
        ];
        
        // Log support meeting trigger
        $logFile = __DIR__ . '/../logs/support_meetings.log';
        $logEntry = json_encode($meetingData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // In a real implementation, this would trigger the actual support meeting UI
        // and notify Captain WOLFIE that a meeting is needed
        $this->notifyCaptainWolfie($meetingData);
    }
    
    /**
     * Notify Captain WOLFIE of support meeting requirement
     */
    private function notifyCaptainWolfie($meetingData) {
        $notification = [
            'type' => 'SUPPORT_MEETING_REQUIRED',
            'meeting_id' => $meetingData['meeting_id'],
            'priority' => $meetingData['priority'],
            'operation' => $meetingData['operation'],
            'urgency' => $meetingData['urgency'],
            'coffee_mug_ritual' => $meetingData['coffee_mug_ritual'],
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => "Captain WOLFIE, a support meeting is required for operation approval. " .
                        "Operation: " . $meetingData['operation'] . " | " .
                        "Urgency: " . $meetingData['urgency'] . " | " .
                        "Coffee Mug Ritual: " . $meetingData['coffee_mug_ritual']
        ];
        
        // Log notification
        $logFile = __DIR__ . '/../logs/captain_notifications.log';
        $logEntry = json_encode($notification) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Process coffee mug ritual
     */
    public function processCoffeeMugRitual($approvalId, $ritualType) {
        if (!isset($this->pendingApprovals[$approvalId])) {
            return false;
        }
        
        $approval = $this->pendingApprovals[$approvalId];
        $ritual = $this->coffeeMugRituals[$ritualType];
        
        $ritualData = [
            'approval_id' => $approvalId,
            'ritual_type' => $ritualType,
            'started_at' => date('Y-m-d H:i:s'),
            'steps' => $ritual['steps'],
            'timeout' => $ritual['timeout']
        ];
        
        $this->logCoffeeMugRitual($ritualData);
        
        // In a real implementation, this would wait for the actual coffee mug placement
        // For now, we'll simulate the ritual
        $this->simulateCoffeeMugRitual($approvalId, $ritualType);
        
        return true;
    }
    
    /**
     * Simulate coffee mug ritual (for testing)
     */
    private function simulateCoffeeMugRitual($approvalId, $ritualType) {
        $ritual = $this->coffeeMugRituals[$ritualType];
        $contemplationTime = $this->getContemplationTime($ritualType);
        
        // Simulate contemplation period
        sleep($contemplationTime);
        
        $ritualCompletion = [
            'approval_id' => $approvalId,
            'ritual_type' => $ritualType,
            'completed_at' => date('Y-m-d H:i:s'),
            'contemplation_time' => $contemplationTime,
            'status' => 'COMPLETED'
        ];
        
        $this->logCoffeeMugRitual($ritualCompletion);
    }
    
    /**
     * Get contemplation time for ritual type
     */
    private function getContemplationTime($ritualType) {
        switch ($ritualType) {
            case 'CRITICAL_DECISION':
                return 30; // 30 seconds
            case 'HIGH_STAKES_DECISION':
                return 15; // 15 seconds
            case 'ROUTINE_APPROVAL':
                return 5; // 5 seconds
            default:
                return 10; // 10 seconds
        }
    }
    
    /**
     * Approve an operation
     */
    public function approveOperation($approvalId, $approvedBy = 'CAPTAIN_WOLFIE', $notes = '') {
        if (!isset($this->pendingApprovals[$approvalId])) {
            return false;
        }
        
        $approval = $this->pendingApprovals[$approvalId];
        $approval['status'] = 'APPROVED';
        $approval['approved_by'] = $approvedBy;
        $approval['approved_at'] = date('Y-m-d H:i:s');
        $approval['notes'] = $notes;
        
        $this->pendingApprovals[$approvalId] = $approval;
        $this->logApprovalDecision($approval);
        
        return true;
    }
    
    /**
     * Reject an operation
     */
    public function rejectOperation($approvalId, $rejectedBy = 'CAPTAIN_WOLFIE', $reason = '') {
        if (!isset($this->pendingApprovals[$approvalId])) {
            return false;
        }
        
        $approval = $this->pendingApprovals[$approvalId];
        $approval['status'] = 'REJECTED';
        $approval['rejected_by'] = $rejectedBy;
        $approval['rejected_at'] = date('Y-m-d H:i:s');
        $approval['reason'] = $reason;
        
        $this->pendingApprovals[$approvalId] = $approval;
        $this->logApprovalDecision($approval);
        
        return true;
    }
    
    /**
     * Get pending approvals
     */
    public function getPendingApprovals() {
        return array_filter($this->pendingApprovals, function($approval) {
            return $approval['status'] === 'PENDING';
        });
    }
    
    /**
     * Get approval status
     */
    public function getApprovalStatus($approvalId) {
        return isset($this->pendingApprovals[$approvalId]) ? $this->pendingApprovals[$approvalId] : null;
    }
    
    /**
     * Log approval request
     */
    private function logApprovalRequest($approval) {
        $logFile = __DIR__ . '/../logs/human_in_loop.log';
        $logEntry = json_encode($approval) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log coffee mug ritual
     */
    private function logCoffeeMugRitual($ritualData) {
        $logFile = __DIR__ . '/../logs/coffee_mug_rituals.log';
        $logEntry = json_encode($ritualData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log approval decision
     */
    private function logApprovalDecision($approval) {
        $logFile = __DIR__ . '/../logs/approval_decisions.log';
        $logEntry = json_encode($approval) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get system statistics
     */
    public function getSystemStatistics() {
        $totalApprovals = count($this->pendingApprovals);
        $pendingApprovals = count($this->getPendingApprovals());
        $approvedOperations = count(array_filter($this->pendingApprovals, function($a) {
            return $a['status'] === 'APPROVED';
        }));
        $rejectedOperations = count(array_filter($this->pendingApprovals, function($a) {
            return $a['status'] === 'REJECTED';
        }));
        
        return [
            'total_approvals' => $totalApprovals,
            'pending_approvals' => $pendingApprovals,
            'approved_operations' => $approvedOperations,
            'rejected_operations' => $rejectedOperations,
            'approval_rate' => $totalApprovals > 0 ? ($approvedOperations / $totalApprovals) * 100 : 0
        ];
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
    $humanLoop = new HumanInTheLoopSystem();
    
    echo "=== WOLFIE AGI UI Human in the Loop System Test ===\n\n";
    
    // Test approval requests
    $approval1 = $humanLoop->requestApproval(
        'DROP TABLE users',
        ['table' => 'users', 'operation' => 'DROP'],
        'CRITICAL'
    );
    
    $approval2 = $humanLoop->requestApproval(
        'file_get_contents("config.php")',
        ['file' => 'config.php', 'operation' => 'READ'],
        'NORMAL'
    );
    
    echo "Created approval requests: $approval1, $approval2\n";
    
    // Process coffee mug rituals
    $humanLoop->processCoffeeMugRitual($approval1, 'CRITICAL_DECISION');
    $humanLoop->processCoffeeMugRitual($approval2, 'ROUTINE_APPROVAL');
    
    // Approve operations
    $humanLoop->approveOperation($approval1, 'CAPTAIN_WOLFIE', 'Approved after careful consideration');
    $humanLoop->approveOperation($approval2, 'CAPTAIN_WOLFIE', 'Routine approval');
    
    // Show statistics
    $stats = $humanLoop->getSystemStatistics();
    echo "\n=== Human in the Loop Statistics ===\n";
    echo "Total Approvals: " . $stats['total_approvals'] . "\n";
    echo "Pending Approvals: " . $stats['pending_approvals'] . "\n";
    echo "Approved Operations: " . $stats['approved_operations'] . "\n";
    echo "Rejected Operations: " . $stats['rejected_operations'] . "\n";
    echo "Approval Rate: " . number_format($stats['approval_rate'], 2) . "%\n";
    
    $humanLoop->close();
}
?>
