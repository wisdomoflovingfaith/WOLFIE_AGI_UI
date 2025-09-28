<?php
/**
 * WOLFIE AGI UI - Channel System (Based on SalesSyntax 3.7.0)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Real channel system based on salessyntax3.7.0 XMLHttpRequest implementation
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 15:00:00 CDT
 * WHY: To implement proper channels like salessyntax3.7.0, not fake ones
 * HOW: PHP-based channel system with CSV storage and 2.1 second polling
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of real channel communication
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [WOLFIE_CHANNEL_SYSTEM_001, WOLFIE_AGI_UI_020]
 * 
 * VERSION: 1.0.0 - The Captain's Real Channel System
 * STATUS: Active - Based on SalesSyntax 3.7.0
 */

class WolfieChannelSystem {
    private $channelsFile;
    private $messagesFile;
    private $usersFile;
    private $channels;
    private $messages;
    private $users;
    
    public function __construct() {
        $this->channelsFile = __DIR__ . '/../data/channels.csv';
        $this->messagesFile = __DIR__ . '/../data/messages.csv';
        $this->usersFile = __DIR__ . '/../data/users.csv';
        $this->ensureDataDirectory();
        $this->loadData();
    }
    
    private function ensureDataDirectory() {
        $dataDir = dirname($this->channelsFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }
    }
    
    private function loadData() {
        $this->loadChannels();
        $this->loadMessages();
        $this->loadUsers();
    }
    
    private function loadChannels() {
        if (!file_exists($this->channelsFile)) {
            $this->channels = [];
            return;
        }
        
        $rows = array_map('str_getcsv', file($this->channelsFile));
        if (empty($rows)) {
            $this->channels = [];
            return;
        }
        
        $headerRow = array_shift($rows);
        $this->channels = [];
        foreach ($rows as $row) {
            if (count($row) === count($headerRow)) {
                $this->channels[] = array_combine($headerRow, $row);
            }
        }
    }
    
    private function loadMessages() {
        if (!file_exists($this->messagesFile)) {
            $this->messages = [];
            return;
        }
        
        $rows = array_map('str_getcsv', file($this->messagesFile));
        if (empty($rows)) {
            $this->messages = [];
            return;
        }
        
        $headerRow = array_shift($rows);
        $this->messages = [];
        foreach ($rows as $row) {
            if (count($row) === count($headerRow)) {
                $this->messages[] = array_combine($headerRow, $row);
            }
        }
    }
    
    private function loadUsers() {
        if (!file_exists($this->usersFile)) {
            $this->users = [];
            return;
        }
        
        $rows = array_map('str_getcsv', file($this->usersFile));
        if (empty($rows)) {
            $this->users = [];
            return;
        }
        
        $headerRow = array_shift($rows);
        $this->users = [];
        foreach ($rows as $row) {
            if (count($row) === count($headerRow)) {
                $this->users[] = array_combine($headerRow, $row);
            }
        }
    }
    
    private function saveChannels() {
        $handle = fopen($this->channelsFile, 'w');
        if ($handle === false) return false;
        
        if (empty($this->channels)) {
            fputcsv($handle, ['channel_id', 'name', 'type', 'description', 'created_at', 'status']);
        } else {
            $headerRow = array_keys($this->channels[0]);
            fputcsv($handle, $headerRow);
            foreach ($this->channels as $channel) {
                fputcsv($handle, $channel);
            }
        }
        fclose($handle);
        return true;
    }
    
    private function saveMessages() {
        $handle = fopen($this->messagesFile, 'w');
        if ($handle === false) return false;
        
        if (empty($this->messages)) {
            fputcsv($handle, ['message_id', 'channel_id', 'user_id', 'message', 'timeof', 'type', 'jsrn']);
        } else {
            $headerRow = array_keys($this->messages[0]);
            fputcsv($handle, $headerRow);
            foreach ($this->messages as $message) {
                fputcsv($handle, $message);
            }
        }
        fclose($handle);
        return true;
    }
    
    private function saveUsers() {
        $handle = fopen($this->usersFile, 'w');
        if ($handle === false) return false;
        
        if (empty($this->users)) {
            fputcsv($handle, ['user_id', 'sessionid', 'username', 'onchannel', 'status', 'lastaction', 'jsrn']);
        } else {
            $headerRow = array_keys($this->users[0]);
            fputcsv($handle, $headerRow);
            foreach ($this->users as $user) {
                fputcsv($handle, $user);
            }
        }
        fclose($handle);
        return true;
    }
    
    /**
     * Create a new channel (like salessyntax3.7.0)
     */
    public function createChannel($name, $type = 'general', $description = '') {
        $channelId = 'channel_' . uniqid();
        $timeof = date('YmdHis');
        
        $channel = [
            'channel_id' => $channelId,
            'name' => $name,
            'type' => $type,
            'description' => $description,
            'created_at' => $timeof,
            'status' => 'active'
        ];
        
        $this->channels[] = $channel;
        $this->saveChannels();
        
        return $channelId;
    }
    
