/**
 * WOLFIE AGI UI - Modern Channel System (JavaScript)
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Modern JavaScript frontend for secure channel system integration
 * WHERE: C:\START\WOLFIE_AGI_UI\ui\wolfie_channels\
 * WHEN: 2025-09-26 17:35:00 CDT
 * WHY: To provide secure frontend integration with MySQL backend
 * HOW: ES6+ JavaScript with fetch API and WebSocket support
 * HELP: Contact Captain WOLFIE for frontend integration questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for secure frontend
 * GENESIS: Foundation of modern frontend channel communication
 * MD: Markdown documentation with .js implementation
 * 
 * FILE IDS: [MODERN_CHANNEL_SYSTEM_JS_001, WOLFIE_AGI_UI_047]
 * 
 * VERSION: 1.0.0 - The Captain's Modern Channel System
 * STATUS: Active - Secure Frontend with MySQL Backend Integration
 */

class ModernChannelSystem {
    constructor() {
        this.apiUrl = 'http://localhost/WOLFIE_AGI_UI/api/endpoint_handler_secure.php';
        this.wsUrl = 'ws://localhost:8080';
        this.eventListeners = {};
        this.currentChannel = null;
        this.pollingInterval = null;
        this.polling = false;
        this.websocket = null;
        this.websocketConnected = false;
        this.retryCount = 0;
        this.maxRetries = 5;
        this.retryDelay = 1000;
        
        // Security settings
        this.maxMessageLength = 1000;
        this.allowedAgents = [
            'captain_wolfie', 'cursor', 'copilot', 'ara', 'grok', 'claude', 
            'deepseek', 'gemini', 'doctor_bones', 'parallel_wolfie', 'wolfie_ai',
            'agape_guide', 'ethical_guardian', 'wisdom_keeper'
        ];
        
        // XSS protection patterns
        this.xssPatterns = [
            /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi,
            /<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi,
            /<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi,
            /<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi,
            /javascript:/i,
            /vbscript:/i,
            /onload\s*=/i,
            /onerror\s*=/i,
            /onclick\s*=/i,
            /onmouseover\s*=/i
        ];
        
        this.initializeSystem();
    }
    
    /**
     * Initialize the system
     */
    initializeSystem() {
        this.logEvent('ModernChannelSystem initialized');
        this.connectWebSocket();
        this.startHealthCheck();
    }
    
    /**
     * Event system
     */
    on(event, callback) {
        this.eventListeners[event] = this.eventListeners[event] || [];
        this.eventListeners[event].push(callback);
    }
    
    emit(event, data) {
        (this.eventListeners[event] || []).forEach(callback => callback(data));
    }
    
    /**
     * Connect to WebSocket for real-time communication
     */
    connectWebSocket() {
        try {
            this.websocket = new WebSocket(this.wsUrl);
            
            this.websocket.onopen = () => {
                this.websocketConnected = true;
                this.retryCount = 0;
                this.logEvent('WebSocket connected');
                this.emit('connected', { type: 'websocket' });
            };
            
            this.websocket.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    this.handleWebSocketMessage(data);
                } catch (error) {
                    this.logEvent('WebSocket message parse error: ' + error.message, 'ERROR');
                }
            };
            
            this.websocket.onclose = () => {
                this.websocketConnected = false;
                this.logEvent('WebSocket disconnected');
                this.emit('disconnected', { type: 'websocket' });
                this.attemptReconnect();
            };
            
