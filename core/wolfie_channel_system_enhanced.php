<?php
/**
 * WOLFIE AGI UI - Enhanced Channel System (CSV with Security & Performance)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Enhanced CSV-based channel system with security and performance improvements
 * WHERE: C:\START\WOLFIE_AGI_UI\core\
 * WHEN: 2025-09-26 16:35:00 CDT
 * WHY: To address security, performance, and concurrency issues in CSV-based system
 * HOW: Enhanced PHP with file locking, input validation, and optimized operations
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for secure communication
 * GENESIS: Foundation of enhanced channel communication
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [WOLFIE_CHANNEL_SYSTEM_ENHANCED_001, WOLFIE_AGI_UI_036]
 * 
 * VERSION: 1.0.0 - The Captain's Enhanced Channel System
 * STATUS: Active - Security and Performance Enhanced
 */

class WolfieChannelSystemEnhanced {
    private $channelsFile;
    private $messagesFile;
    private $usersFile;
    private $dataDir;
    private $lockDir;
    
    // In-memory cache for performance
    private $channels = [];
    private $messages = [];
    private $users = [];
    private $cacheTimestamp = 0;
    private $cacheDuration = 30; // 30 seconds cache
    
    // Security settings
    private $maxMessageLength = 1000;
    private $maxChannelNameLength = 100;
    private $allowedMessageTypes = ['HTML', 'TEXT', 'JSON', 'SYSTEM'];
    private $allowedChannelTypes = ['general', 'private', 'public', 'meeting', 'support'];
    
    public function __construct() {
        $this->dataDir = __DIR__ . '/../data';
        $this->lockDir = __DIR__ . '/../data/locks';
        $this->channelsFile = $this->dataDir . '/channels.csv';
        $this->messagesFile = $this->dataDir . '/messages.csv';
        $this->usersFile = $this->dataDir . '/users.csv';
        
        $this->ensureDataDirectory();
        $this->loadData();
    }
    
    /**
     * Enhanced data directory creation with proper permissions
     */
    private function ensureDataDirectory() {
        if (!is_dir($this->dataDir)) {
            if (!mkdir($this->dataDir, 0755, true)) {
                throw new Exception("Failed to create data directory: {$this->dataDir}");
            }
        }
        
        if (!is_dir($this->lockDir)) {
            if (!mkdir($this->lockDir, 0755, true)) {
                throw new Exception("Failed to create lock directory: {$this->lockDir}");
            }
        }
        
        // Ensure files exist with proper headers
        $this->ensureFileExists($this->channelsFile, ['channel_id', 'name', 'type', 'description', 'created_at', 'status', 'user_count']);
        $this->ensureFileExists($this->messagesFile, ['message_id', 'channel_id', 'user_id', 'message', 'type', 'timeof', 'jsrn', 'created_at']);
        $this->ensureFileExists($this->usersFile, ['user_id', 'username', 'session_id', 'jsrn', 'last_action', 'status', 'created_at']);
    }
    
    /**
     * Ensure file exists with proper headers
     */
    private function ensureFileExists($file, $headers) {
        if (!file_exists($file)) {
            $handle = fopen($file, 'w');
            if ($handle === false) {
                throw new Exception("Failed to create file: $file");
            }
            fputcsv($handle, $headers);
            fclose($handle);
        }
    }
    
    /**
     * Enhanced file locking mechanism
     */
    private function acquireLock($file, $operation = 'r') {
        $lockFile = $this->lockDir . '/' . basename($file) . '.lock';
        $handle = fopen($lockFile, 'c');
        
        if ($handle === false) {
            throw new Exception("Failed to create lock file: $lockFile");
        }
        
        $lockType = ($operation === 'w') ? LOCK_EX : LOCK_SH;
        $timeout = 5; // 5 second timeout
        $startTime = time();
        
        while (!flock($handle, $lockType | LOCK_NB)) {
            if (time() - $startTime > $timeout) {
                fclose($handle);
                throw new Exception("Lock timeout for file: $file");
            }
            usleep(100000); // 0.1 second
        }
        
        return $handle;
    }
    
