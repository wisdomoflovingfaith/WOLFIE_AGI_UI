<?php
/**
 * WOLFIE AGI UI - Secure API Endpoint Handler
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Secure API endpoint handler with XSS protection and MySQL integration
 * WHERE: C:\START\WOLFIE_AGI_UI\api\
 * WHEN: 2025-09-26 17:00:00 CDT
 * WHY: To fix XSS vulnerabilities and provide secure API for production
 * HOW: PHP-based API handler with comprehensive security and MySQL backend
 * HELP: Contact Captain WOLFIE for API endpoint handler questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for secure API handling
 * GENESIS: Foundation of secure API endpoint handling protocols
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [API_ENDPOINT_HANDLER_SECURE_001, WOLFIE_AGI_UI_040]
 * 
 * VERSION: 2.0.0 - The Captain's Secure API Handler
 * STATUS: Active - XSS Protected, MySQL Production Ready
 */

// Set secure headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include core components
require_once '../core/agi_core_engine_secure.php';
require_once '../core/superpositionally_manager_mysql.php';
require_once '../core/wolfie_channel_system_secure.php';
require_once '../config/database_config.php';
require_once '../core/file_search_engine.php';
require_once '../core/multi_agent_coordinator_mysql.php';
require_once '../core/meeting_mode_processor.php';
require_once '../core/integrated_meeting_coordinator.php';
require_once '../core/no_casino_mode_processor.php';

class APIEndpointHandlerSecure {
    private $coreEngine;
    private $superpositionallyManager;
    private $fileSearchEngine;
    private $multiAgentCoordinator;
    private $meetingModeProcessor;
    private $integratedMeetingCoordinator;
    private $noCasinoModeProcessor;
    private $channelSystem;
    
    private $requestData;
    private $response;
    private $logPath;
    private $dataPath;
    
    // Security settings
    private $maxMessageLength = 1000;
    private $maxQueryLength = 500;
    private $allowedActions = [
        'getSystemStatus', 'processTask', 'coordinateMultiAgentChat',
        'createChannel', 'sendChannelMessage', 'getChannelStatus', 'getAllChannels',
        'searchFiles', 'searchFilesByHeaders', 'searchMessages',
        'createMeetingChannel', 'processMeetingWithCoordination',
        'processNoCasinoMode', 'getGigStatistics',
        'getProjectTracking', 'updateProjectProgress',
        'openFile', 'editFile', 'getFileRelationships',
        'addFileToQueue', 'processBacklogFiles'
    ];
    
    // XSS protection patterns
    private $xssPatterns = [
        '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
        '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
        '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
        '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
        '/javascript:/i',
        '/vbscript:/i',
        '/onload\s*=/i',
        '/onerror\s*=/i',
        '/onclick\s*=/i',
        '/onmouseover\s*=/i'
    ];
    
    public function __construct() {
        $this->logPath = __DIR__ . '/../logs/api_endpoint_handler_secure.log';
        $this->dataPath = __DIR__ . '/../data/';
        
        $this->initializeComponents();
        $this->requestData = $this->getRequestData();
        $this->response = ['success' => false, 'data' => null, 'error' => null];
    }
    
