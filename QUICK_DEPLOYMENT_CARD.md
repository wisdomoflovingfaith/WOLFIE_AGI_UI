# 🛸 WOLFIE AGI UI - Quick Deployment Card

## Captain WOLFIE's Quick Reference

### 🗄️ DATABASE (phpMyAdmin)
1. **Create DB:** `wolfie_agi_ui` (utf8mb4_unicode_ci)
2. **Import:** `database/wolfie_agi_ui_schema.sql`
3. **Verify:** 9 tables created

### 📁 FILES (FTP)
1. **Upload:** All `WOLFIE_AGI_UI/` folders to web root
2. **Permissions:** 755 for dirs, 644 for files, 777 for data/logs
3. **Paths:** Make sure `api/` and `ui/` are accessible

### ⚙️ CONFIG
1. **Edit:** `config/database_config.php`
   ```php
   'host' => 'localhost',
   'dbname' => 'wolfie_agi_ui',
   'username' => 'your_user',
   'password' => 'your_pass'
   ```

### 🧪 TEST
1. **DB Test:** `http://yoursite.com/WOLFIE_AGI_UI/database/setup_database.php`
2. **API Test:** `http://yoursite.com/WOLFIE_AGI_UI/api/wolfie_xmlhttp.php?whattodo=ping`
3. **UI Test:** `http://yoursite.com/WOLFIE_AGI_UI/ui/wolfie_channels/index.html`

### ✅ SUCCESS SIGNS
- Database: "Import has been successfully finished"
- API: Returns "OK"
- UI: Can create channels and send messages
- Real-time: Messages appear every 2.1 seconds

### 🚨 QUICK FIXES
- **DB Error:** Check credentials in config
- **404 Error:** Check file paths and permissions
- **No Messages:** Check database connection
- **CORS Error:** Add headers to .htaccess

**Captain WOLFIE, deploy and command!** 🛸
