# 🛸 WOLFIE AGI UI - Complete Deployment Guide

## Captain WOLFIE's Step-by-Step Server Deployment

**WHO:** Captain WOLFIE (Eric Robin Gerdes)  
**WHAT:** Complete deployment guide for WOLFIE AGI UI  
**WHERE:** Your web server  
**WHEN:** 2025-09-26  
**WHY:** To get your real channel system running on the server  
**HOW:** phpMyAdmin + FTP + Configuration updates  

---

## 📋 PRE-DEPLOYMENT CHECKLIST

- [ ] Web server with PHP 7.4+ and MySQL 5.7+
- [ ] FTP access to your web server
- [ ] phpMyAdmin access
- [ ] Database credentials
- [ ] All WOLFIE AGI UI files ready

---

## 🗄️ STEP 1: DATABASE SETUP (phpMyAdmin)

### 1.1 Access phpMyAdmin
1. Open your web browser
2. Go to `http://yourdomain.com/phpmyadmin` (or your server's phpMyAdmin URL)
3. Login with your database credentials

### 1.2 Create Database
1. Click **"Databases"** tab
2. Enter database name: `wolfie_agi_ui`
3. Select collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### 1.3 Load Schema
1. Select the `wolfie_agi_ui` database
2. Click **"Import"** tab
3. Click **"Choose File"** button
4. Select `database/wolfie_agi_ui_schema.sql`
5. Click **"Go"** to import
6. Wait for "Import has been successfully finished" message

### 1.4 Verify Tables Created
You should see these tables:
- `livehelp_users`
- `livehelp_messages`
- `livehelp_operator_channels`
- `livehelp_departments`
- `livehelp_visit_track`
- `superpositionally_headers`
- `meeting_mode_data`
- `no_casino_data`
- `captain_intent_log`

### 1.5 Test Database
1. Click **"SQL"** tab
2. Run this test query:
```sql
SELECT 'Database setup successful!' as message;
SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = 'wolfie_agi_ui';
```

---

## 📁 STEP 2: FILE UPLOAD (FTP)

### 2.1 Connect via FTP
1. Open your FTP client (FileZilla, WinSCP, etc.)
2. Connect to your server:
   - **Host:** yourdomain.com (or server IP)
   - **Username:** your FTP username
   - **Password:** your FTP password
   - **Port:** 21 (or 22 for SFTP)

### 2.2 Upload Files
Upload these directories to your web root (usually `public_html` or `www`):

```
WOLFIE_AGI_UI/
├── api/
│   ├── wolfie_xmlhttp.php
│   └── endpoint_handler_csv.php
├── config/
│   └── database_config.php
├── core/
│   ├── wolfie_channel_system_mysql.php
│   ├── multi_agent_coordinator.php
│   ├── meeting_mode_processor.php
│   └── no_casino_mode_processor.php
├── ui/
│   ├── wolfie_channels/
│   │   ├── index.html
│   │   └── wolfie_xmlhttp.js
│   ├── cursor_like_search/
│   └── multi_agent_chat/
├── data/
│   └── (create empty directory)
└── logs/
    └── (create empty directory)
```

### 2.3 Set Permissions
Set these permissions on your server:
- **Directories:** 755 (rwxr-xr-x)
- **PHP files:** 644 (rw-r--r--)
- **Data directory:** 777 (rwxrwxrwx)
- **Logs directory:** 777 (rwxrwxrwx)

---

## ⚙️ STEP 3: CONFIGURATION UPDATES

### 3.1 Update Database Configuration
Edit `config/database_config.php`:

```php
<?php
// Update these values for your server
$database_configs['production'] = [
    'host' => 'localhost',           // Your database host
    'port' => 3306,                 // Your database port
    'dbname' => 'wolfie_agi_ui',    // Your database name
    'username' => 'your_db_user',   // Your database username
    'password' => 'your_db_pass',   // Your database password
    'charset' => 'utf8mb4'
];
```

### 3.2 Update File Paths
Edit `core/wolfie_channel_system_mysql.php` if needed:

```php
// Update these paths if your server structure is different
private $host = 'localhost';
private $dbname = 'wolfie_agi_ui';
private $username = 'your_db_user';
private $password = 'your_db_pass';
```

### 3.3 Update API Endpoints
Edit `ui/wolfie_channels/wolfie_xmlhttp.js`:

```javascript
// Update these URLs to match your server
sURL = 'wolfie_xmlhttp.php';  // Make sure this path is correct
fullurl = 'wolfie_xmlhttp.php?' + sPostData;
```

---

## 🧪 STEP 4: TESTING

### 4.1 Test Database Connection
1. Go to `http://yourdomain.com/WOLFIE_AGI_UI/database/setup_database.php`
2. You should see:
   ```
   🛸 WOLFIE AGI UI - Database Setup Script
   ✅ Database connection successful!
   ✅ Schema loaded successfully!
   ✅ WOLFIE Channel System test completed successfully!
   ```

### 4.2 Test XMLHttpRequest Handler
1. Go to `http://yourdomain.com/WOLFIE_AGI_UI/api/wolfie_xmlhttp.php?whattodo=ping`
2. You should see: `OK`

### 4.3 Test Channel Interface
1. Go to `http://yourdomain.com/WOLFIE_AGI_UI/ui/wolfie_channels/index.html`
2. Click "Create Channel"
3. Send a test message
4. Verify real-time polling works (2.1 seconds)

---

## 🔧 STEP 5: SERVER CONFIGURATION

### 5.1 PHP Configuration
Make sure your server has:
- **PHP 7.4+** (preferably 8.0+)
- **MySQL 5.7+** (preferably 8.0+)
- **PDO MySQL** extension enabled
- **JSON** extension enabled
- **File permissions** set correctly

### 5.2 Web Server Configuration
For **Apache**, add to `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Enable CORS for XMLHttpRequest
Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
```

For **Nginx**, add to your config:
```nginx
location /WOLFIE_AGI_UI/ {
    try_files $uri $uri/ /WOLFIE_AGI_UI/index.php?$query_string;
}

# Enable CORS
add_header Access-Control-Allow-Origin "*" always;
add_header Access-Control-Allow-Methods "GET, POST, OPTIONS" always;
add_header Access-Control-Allow-Headers "Content-Type, Authorization" always;
```

---

## 🚨 TROUBLESHOOTING

### Common Issues:

#### Database Connection Failed
- Check database credentials in `config/database_config.php`
- Verify database exists and user has permissions
- Check if MySQL is running

#### Files Not Found (404)
- Verify file paths are correct
- Check file permissions (755 for directories, 644 for files)
- Make sure files uploaded to correct directory

#### XMLHttpRequest Not Working
- Check browser console for errors
- Verify API endpoints are accessible
- Check CORS headers are set

#### Messages Not Appearing
- Check database connection
- Verify channel exists
- Check message timestamps

### Debug Mode:
Add this to the top of PHP files for debugging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📊 STEP 6: MONITORING

### 6.1 Check Database
```sql
-- Check active users
SELECT COUNT(*) as active_users FROM livehelp_users WHERE lastaction > DATE_SUB(NOW(), INTERVAL 5 MINUTE);

-- Check messages
SELECT COUNT(*) as total_messages FROM livehelp_messages;

-- Check channels
SELECT COUNT(*) as total_channels FROM livehelp_departments;
```

### 6.2 Check Logs
Monitor these files:
- `logs/agi_core_engine_ui.log`
- `logs/channel_system.log`
- Server error logs

---

## 🎉 STEP 7: SUCCESS!

If everything is working, you should see:

1. **Database:** All tables created and populated
2. **API:** XMLHttpRequest endpoints responding
3. **Channels:** Real-time communication working
4. **Interface:** Channel creation and messaging functional

---

## 📋 POST-DEPLOYMENT CHECKLIST

- [ ] Database imported successfully
- [ ] All files uploaded via FTP
- [ ] Configuration updated for production
- [ ] Database connection test passed
- [ ] XMLHttpRequest handler responding
- [ ] Channel interface working
- [ ] Real-time polling functional
- [ ] File permissions set correctly
- [ ] CORS headers configured
- [ ] Error logging enabled

---

## 🛸 CAPTAIN WOLFIE'S FINAL NOTES

This deployment guide is based on the proven SalesSyntax 3.7.0 system. Your WOLFIE AGI UI now has:

- **Real MySQL channels** (not CSV)
- **2.1 second polling** like salessyntax3.7.0
- **Proper message timestamps** (YmdHis format)
- **User session management**
- **Channel switching**
- **Typing indicators**
- **Foreign key relationships**
- **Optimized indexes**

**Your system is now ready for real channel communication!** 🚀

---

## 📞 SUPPORT

If you run into issues:
1. Check the troubleshooting section above
2. Verify all steps were completed
3. Check server error logs
4. Test database connection manually
5. Verify file permissions

**Captain WOLFIE, your MySQL-based channel system is ready to command!** 🛸