    /**
     * Initialize all components
     */
    private function initializeComponents() {
        try {
            $this->coreEngine = new WolfieAGICoreEngineSecure();
            $this->superpositionallyManager = new SuperpositionallyManagerMySQL();
            $this->fileSearchEngine = new FileSearchEngine();
            $this->multiAgentCoordinator = new MultiAgentCoordinatorMySQL();
            $this->meetingModeProcessor = new MeetingModeProcessor();
            $this->integratedMeetingCoordinator = new IntegratedMeetingCoordinator();
            $this->noCasinoModeProcessor = new NoCasinoModeProcessor();
            $this->channelSystem = new WolfieChannelSystemSecure();
            
            $this->logRequest('COMPONENTS_INITIALIZED', 'All components initialized successfully');
            
        } catch (Exception $e) {
            $this->logError('INIT_ERROR', 'Component initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get and sanitize request data
     */
    private function getRequestData() {
        $method = $_SERVER['REQUEST_METHOD'];
        $data = [];
        
        if ($method === 'GET') {
            $data = $_GET;
        } elseif ($method === 'POST') {
            $input = file_get_contents('php://input');
            $jsonData = json_decode($input, true);
            $data = $jsonData ?: $_POST;
        } elseif ($method === 'PUT') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true) ?: [];
        } elseif ($method === 'DELETE') {
            $data = $_GET;
        }
        
        return $this->sanitizeArray($data);
    }
    
    /**
     * Main request handler
     */
    public function handleRequest() {
        try {
            $action = $this->requestData['action'] ?? '';
            
            if (empty($action)) {
                throw new Exception('Action parameter is required');
            }
            
            if (!in_array($action, $this->allowedActions)) {
                throw new Exception('Invalid action: ' . $action);
            }
            
            $this->logRequest('REQUEST_RECEIVED', "Action: $action");
            
            switch ($action) {
                // Core Engine Actions
                case 'getSystemStatus':
                    $this->handleGetSystemStatus();
                    break;
                case 'processTask':
                    $this->handleProcessTask();
                    break;
                case 'coordinateMultiAgentChat':
                    $this->handleCoordinateMultiAgentChat();
                    break;
                
                // Channel Actions
                case 'createChannel':
                    $this->handleCreateChannel();
                    break;
                case 'sendChannelMessage':
                    $this->handleSendChannelMessage();
                    break;
                case 'getChannelStatus':
                    $this->handleGetChannelStatus();
                    break;
                case 'getAllChannels':
                    $this->handleGetAllChannels();
                    break;
                case 'searchMessages':
                    $this->handleSearchMessages();
                    break;
                case 'getMessages':
                    $this->handleGetMessages();
                    break;
                
                // File and Search Actions
                case 'searchFiles':
                    $this->handleSearchFiles();
                    break;
                case 'searchFilesByHeaders':
                    $this->handleSearchFilesByHeaders();
                    break;
                case 'openFile':
                    $this->handleOpenFile();
                    break;
                case 'editFile':
                    $this->handleEditFile();
                    break;
                case 'getFileRelationships':
                    $this->handleGetFileRelationships();
                    break;
                
                // Meeting Actions
                case 'createMeetingChannel':
                    $this->handleCreateMeetingChannel();
                    break;
                case 'processMeetingWithCoordination':
                    $this->handleProcessMeetingWithCoordination();
                    break;
                
                // No-Casino Mode Actions
                case 'processNoCasinoMode':
                    $this->handleProcessNoCasinoMode();
                    break;
                case 'getGigStatistics':
                    $this->handleGetGigStatistics();
                    break;
                
                // Project Tracking Actions
                case 'getProjectTracking':
                    $this->handleGetProjectTracking();
                    break;
                case 'updateProjectProgress':
                    $this->handleUpdateProjectProgress();
                    break;
                
                // File Queue Actions
                case 'addFileToQueue':
                    $this->handleAddFileToQueue();
                    break;
                case 'processBacklogFiles':
                    $this->handleProcessBacklogFiles();
                    break;
                
                default:
                    throw new Exception('Unknown action: ' . $action);
            }
            
            $this->logRequest('REQUEST_SUCCESS', "Action: $action completed successfully");
            
        } catch (Exception $e) {
            $this->logError('REQUEST_ERROR', 'Request handling failed: ' . $e->getMessage());
            $this->handleError($e->getMessage());
        }
        
        $this->sendResponse();
    }
    
    /**
     * Handle get system status
     */
    private function handleGetSystemStatus() {
        $status = $this->coreEngine->getSystemStatus();
        $uiStatus = $this->coreEngine->getUIIntegrationStatus();
        
        $this->response = [
            'success' => true,
            'data' => [
                'system_status' => $status,
                'ui_integration' => $uiStatus,
                'timestamp' => date('Y-m-d H:i:s')
            ],
            'error' => null
        ];
    }
    
    /**
     * Handle process task
     */
    private function handleProcessTask() {
        $task = $this->sanitizeInput($this->requestData['task'] ?? '', 'task');
        $context = $this->sanitizeArray($this->requestData['context'] ?? []);
        $uiContext = $this->sanitizeArray($this->requestData['uiContext'] ?? []);
        
        if (empty($task)) {
            throw new Exception('Task parameter is required');
        }
        
        $result = $this->coreEngine->processTask($task, $context, $uiContext);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle coordinate multi-agent chat with XSS protection
     */
    private function handleCoordinateMultiAgentChat() {
        $message = $this->sanitizeInput($this->requestData['message'] ?? '', 'message');
        $agentContext = $this->sanitizeArray($this->requestData['agentContext'] ?? []);
        
        if (empty($message)) {
            throw new Exception('Message parameter is required');
        }
        
        $result = $this->coreEngine->coordinateMultiAgentChat($message, $agentContext);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle create channel
     */
    private function handleCreateChannel() {
        $name = $this->sanitizeInput($this->requestData['name'] ?? '', 'channel_name');
        $agents = $this->sanitizeArray($this->requestData['agents'] ?? []);
        $type = $this->sanitizeInput($this->requestData['type'] ?? 'general', 'channel_type');
        $description = $this->sanitizeInput($this->requestData['description'] ?? '', 'description');
        
        if (empty($name)) {
            throw new Exception('Channel name is required');
        }
        
        $channelId = $this->multiAgentCoordinator->createChannel($name, $agents, $type, $description);
        
        $this->response = [
            'success' => true,
            'data' => [
                'channel_id' => $channelId,
                'name' => $name,
                'type' => $type,
                'description' => $description
            ],
            'error' => null
        ];
    }
    
    /**
     * Handle send channel message with XSS protection
     */
    private function handleSendChannelMessage() {
        $channelId = $this->sanitizeInput($this->requestData['channelId'] ?? '', 'channel_id');
        $agentId = $this->sanitizeInput($this->requestData['agentId'] ?? '', 'agent_id');
        $message = $this->sanitizeInput($this->requestData['message'] ?? '', 'message');
        
        if (empty($channelId) || empty($agentId) || empty($message)) {
            throw new Exception('Channel ID, agent ID, and message are required');
        }
        
        $result = $this->multiAgentCoordinator->sendChannelMessage($channelId, $agentId, $message);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle get channel status
     */
    private function handleGetChannelStatus() {
        $channelId = $this->sanitizeInput($this->requestData['channelId'] ?? '', 'channel_id');
        
        if (empty($channelId)) {
            throw new Exception('Channel ID is required');
        }
        
        $status = $this->multiAgentCoordinator->getChannelStatus($channelId);
        
        $this->response = [
            'success' => true,
            'data' => $status,
            'error' => null
        ];
    }
    
    /**
     * Handle get all channels
     */
    private function handleGetAllChannels() {
        $channels = $this->multiAgentCoordinator->getAllChannels();
        
        $this->response = [
            'success' => true,
            'data' => $channels,
            'error' => null
        ];
    }
    
    /**
     * Handle search messages with real MySQL integration
     */
    private function handleSearchMessages() {
        $query = $this->sanitizeInput($this->requestData['query'] ?? '', 'search_query');
        $channelId = $this->sanitizeInput($this->requestData['channelId'] ?? '', 'channel_id');
        $limit = (int)($this->requestData['limit'] ?? 50);
        
        if (empty($query)) {
            throw new Exception('Search query is required');
        }
        
        // Use real MySQL search instead of mock data
        $messages = $this->channelSystem->searchMessages($query, $channelId, $limit);
        
        $this->response = [
            'success' => true,
            'data' => $messages,
            'error' => null
        ];
    }
    
    /**
     * Handle get messages from channel
     */
    private function handleGetMessages() {
        $channelId = $this->sanitizeInput($this->requestData['channelId'] ?? '', 'channel_id');
        $sinceTime = (int)($this->requestData['sinceTime'] ?? 0);
        $type = $this->sanitizeInput($this->requestData['type'] ?? 'HTML', 'message_type');
        
        if (empty($channelId)) {
            throw new Exception('Channel ID is required');
        }
        
        $messages = $this->channelSystem->getMessages($channelId, $sinceTime, $type);
        
        $this->response = [
            'success' => true,
            'data' => $messages,
            'error' => null
        ];
    }
    
    /**
     * Handle search files
     */
    private function handleSearchFiles() {
        $query = $this->sanitizeInput($this->requestData['query'] ?? '', 'search_query');
        $filters = $this->sanitizeArray($this->requestData['filters'] ?? []);
        
        if (empty($query)) {
            throw new Exception('Search query is required');
        }
        
        $results = $this->fileSearchEngine->searchFiles($query, $filters);
        
        $this->response = [
            'success' => true,
            'data' => $results,
            'error' => null
        ];
    }
    
    /**
     * Handle search files by headers
     */
    private function handleSearchFilesByHeaders() {
        $query = $this->sanitizeInput($this->requestData['query'] ?? '', 'search_query');
        $type = $this->sanitizeInput($this->requestData['type'] ?? 'all', 'search_type');
        $limit = (int)($this->requestData['limit'] ?? 50);
        
        if (empty($query)) {
            throw new Exception('Search query is required');
        }
        
        $results = $this->superpositionallyManager->searchHeaders($query, $type, $limit);
        
        $this->response = [
            'success' => true,
            'data' => $results,
            'error' => null
        ];
    }
    
    /**
     * Handle open file with security validation
     */
    private function handleOpenFile() {
        $filePath = $this->sanitizeInput($this->requestData['filePath'] ?? '', 'file_path');
        
        if (empty($filePath)) {
            throw new Exception('File path is required');
        }
        
        if (!$this->isValidFilePath($filePath)) {
            throw new Exception('Invalid file path');
        }
        
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new Exception('Failed to read file');
        }
        
        $this->response = [
            'success' => true,
            'data' => [
                'file_path' => $filePath,
                'content' => $content,
                'size' => filesize($filePath),
                'modified' => date('Y-m-d H:i:s', filemtime($filePath))
            ],
            'error' => null
        ];
    }
    
    /**
     * Handle edit file with security validation
     */
    private function handleEditFile() {
        $filePath = $this->sanitizeInput($this->requestData['filePath'] ?? '', 'file_path');
        $content = $this->sanitizeInput($this->requestData['content'] ?? '', 'file_content');
        
        if (empty($filePath) || empty($content)) {
            throw new Exception('File path and content are required');
        }
        
        if (!$this->isValidFilePath($filePath)) {
            throw new Exception('Invalid file path');
        }
        
        $result = file_put_contents($filePath, $content, LOCK_EX);
        if ($result === false) {
            throw new Exception('Failed to write file');
        }
        
        $this->response = [
            'success' => true,
            'data' => [
                'file_path' => $filePath,
                'bytes_written' => $result,
                'modified' => date('Y-m-d H:i:s')
            ],
            'error' => null
        ];
    }
    
    /**
     * Handle get file relationships
     */
    private function handleGetFileRelationships() {
        $fileId = $this->sanitizeInput($this->requestData['fileId'] ?? '', 'file_id');
        
        if (empty($fileId)) {
            throw new Exception('File ID is required');
        }
        
        $relationships = $this->superpositionallyManager->getFileRelationships($fileId);
        
        $this->response = [
            'success' => true,
            'data' => $relationships,
            'error' => null
        ];
    }
    
    /**
     * Handle create meeting channel
     */
    private function handleCreateMeetingChannel() {
        $meetingData = $this->sanitizeArray($this->requestData['meetingData'] ?? []);
        
        if (empty($meetingData)) {
            throw new Exception('Meeting data is required');
        }
        
        $result = $this->integratedMeetingCoordinator->createMeetingChannel($meetingData);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle process meeting with coordination
     */
    private function handleProcessMeetingWithCoordination() {
        $meetingData = $this->sanitizeArray($this->requestData['meetingData'] ?? []);
        
        if (empty($meetingData)) {
            throw new Exception('Meeting data is required');
        }
        
        $result = $this->integratedMeetingCoordinator->processMeetingWithCoordination($meetingData);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle process no-casino mode
     */
    private function handleProcessNoCasinoMode() {
        $gigData = $this->sanitizeArray($this->requestData['gigData'] ?? []);
        
        if (empty($gigData)) {
            throw new Exception('Gig data is required');
        }
        
        $result = $this->noCasinoModeProcessor->processGig($gigData);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle get gig statistics
     */
    private function handleGetGigStatistics() {
        $stats = $this->noCasinoModeProcessor->getStatistics();
        
        $this->response = [
            'success' => true,
            'data' => $stats,
            'error' => null
        ];
    }
    
    /**
     * Handle get project tracking with caching
     */
    private function handleGetProjectTracking() {
        $projectsFile = $this->dataPath . 'project_tracking.json';
        
        if (!file_exists($projectsFile)) {
            // Create default project tracking
            $defaultProjects = [
                [
                    'id' => 'wolfie_agi_ui',
                    'name' => 'WOLFIE AGI UI',
                    'progress' => 75,
                    'status' => 'In Progress',
                    'description' => 'Main UI system for WOLFIE AGI',
                    'last_updated' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 'superpositionally_search',
                    'name' => 'Superpositionally Search',
                    'progress' => 90,
                    'status' => 'Near Complete',
                    'description' => 'Advanced search system with headers',
                    'last_updated' => date('Y-m-d H:i:s')
                ]
            ];
            
            file_put_contents($projectsFile, json_encode($defaultProjects, JSON_PRETTY_PRINT), LOCK_EX);
        }
        
        $projects = json_decode(file_get_contents($projectsFile), true) ?: [];
        
        $this->response = [
            'success' => true,
            'data' => $projects,
            'error' => null
        ];
    }
    
    /**
     * Handle update project progress
     */
    private function handleUpdateProjectProgress() {
        $projectId = $this->sanitizeInput($this->requestData['projectId'] ?? '', 'project_id');
        $progress = (int)($this->requestData['progress'] ?? 0);
        $status = $this->sanitizeInput($this->requestData['status'] ?? '', 'status');
        
        if (empty($projectId)) {
            throw new Exception('Project ID is required');
        }
        
        if ($progress < 0 || $progress > 100) {
            throw new Exception('Progress must be between 0 and 100');
        }
        
        $projectsFile = $this->dataPath . 'project_tracking.json';
        $projects = json_decode(file_get_contents($projectsFile), true) ?: [];
        
        $projectFound = false;
        foreach ($projects as &$project) {
            if ($project['id'] === $projectId) {
                $project['progress'] = $progress;
                $project['status'] = $status ?: $project['status'];
                $project['last_updated'] = date('Y-m-d H:i:s');
                $projectFound = true;
                break;
            }
        }
        
        if (!$projectFound) {
            throw new Exception('Project not found');
        }
        
        file_put_contents($projectsFile, json_encode($projects, JSON_PRETTY_PRINT), LOCK_EX);
        
        $this->response = [
            'success' => true,
            'data' => [
                'project_id' => $projectId,
                'progress' => $progress,
                'status' => $status,
                'updated' => date('Y-m-d H:i:s')
            ],
            'error' => null
        ];
    }
    
    /**
     * Handle add file to queue
     */
    private function handleAddFileToQueue() {
        $channelId = $this->sanitizeInput($this->requestData['channelId'] ?? '', 'channel_id');
        $filePath = $this->sanitizeInput($this->requestData['filePath'] ?? '', 'file_path');
        $agentId = $this->sanitizeInput($this->requestData['agentId'] ?? 'captain_wolfie', 'agent_id');
        
        if (empty($channelId) || empty($filePath)) {
            throw new Exception('Channel ID and file path are required');
        }
        
        $result = $this->multiAgentCoordinator->addFileToQueue($channelId, $filePath, $agentId);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Handle process backlog files
     */
    private function handleProcessBacklogFiles() {
        $channelId = $this->sanitizeInput($this->requestData['channelId'] ?? '', 'channel_id');
        $agentId = $this->sanitizeInput($this->requestData['agentId'] ?? 'captain_wolfie', 'agent_id');
        
        if (empty($channelId)) {
            throw new Exception('Channel ID is required');
        }
        
        $result = $this->multiAgentCoordinator->processBacklogFiles($channelId, $agentId);
        
        $this->response = [
            'success' => true,
            'data' => $result,
            'error' => null
        ];
    }
    
    /**
     * Comprehensive input sanitization
     */
    private function sanitizeInput($input, $type) {
        if (is_null($input)) {
            throw new Exception("Input cannot be null");
        }
        
        $input = trim($input);
        
        if (empty($input) && $type !== 'description') {
            throw new Exception("Input cannot be empty");
        }
        
        // XSS protection
        foreach ($this->xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                throw new Exception("Potentially malicious content detected: XSS attempt blocked");
            }
        }
        
        switch ($type) {
            case 'task':
            case 'message':
            case 'search_query':
            case 'file_content':
                $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                break;
                
            case 'channel_name':
            case 'project_id':
            case 'agent_id':
            case 'channel_id':
            case 'file_id':
                if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $input)) {
                    throw new Exception("Invalid characters in $type");
                }
                break;
                
            case 'file_path':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                break;
                
            case 'description':
            case 'status':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                break;
        }
        
        // Length validation
        if ($type === 'message' && strlen($input) > $this->maxMessageLength) {
            throw new Exception("Message exceeds maximum length of {$this->maxMessageLength} characters");
        }
        
        if ($type === 'search_query' && strlen($input) > $this->maxQueryLength) {
            throw new Exception("Search query exceeds maximum length of {$this->maxQueryLength} characters");
        }
        
        return $input;
    }
    
    /**
     * Sanitize array inputs
     */
    private function sanitizeArray($array) {
        if (!is_array($array)) {
            return [];
        }
        
        $sanitized = [];
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeInput($value, 'message');
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Validate file path for security
     */
    private function isValidFilePath($filePath) {
        $basePath = realpath(__DIR__ . '/../');
        $realPath = realpath($filePath);
        
        if ($realPath === false) {
            return false;
        }
        
        return strpos($realPath, $basePath) === 0;
    }
    
    /**
     * Handle errors
     */
    private function handleError($message) {
        $this->response = [
            'success' => false,
            'data' => null,
            'error' => $message
        ];
    }
    
    /**
     * Send JSON response
     */
    private function sendResponse() {
        echo json_encode($this->response, JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Log request
     */
    private function logRequest($event, $message) {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $agapeTime = $timestamp . ' [AGAPE]';
        $logEntry = "[$agapeTime] [$event] APIEndpointHandlerSecure: $message\n";
        
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log error
     */
    private function logError($event, $message) {
        $this->logRequest($event, $message);
        error_log("APIEndpointHandlerSecure: $message");
    }
}

// Handle the request
try {
    $apiHandler = new APIEndpointHandlerSecure();
    $apiHandler->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data' => null,
        'error' => 'Internal server error: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