            this.websocket.onerror = (error) => {
                this.logEvent('WebSocket error: ' + error.message, 'ERROR');
                this.emit('error', error);
            };
            
        } catch (error) {
            this.logEvent('WebSocket connection failed: ' + error.message, 'ERROR');
            this.fallbackToPolling();
        }
    }
    
    /**
     * Handle WebSocket messages
     */
    handleWebSocketMessage(data) {
        switch (data.type) {
            case 'message':
                this.emit('newMessages', [data.message]);
                break;
            case 'channelUpdate':
                this.emit('channelUpdated', data.channel);
                break;
            case 'systemStatus':
                this.emit('statusUpdate', data.status);
                break;
            case 'error':
                this.emit('error', new Error(data.message));
                break;
            default:
                this.logEvent('Unknown WebSocket message type: ' + data.type);
        }
    }
    
    /**
     * Attempt to reconnect WebSocket
     */
    attemptReconnect() {
        if (this.retryCount < this.maxRetries) {
            this.retryCount++;
            this.logEvent(`Attempting WebSocket reconnection ${this.retryCount}/${this.maxRetries}`);
            setTimeout(() => this.connectWebSocket(), this.retryDelay * this.retryCount);
        } else {
            this.logEvent('WebSocket reconnection failed, falling back to polling', 'WARN');
            this.fallbackToPolling();
        }
    }
    
    /**
     * Fallback to polling if WebSocket fails
     */
    fallbackToPolling() {
        this.logEvent('Starting polling fallback');
        this.startPolling();
    }
    
    /**
     * Start polling for updates
     */
    startPolling() {
        if (this.polling) return;
        this.polling = true;
        this.pollingInterval = setInterval(() => this.pollMessages(), 2100); // 2.1s like SalesSyntax
        this.emit('connected', { type: 'polling' });
    }
    
    /**
     * Stop polling
     */
    stopPolling() {
        this.polling = false;
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
    
    /**
     * Get system status
     */
    async getSystemStatus() {
        try {
            const response = await this.makeApiCall('getSystemStatus');
            if (response.success) {
                this.emit('statusUpdate', {
                    connection: response.data.system_status,
                    channel: this.currentChannel,
                    polling: this.polling,
                    websocket: this.websocketConnected
                });
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            return { connection: 'error', channel: null, polling: false, websocket: false };
        }
    }
    
    /**
     * Get all channels
     */
    async getAllChannels() {
        try {
            const response = await this.makeApiCall('getAllChannels');
            if (response.success) {
                this.emit('channelsLoaded', response.data);
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            return [];
        }
    }
    
    /**
     * Create a new channel
     */
    async createChannel(name, agents = ['captain_wolfie'], type = 'general', description = '') {
        try {
            // Validate inputs
            this.validateChannelName(name);
            this.validateAgents(agents);
            
            const response = await this.makeApiCall('createChannel', {
                name,
                agents,
                type,
                description
            });
            
            if (response.success) {
                this.currentChannel = response.data.channel_id;
                this.emit('channelCreated', response.data);
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            throw error;
        }
    }
    
    /**
     * Send message to current channel
     */
    async sendMessage(message, agentId = 'captain_wolfie') {
        if (!this.currentChannel) {
            throw new Error('No active channel');
        }
        
        try {
            // Validate and sanitize message
            this.validateMessage(message);
            const sanitizedMessage = this.sanitizeMessage(message);
            
            const response = await this.makeApiCall('sendChannelMessage', {
                channelId: this.currentChannel,
                agentId,
                message: sanitizedMessage
            });
            
            if (response.success) {
                this.emit('messageSent', response.data);
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            throw error;
        }
    }
    
    /**
     * Get messages from current channel
     */
    async getMessages(sinceTime = 0, type = 'HTML') {
        if (!this.currentChannel) {
            throw new Error('No active channel');
        }
        
        try {
            const response = await this.makeApiCall('getMessages', {
                channelId: this.currentChannel,
                sinceTime,
                type
            });
            
            if (response.success) {
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            return [];
        }
    }
    
    /**
     * Poll for new messages
     */
    async pollMessages() {
        if (!this.currentChannel) return;
        
        try {
            const messages = await this.getMessages();
            if (messages.length > 0) {
                this.emit('newMessages', messages);
            }
        } catch (error) {
            this.emit('error', error);
        }
    }
    
    /**
     * Get channel status
     */
    async getChannelStatus(channelId = null) {
        const targetChannel = channelId || this.currentChannel;
        if (!targetChannel) {
            throw new Error('No channel specified');
        }
        
        try {
            const response = await this.makeApiCall('getChannelStatus', {
                channelId: targetChannel
            });
            
            if (response.success) {
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            return null;
        }
    }
    
    /**
     * Search messages
     */
    async searchMessages(query, channelId = null, limit = 50) {
        try {
            const response = await this.makeApiCall('searchMessages', {
                query,
                channelId: channelId || this.currentChannel,
                limit
            });
            
            if (response.success) {
                this.emit('searchResults', response.data);
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            return [];
        }
    }
    
    /**
     * Coordinate multi-agent chat
     */
    async coordinateMultiAgentChat(message, context = {}) {
        try {
            this.validateMessage(message);
            const sanitizedMessage = this.sanitizeMessage(message);
            
            const response = await this.makeApiCall('coordinateMultiAgentChat', {
                message: sanitizedMessage,
                agentContext: context
            });
            
            if (response.success) {
                this.emit('multiAgentResponse', response.data);
                return response.data;
            } else {
                throw new Error(response.error);
            }
        } catch (error) {
            this.emit('error', error);
            throw error;
        }
    }
    
    /**
     * Make API call with error handling
     */
    async makeApiCall(action, data = {}) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action,
                    ...data
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            return result;
            
        } catch (error) {
            this.logEvent(`API call failed: ${action} - ${error.message}`, 'ERROR');
            throw error;
        }
    }
    
    /**
     * Validate channel name
     */
    validateChannelName(name) {
        if (!name || typeof name !== 'string') {
            throw new Error('Channel name is required');
        }
        
        if (name.length > this.maxMessageLength) {
            throw new Error(`Channel name exceeds maximum length of ${this.maxMessageLength} characters`);
        }
        
        if (!/^[a-zA-Z0-9\s\-_]+$/.test(name)) {
            throw new Error('Channel name contains invalid characters');
        }
    }
    
    /**
     * Validate agents
     */
    validateAgents(agents) {
        if (!Array.isArray(agents)) {
            throw new Error('Agents must be an array');
        }
        
        for (const agent of agents) {
            if (!this.allowedAgents.includes(agent)) {
                throw new Error(`Unauthorized agent: ${agent}`);
            }
        }
    }
    
    /**
     * Validate message
     */
    validateMessage(message) {
        if (!message || typeof message !== 'string') {
            throw new Error('Message is required');
        }
        
        if (message.length > this.maxMessageLength) {
            throw new Error(`Message exceeds maximum length of ${this.maxMessageLength} characters`);
        }
        
        // Check for XSS patterns
        for (const pattern of this.xssPatterns) {
            if (pattern.test(message)) {
                throw new Error('Potentially malicious content detected: XSS attempt blocked');
            }
        }
    }
    
    /**
     * Sanitize message content
     */
    sanitizeMessage(message) {
        // Escape HTML characters
        const div = document.createElement('div');
        div.textContent = message;
        return div.innerHTML;
    }
    
    /**
     * Escape HTML for display
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Start health check
     */
    startHealthCheck() {
        setInterval(async () => {
            try {
                await this.getSystemStatus();
            } catch (error) {
                this.logEvent('Health check failed: ' + error.message, 'ERROR');
            }
        }, 30000); // Every 30 seconds
    }
    
    /**
     * Log events
     */
    logEvent(message, level = 'INFO') {
        const timestamp = new Date().toISOString();
        const logMessage = `[${timestamp}] [${level}] ModernChannelSystem: ${message}`;
        
        console.log(logMessage);
        
        // Emit log event for UI
        this.emit('log', { message, level, timestamp });
    }
    
    /**
     * Get connection status
     */
    getConnectionStatus() {
        return {
            websocket: this.websocketConnected,
            polling: this.polling,
            currentChannel: this.currentChannel,
            retryCount: this.retryCount
        };
    }
    
    /**
     * Disconnect and cleanup
     */
    disconnect() {
        this.stopPolling();
        
        if (this.websocket) {
            this.websocket.close();
            this.websocket = null;
        }
        
        this.currentChannel = null;
        this.websocketConnected = false;
        this.polling = false;
        
        this.logEvent('Disconnected from channel system');
    }
}

// Export for use in HTML
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModernChannelSystem;
} else {
    window.ModernChannelSystem = ModernChannelSystem;
}
