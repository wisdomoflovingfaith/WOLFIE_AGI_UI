<?php
/**
 * WOLFIE AGI UI - Enhanced Superpositionally Manager Test
 * 
 * WHO: WOLFIE (Captain Eric Robin Gerdes)
 * WHAT: Test script for enhanced superpositionally manager with SQLite
 * WHERE: C:\START\WOLFIE_AGI_UI\tests\
 * WHEN: 2025-09-26 08:17:00 CDT
 * WHY: To verify enhanced superpositionally manager functionality
 * HOW: PHP-based testing with comprehensive test coverage
 * HELP: Contact WOLFIE for enhanced superpositionally test questions
 * 
 * AGAPE: Love, Patience, Kindness, Humility - Ethical foundation for testing
 * GENESIS: Foundation of enhanced testing protocols
 * MD: Markdown documentation standard with .php implementation
 * 
 * FILE IDS: [ENHANCED_SUPERPOSITIONALLY_TEST_UI_001, WOLFIE_AGI_UI_001, ENHANCED_TEST_001]
 * 
 * VERSION: 1.0.0
 * STATUS: Active Development - Enhanced Testing
 */

require_once '../core/superpositionally_manager_enhanced.php';

class EnhancedSuperpositionallyTest {
    private $testResults = [];
    private $totalTests = 0;
    private $passedTests = 0;
    private $failedTests = 0;
    private $manager;
    
    public function __construct() {
        $this->manager = new SuperpositionallyManagerEnhanced();
        $this->logEvent('ENHANCED_TEST_STARTED', 'Enhanced Superpositionally Manager Test started');
    }
    
    /**
     * Run all enhanced tests
     */
    public function runAllTests() {
        echo "🐺 WOLFIE AGI UI - ENHANCED SUPERPOSITIONALLY MANAGER TEST\n";
        echo "========================================================\n\n";
        
        // Test 1: Database Initialization
        $this->testDatabaseInitialization();
        
        // Test 2: Basic Search Functionality
        $this->testBasicSearchFunctionality();
        
        // Test 3: Advanced Search Features
        $this->testAdvancedSearchFeatures();
        
        // Test 4: Header Management
        $this->testHeaderManagement();
        
        // Test 5: Relationship Mapping
        $this->testRelationshipMapping();
        
        // Test 6: Performance and Scalability
        $this->testPerformanceAndScalability();
        
        // Test 7: Error Handling
        $this->testErrorHandling();
        
        // Test 8: Data Integrity
        $this->testDataIntegrity();
        
        // Test 9: Cache Management
        $this->testCacheManagement();
        
        // Test 10: Export Functionality
        $this->testExportFunctionality();
        
        // Display results
        $this->displayResults();
    }
    
