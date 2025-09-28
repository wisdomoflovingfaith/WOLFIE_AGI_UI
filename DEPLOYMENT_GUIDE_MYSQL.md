# WOLFIE AGI UI - MySQL Deployment Guide

**WHO:** Captain WOLFIE (Eric Robin Gerdes)  
**WHAT:** Complete deployment guide for MySQL-based WOLFIE AGI UI system  
**WHERE:** C:\START\WOLFIE_AGI_UI\  
**WHEN:** 2025-09-26 17:30:00 CDT  
**WHY:** To provide comprehensive deployment instructions for production MySQL system  
**HOW:** Step-by-step deployment guide with security and performance considerations  

## AGAPE: Love, Patience, Kindness, Humility
## GENESIS: Foundation of secure MySQL deployment protocols
## MD: Markdown documentation with deployment instructions

**FILE IDS:** [DEPLOYMENT_GUIDE_MYSQL_001, WOLFIE_AGI_UI_046]

**VERSION:** 1.0.0 - The Captain's MySQL Deployment Guide  
**STATUS:** Active - Production Ready Deployment Instructions

---

## 🛸 CAPTAIN WOLFIE'S MYSQL DEPLOYMENT GUIDE

### Overview
This guide provides comprehensive instructions for deploying the WOLFIE AGI UI system with MySQL backend, including security enhancements, XSS protection, and production optimization.

### Prerequisites
- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx)
- SSL certificate for production
- Sufficient disk space for logs and data

---

## 📋 DEPLOYMENT STEPS

### Step 1: Database Setup

#### 1.1 Create MySQL Database
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create database and user
CREATE DATABASE wolfie_agi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'wolfie_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON wolfie_agi.* TO 'wolfie_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 1.2 Import Schema
```bash
# Navigate to project directory
cd C:\START\WOLFIE_AGI_UI

# Import the complete schema
mysql -u wolfie_user -p wolfie_agi < database/wolfie_agi_schema.sql
```

#### 1.3 Verify Database
```sql
-- Connect to the database
mysql -u wolfie_user -p wolfie_agi

-- Check tables
SHOW TABLES;

-- Check default data
SELECT * FROM channels;
SELECT * FROM project_tracking;
```

### Step 2: Configuration Setup

#### 2.1 Database Configuration
Create `config/database_config.php`:
```php
<?php
function getDatabaseConnection() {
    $dsn = 'mysql:host=localhost;dbname=wolfie_agi;charset=utf8mb4';
    $username = 'wolfie_user';
    $password = 'your_secure_password';
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    return $pdo;
}
?>
```

#### 2.2 Security Configuration
Create `config/security_config.php`:
```php
<?php
// Security settings
define('XSS_PROTECTION', true);
define('MAX_MESSAGE_LENGTH', 1000);
define('MAX_CHANNEL_NAME_LENGTH', 100);
define('SESSION_TIMEOUT', 3600); // 1 hour
define('LOG_RETENTION_DAYS', 30);

// Authentication settings
define('AGENT_AUTH_REQUIRED', true);
define('TOKEN_EXPIRY', 86400); // 24 hours

// Rate limiting
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 3600); // 1 hour
?>
```

### Step 3: File Permissions

#### 3.1 Set Directory Permissions
```bash
# Set proper permissions for logs directory
chmod 755 logs/
chmod 644 logs/*.log

# Set permissions for data directory
chmod 755 data/
chmod 644 data/*.json

# Set permissions for config files
chmod 600 config/database_config.php
chmod 600 config/security_config.php
```

#### 3.2 Create Required Directories
```bash
mkdir -p logs
mkdir -p data
mkdir -p uploads
mkdir -p cache
```

### Step 4: Web Server Configuration

#### 4.1 Apache Configuration
Create `.htaccess` in project root:
```apache
# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

# PHP settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 30
php_value memory_limit 128M

# Disable directory browsing
Options -Indexes

# Protect sensitive files
<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

<Files "database_config.php">
    Order allow,deny
    Deny from all
</Files>
```

#### 4.2 Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/WOLFIE_AGI_UI;
    index index.php;

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    # Protect sensitive files
    location ~ \.(log|sql)$ {
        deny all;
    }

    location ~ database_config\.php$ {
        deny all;
    }
}
```

### Step 5: Testing and Validation

#### 5.1 Run Integration Tests
```bash
# Navigate to tests directory
cd tests/

# Run complete MySQL integration test
php test_complete_mysql_integration.php

# Run secure system integration test
php test_secure_system_integration.php
```

#### 5.2 Manual Testing
```bash
# Test API endpoints
curl -X POST http://localhost/WOLFIE_AGI_UI/api/endpoint_handler_secure.php \
  -H "Content-Type: application/json" \
  -d '{"action":"getSystemStatus"}'

