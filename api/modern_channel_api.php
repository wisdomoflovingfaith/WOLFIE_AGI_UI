<?php
/**
 * WOLFIE AGI UI - Modern Channel API (React-style)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Modern API endpoint using JSON and modern HTTP methods
 * WHERE: C:\START\WOLFIE_AGI_UI\api\
 * WHEN: 2025-09-26 16:10:00 CDT
 * WHY: To support modern fetch API and React-style frontend
 * HOW: PHP API with JSON responses and modern HTTP methods
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of modern API communication
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [MODERN_CHANNEL_API_001, WOLFIE_AGI_UI_031]
 * 
 * VERSION: 1.0.0 - The Captain's Modern API
 * STATUS: Active - Modern HTTP/JSON API
 */

// Set JSON headers for modern API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once '../core/wolfie_channel_system_mysql.php';
require_once '../config/database_config.php';

// Initialize channel system
$channelSystem = new WolfieChannelSystemMySQL();

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? 'ping';

// Response helper
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

// Error handler
function sendError($message, $status = 400) {
    sendResponse(['error' => $message, 'success' => false], $status);
}

try {
    switch ($action) {
        case 'ping':
            sendResponse(['status' => 'OK', 'message' => 'WOLFIE AGI UI Modern API Ready']);
            break;
            
        case 'create_channel':
            if ($method !== 'POST') {
                sendError('Method not allowed', 405);
            }
            
            $name = $input['name'] ?? '';
            $type = $input['type'] ?? 'general';
            $description = $input['description'] ?? '';
            $user_id = $input['user_id'] ?? 'captain_wolfie';
            
            if (empty($name)) {
                sendError('Channel name is required');
            }
            
            $channelId = $channelSystem->createChannel($name, $type, $description);
            
            // Add user to channel
            $channelSystem->addUserToChannel($user_id, $channelId);
            
            sendResponse([
                'success' => true,
                'channel_id' => $channelId,
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            break;
            
        case 'send_message':
            if ($method !== 'POST') {
                sendError('Method not allowed', 405);
            }
            
            $channelId = $input['channel_id'] ?? '';
            $userId = $input['user_id'] ?? 'captain_wolfie';
            $message = $input['message'] ?? '';
            $type = $input['type'] ?? 'HTML';
            
            if (empty($channelId) || empty($message)) {
                sendError('Channel ID and message are required');
            }
            
            $result = $channelSystem->sendMessage($channelId, $userId, $message, $type);
            
            sendResponse([
                'success' => true,
                'message_id' => $result['message_id'] ?? uniqid(),
                'timeof' => $result['timeof'] ?? time(),
                'channel_id' => $channelId,
                'user_id' => $userId,
                'message' => $message
            ]);
            break;
            
        case 'get_messages':
            $channelId = $_GET['channel_id'] ?? $input['channel_id'] ?? '';
            $userId = $_GET['user_id'] ?? $input['user_id'] ?? 'captain_wolfie';
            $sinceTime = $_GET['since_time'] ?? $input['since_time'] ?? 0;
            $format = $_GET['format'] ?? $input['format'] ?? 'JSON';
            
            if (empty($channelId)) {
                sendError('Channel ID is required');
            }
            
            $messages = $channelSystem->getMessages($channelId, $sinceTime, $format);
            
            sendResponse([
                'success' => true,
                'messages' => $messages,
                'channel_id' => $channelId,
                'count' => count($messages),
                'since_time' => $sinceTime
            ]);
            break;
            
        case 'get_channels':
            $channels = $channelSystem->getAllChannels();
            
            sendResponse([
                'success' => true,
                'channels' => $channels,
                'count' => count($channels)
            ]);
            break;
            
        case 'get_channel_status':
            $channelId = $_GET['channel_id'] ?? $input['channel_id'] ?? '';
            
            if (empty($channelId)) {
                sendError('Channel ID is required');
            }
            
            $status = $channelSystem->getChannelStatus($channelId);
            
            if (!$status) {
                sendError('Channel not found', 404);
            }
            
            sendResponse([
                'success' => true,
                'status' => $status
            ]);
            break;
            
        case 'add_user_to_channel':
            if ($method !== 'POST') {
                sendError('Method not allowed', 405);
            }
            
            $channelId = $input['channel_id'] ?? '';
            $userId = $input['user_id'] ?? '';
            
            if (empty($channelId) || empty($userId)) {
                sendError('Channel ID and User ID are required');
            }
            
            $result = $channelSystem->addUserToChannel($userId, $channelId);
            
            sendResponse([
                'success' => true,
                'channel_id' => $channelId,
                'user_id' => $userId,
                'added' => $result
            ]);
            break;
            
        case 'create_user':
            if ($method !== 'POST') {
                sendError('Method not allowed', 405);
            }
            
            $sessionId = $input['session_id'] ?? uniqid();
            $username = $input['username'] ?? 'user_' . time();
            
            $user = $channelSystem->createOrGetUser($sessionId, $username);
            
            sendResponse([
                'success' => true,
                'user' => $user
            ]);
            break;
            
        case 'get_system_status':
            $channels = $channelSystem->getAllChannels();
            $totalMessages = 0;
            
            foreach ($channels as $channel) {
                $messages = $channelSystem->getMessages($channel['channel_id'], 0, 'JSON');
                $totalMessages += count($messages);
            }
            
            sendResponse([
                'success' => true,
                'status' => [
                    'total_channels' => count($channels),
                    'total_messages' => $totalMessages,
                    'database_status' => 'Connected',
                    'api_version' => '1.0.0',
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        default:
            sendError('Unknown action: ' . $action, 404);
            break;
    }
    
} catch (Exception $e) {
    error_log('Modern Channel API Error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
?>