    /**
     * Add user to channel (like salessyntax3.7.0)
     */
    public function addUserToChannel($userId, $channelId, $sessionId = null) {
        // Update user's channel
        foreach ($this->users as $index => $user) {
            if ($user['user_id'] === $userId) {
                $this->users[$index]['onchannel'] = $channelId;
                $this->users[$index]['lastaction'] = date('YmdHis');
                break;
            }
        }
        
        // Create user if doesn't exist
        if (!$this->userExists($userId)) {
            $this->users[] = [
                'user_id' => $userId,
                'sessionid' => $sessionId ?: 'session_' . uniqid(),
                'username' => $userId,
                'onchannel' => $channelId,
                'status' => 'chat',
                'lastaction' => date('YmdHis'),
                'jsrn' => rand(1, 100)
            ];
        }
        
        $this->saveUsers();
        return true;
    }
    
    /**
     * Send message to channel (like salessyntax3.7.0)
     */
    public function sendMessage($channelId, $userId, $message, $type = 'HTML') {
        $timeof = date('YmdHis');
        
        // Check for duplicate timestamps (like salessyntax3.7.0)
        while ($this->messageExists($timeof)) {
            if (function_exists('sleep')) {
                sleep(1);
                $timeof = date('YmdHis');
            } else {
                $timeof++;
            }
        }
        
        $messageId = 'msg_' . uniqid();
        $jsrn = $this->getUserJSRN($userId);
        
        $messageData = [
            'message_id' => $messageId,
            'channel_id' => $channelId,
            'user_id' => $userId,
            'message' => $message,
            'timeof' => $timeof,
            'type' => $type,
            'jsrn' => $jsrn
        ];
        
        $this->messages[] = $messageData;
        $this->saveMessages();
        
        // Update user's last action
        $this->updateUserLastAction($userId);
        
        return $messageData;
    }
    
    /**
     * Get messages for channel (like salessyntax3.7.0)
     */
    public function getMessages($channelId, $sinceTime = 0, $type = 'HTML') {
        $messages = [];
        
        foreach ($this->messages as $message) {
            if ($message['channel_id'] === $channelId && 
                $message['type'] === $type && 
                $message['timeof'] > $sinceTime) {
                $messages[] = $message;
            }
        }
        
        // Sort by timestamp
        usort($messages, function($a, $b) {
            return $a['timeof'] - $b['timeof'];
        });
        
        return $messages;
    }
    
    /**
     * Generate JavaScript message array (like salessyntax3.7.0)
     */
    public function generateMessageJS($messages) {
        $js = '';
        
        foreach ($messages as $index => $message) {
            $js .= "messages[{$index}] = new Array(); ";
            $js .= "messages[{$index}][0] = {$message['timeof']}; ";
            $js .= "messages[{$index}][1] = {$message['jsrn']}; ";
            $js .= "messages[{$index}][2] = \"{$message['type']}\"; ";
            $js .= "messages[{$index}][3] = \"" . addslashes($message['message']) . "\"; ";
            $js .= "messages[{$index}][4] = \"\"; ";
        }
        
        return $js;
    }
    
    /**
     * Check if user exists
     */
    private function userExists($userId) {
        foreach ($this->users as $user) {
            if ($user['user_id'] === $userId) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if message exists (for timestamp collision)
     */
    private function messageExists($timeof) {
        foreach ($this->messages as $message) {
            if ($message['timeof'] == $timeof) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get user's JSRN (like salessyntax3.7.0)
     */
    private function getUserJSRN($userId) {
        foreach ($this->users as $user) {
            if ($user['user_id'] === $userId) {
                return $user['jsrn'];
            }
        }
        return rand(1, 100);
    }
    
    /**
     * Update user's last action
     */
    private function updateUserLastAction($userId) {
        foreach ($this->users as $index => $user) {
            if ($user['user_id'] === $userId) {
                $this->users[$index]['lastaction'] = date('YmdHis');
                break;
            }
        }
        $this->saveUsers();
    }
    
    /**
     * Get channel status
     */
    public function getChannelStatus($channelId) {
        foreach ($this->channels as $channel) {
            if ($channel['channel_id'] === $channelId) {
                $userCount = 0;
                foreach ($this->users as $user) {
                    if ($user['onchannel'] === $channelId) {
                        $userCount++;
                    }
                }
                
                $messageCount = 0;
                foreach ($this->messages as $message) {
                    if ($message['channel_id'] === $channelId) {
                        $messageCount++;
                    }
                }
                
                return [
                    'channel_id' => $channelId,
                    'name' => $channel['name'],
                    'type' => $channel['type'],
                    'status' => $channel['status'],
                    'user_count' => $userCount,
                    'message_count' => $messageCount,
                    'created_at' => $channel['created_at']
                ];
            }
        }
        return null;
    }
    
    /**
     * Get all channels
     */
    public function getAllChannels() {
        return $this->channels;
    }
    
    /**
     * Get all users
     */
    public function getAllUsers() {
        return $this->users;
    }
    
    /**
     * Get all messages
     */
    public function getAllMessages() {
        return $this->messages;
    }
}
?>
