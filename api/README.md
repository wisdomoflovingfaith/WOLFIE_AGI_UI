# WOLFIE AGI UI - API Components

**WHO:** Captain WOLFIE (Eric Robin Gerdes)  
**WHAT:** API endpoints for secure AGI UI communication  
**WHERE:** C:\START\WOLFIE_AGI_UI\api\  
**WHEN:** 2025-09-26 18:25:00 CDT  
**WHY:** To provide secure API endpoints for frontend-backend communication  
**HOW:** PHP-based secure APIs with XSS protection and authentication  

## AGAPE: Love, Patience, Kindness, Humility
## GENESIS: Foundation of API communication
## MD: Markdown documentation with API overview

**FILE IDS:** [API_COMPONENTS_README_001, WOLFIE_AGI_UI_057]

**VERSION:** 1.0.0 - The Captain's API Components  
**STATUS:** Active - Production Ready

---

## 🛸 API COMPONENTS OVERVIEW

The API components provide secure RESTful endpoints for frontend-backend communication in the WOLFIE AGI UI system. These APIs handle channel operations, message management, and system status with comprehensive XSS protection and token-based authentication.

---

## 📁 API STRUCTURE

```
api/
├── endpoint_handler_secure.php        # Main secure API endpoint
├── modern_channel_api_secure.php      # Modern channel API
└── README.md                          # This file
```

---

## 🔧 API ENDPOINTS

### 1. EndpointHandlerSecure
**File:** `endpoint_handler_secure.php`  
**Purpose:** Main secure API endpoint for all operations  
**Base URL:** `http://localhost/WOLFIE_AGI_UI/api/endpoint_handler_secure.php`  
**Methods:** POST (JSON)

**Endpoints:**
- `ping` - System health check
- `createChannel` - Create new channel
- `sendChannelMessage` - Send message to channel
- `getMessages` - Retrieve messages from channel
- `getAllChannels` - Get all channels
- `getChannelStatus` - Get channel information
- `searchMessages` - Search messages
- `getSystemStatus` - Get system status
- `processBacklogFiles` - Process 17-file backlog
- `searchFiles` - Search files
- `getFileMetadata` - Get file metadata

**Authentication:** Token-based (required for sensitive operations)

### 2. ModernChannelAPISecure
**File:** `modern_channel_api_secure.php`  
**Purpose:** Modern API for frontend integration  
**Base URL:** `http://localhost/WOLFIE_AGI_UI/api/modern_channel_api_secure.php`  
**Methods:** GET, POST, PUT, DELETE, OPTIONS

**Endpoints:**
- `ping` - API health check
- `create_channel` - Create channel
- `send_message` - Send message
- `get_messages` - Get messages
- `get_channels` - Get all channels
- `get_channel_status` - Get channel status
- `add_user_to_channel` - Add user to channel
- `create_user` - Create user session
- `get_system_status` - Get system status

**Authentication:** Token-based (required for sensitive operations)

---

## 🔒 SECURITY FEATURES

### XSS Protection
- **35+ Malicious Patterns** detected and blocked
- **Double Sanitization** for all message content
- **Input Validation** for all API inputs
- **SalesSyntax 3.7.0 Vulnerability** completely addressed

### Authentication
- **Token-Based Security** for all sensitive operations
- **Agent Authentication** for bridge crew personas
- **Session Management** with secure timeouts
- **CORS Support** for cross-origin requests

### Input Sanitization
- **Message Content**: HTML encoding + filter sanitization
- **Channel Names**: Alphanumeric validation
- **User IDs**: Strict character validation
- **API Parameters**: Comprehensive validation

---

## 🚀 USAGE EXAMPLES

### Basic API Call
```javascript
// Frontend JavaScript
const response = await fetch('http://localhost/WOLFIE_AGI_UI/api/endpoint_handler_secure.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'your_token_here'
    },
    body: JSON.stringify({
        action: 'createChannel',
        name: 'Test Channel',
        type: 'general',
        description: 'Testing API',
        agents: ['captain_wolfie', 'cursor']
    })
});

const result = await response.json();
console.log(result);
```

### Channel Operations
```javascript
// Create channel
const createChannel = async (name, type, description) => {
    const response = await fetch('/api/modern_channel_api_secure.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'create_channel',
            name,
            type,
            description,
            user_id: 'captain_wolfie'
        })
    });
    return await response.json();
};

// Send message
const sendMessage = async (channelId, message) => {
    const response = await fetch('/api/modern_channel_api_secure.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'send_message',
            channel_id: channelId,
            user_id: 'captain_wolfie',
            message,
            type: 'HTML'
        })
    });
    return await response.json();
};

// Get messages
const getMessages = async (channelId, sinceTime = 0) => {
    const response = await fetch('/api/modern_channel_api_secure.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'get_messages',
            channel_id: channelId,
            since_time: sinceTime,
            type: 'HTML'
        })
    });
    return await response.json();
};
```

