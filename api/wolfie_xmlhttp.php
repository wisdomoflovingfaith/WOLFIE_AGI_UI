<?php
/**
 * WOLFIE AGI UI - XMLHttpRequest Handler (Based on SalesSyntax 3.7.0)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Real XMLHttpRequest handler for channels like salessyntax3.7.0
 * WHERE: C:\START\WOLFIE_AGI_UI\api\
 * WHEN: 2025-09-26 15:05:00 CDT
 * WHY: To implement proper channel communication like salessyntax3.7.0
 * HOW: PHP-based XMLHttpRequest handler with 2.1 second polling
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of real channel communication
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [WOLFIE_XMLHTTP_001, WOLFIE_AGI_UI_021]
 * 
 * VERSION: 1.0.0 - The Captain's Real XMLHttpRequest Handler
 * STATUS: Active - Based on SalesSyntax 3.7.0
 */

require_once '../core/wolfie_channel_system_mysql.php';
require_once '../config/database_config.php';

// Set headers like salessyntax3.7.0
header('Content-Type: text/html; charset=utf-8');

// Get parameters
$whattodo = $_GET['whattodo'] ?? $_POST['whattodo'] ?? '';
$channelId = $_GET['channel_id'] ?? $_POST['channel_id'] ?? '';
$userId = $_GET['user_id'] ?? $_POST['user_id'] ?? 'captain_wolfie';
$message = $_GET['message'] ?? $_POST['message'] ?? '';
$htmlTime = $_GET['HTML'] ?? $_POST['HTML'] ?? 0;
$layerTime = $_GET['LAYER'] ?? $_POST['LAYER'] ?? 0;
$rand = $_GET['rand'] ?? $_POST['rand'] ?? '';

// Initialize channel system with MySQL
$channelSystem = new WolfieChannelSystemMySQL();

// Handle different actions like salessyntax3.7.0
switch ($whattodo) {
    case 'ping':
        echo 'OK';
        exit;
        
    case 'messages':
        handleMessages($channelSystem, $channelId, $userId, $htmlTime, $layerTime);
        break;
        
    case 'send':
        handleSendMessage($channelSystem, $channelId, $userId, $message);
        break;
        
    case 'create_channel':
        handleCreateChannel($channelSystem, $userId);
        break;
        
    case 'get_channels':
        handleGetChannels($channelSystem);
        break;
        
    case 'get_users':
        handleGetUsers($channelSystem, $channelId);
        break;
        
    default:
        echo 'Unknown action: ' . $whattodo;
        exit;
}

/**
 * Handle messages (like salessyntax3.7.0)
 */
function handleMessages($channelSystem, $channelId, $userId, $htmlTime, $layerTime) {
    if (empty($channelId)) {
        echo 'No channel specified';
        return;
    }
    
    // Get HTML messages
    $htmlMessages = $channelSystem->getMessages($channelId, $htmlTime, 'HTML');
    $htmlJS = $channelSystem->generateMessageJS($htmlMessages);
    
    // Get LAYER messages (typing indicators)
    $layerMessages = $channelSystem->getMessages($channelId, $layerTime, 'LAYER');
    $layerJS = $channelSystem->generateMessageJS($layerMessages);
    
    // Combine like salessyntax3.7.0
    echo $htmlJS . $layerJS;
}

/**
 * Handle send message (like salessyntax3.7.0)
 */
function handleSendMessage($channelSystem, $channelId, $userId, $message) {
    if (empty($channelId) || empty($message)) {
        echo 'Missing channel or message';
        return;
    }
    
    // Add user to channel if not already there
    $channelSystem->addUserToChannel($userId, $channelId);
    
    // Send message
    $result = $channelSystem->sendMessage($channelId, $userId, $message, 'HTML');
    
    if ($result) {
        echo 'Message sent successfully';
    } else {
        echo 'Failed to send message';
    }
}

/**
 * Handle create channel
 */
function handleCreateChannel($channelSystem, $userId) {
    $channelId = $channelSystem->createChannel('WOLFIE Channel', 'general', 'Captain WOLFIE Channel');
    
    // Add user to channel
    $channelSystem->addUserToChannel($userId, $channelId);
    
    echo json_encode(['channel_id' => $channelId]);
}

/**
 * Handle get channels
 */
function handleGetChannels($channelSystem) {
    $channels = $channelSystem->getAllChannels();
    echo json_encode($channels);
}

/**
 * Handle get users
 */
function handleGetUsers($channelSystem, $channelId) {
    $users = $channelSystem->getAllUsers();
    $channelUsers = [];
    
    foreach ($users as $user) {
        if ($user['onchannel'] === $channelId) {
            $channelUsers[] = $user;
        }
    }
    
    echo json_encode($channelUsers);
}
?>