    /**
     * Release file lock
     */
    private function releaseLock($handle, $file) {
        if ($handle) {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
        
        $lockFile = $this->lockDir . '/' . basename($file) . '.lock';
        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
    
    /**
     * Enhanced input validation and sanitization
     */
    private function validateAndSanitize($input, $type, $maxLength = null) {
        if (is_null($input)) {
            throw new Exception("Input cannot be null");
        }
        
        $input = trim($input);
        
        if (empty($input) && $type !== 'description') {
            throw new Exception("Input cannot be empty");
        }
        
        if ($maxLength && strlen($input) > $maxLength) {
            throw new Exception("Input exceeds maximum length of $maxLength characters");
        }
        
        switch ($type) {
            case 'channel_name':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                if (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $input)) {
                    throw new Exception("Channel name contains invalid characters");
                }
                break;
                
            case 'message':
                $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
                break;
                
            case 'description':
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                break;
                
            case 'user_id':
                if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $input)) {
                    throw new Exception("User ID contains invalid characters");
                }
                break;
                
            case 'channel_type':
                if (!in_array($input, $this->allowedChannelTypes)) {
                    throw new Exception("Invalid channel type: $input");
                }
                break;
                
            case 'message_type':
                if (!in_array($input, $this->allowedMessageTypes)) {
                    throw new Exception("Invalid message type: $input");
                }
                break;
        }
        