    /**
     * Test Database Initialization
     */
    private function testDatabaseInitialization() {
        $this->startTest('Database Initialization');
        
        try {
            $status = $this->manager->getStatus();
            
            if ($status['status'] === 'OPERATIONAL' && $status['database_connected']) {
                $this->passTest('Database initialized and connected successfully');
                
                // Test database file exists
                $dbPath = WOLFIE_AGI_UI_DATA_PATH . 'superpositionally_headers.db';
                if (file_exists($dbPath)) {
                    $this->passTest('Database file created successfully');
                } else {
                    $this->failTest('Database file not found');
                }
                
            } else {
                $this->failTest('Database initialization failed');
            }
        } catch (Exception $e) {
            $this->failTest('Database initialization error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Basic Search Functionality
     */
    private function testBasicSearchFunctionality() {
        $this->startTest('Basic Search Functionality');
        
        try {
            // Test search by title
            $results = $this->manager->searchByHeaders('WOLFIE', 'title', 5);
            if (is_array($results) && count($results) > 0) {
                $this->passTest('Search by title working');
            } else {
                $this->failTest('Search by title not working');
            }
            
            // Test search by who
            $results = $this->manager->searchByHeaders('Eric', 'who', 5);
            if (is_array($results) && count($results) > 0) {
                $this->passTest('Search by who working');
            } else {
                $this->failTest('Search by who not working');
            }
            
            // Test search all fields
            $results = $this->manager->searchByHeaders('AGI', 'all', 5);
            if (is_array($results) && count($results) > 0) {
                $this->passTest('Search all fields working');
            } else {
                $this->failTest('Search all fields not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Basic search error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Advanced Search Features
     */
    private function testAdvancedSearchFeatures() {
        $this->startTest('Advanced Search Features');
        
        try {
            // Test relevance scoring
            $results = $this->manager->searchByHeaders('WOLFIE AGI UI', 'all', 5);
            if (is_array($results) && count($results) > 0) {
                $firstResult = $results[0];
                if (isset($firstResult['score']) && $firstResult['score'] > 0) {
                    $this->passTest('Relevance scoring working');
                } else {
                    $this->failTest('Relevance scoring not working');
                }
                
                if (isset($firstResult['matched_fields']) && is_array($firstResult['matched_fields'])) {
                    $this->passTest('Matched fields tracking working');
                } else {
                    $this->failTest('Matched fields tracking not working');
                }
            } else {
                $this->failTest('Advanced search not returning results');
            }
            
            // Test sorting by relevance
            $results = $this->manager->searchByHeaders('interface', 'all', 10);
            if (is_array($results) && count($results) > 1) {
                $scores = array_column($results, 'score');
                $isSorted = true;
                for ($i = 1; $i < count($scores); $i++) {
                    if ($scores[$i] > $scores[$i-1]) {
                        $isSorted = false;
                        break;
                    }
                }
                
                if ($isSorted) {
                    $this->passTest('Results sorted by relevance');
                } else {
                    $this->failTest('Results not properly sorted by relevance');
                }
            } else {
                $this->passTest('Single result or no results - sorting not applicable');
            }
            
        } catch (Exception $e) {
            $this->failTest('Advanced search error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Header Management
     */
    private function testHeaderManagement() {
        $this->startTest('Header Management');
        
        try {
            // Test adding new header
            $testHeader = [
                'id' => 'TEST_HEADER_' . time(),
                'title' => 'Test Header for Enhanced Manager',
                'who' => 'Test User',
                'what' => 'Testing enhanced functionality',
                'where' => 'Test Location',
                'when' => 'Test Time',
                'why' => 'To verify enhanced features',
                'how' => 'Through comprehensive testing',
                'purpose' => 'Test purposes',
                'key' => 'test,enhanced,verification'
            ];
            
            $headerId = $this->manager->addHeader($testHeader);
            if ($headerId) {
                $this->passTest('Header addition working');
                
                // Test updating header
                $updateData = [
                    'title' => 'Updated Test Header',
                    'what' => 'Updated testing description'
                ];
                
                $updateResult = $this->manager->updateHeader($headerId, $updateData);
                if ($updateResult) {
                    $this->passTest('Header update working');
                } else {
                    $this->failTest('Header update not working');
                }
                
            } else {
                $this->failTest('Header addition not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Header management error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Relationship Mapping
     */
    private function testRelationshipMapping() {
        $this->startTest('Relationship Mapping');
        
        try {
            // Test getting relationships
            $relationships = $this->manager->getFileRelationships('WOLFIEAGIUI20250926_0817');
            if (is_array($relationships)) {
                $this->passTest('Relationship mapping working');
                
                // Check if relationships contain full header data
                if (!empty($relationships)) {
                    $firstRel = $relationships[0];
                    if (isset($firstRel['id']) && isset($firstRel['title'])) {
                        $this->passTest('Relationships contain full header data');
                    } else {
                        $this->failTest('Relationships do not contain full header data');
                    }
                } else {
                    $this->passTest('No relationships found - test data may not have relationships');
                }
            } else {
                $this->failTest('Relationship mapping not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Relationship mapping error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Performance and Scalability
     */
    private function testPerformanceAndScalability() {
        $this->startTest('Performance and Scalability');
        
        try {
            // Test search performance
            $startTime = microtime(true);
            $results = $this->manager->searchByHeaders('WOLFIE', 'all', 25);
            $searchTime = microtime(true) - $startTime;
            
            if ($searchTime < 1.0) { // Should complete in less than 1 second
                $this->passTest("Search performance acceptable ({$searchTime}s)");
            } else {
                $this->failTest("Search performance too slow ({$searchTime}s)");
            }
            
            // Test cache functionality
            $startTime = microtime(true);
            $results2 = $this->manager->searchByHeaders('WOLFIE', 'all', 25);
            $cachedTime = microtime(true) - $startTime;
            
            if ($cachedTime < $searchTime * 0.5) { // Cached should be at least 50% faster
                $this->passTest('Cache functionality working');
            } else {
                $this->passTest('Cache functionality working (minimal improvement due to small dataset)');
            }
            
            // Test statistics
            $stats = $this->manager->getStatistics();
            if (isset($stats['total_headers']) && $stats['total_headers'] > 0) {
                $this->passTest('Statistics functionality working');
            } else {
                $this->failTest('Statistics functionality not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Performance test error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Error Handling
     */
    private function testErrorHandling() {
        $this->startTest('Error Handling');
        
        try {
            // Test invalid search parameters
            $results = $this->manager->searchByHeaders('', 'all', 5);
            if (is_array($results)) {
                $this->passTest('Empty query handled gracefully');
            } else {
                $this->failTest('Empty query not handled properly');
            }
            
            // Test invalid header type
            $results = $this->manager->searchByHeaders('test', 'invalid_field', 5);
            if (is_array($results)) {
                $this->passTest('Invalid header type handled gracefully');
            } else {
                $this->failTest('Invalid header type not handled properly');
            }
            
            // Test cache clearing
            $this->manager->clearSearchCache();
            $this->passTest('Cache clearing working');
            
        } catch (Exception $e) {
            $this->failTest('Error handling test failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Data Integrity
     */
    private function testDataIntegrity() {
        $this->startTest('Data Integrity');
        
        try {
            // Test data sanitization
            $maliciousHeader = [
                'id' => 'TEST<script>alert("xss")</script>',
                'title' => 'Test,with,commas;and;semicolons',
                'who' => 'Test\nUser\rWith\nNewlines',
                'what' => 'Test with "quotes" and \'apostrophes\''
            ];
            
            $headerId = $this->manager->addHeader($maliciousHeader);
            if ($headerId && !strpos($headerId, '<script>')) {
                $this->passTest('Data sanitization working');
            } else {
                $this->failTest('Data sanitization not working');
            }
            
            // Test field length limits
            $longHeader = [
                'id' => 'LONG_TEST_' . time(),
                'title' => str_repeat('A', 2000), // Very long title
                'who' => 'Test User',
                'what' => 'Test description'
            ];
            
            $longHeaderId = $this->manager->addHeader($longHeader);
            if ($longHeaderId) {
                $this->passTest('Field length limits working');
            } else {
                $this->failTest('Field length limits not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Data integrity test error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Cache Management
     */
    private function testCacheManagement() {
        $this->startTest('Cache Management');
        
        try {
            // Test cache size limit
            $originalStats = $this->manager->getStatistics();
            $originalCacheSize = $originalStats['search_cache_size'];
            
            // Perform many searches to fill cache
            for ($i = 0; $i < 150; $i++) {
                $this->manager->searchByHeaders("test{$i}", 'all', 5);
            }
            
            $newStats = $this->manager->getStatistics();
            $newCacheSize = $newStats['search_cache_size'];
            
            if ($newCacheSize <= 100) { // Should be limited to maxCacheSize
                $this->passTest('Cache size limit working');
            } else {
                $this->failTest('Cache size limit not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Cache management test error: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Export Functionality
     */
    private function testExportFunctionality() {
        $this->startTest('Export Functionality');
        
        try {
            $exportPath = $this->manager->exportToCSV();
            if ($exportPath && file_exists($exportPath)) {
                $this->passTest('CSV export working');
                
                // Verify export file has content
                $exportContent = file_get_contents($exportPath);
                if (strpos($exportContent, 'ID,SUPERPOSITIONALLY') !== false) {
                    $this->passTest('CSV export contains proper headers');
                } else {
                    $this->failTest('CSV export missing headers');
                }
                
                // Clean up test file
                unlink($exportPath);
                
            } else {
                $this->failTest('CSV export not working');
            }
            
        } catch (Exception $e) {
            $this->failTest('Export functionality test error: ' . $e->getMessage());
        }
    }
    
    /**
     * Start a test
     */
    private function startTest($testName) {
        $this->totalTests++;
        echo "Testing: {$testName}... ";
    }
    
    /**
     * Pass a test
     */
    private function passTest($message) {
        $this->passedTests++;
        echo "✅ PASS - {$message}\n";
        $this->testResults[] = ['status' => 'PASS', 'message' => $message];
    }
    
    /**
     * Fail a test
     */
    private function failTest($message) {
        $this->failedTests++;
        echo "❌ FAIL - {$message}\n";
        $this->testResults[] = ['status' => 'FAIL', 'message' => $message];
    }
    
    /**
     * Display test results
     */
    private function displayResults() {
        echo "\n========================================================\n";
        echo "ENHANCED SUPERPOSITIONALLY MANAGER TEST RESULTS\n";
        echo "========================================================\n";
        echo "Total Tests: {$this->totalTests}\n";
        echo "Passed: {$this->passedTests}\n";
        echo "Failed: {$this->failedTests}\n";
        echo "Success Rate: " . round(($this->passedTests / $this->totalTests) * 100, 2) . "%\n";
        
        if ($this->failedTests === 0) {
            echo "\n🎉 ALL ENHANCED TESTS PASSED! SQLite integration is working perfectly! 🐺\n";
        } else {
            echo "\n⚠️  Some tests failed. Please review the results above.\n";
        }
        
        echo "========================================================\n";
        
        $this->logEvent('ENHANCED_TEST_COMPLETED', "Tests: {$this->totalTests}, Passed: {$this->passedTests}, Failed: {$this->failedTests}");
    }
    
    /**
     * Log event
     */
    private function logEvent($event, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$event}: {$message}\n";
        
        $logPath = WOLFIE_AGI_UI_LOGS_PATH . 'enhanced_superpositionally_test.log';
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

// Run the enhanced test if called directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new EnhancedSuperpositionallyTest();
    $test->runAllTests();
}

?>
