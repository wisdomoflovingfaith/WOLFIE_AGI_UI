<?php
/**
 * WOLFIE AGI UI - WebSocket Server for Real-Time Communication
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: WebSocket server for real-time channel communication
 * WHERE: C:\START\WOLFIE_AGI_UI\websocket\
 * WHEN: 2025-09-26 17:40:00 CDT
 * WHY: To provide real-time communication for multi-agent coordination
 * HOW: Ratchet WebSocket server with secure message handling
 * HELP: Contact Captain WOLFIE for WebSocket server questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for real-time communication
 * GENESIS: Foundation of WebSocket communication protocols
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [WOLFIE_WEBSOCKET_SERVER_001, WOLFIE_AGI_UI_048]
 * 
 * VERSION: 1.0.0 - The Captain's WebSocket Server
 * STATUS: Active - Real-Time Communication Ready
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/multi_agent_coordinator_secure.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WolfieWebSocketHandler implements MessageComponentInterface {
    protected $clients;
    protected $coordinator;
    protected $channels;
    protected $logPath;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->coordinator = new MultiAgentCoordinatorSecure();
        $this->channels = [];
        $this->logPath = __DIR__ . '/../logs/websocket_server.log';
        
        $this->logEvent('WebSocket server initialized');
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->logEvent("New connection: {$conn->resourceId}");
        
        // Send welcome message
        $conn->send(json_encode([
            'type' => 'welcome',
            'message' => 'Connected to WOLFIE AGI WebSocket Server',
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        try {
            $data = json_decode($msg, true);
            
            if (!$data) {
                throw new Exception('Invalid JSON message');
            }
            
            $this->logEvent("Message from {$from->resourceId}: " . $data['action']);
            
            switch ($data['action']) {
                case 'joinChannel':
                    $this->handleJoinChannel($from, $data);
                    break;
                    
                case 'leaveChannel':
                    $this->handleLeaveChannel($from, $data);
                    break;
                    
                case 'sendMessage':
                    $this->handleSendMessage($from, $data);
                    break;
                    
                case 'getChannels':
                    $this->handleGetChannels($from, $data);
                    break;
                    
                case 'getMessages':
                    $this->handleGetMessages($from, $data);
                    break;
                    
                case 'searchMessages':
                    $this->handleSearchMessages($from, $data);
                    break;
                    
                case 'createChannel':
                    $this->handleCreateChannel($from, $data);
                    break;
                    
                case 'getSystemStatus':
                    $this->handleGetSystemStatus($from, $data);
                    break;
                    
                default:
                    throw new Exception('Unknown action: ' . $data['action']);
            }
            
        } catch (Exception $e) {
            $this->logEvent("Error processing message: " . $e->getMessage(), 'ERROR');
            $from->send(json_encode([
                'type' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]));
        }
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $this->logEvent("Connection closed: {$conn->resourceId}");
        
        // Remove from all channels
        foreach ($this->channels as $channelId => $clients) {
            if (isset($clients[$conn->resourceId])) {
                unset($this->channels[$channelId][$conn->resourceId]);
            }
        }
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->logEvent("WebSocket error: " . $e->getMessage(), 'ERROR');
        $conn->close();
    }
    
    /**
     * Handle join channel
     */
    private function handleJoinChannel(ConnectionInterface $from, $data) {
        $channelId = $data['channelId'] ?? '';
        $agentId = $data['agentId'] ?? 'captain_wolfie';
        
        if (empty($channelId)) {
            throw new Exception('Channel ID is required');
        }
        
        // Add to channel
        if (!isset($this->channels[$channelId])) {
            $this->channels[$channelId] = [];
        }
        $this->channels[$channelId][$from->resourceId] = $from;
        
        // Add user to channel in coordinator
        $this->coordinator->addUserToChannel($agentId, $channelId);
        
        $from->send(json_encode([
            'type' => 'channelJoined',
            'channelId' => $channelId,
            'agentId' => $agentId,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        
        $this->logEvent("Agent $agentId joined channel $channelId");
    }
    
    /**
     * Handle leave channel
     */
    private function handleLeaveChannel(ConnectionInterface $from, $data) {
        $channelId = $data['channelId'] ?? '';
        
        if (isset($this->channels[$channelId][$from->resourceId])) {
            unset($this->channels[$channelId][$from->resourceId]);
        }
        
        $from->send(json_encode([
            'type' => 'channelLeft',
            'channelId' => $channelId,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        
        $this->logEvent("Connection {$from->resourceId} left channel $channelId");
    }
    
    /**
     * Handle send message
     */
    private function handleSendMessage(ConnectionInterface $from, $data) {
        $channelId = $data['channelId'] ?? '';
        $agentId = $data['agentId'] ?? 'captain_wolfie';
        $message = $data['message'] ?? '';
        $token = $data['token'] ?? null;
        
        if (empty($channelId) || empty($message)) {
            throw new Exception('Channel ID and message are required');
        }
        
        // Send message via coordinator
        $result = $this->coordinator->sendChannelMessage($channelId, $agentId, $message, $token);
        
        if ($result['success']) {
            // Broadcast to all clients in the channel
            $this->broadcastToChannel($channelId, [
                'type' => 'newMessage',
                'message' => $result['response'],
                'channelId' => $channelId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $from->send(json_encode([
                'type' => 'messageSent',
                'success' => true,
                'messageId' => $result['response']['message_id'],
                'timestamp' => date('Y-m-d H:i:s')
            ]));
        } else {
            throw new Exception($result['error']);
        }
    }
    
    /**
     * Handle get channels
     */
    private function handleGetChannels(ConnectionInterface $from, $data) {
        $channels = $this->coordinator->getAllChannels();
        
        $from->send(json_encode([
            'type' => 'channelsList',
            'channels' => $channels,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
    
    /**
     * Handle get messages
     */
    private function handleGetMessages(ConnectionInterface $from, $data) {
        $channelId = $data['channelId'] ?? '';
        $sinceTime = $data['sinceTime'] ?? 0;
        $type = $data['type'] ?? 'HTML';
        
        if (empty($channelId)) {
            throw new Exception('Channel ID is required');
        }
        
        $messages = $this->coordinator->getMessages($channelId, $sinceTime, $type);
        
        $from->send(json_encode([
            'type' => 'messagesList',
            'messages' => $messages,
            'channelId' => $channelId,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
    
    /**
     * Handle search messages
     */
    private function handleSearchMessages(ConnectionInterface $from, $data) {
        $query = $data['query'] ?? '';
        $channelId = $data['channelId'] ?? '';
        $limit = $data['limit'] ?? 50;
        
        if (empty($query)) {
            throw new Exception('Search query is required');
        }
        
        $messages = $this->coordinator->searchMessages($query, $channelId, $limit);
        
        $from->send(json_encode([
            'type' => 'searchResults',
            'messages' => $messages,
            'query' => $query,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
    
    /**
     * Handle create channel
     */
    private function handleCreateChannel(ConnectionInterface $from, $data) {
        $name = $data['name'] ?? '';
        $agents = $data['agents'] ?? ['captain_wolfie'];
        $type = $data['type'] ?? 'general';
        $description = $data['description'] ?? '';
        $token = $data['token'] ?? null;
        
        if (empty($name)) {
            throw new Exception('Channel name is required');
        }
        
        $channel = $this->coordinator->createChannel($name, $agents, $type, $description, $token);
        
        $from->send(json_encode([
            'type' => 'channelCreated',
            'channel' => $channel,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        
        // Broadcast to all clients
        $this->broadcastToAll([
            'type' => 'channelCreated',
            'channel' => $channel,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Handle get system status
     */
    private function handleGetSystemStatus(ConnectionInterface $from, $data) {
        $status = $this->coordinator->getStatus();
        
        $from->send(json_encode([
            'type' => 'systemStatus',
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
    
    /**
     * Broadcast message to all clients in a channel
     */
    private function broadcastToChannel($channelId, $message) {
        if (isset($this->channels[$channelId])) {
            foreach ($this->channels[$channelId] as $client) {
                $client->send(json_encode($message));
            }
        }
    }
    
    /**
     * Broadcast message to all connected clients
     */
    private function broadcastToAll($message) {
        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }
    
    /**
     * Log events
     */
    private function logEvent($message, $level = 'INFO') {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] WolfieWebSocketServer: $message\n";
        
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($level === 'ERROR') {
            error_log("WolfieWebSocketServer: $message");
        }
    }
}

// Start the WebSocket server
if (php_sapi_name() === 'cli') {
    $port = 8080;
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new WolfieWebSocketHandler()
            )
        ),
        $port
    );
    
    echo "🛸 WOLFIE AGI WebSocket Server starting on port $port...\n";
    echo "Press Ctrl+C to stop the server\n";
    
    $server->run();
} else {
    echo "This script must be run from the command line\n";
}
?>