        return $input;
    }
    
    /**
     * Load data with caching
     */
    private function loadData() {
        $currentTime = time();
        
        // Use cache if still valid
        if ($this->cacheTimestamp && ($currentTime - $this->cacheTimestamp) < $this->cacheDuration) {
            return;
        }
        
        $this->loadChannels();
        $this->loadMessages();
        $this->loadUsers();
        
        $this->cacheTimestamp = $currentTime;
    }
    
    /**
     * Enhanced channel loading with file locking
     */
    private function loadChannels() {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->channelsFile, 'r');
            
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
            
        } finally {
            $this->releaseLock($handle, $this->channelsFile);
        }
    }
    
    /**
     * Enhanced message loading with file locking
     */
    private function loadMessages() {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->messagesFile, 'r');
            
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
            
        } finally {
            $this->releaseLock($handle, $this->messagesFile);
        }
    }
    
    /**
     * Enhanced user loading with file locking
     */
    private function loadUsers() {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->usersFile, 'r');
            
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
            
        } finally {
            $this->releaseLock($handle, $this->usersFile);
        }
    }
    
    /**
     * Enhanced channel creation with validation
     */
    public function createChannel($name, $type = 'general', $description = '') {
        try {
            // Validate and sanitize inputs
            $name = $this->validateAndSanitize($name, 'channel_name', $this->maxChannelNameLength);
            $type = $this->validateAndSanitize($type, 'channel_type');
            $description = $this->validateAndSanitize($description, 'description', 500);
            
            // Check if channel name already exists
            foreach ($this->channels as $channel) {
                if (strtolower($channel['name']) === strtolower($name)) {
                    throw new Exception("Channel name already exists: $name");
                }
            }
            
            $channelId = 'channel_' . uniqid();
            $timestamp = time();
            
            $newChannel = [
                'channel_id' => $channelId,
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'created_at' => $timestamp,
                'status' => 'active',
                'user_count' => 0
            ];
            
            $this->channels[] = $newChannel;
            $this->saveChannels();
            
            $this->log("Channel created: $name ($channelId)");
            return $channelId;
            
        } catch (Exception $e) {
            $this->log("Error creating channel: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Enhanced message sending with validation and collision prevention
     */
    public function sendMessage($channelId, $userId, $message, $type = 'HTML') {
        try {
            // Validate and sanitize inputs
            $message = $this->validateAndSanitize($message, 'message', $this->maxMessageLength);
            $type = $this->validateAndSanitize($type, 'message_type');
            $userId = $this->validateAndSanitize($userId, 'user_id');
            
            // Verify channel exists
            $channelExists = false;
            foreach ($this->channels as $channel) {
                if ($channel['channel_id'] === $channelId) {
                    $channelExists = true;
                    break;
                }
            }
            
            if (!$channelExists) {
                throw new Exception("Channel not found: $channelId");
            }
            
            // Generate unique timestamp with collision prevention
            $timeof = $this->generateUniqueTimestamp();
            $jsrn = $this->getUserJSRN($userId);
            $messageId = 'msg_' . uniqid();
            $timestamp = time();
            
            $newMessage = [
                'message_id' => $messageId,
                'channel_id' => $channelId,
                'user_id' => $userId,
                'message' => $message,
                'type' => $type,
                'timeof' => $timeof,
                'jsrn' => $jsrn,
                'created_at' => $timestamp
            ];
            
            $this->messages[] = $newMessage;
            $this->appendMessage($newMessage); // Append instead of rewriting entire file
            
            $this->log("Message sent to channel $channelId by user $userId");
            return [
                'message_id' => $messageId,
                'timeof' => $timeof,
                'jsrn' => $jsrn
            ];
            
        } catch (Exception $e) {
            $this->log("Error sending message: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Generate unique timestamp with collision prevention
     */
    private function generateUniqueTimestamp() {
        $timeof = time();
        $attempts = 0;
        $maxAttempts = 100;
        
        while ($this->messageExists($timeof) && $attempts < $maxAttempts) {
            $timeof = time() + $attempts;
            $attempts++;
        }
        
        if ($attempts >= $maxAttempts) {
            $timeof = microtime(true) * 1000000; // Use microtime as fallback
        }
        
        return $timeof;
    }
    
    /**
     * Check if message timestamp exists
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
     * Optimized message appending (instead of rewriting entire file)
     */
    private function appendMessage($message) {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->messagesFile, 'w');
            
            // Move to end of file
            fseek($handle, 0, SEEK_END);
            
            // Append message
            fputcsv($handle, $message);
            
        } finally {
            $this->releaseLock($handle, $this->messagesFile);
        }
    }
    
    /**
     * Enhanced message retrieval with filtering
     */
    public function getMessages($channelId, $sinceTime = 0, $format = 'JSON') {
        try {
            $this->loadData(); // Refresh cache
            
            $channelMessages = array_filter($this->messages, function($message) use ($channelId, $sinceTime) {
                return $message['channel_id'] === $channelId && $message['timeof'] > $sinceTime;
            });
            
            // Sort by timestamp
            usort($channelMessages, function($a, $b) {
                return $a['timeof'] <=> $b['timeof'];
            });
            
            if ($format === 'JS') {
                return $this->generateMessageJS($channelMessages);
            }
            
            return $channelMessages;
            
        } catch (Exception $e) {
            $this->log("Error retrieving messages: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Enhanced JavaScript generation with security
     */
    private function generateMessageJS($messages) {
        $js = '';
        $index = 0;
        
        foreach ($messages as $message) {
            $js .= "messages[$index] = new Array();\n";
            $js .= "messages[$index][0] = " . intval($message['timeof']) . ";\n";
            $js .= "messages[$index][1] = " . intval($message['jsrn']) . ";\n";
            $js .= "messages[$index][2] = \"" . addslashes($message['type']) . "\";\n";
            $js .= "messages[$index][3] = \"" . addslashes($message['message']) . "\";\n";
            $js .= "messages[$index][4] = \"\";\n";
            $index++;
        }
        
        return $js;
    }
    
    /**
     * Enhanced user management
     */
    public function addUserToChannel($userId, $channelId, $sessionId = null) {
        try {
            $userId = $this->validateAndSanitize($userId, 'user_id');
            
            // Create user if doesn't exist
            if (!$this->userExists($userId)) {
                $this->createUser($userId, $sessionId);
            }
            
            // Add user to channel (update user count)
            foreach ($this->channels as $index => $channel) {
                if ($channel['channel_id'] === $channelId) {
                    $this->channels[$index]['user_count']++;
                    $this->saveChannels();
                    break;
                }
            }
            
            $this->log("User $userId added to channel $channelId");
            return true;
            
        } catch (Exception $e) {
            $this->log("Error adding user to channel: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Create user with validation
     */
    private function createUser($userId, $sessionId = null) {
        $sessionId = $sessionId ?: 'session_' . uniqid();
        $jsrn = rand(1, 999999);
        $timestamp = time();
        
        $newUser = [
            'user_id' => $userId,
            'username' => $userId,
            'session_id' => $sessionId,
            'jsrn' => $jsrn,
            'last_action' => $timestamp,
            'status' => 'active',
            'created_at' => $timestamp
        ];
        
        $this->users[] = $newUser;
        $this->saveUsers();
        
        $this->log("User created: $userId");
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
     * Get user JSRN
     */
    private function getUserJSRN($userId) {
        foreach ($this->users as $user) {
            if ($user['user_id'] === $userId) {
                return intval($user['jsrn']);
            }
        }
        return rand(1, 999999);
    }
    
    /**
     * Enhanced channel status with real-time data
     */
    public function getChannelStatus($channelId) {
        try {
            $this->loadData(); // Refresh cache
            
            foreach ($this->channels as $channel) {
                if ($channel['channel_id'] === $channelId) {
                    // Count actual messages for this channel
                    $messageCount = 0;
                    foreach ($this->messages as $message) {
                        if ($message['channel_id'] === $channelId) {
                            $messageCount++;
                        }
                    }
                    
                    return [
                        'channel_id' => $channel['channel_id'],
                        'name' => $channel['name'],
                        'type' => $channel['type'],
                        'status' => $channel['status'],
                        'user_count' => intval($channel['user_count']),
                        'message_count' => $messageCount,
                        'created_at' => $channel['created_at']
                    ];
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            $this->log("Error getting channel status: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    /**
     * Enhanced data saving with file locking
     */
    private function saveChannels() {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->channelsFile, 'w');
            
            if (empty($this->channels)) {
                fputcsv($handle, ['channel_id', 'name', 'type', 'description', 'created_at', 'status', 'user_count']);
            } else {
                $headerRow = array_keys($this->channels[0]);
                fputcsv($handle, $headerRow);
                foreach ($this->channels as $channel) {
                    fputcsv($handle, $channel);
                }
            }
            
        } finally {
            $this->releaseLock($handle, $this->channelsFile);
        }
    }
    
    private function saveUsers() {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->usersFile, 'w');
            
            if (empty($this->users)) {
                fputcsv($handle, ['user_id', 'username', 'session_id', 'jsrn', 'last_action', 'status', 'created_at']);
            } else {
                $headerRow = array_keys($this->users[0]);
                fputcsv($handle, $headerRow);
                foreach ($this->users as $user) {
                    fputcsv($handle, $user);
                }
            }
            
        } finally {
            $this->releaseLock($handle, $this->usersFile);
        }
    }
    
    /**
     * Enhanced logging system
     */
    private function log($message, $level = 'INFO') {
        $logFile = $this->dataDir . '/logs/system.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] WolfieChannelSystemEnhanced: $message\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also log to error log for ERROR level
        if ($level === 'ERROR') {
            error_log("WolfieChannelSystemEnhanced: $message");
        }
    }
    
    /**
     * Get system statistics
     */
    public function getSystemStats() {
        $this->loadData();
        
        return [
            'total_channels' => count($this->channels),
            'total_messages' => count($this->messages),
            'total_users' => count($this->users),
            'cache_timestamp' => $this->cacheTimestamp,
            'cache_duration' => $this->cacheDuration
        ];
    }
    
    /**
     * Cleanup old messages (maintenance)
     */
    public function cleanupOldMessages($daysOld = 30) {
        try {
            $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
            $originalCount = count($this->messages);
            
            $this->messages = array_filter($this->messages, function($message) use ($cutoffTime) {
                return $message['timeof'] > $cutoffTime;
            });
            
            $removedCount = $originalCount - count($this->messages);
            
            if ($removedCount > 0) {
                $this->saveMessages();
                $this->log("Cleaned up $removedCount old messages");
            }
            
            return $removedCount;
            
        } catch (Exception $e) {
            $this->log("Error cleaning up messages: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }
    
    private function saveMessages() {
        $handle = null;
        try {
            $handle = $this->acquireLock($this->messagesFile, 'w');
            
            if (empty($this->messages)) {
                fputcsv($handle, ['message_id', 'channel_id', 'user_id', 'message', 'type', 'timeof', 'jsrn', 'created_at']);
            } else {
                $headerRow = array_keys($this->messages[0]);
                fputcsv($handle, $headerRow);
                foreach ($this->messages as $message) {
                    fputcsv($handle, $message);
                }
            }
            
        } finally {
            $this->releaseLock($handle, $this->messagesFile);
        }
    }
}
?>
