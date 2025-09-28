<?php
/**
 * WOLFIE AGI UI - Secure Modern Channel API
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Secure modern API endpoint with XSS protection and authentication
 * WHERE: C:\START\WOLFIE_AGI_UI\api\
 * WHEN: 2025-09-26 18:00:00 CDT
 * WHY: To provide secure API with XSS protection and authentication
 * HOW: PHP-based secure API with comprehensive security measures
 * HELP: Contact Captain WOLFIE for secure API questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for secure API
 * GENESIS: Foundation of secure API endpoint protocols
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [MODERN_CHANNEL_API_SECURE_001, WOLFIE_AGI_UI_052]
 * 
 * VERSION: 1.0.0 - The Captain's Secure Modern Channel API
 * STATUS: Active - XSS Protected, Authentication Ready
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

require_once '../core/wolfie_channel_system_secure.php';
require_once '../config/database_config.php';

class ModernChannelAPISecure {
    private $channelSystem;
    private $db;
    private $logPath;
    
    // Security settings
    private $maxMessageLength = 1000;
    private $maxChannelNameLength = 100;
    private $maxDescriptionLength = 500;
    
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
        $this->channelSystem = new WolfieChannelSystemSecure();
        $this->db = getDatabaseConnection();
        $this->logPath = __DIR__ . '/../logs/modern_channel_api_secure.log';
        $this->logEvent('ModernChannelAPISecure initialized with XSS protection');
    }
    
    /**
     * Handle the request
     */
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $input = $this->getInput();
            $action = $input['action'] ?? '';
            
            if (empty($action)) {
                $this->sendError('Action parameter is required', 400);
                return;
            }
            
            // Verify authentication for sensitive actions
            if (in_array($action, ['create_channel', 'send_message', 'add_user_to_channel'])) {
                if (!$this->verifyAuthentication()) {
                    $this->sendError('Authentication required', 401);
                    return;
                }
            }
            
            $this->logEvent("Processing action: $action via $method");
            
            switch ($action) {
                case 'ping':
                    $this->handlePing();
                    break;
                    
                case 'create_channel':
                    $this->handleCreateChannel($input, $method);
                    break;
                    
                case 'send_message':
                    $this->handleSendMessage($input, $method);
                    break;
                    
                case 'get_messages':
                    $this->handleGetMessages($input, $method);
                    break;
                    
                case 'get_channels':
                    $this->handleGetChannels($input, $method);
                    break;
                    
                case 'get_channel_status':
                    $this->handleGetChannelStatus($input, $method);
                    break;
                    
                case 'add_user_to_channel':
                    $this->handleAddUserToChannel($input, $method);
                    break;
                    
                case 'create_user':
                    $this->handleCreateUser($input, $method);
                    break;
                    
                case 'get_system_status':
                    $this->handleGetSystemStatus($input, $method);
                    break;
                    
                default:
                    $this->sendError('Unknown action: ' . $action, 400);
            }
            
        } catch (Exception $e) {
            $this->logEvent('Error processing request: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Internal server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get input data
     */
    private function getInput() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'GET') {
            return $_GET;
        } elseif ($method === 'POST') {
            $input = file_get_contents('php://input');
            $jsonData = json_decode($input, true);
            return $jsonData ?: $_POST;
        } elseif ($method === 'PUT') {
            $input = file_get_contents('php://input');
            return json_decode($input, true) ?: [];
        } elseif ($method === 'DELETE') {
            return $_GET;
        }
        
        return [];
    }
    
    /**
     * Verify authentication
     */
    private function verifyAuthentication() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        
        if (empty($token)) {
            return false;
        }
        
        // Simple token verification (in production, use proper JWT)
        $expectedToken = hash('sha256', 'AGAPE_SECRET_KEY_' . date('Ymd'));
        return $token === $expectedToken;
    }
    
    /**
     * Handle ping
     */
    private function handlePing() {
        $this->sendResponse([
            'success' => true,
            'message' => 'WOLFIE AGI UI Secure API is running',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'security' => 'XSS Protected'
        ]);
    }
    
    /**
     * Handle create channel with XSS protection
     */
    private function handleCreateChannel($input, $method) {
        if ($method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $name = $this->sanitizeInput($input['name'] ?? '', 'channel_name', $this->maxChannelNameLength);
        $type = $this->sanitizeInput($input['type'] ?? 'general', 'channel_type');
        $description = $this->sanitizeInput($input['description'] ?? '', 'description', $this->maxDescriptionLength);
        $userId = $this->sanitizeInput($input['user_id'] ?? 'captain_wolfie', 'user_id');
        
        if (empty($name)) {
            $this->sendError('Channel name is required', 400);
            return;
        }
        
        try {
            $channelId = $this->channelSystem->createChannel($name, $type, $description);
            $this->channelSystem->addUserToChannel($userId, $channelId);
            
            $this->logEvent("Secure channel created: $name (ID: $channelId)");
            
            $this->sendResponse([
                'success' => true,
                'channel_id' => $channelId,
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $userId
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error creating secure channel: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to create channel: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle send message with XSS protection
     */
    private function handleSendMessage($input, $method) {
        if ($method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $channelId = $this->sanitizeInput($input['channel_id'] ?? '', 'channel_id');
        $userId = $this->sanitizeInput($input['user_id'] ?? 'captain_wolfie', 'user_id');
        $message = $this->sanitizeInput($input['message'] ?? '', 'message', $this->maxMessageLength);
        $type = $this->sanitizeInput($input['type'] ?? 'HTML', 'message_type');
        
        if (empty($channelId) || empty($message)) {
            $this->sendError('Channel ID and message are required', 400);
            return;
        }
        
        try {
            $result = $this->channelSystem->sendMessage($channelId, $userId, $message, $type);
            
            $this->logEvent("Secure message sent to channel $channelId by $userId");
            
            $this->sendResponse([
                'success' => true,
                'message_id' => $result['message_id'],
                'timeof' => $result['timeof'],
                'channel_id' => $channelId,
                'user_id' => $userId,
                'message' => $message,
                'type' => $type
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error sending secure message: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to send message: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle get messages
     */
    private function handleGetMessages($input, $method) {
        if ($method !== 'GET' && $method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $channelId = $this->sanitizeInput($input['channel_id'] ?? '', 'channel_id');
        $sinceTime = (int)($input['since_time'] ?? 0);
        $type = $this->sanitizeInput($input['type'] ?? 'HTML', 'message_type');
        
        if (empty($channelId)) {
            $this->sendError('Channel ID is required', 400);
            return;
        }
        
        try {
            $messages = $this->channelSystem->getMessages($channelId, $sinceTime, $type);
            
            $this->sendResponse([
                'success' => true,
                'messages' => $messages,
                'channel_id' => $channelId,
                'count' => count($messages)
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error getting secure messages: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to get messages: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle get channels
     */
    private function handleGetChannels($input, $method) {
        if ($method !== 'GET' && $method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        try {
            $channels = $this->channelSystem->getAllChannels();
            
            $this->sendResponse([
                'success' => true,
                'channels' => $channels,
                'count' => count($channels)
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error getting secure channels: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to get channels: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle get channel status
     */
    private function handleGetChannelStatus($input, $method) {
        if ($method !== 'GET' && $method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $channelId = $this->sanitizeInput($input['channel_id'] ?? '', 'channel_id');
        
        if (empty($channelId)) {
            $this->sendError('Channel ID is required', 400);
            return;
        }
        
        try {
            $status = $this->channelSystem->getChannelStatus($channelId);
            
            if (!$status) {
                $this->sendError('Channel not found', 404);
                return;
            }
            
            $this->sendResponse([
                'success' => true,
                'channel' => $status
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error getting secure channel status: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to get channel status: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle add user to channel
     */
    private function handleAddUserToChannel($input, $method) {
        if ($method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $channelId = $this->sanitizeInput($input['channel_id'] ?? '', 'channel_id');
        $userId = $this->sanitizeInput($input['user_id'] ?? '', 'user_id');
        $sessionId = $this->sanitizeInput($input['session_id'] ?? '', 'session_id');
        
        if (empty($channelId) || empty($userId)) {
            $this->sendError('Channel ID and user ID are required', 400);
            return;
        }
        
        try {
            $this->channelSystem->addUserToChannel($userId, $channelId, $sessionId);
            
            $this->logEvent("User $userId added to secure channel $channelId");
            
            $this->sendResponse([
                'success' => true,
                'message' => 'User added to channel successfully',
                'channel_id' => $channelId,
                'user_id' => $userId
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error adding user to secure channel: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to add user to channel: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle create user
     */
    private function handleCreateUser($input, $method) {
        if ($method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $sessionId = $this->sanitizeInput($input['session_id'] ?? uniqid(), 'session_id');
        $username = $this->sanitizeInput($input['username'] ?? 'user_' . time(), 'user_id');
        
        try {
            // Create user session
            $stmt = $this->db->prepare("INSERT INTO agent_sessions (session_id, agent_id, status, last_activity) VALUES (?, ?, 'active', ?)");
            $stmt->execute([$sessionId, $username, date('YmdHis')]);
            
            $this->logEvent("Secure user created: $username (Session: $sessionId)");
            
            $this->sendResponse([
                'success' => true,
                'user' => [
                    'session_id' => $sessionId,
                    'username' => $username,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error creating secure user: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to create user: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Handle get system status
     */
    private function handleGetSystemStatus($input, $method) {
        if ($method !== 'GET' && $method !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        try {
            $channels = $this->channelSystem->getAllChannels();
            $totalChannels = count($channels);
            $activeChannels = 0;
            
            foreach ($channels as $channel) {
                if ($channel['status'] === 'active') {
                    $activeChannels++;
                }
            }
            
            $this->sendResponse([
                'success' => true,
                'status' => [
                    'api_status' => 'running',
                    'security' => 'XSS Protected',
                    'authentication' => 'Required',
                    'total_channels' => $totalChannels,
                    'active_channels' => $activeChannels,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'version' => '1.0.0'
                ]
            ]);
            
        } catch (Exception $e) {
            $this->logEvent('Error getting secure system status: ' . $e->getMessage(), 'ERROR');
            $this->sendError('Failed to get system status: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Comprehensive input sanitization with XSS protection
     */
    private function sanitizeInput($input, $type, $maxLength = null) {
        if (is_null($input)) {
            throw new Exception("Input cannot be null");
        }
        
        $input = trim($input);
        
        if (empty($input) && $type !== 'description') {
            throw new Exception("Input cannot be empty");
        }
        
        // XSS protection - check for malicious patterns
        foreach ($this->xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                throw new Exception("Potentially malicious content detected: XSS attempt blocked");
            }
        }
        
        switch ($type) {
            case 'message':
                // Double sanitization for messages
                $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                break;
                
            case 'channel_name':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                if (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $input)) {
                    throw new Exception("Channel name contains invalid characters");
                }
                break;
                
            case 'user_id':
            case 'channel_id':
            case 'session_id':
                if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $input)) {
                    throw new Exception("Invalid characters in $type");
                }
                break;
                
            case 'description':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
                break;
                
            case 'channel_type':
                if (!in_array($input, ['general', 'private', 'public', 'meeting', 'support'])) {
                    throw new Exception("Invalid channel type: $input");
                }
                break;
                
            case 'message_type':
                if (!in_array($input, ['HTML', 'TEXT', 'JSON', 'MARKDOWN'])) {
                    throw new Exception("Invalid message type: $input");
                }
                break;
        }
        
        if ($maxLength && strlen($input) > $maxLength) {
            throw new Exception("Input exceeds maximum length of $maxLength characters");
        }
        
        return $input;
    }
    
    /**
     * Send success response
     */
    private function sendResponse($data) {
        http_response_code(200);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Send error response
     */
    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'code' => $code,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Enhanced logging with AGAPE timestamps
     */
    private function logEvent($message, $level = 'INFO') {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $agapeTime = $timestamp . ' [AGAPE]';
        $logEntry = "[$agapeTime] [$level] ModernChannelAPISecure: $message\n";
        
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($level === 'ERROR') {
            error_log("ModernChannelAPISecure: $message");
        }
    }
}

// Handle the request
try {
    $api = new ModernChannelAPISecure();
    $api->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
