<?php
/**
 * WOLFIE AGI UI - Deployment Test Script
 * 
 * WHO: Captain WOLFIE (Eric Robin Gerdes)
 * WHAT: Test script to verify deployment is working
 * WHERE: C:\START\WOLFIE_AGI_UI\
 * WHEN: 2025-09-26 15:45:00 CDT
 * WHY: To quickly test if everything is working after deployment
 * HOW: PHP script to test all components
 * 
 * AGAPE: Love, Patience, Kindness, Humility
 * GENESIS: Foundation of deployment testing
 * MD: Markdown documentation with .php implementation
 * 
 * FILE IDS: [TEST_DEPLOYMENT_001, WOLFIE_AGI_UI_027]
 * 
 * VERSION: 1.0.0 - The Captain's Deployment Test
 * STATUS: Active - Ready for server testing
 */

echo "🛸 WOLFIE AGI UI - DEPLOYMENT TEST\n";
echo "===================================\n";
echo "WHO: Captain WOLFIE (Eric Robin Gerdes)\n";
echo "WHAT: Testing deployment on server\n";
echo "WHEN: " . date('Y-m-d H:i:s') . " CDT\n";
echo "WHY: To verify everything is working\n";
echo "HOW: Comprehensive deployment test\n\n";

$tests = [];
$passed = 0;
$failed = 0;

// Test 1: PHP Version
echo "🧪 TEST 1: PHP VERSION\n";
echo "=====================\n";
$phpVersion = phpversion();
$phpVersionOk = version_compare($phpVersion, '7.4.0', '>=');
$tests[] = ['PHP Version', $phpVersionOk, $phpVersion];
echo ($phpVersionOk ? "✅" : "❌") . " PHP Version: $phpVersion\n";
if ($phpVersionOk) $passed++; else $failed++;

// Test 2: Required Extensions
echo "\n🧪 TEST 2: REQUIRED EXTENSIONS\n";
echo "=============================\n";
$extensions = ['pdo', 'pdo_mysql', 'json', 'curl'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    $tests[] = ["Extension: $ext", $loaded, $loaded ? 'Loaded' : 'Not loaded'];
    echo ($loaded ? "✅" : "❌") . " $ext: " . ($loaded ? 'Loaded' : 'Not loaded') . "\n";
    if ($loaded) $passed++; else $failed++;
}

