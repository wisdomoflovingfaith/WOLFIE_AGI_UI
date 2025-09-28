<?php
/**
 * WOLFIE AGI UI - Database Setup Script
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Database setup script for WOLFIE AGI UI
 * WHERE: C:\START\WOLFIE_AGI_UI\database\
 * WHEN: 2025-09-26 15:30:00 CDT
 * WHY: To easily set up the database on the server
 * HOW: PHP script to create database and load schema
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of database setup
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [DATABASE_SETUP_001, WOLFIE_AGI_UI_026]
 * 
 * VERSION: 1.0.0 - The Captain's Database Setup
 * STATUS: Active - Ready for server deployment
 */

require_once '../config/database_config.php';

echo "🛸 WOLFIE AGI UI - Database Setup Script\n";
echo "========================================\n";
echo "WHO: Captain WOLFIE (Eric Robin Gerdes)\n";
echo "WHAT: Setting up MySQL database for WOLFIE AGI UI\n";
echo "WHEN: " . date('Y-m-d H:i:s') . " CDT\n";
echo "WHY: To prepare the database for real channel communication\n";
echo "HOW: PHP script with MySQL schema loading\n\n";

// Test connection
echo "🧪 Testing database connection...\n";
if (testDatabaseConnection()) {
    echo "✅ Database connection successful!\n";
} else {
    echo "❌ Database connection failed!\n";
    echo "Please check your database configuration in config/database_config.php\n";
    exit(1);
}

// Get database info
echo "\n📊 Database Information:\n";
$info = getDatabaseInfo();
if (isset($info['error'])) {
    echo "❌ Error getting database info: " . $info['error'] . "\n";
} else {
    echo "✅ MySQL Version: " . $info['version'] . "\n";
    echo "✅ Database: " . $info['database_name'] . "\n";
}

// Load schema
echo "\n📋 Loading database schema...\n";
$schemaFile = __DIR__ . '/wolfie_agi_ui_schema.sql';

if (!file_exists($schemaFile)) {
    echo "❌ Schema file not found: $schemaFile\n";
    exit(1);
}

try {
    $pdo = getDatabaseConnection();
    $sql = file_get_contents($schemaFile);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
            echo "⚠️  Warning: " . $e->getMessage() . "\n";
        }
    }
    
    echo "✅ Schema loaded successfully!\n";
    echo "✅ Successful statements: $successCount\n";
    if ($errorCount > 0) {
        echo "⚠️  Warnings: $errorCount\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error loading schema: " . $e->getMessage() . "\n";
    exit(1);
}

// Test the channel system
echo "\n🧪 Testing WOLFIE Channel System...\n";
try {
    require_once '../core/wolfie_channel_system_mysql.php';
    
    $channelSystem = new WolfieChannelSystemMySQL();
    echo "✅ WolfieChannelSystemMySQL initialized successfully\n";
    
    // Test channel creation
    $channelId = $channelSystem->createChannel('Test Channel', 'general', 'Test channel for Captain WOLFIE');
    echo "✅ Test channel created: $channelId\n";
    
    // Test user creation
    $user = $channelSystem->createOrGetUser('test_session_' . time(), 'test_user');
    echo "✅ Test user created: " . $user['username'] . "\n";
    
    // Test adding user to channel
    $channelSystem->addUserToChannel($user['user_id'], $channelId);
    echo "✅ User added to channel\n";
    
    // Test sending message
    $message = $channelSystem->sendMessage($channelId, $user['user_id'], 'Hello from Captain WOLFIE!');
    echo "✅ Test message sent: " . $message['timeof'] . "\n";
    
    // Test getting messages
    $messages = $channelSystem->getMessages($channelId, 0, 'HTML');
    echo "✅ Messages retrieved: " . count($messages) . " messages\n";
    
    echo "✅ WOLFIE Channel System test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error testing channel system: " . $e->getMessage() . "\n";
    exit(1);
}

// Final status
echo "\n🎉 DATABASE SETUP COMPLETED SUCCESSFULLY!\n";
echo "==========================================\n";
echo "✅ Database: " . CURRENT_DB_NAME . "\n";
echo "✅ Host: " . CURRENT_DB_HOST . ":" . CURRENT_DB_PORT . "\n";
echo "✅ User: " . CURRENT_DB_USER . "\n";
echo "✅ Charset: " . CURRENT_DB_CHARSET . "\n";
echo "\n🛸 Captain WOLFIE, your MySQL database is ready for action!\n";
echo "You can now use the WOLFIE AGI UI with real channel communication.\n";
echo "\n📋 Next steps:\n";
echo "1. Update your web server configuration\n";
echo "2. Test the XMLHttpRequest endpoints\n";
echo "3. Deploy the frontend interfaces\n";
echo "4. Start using real channels like salessyntax3.7.0!\n";
?>