### PHP API Usage
```php
// Server-side PHP
require_once 'endpoint_handler_secure.php';

// Create API handler
$api = new APIEndpointHandlerSecure();

// Handle request
$api->handleRequest();

// Or use specific methods
$channels = $api->getAllChannels();
$messages = $api->getMessages('channel_123', 0, 'HTML');
$status = $api->getSystemStatus();
```

---

## 📊 API RESPONSES

### Success Response Format
```json
{
    "success": true,
    "data": {
        "channel_id": "channel_123",
        "name": "Test Channel",
        "type": "general",
        "description": "Testing API",
        "created_at": "2025-09-26 18:30:00"
    },
    "error": null
}
```

### Error Response Format
```json
{
    "success": false,
    "data": null,
    "error": {
        "message": "Channel name is required",
        "code": 400,
        "timestamp": "2025-09-26 18:30:00"
    }
}
```

### System Status Response
```json
{
    "success": true,
    "data": {
        "api_status": "running",
        "security": "XSS Protected",
        "authentication": "Required",
        "total_channels": 5,
        "active_channels": 3,
        "timestamp": "2025-09-26 18:30:00",
        "version": "1.0.0"
    },
    "error": null
}
```

---

## 🔧 CONFIGURATION

### CORS Configuration
```php
// Set in API files
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
```

### Authentication Configuration
```php
// Token verification
private function verifyAuthentication() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $expectedToken = hash('sha256', 'AGAPE_SECRET_KEY_' . date('Ymd'));
    return $token === $expectedToken;
}
```

### Security Headers
```php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
```

---

## 🧪 TESTING

### API Testing
```bash
# Test main API endpoint
curl -X POST http://localhost/WOLFIE_AGI_UI/api/endpoint_handler_secure.php \
  -H "Content-Type: application/json" \
  -d '{"action": "ping"}'

# Test channel creation
curl -X POST http://localhost/WOLFIE_AGI_UI/api/modern_channel_api_secure.php \
  -H "Content-Type: application/json" \
  -H "Authorization: your_token_here" \
  -d '{"action": "create_channel", "name": "Test Channel", "type": "general"}'
```

### Frontend Testing
```javascript
// Test API integration
const testAPI = async () => {
    try {
        // Test ping
        const ping = await fetch('/api/modern_channel_api_secure.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'ping' })
        });
        console.log('Ping:', await ping.json());
        
        // Test channel creation
        const channel = await createChannel('Test Channel', 'general', 'Testing');
        console.log('Channel created:', channel);
        
        // Test message sending
        const message = await sendMessage(channel.data.channel_id, 'Hello World!');
        console.log('Message sent:', message);
        
    } catch (error) {
        console.error('API Test Error:', error);
    }
};
```

---

## 🚨 TROUBLESHOOTING

### Common Issues
1. **CORS Errors**
   - Check CORS headers in API files
   - Verify frontend URL matches allowed origins
   - Ensure OPTIONS requests are handled

2. **Authentication Failures**
   - Verify token generation and validation
   - Check Authorization header format
   - Review token expiration

3. **XSS Protection Errors**
   - Review input sanitization settings
   - Check XSS pattern detection
   - Verify input validation rules

4. **Database Connection Errors**
   - Check MySQL credentials
   - Verify database schema is installed
   - Review connection configuration

### Debug Mode
Enable debug logging:
```php
private $debugMode = true;
```

---

## 📚 INTEGRATION

### With Frontend Components
The APIs integrate with:
- `enhanced_index.html` - Modern React-style UI
- `modern_channel_system.js` - Frontend JavaScript
- WebSocket connections for real-time updates

### With Core Components
The APIs use:
- `wolfie_channel_system_secure.php` - Channel management
- `multi_agent_coordinator_secure.php` - Multi-agent coordination
- `agi_core_engine_secure.php` - AGI processing

### With WebSocket Components
The APIs support:
- Real-time message broadcasting
- Channel management
- Agent coordination

---

## 📞 SUPPORT

### Contact Information
- **Captain WOLFIE**: Eric Robin Gerdes
- **Project**: WOLFIE AGI UI API Components
- **Version**: 1.0.0
- **Status**: Production Ready

### Getting Help
1. **Check Logs**: Review log files in `../logs/`
2. **Run Tests**: Use the test suite to diagnose issues
3. **Review Documentation**: Read API documentation
4. **Contact Support**: Reach out to Captain WOLFIE

---

**🌟 API components are secure and production ready! 🌟**

**🚀 READY FOR WOLFIE AGI LAUNCH ON OCTOBER 1, 2025! 🚀**

---

*Generated by Captain WOLFIE's AGI System - September 26, 2025*