// Test 3: Database Connection
echo "\n🧪 TEST 3: DATABASE CONNECTION\n";
echo "=============================\n";
try {
    require_once 'config/database_config.php';
    $pdo = getDatabaseConnection();
    $tests[] = ['Database Connection', true, 'Connected successfully'];
    echo "✅ Database Connection: Connected successfully\n";
    $passed++;
} catch (Exception $e) {
    $tests[] = ['Database Connection', false, $e->getMessage()];
    echo "❌ Database Connection: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 4: Database Tables
echo "\n🧪 TEST 4: DATABASE TABLES\n";
echo "=========================\n";
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $expectedTables = [
            'livehelp_users',
            'livehelp_messages',
            'livehelp_operator_channels',
            'livehelp_departments',
            'livehelp_visit_track',
            'superpositionally_headers',
            'meeting_mode_data',
            'no_casino_data',
            'captain_intent_log'
        ];
        
        $missingTables = array_diff($expectedTables, $tables);
        $tablesOk = empty($missingTables);
        
        $tests[] = ['Database Tables', $tablesOk, count($tables) . ' tables found'];
        echo ($tablesOk ? "✅" : "❌") . " Database Tables: " . count($tables) . " tables found\n";
        
        if (!empty($missingTables)) {
            echo "⚠️  Missing tables: " . implode(', ', $missingTables) . "\n";
        }
        
        if ($tablesOk) $passed++; else $failed++;
    } else {
        $tests[] = ['Database Tables', false, 'No database connection'];
        echo "❌ Database Tables: No database connection\n";
        $failed++;
    }
} catch (Exception $e) {
    $tests[] = ['Database Tables', false, $e->getMessage()];
    echo "❌ Database Tables: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 5: Channel System
echo "\n🧪 TEST 5: CHANNEL SYSTEM\n";
echo "========================\n";
try {
    require_once 'core/wolfie_channel_system_mysql.php';
    $channelSystem = new WolfieChannelSystemMySQL();
    $tests[] = ['Channel System', true, 'Initialized successfully'];
    echo "✅ Channel System: Initialized successfully\n";
    $passed++;
} catch (Exception $e) {
    $tests[] = ['Channel System', false, $e->getMessage()];
    echo "❌ Channel System: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 6: File Permissions
echo "\n🧪 TEST 6: FILE PERMISSIONS\n";
echo "==========================\n";
$directories = ['data', 'logs'];
$permissionsOk = true;

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $writable = is_writable($dir);
    $tests[] = ["Directory: $dir", $writable, $writable ? 'Writable' : 'Not writable'];
    echo ($writable ? "✅" : "❌") . " Directory $dir: " . ($writable ? 'Writable' : 'Not writable') . "\n";
    
    if (!$writable) $permissionsOk = false;
}

if ($permissionsOk) $passed++; else $failed++;

// Test 7: API Endpoints
echo "\n🧪 TEST 7: API ENDPOINTS\n";
echo "=======================\n";
$apiFiles = [
    'api/wolfie_xmlhttp.php',
    'api/endpoint_handler_csv.php'
];

$apiOk = true;
foreach ($apiFiles as $file) {
    $exists = file_exists($file);
    $tests[] = ["API File: $file", $exists, $exists ? 'Exists' : 'Missing'];
    echo ($exists ? "✅" : "❌") . " API File $file: " . ($exists ? 'Exists' : 'Missing') . "\n";
    
    if (!$exists) $apiOk = false;
}

if ($apiOk) $passed++; else $failed++;

// Test 8: UI Files
echo "\n🧪 TEST 8: UI FILES\n";
echo "==================\n";
$uiFiles = [
    'ui/wolfie_channels/index.html',
    'ui/wolfie_channels/wolfie_xmlhttp.js'
];

$uiOk = true;
foreach ($uiFiles as $file) {
    $exists = file_exists($file);
    $tests[] = ["UI File: $file", $exists, $exists ? 'Exists' : 'Missing'];
    echo ($exists ? "✅" : "❌") . " UI File $file: " . ($exists ? 'Exists' : 'Missing') . "\n";
    
    if (!$exists) $uiOk = false;
}

if ($uiOk) $passed++; else $failed++;

// Summary
echo "\n🎉 DEPLOYMENT TEST SUMMARY\n";
echo "==========================\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "📊 Total: " . ($passed + $failed) . "\n\n";

if ($failed === 0) {
    echo "🛸 CAPTAIN WOLFIE, YOUR DEPLOYMENT IS PERFECT!\n";
    echo "==============================================\n";
    echo "All tests passed! Your WOLFIE AGI UI is ready for action.\n";
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Test the channel interface: ui/wolfie_channels/index.html\n";
    echo "2. Create a channel and send messages\n";
    echo "3. Verify real-time polling works (2.1 seconds)\n";
    echo "4. Start using your real MySQL channels!\n";
} else {
    echo "⚠️  DEPLOYMENT NEEDS ATTENTION\n";
    echo "=============================\n";
    echo "Some tests failed. Please check the issues above.\n";
    echo "\n🔧 COMMON FIXES:\n";
    echo "1. Check database credentials in config/database_config.php\n";
    echo "2. Verify all files uploaded correctly\n";
    echo "3. Check file permissions (755 for dirs, 644 for files)\n";
    echo "4. Make sure database schema was imported\n";
}

echo "\n🌟 The system is yours to command, Captain WOLFIE! 🌟\n";
?>
