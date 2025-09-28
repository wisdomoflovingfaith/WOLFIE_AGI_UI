<?php
/**
 * WOLFIE AGI UI - Channel API Endpoint
 * 
 * WHO: WOLFIE (Captain Eric Robin Gerdes)
 * WHAT: API endpoint for Crafty Syntax channel management
 * WHERE: C:\START\WOLFIE_AGI_UI\api\
 * WHEN: 2025-09-26 08:45:00 CDT
 * WHY: To provide REST API access to the channel system for UI integration
 * HOW: PHP REST API with JSON responses
 * HELP: Contact WOLFIE for channel API questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Sacred foundation for API design
 * GENESIS: Foundation of channel API protocols
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [CHANNEL_API_001, WOLFIE_AGI_UI_005, CRAFTY_SYNTAX_API_001]
 * 
 * VERSION: 1.0.0
 * STATUS: Active Development - Channel Management
 */

// Set headers for JSON responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once '../core/multi_agent_coordinator.php';

// Initialize Multi-Agent Coordinator
$coordinator = new MultiAgentCoordinator();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Get request data
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Response helper function
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

// Error response helper
function sendError($message, $status = 400) {
    sendResponse(['success' => false, 'error' => $message], $status);
}

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'getAllChannels':
                    $channels = $coordinator->getAllChannels();
                    sendResponse(['success' => true, 'channels' => $channels]);
                    break;
                    
                case 'getChannelStatus':
                    $channelId = $_GET['channelId'] ?? '';
                    if (empty($channelId)) {
                        sendError('Channel ID is required');
                    }
                    $status = $coordinator->getChannelStatus($channelId);
                    if ($status) {
                        sendResponse(['success' => true, 'channel' => $status]);
                    } else {
                        sendError('Channel not found', 404);
                    }
                    break;
                    
                case 'getSystemStatus':
                    $status = $coordinator->getStatus();
                    sendResponse(['success' => true, 'status' => $status]);
                    break;
                    
                case 'getChannelMessages':
                    $channelId = $_GET['channelId'] ?? '';
                    if (empty($channelId)) {
                        sendError('Channel ID is required');
                    }
                    $status = $coordinator->getChannelStatus($channelId);
                    if ($status) {
                        sendResponse(['success' => true, 'messages' => $status['recent_messages'] ?? []]);
                    } else {
                        sendError('Channel not found', 404);
                    }
                    break;
                    
                case 'getFileQueue':
                    $channelId = $_GET['channelId'] ?? '';
                    if (empty($channelId)) {
                        sendError('Channel ID is required');
                    }
                    $status = $coordinator->getChannelStatus($channelId);
                    if ($status) {
                        sendResponse(['success' => true, 'files' => $status['file_queue'] ?? []]);
                    } else {
                        sendError('Channel not found', 404);
                    }
                    break;
                    
                default:
                    sendError('Invalid action for GET request');
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'createChannel':
                    $name = $input['name'] ?? '';
                    $agents = $input['agents'] ?? [];
                    $type = $input['type'] ?? 'general';
                    $description = $input['description'] ?? '';
                    
                    if (empty($name) || empty($agents)) {
                        sendError('Name and agents are required');
                    }
                    
                    $channelId = $coordinator->createChannel($name, $agents, $type, $description);
                    sendResponse(['success' => true, 'channelId' => $channelId]);
                    break;
                    
                case 'initiateAIChannel':
                    $selectedAgents = $input['selectedAgents'] ?? [];
                    $task = $input['task'] ?? '';
                    $maxIterations = $input['maxIterations'] ?? 10;
                    $name = $input['name'] ?? 'AI Channel';
                    $description = $input['description'] ?? '';
                    
                    if (empty($selectedAgents) || empty($task)) {
                        sendError('Selected agents and task are required');
                    }
                    
                    $channelId = $coordinator->initiateAIChannel($selectedAgents, $task, $maxIterations);
                    sendResponse(['success' => true, 'channelId' => $channelId]);
                    break;
                    
                case 'sendChannelMessage':
                    $channelId = $input['channelId'] ?? '';
                    $agentId = $input['agentId'] ?? '';
                    $message = $input['message'] ?? '';
                    
                    if (empty($channelId) || empty($agentId) || empty($message)) {
                        sendError('Channel ID, agent ID, and message are required');
                    }
                    
                    $response = $coordinator->sendChannelMessage($channelId, $agentId, $message);
                    sendResponse($response);
                    break;
                    
                case 'addFileToQueue':
                    $channelId = $input['channelId'] ?? '';
                    $filePath = $input['filePath'] ?? '';
                    $priority = $input['priority'] ?? 1;
                    
                    if (empty($channelId) || empty($filePath)) {
                        sendError('Channel ID and file path are required');
                    }
                    
                    $success = $coordinator->addFileToQueue($channelId, $filePath, $priority);
                    if ($success) {
                        sendResponse(['success' => true, 'message' => 'File added to queue']);
                    } else {
                        sendError('Failed to add file to queue');
                    }
                    break;
                    
                case 'processBacklogFiles':
                    $fileList = $input['fileList'] ?? [];
                    
                    if (empty($fileList)) {
                        sendError('File list is required');
                    }
                    
                    $channelId = $coordinator->processBacklogFiles($fileList);
                    sendResponse(['success' => true, 'channelId' => $channelId, 'message' => 'Backlog processing started']);
                    break;
                    
                case 'stopChannel':
                    $channelId = $input['channelId'] ?? '';
                    
                    if (empty($channelId)) {
                        sendError('Channel ID is required');
                    }
                    
                    $coordinator->stopChannel($channelId);
                    sendResponse(['success' => true, 'message' => 'Channel stopped']);
                    break;
                    
                case 'coordinateMultiAgentChat':
                    $message = $input['message'] ?? '';
                    $context = $input['context'] ?? [];
                    
                    if (empty($message)) {
                        sendError('Message is required');
                    }
                    
                    $response = $coordinator->coordinateMultiAgentChat($message, $context);
                    sendResponse(['success' => true, 'response' => $response]);
                    break;
                    
                default:
                    sendError('Invalid action for POST request');
            }
            break;
            
        case 'PUT':
            switch ($action) {
                case 'updateChannelStatus':
                    $channelId = $input['channelId'] ?? '';
                    $status = $input['status'] ?? '';
                    
                    if (empty($channelId) || empty($status)) {
                        sendError('Channel ID and status are required');
                    }
                    
                    // Update channel status logic would go here
                    sendResponse(['success' => true, 'message' => 'Channel status updated']);
                    break;
                    
                default:
                    sendError('Invalid action for PUT request');
            }
            break;
            
        case 'DELETE':
            switch ($action) {
                case 'deleteChannel':
                    $channelId = $_GET['channelId'] ?? '';
                    
                    if (empty($channelId)) {
                        sendError('Channel ID is required');
                    }
                    
                    // Delete channel logic would go here
                    sendResponse(['success' => true, 'message' => 'Channel deleted']);
                    break;
                    
                default:
                    sendError('Invalid action for DELETE request');
            }
            break;
            
        default:
            sendError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    sendError('Internal server error: ' . $e->getMessage(), 500);
}

// If we get here, something went wrong
sendError('Invalid request', 400);
?>
