<?php
/**
 * WOLFIE AGI UI - Secure Channel System (XSS Protected)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Secure channel system with comprehensive XSS protection
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 17:55:00 CDT
 * WHY: To fix SalesSyntax 3.7.0 XSS vulnerability and provide secure channels
 * HOW: PHP-based secure channel system with XSS protection and authentication
 * HELP: Contact Captain WOLFIE for secure channel system questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for secure channels
 * GENESIS: Foundation of secure channel communication protocols
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [WOLFIE_CHANNEL_SYSTEM_SECURE_001, WOLFIE_AGI_UI_051]
 * 
 * VERSION: 1.0.0 - The Captain's Secure Channel System
 * STATUS: Active - XSS Protected, Production Ready
 */

require_once '../config/database_config.php';

class WolfieChannelSystemSecure {
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
        '/onmouseover\s*=/i',
        '/onfocus\s*=/i',
        '/onblur\s*=/i',
        '/onchange\s*=/i',
        '/onsubmit\s*=/i',
        '/onreset\s*=/i',
        '/onselect\s*=/i',
        '/onkeydown\s*=/i',
        '/onkeyup\s*=/i',
        '/onkeypress\s*=/i',
        '/onmousedown\s*=/i',
        '/onmouseup\s*=/i',
        '/onmousemove\s*=/i',
        '/onmouseout\s*=/i',
        '/onmouseover\s*=/i',
        '/oncontextmenu\s*=/i',
        '/ondblclick\s*=/i',
        '/onabort\s*=/i',
        '/onbeforeunload\s*=/i',
        '/onerror\s*=/i',
        '/onhashchange\s*=/i',
        '/onload\s*=/i',
        '/onpageshow\s*=/i',
        '/onpagehide\s*=/i',
        '/onresize\s*=/i',
        '/onscroll\s*=/i',
        '/onunload\s*=/i'
    ];
    
    public function __construct() {
        $this->db = getDatabaseConnection();
        $this->logPath = __DIR__ . '/../logs/wolfie_channel_system_secure.log';
        $this->logEvent('WolfieChannelSystemSecure initialized with XSS protection');
    }
    
    /**
     * Create a new channel with XSS protection
     */
    public function createChannel($name, $type = 'general', $description = '') {
        try {
            // Validate and sanitize inputs
            $name = $this->sanitizeInput($name, 'channel_name', $this->maxChannelNameLength);
            $type = $this->sanitizeInput($type, 'channel_type');
            $description = $this->sanitizeInput($description, 'description', $this->maxDescriptionLength);
            
            // Validate channel type
            if (!in_array($type, ['general', 'private', 'public', 'meeting', 'support'])) {
                throw new Exception("Invalid channel type: $type");
            }
            
            $stmt = $this->db->prepare("INSERT INTO channels (channel_id, name, type, description, created_at, status) VALUES (:id, :name, :type, :desc, :created, 'active')");
            $id = 'channel_' . uniqid();
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':type' => $type,
                ':desc' => $description,
                ':created' => date('YmdHis')
            ]);
            
            $this->logEvent("Secure channel created: $name (ID: $id)");
            return $id;
            
        } catch (Exception $e) {
            $this->logEvent("Error creating secure channel: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Add user to channel with security validation
     */
    public function addUserToChannel($userId, $channelId, $sessionId = null) {
        try {
            $userId = $this->sanitizeInput($userId, 'user_id');
            $channelId = $this->sanitizeInput($channelId, 'channel_id');
            $sessionId = $sessionId ? $this->sanitizeInput($sessionId, 'session_id') : 'session_' . uniqid();
            
            // Check if user is already in channel
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM channel_users WHERE channel_id = :channel_id AND user_id = :user_id");
            $stmt->execute([':channel_id' => $channelId, ':user_id' => $userId]);
            
            if ($stmt->fetchColumn() > 0) {
                $this->logEvent("User $userId already in secure channel $channelId");
                return true;
            }
            
            $stmt = $this->db->prepare("INSERT INTO channel_users (channel_id, user_id, session_id, joined_at) VALUES (:channel_id, :user_id, :session_id, :joined)");
            $stmt->execute([
                ':channel_id' => $channelId,
                ':user_id' => $userId,
                ':session_id' => $sessionId,
                ':joined' => date('YmdHis')
            ]);
            
            $this->logEvent("User $userId added to secure channel $channelId");
            return true;
            
        } catch (Exception $e) {
            $this->logEvent("Error adding user to secure channel: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Send message to channel with comprehensive XSS protection
     */
    public function sendMessage($channelId, $userId, $message, $type = 'HTML') {
        try {
            $channelId = $this->sanitizeInput($channelId, 'channel_id');
            $userId = $this->sanitizeInput($userId, 'user_id');
            $message = $this->sanitizeInput($message, 'message', $this->maxMessageLength);
            $type = $this->sanitizeInput($type, 'message_type');
            
            // Validate message type
            if (!in_array($type, ['HTML', 'TEXT', 'JSON', 'MARKDOWN'])) {
                $type = 'HTML';
            }
            
            // Check if user is in channel
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM channel_users WHERE channel_id = :channel_id AND user_id = :user_id");
            $stmt->execute([':channel_id' => $channelId, ':user_id' => $userId]);
            
            if ($stmt->fetchColumn() == 0) {
                throw new Exception("User $userId not in channel $channelId");
            }
            
            $stmt = $this->db->prepare("INSERT INTO messages (message_id, channel_id, user_id, message, timeof, type, jsrn) VALUES (:id, :channel_id, :user_id, :message, :timeof, :type, :jsrn)");
            $id = 'msg_' . uniqid();
            $timeof = date('YmdHis');
            $jsrn = rand(1, 100);
            
            $stmt->execute([
                ':id' => $id,
                ':channel_id' => $channelId,
                ':user_id' => $userId,
                ':message' => $message,
                ':timeof' => $timeof,
                ':type' => $type,
                ':jsrn' => $jsrn
            ]);
            
            $this->logEvent("Secure message sent to channel $channelId by $userId: " . substr($message, 0, 50) . "...");
            
            return [
                'message_id' => $id,
                'channel_id' => $channelId,
                'user_id' => $userId,
                'message' => $message,
                'timeof' => $timeof,
                'type' => $type,
                'jsrn' => $jsrn
            ];
            
        } catch (Exception $e) {
            $this->logEvent("Error sending secure message: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Get messages from channel with security
     */
    public function getMessages($channelId, $sinceTime = 0, $type = 'HTML') {
        try {
            $channelId = $this->sanitizeInput($channelId, 'channel_id');
            $type = $this->sanitizeInput($type, 'message_type');
            
            $stmt = $this->db->prepare("SELECT * FROM messages WHERE channel_id = :channel_id AND type = :type AND timeof > :sinceTime ORDER BY timeof ASC");
            $stmt->execute([
                ':channel_id' => $channelId,
                ':type' => $type,
                ':sinceTime' => $sinceTime
            ]);
            
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->logEvent("Retrieved " . count($messages) . " secure messages from channel $channelId");
            
            return $messages;
            
        } catch (Exception $e) {
            $this->logEvent("Error getting secure messages: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    /**
     * Get channel status with security
     */
    public function getChannelStatus($channelId) {
        try {
            $channelId = $this->sanitizeInput($channelId, 'channel_id');
            
            $stmt = $this->db->prepare("SELECT * FROM channels WHERE channel_id = :channel_id");
            $stmt->execute([':channel_id' => $channelId]);
            $channel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$channel) {
                return null;
            }
            
            // Get user count
            $stmt = $this->db->prepare("SELECT COUNT(*) as user_count FROM channel_users WHERE channel_id = :channel_id");
            $stmt->execute([':channel_id' => $channelId]);
            $userCount = $stmt->fetchColumn();
            
            // Get message count
            $stmt = $this->db->prepare("SELECT COUNT(*) as message_count FROM messages WHERE channel_id = :channel_id");
            $stmt->execute([':channel_id' => $channelId]);
            $messageCount = $stmt->fetchColumn();
            
            // Get last message
            $stmt = $this->db->prepare("SELECT * FROM messages WHERE channel_id = :channel_id ORDER BY timeof DESC LIMIT 1");
            $stmt->execute([':channel_id' => $channelId]);
            $lastMessage = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return array_merge($channel, [
                'user_count' => $userCount,
                'message_count' => $messageCount,
                'last_message' => $lastMessage
            ]);
            
        } catch (Exception $e) {
            $this->logEvent("Error getting secure channel status: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }
    
    /**
     * Get all channels with security
     */
    public function getAllChannels() {
        try {
            $stmt = $this->db->query("SELECT * FROM channels ORDER BY created_at DESC");
            $channels = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add user and message counts for each channel
            foreach ($channels as &$channel) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as user_count FROM channel_users WHERE channel_id = :channel_id");
                $stmt->execute([':channel_id' => $channel['channel_id']]);
                $channel['user_count'] = $stmt->fetchColumn();
                
                $stmt = $this->db->prepare("SELECT COUNT(*) as message_count FROM messages WHERE channel_id = :channel_id");
                $stmt->execute([':channel_id' => $channel['channel_id']]);
                $channel['message_count'] = $stmt->fetchColumn();
            }
            
            $this->logEvent("Retrieved " . count($channels) . " secure channels");
            return $channels;
            
        } catch (Exception $e) {
            $this->logEvent("Error getting all secure channels: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    /**
     * Search messages with XSS protection
     */
    public function searchMessages($query, $channelId = null, $limit = 50) {
        try {
            $query = $this->sanitizeInput($query, 'search_query');
            $limit = (int)$limit;
            
            if ($limit > 100) {
                $limit = 100; // Cap at 100 for performance
            }
            
            $sql = "SELECT m.*, c.name as channel_name FROM messages m 
                    JOIN channels c ON m.channel_id = c.channel_id 
                    WHERE m.message LIKE :query";
            $params = [':query' => '%' . $query . '%'];
            
            if ($channelId) {
                $channelId = $this->sanitizeInput($channelId, 'channel_id');
                $sql .= " AND m.channel_id = :channel_id";
                $params[':channel_id'] = $channelId;
            }
            
            $sql .= " ORDER BY m.timeof DESC LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->logEvent("Secure search found " . count($messages) . " messages for query: $query");
            
            return $messages;
            
        } catch (Exception $e) {
            $this->logEvent("Error searching secure messages: " . $e->getMessage(), 'ERROR');
            return [];
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
                // Additional XSS protection
                $input = str_replace(['<', '>', '"', "'", '&'], ['&lt;', '&gt;', '&quot;', '&#x27;', '&amp;'], $input);
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
                
            case 'search_query':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
                break;
        }
        
        if ($maxLength && strlen($input) > $maxLength) {
            throw new Exception("Input exceeds maximum length of $maxLength characters");
        }
        
        return $input;
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
        $logEntry = "[$agapeTime] [$level] WolfieChannelSystemSecure: $message\n";
        
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($level === 'ERROR') {
            error_log("WolfieChannelSystemSecure: $message");
        }
    }
    
    /**
     * Close database connection
     */
    public function close() {
        $this->db = null;
        $this->logEvent('Secure database connection closed');
    }
}
?>