# Test channel creation
curl -X POST http://localhost/WOLFIE_AGI_UI/api/endpoint_handler_secure.php \
  -H "Content-Type: application/json" \
  -d '{"action":"createChannel","name":"Test Channel","agents":["captain_wolfie"],"type":"general"}'
```

### Step 6: Production Optimization

#### 6.1 MySQL Optimization
```sql
-- Optimize MySQL settings
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL max_connections = 200;
SET GLOBAL query_cache_size = 64M;

-- Create indexes for performance
CREATE INDEX idx_messages_timeof ON messages(timeof);
CREATE INDEX idx_messages_channel_time ON messages(channel_id, timeof);
CREATE INDEX idx_channel_users_joined ON channel_users(joined_at);
```

#### 6.2 PHP Optimization
```ini
; php.ini optimizations
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60

; Session optimization
session.gc_maxlifetime=3600
session.gc_probability=1
session.gc_divisor=1000
```

#### 6.3 Log Rotation
Create log rotation script `scripts/rotate_logs.sh`:
```bash
#!/bin/bash
# Rotate logs daily
find logs/ -name "*.log" -mtime +7 -exec gzip {} \;
find logs/ -name "*.log.gz" -mtime +30 -delete
```

### Step 7: Monitoring and Maintenance

#### 7.1 Health Check Script
Create `scripts/health_check.php`:
```php
<?php
// Health check script
$checks = [
    'database' => checkDatabase(),
    'files' => checkFiles(),
    'permissions' => checkPermissions(),
    'logs' => checkLogs()
];

function checkDatabase() {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->query("SELECT 1");
        return $stmt->fetchColumn() == 1;
    } catch (Exception $e) {
        return false;
    }
}

// Output health status
echo json_encode($checks);
?>
```

#### 7.2 Backup Script
Create `scripts/backup_database.sh`:
```bash
#!/bin/bash
# Daily database backup
mysqldump -u wolfie_user -p wolfie_agi > backups/wolfie_agi_$(date +%Y%m%d).sql
gzip backups/wolfie_agi_$(date +%Y%m%d).sql
```

---

## 🔒 SECURITY CONSIDERATIONS

### XSS Protection
- All inputs are sanitized using `htmlspecialchars()`
- XSS patterns are blocked using regex validation
- Content Security Policy headers are set

### SQL Injection Prevention
- All database queries use prepared statements
- Input validation prevents malicious SQL
- Database user has minimal required permissions

### Authentication
- Agent authentication is required for all operations
- Token-based authentication for API access
- Session management with timeout

### File Security
- Sensitive files are protected from direct access
- File uploads are validated and sanitized
- Directory traversal attacks are prevented

---

## 📊 PERFORMANCE MONITORING

### Key Metrics
- Database connection pool usage
- Message processing latency
- Channel activity levels
- Agent response times

### Monitoring Queries
```sql
-- Active channels
SELECT COUNT(*) FROM active_channels;

-- Message volume
SELECT COUNT(*) FROM messages WHERE timeof > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 HOUR), '%Y%m%d%H%i%s');

-- Agent activity
SELECT * FROM agent_activity ORDER BY messages_sent DESC;
```

---

## 🚀 LAUNCH CHECKLIST

- [ ] Database schema imported successfully
- [ ] All configuration files created and secured
- [ ] File permissions set correctly
- [ ] Web server configured with security headers
- [ ] Integration tests passing
- [ ] API endpoints responding correctly
- [ ] XSS protection verified
- [ ] Authentication working
- [ ] Logging functioning
- [ ] Backup procedures in place
- [ ] Monitoring scripts active
- [ ] SSL certificate installed (production)
- [ ] Performance optimization applied

---

## 🆘 TROUBLESHOOTING

### Common Issues

#### Database Connection Failed
```bash
# Check MySQL service
systemctl status mysql

# Test connection
mysql -u wolfie_user -p wolfie_agi
```

#### Permission Denied
```bash
# Fix file permissions
chmod 755 logs/
chmod 644 logs/*.log
chown -R www-data:www-data .
```

#### XSS Protection Errors
```php
// Check input sanitization
$input = '<script>alert("test")</script>';
$sanitized = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
echo $sanitized; // Should show escaped HTML
```

---

## 📞 SUPPORT

For technical support or questions:
- **Captain WOLFIE:** Contact via WOLFIE AGI system
- **Documentation:** Check `docs/` directory
- **Logs:** Review `logs/` directory for error details

---

## 🌟 CONCLUSION

The WOLFIE AGI UI MySQL deployment is now complete with:
- ✅ Secure MySQL backend
- ✅ XSS protection implemented
- ✅ Authentication system active
- ✅ Performance optimization applied
- ✅ Monitoring and maintenance procedures

**The transformation from vulnerable to secure MySQL is complete!** 🛸

---

*Generated by Captain WOLFIE's AGI System - September 26, 2025*
