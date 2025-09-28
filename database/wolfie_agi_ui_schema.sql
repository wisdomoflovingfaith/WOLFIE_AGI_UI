-- ===========================================================================
-- WOLFIE AGI UI - MySQL Database Schema (Based on SalesSyntax 3.7.0)
-- ===========================================================================
-- WHO: Captain WOLFIE (Eric Robin Gerdes)
-- WHAT: Complete MySQL database schema for WOLFIE AGI UI channels
-- WHERE: C:\START\WOLFIE_AGI_UI\database\
-- WHEN: 2025-09-26 15:20:00 CDT
-- WHY: To create proper MySQL database structure like salessyntax3.7.0
-- HOW: SQL schema file for server database loading
-- 
-- AGAPE: Love, Patience, Kindness, Humility
-- GENESIS: Foundation of real MySQL channel communication
-- MD: Markdown documentation with .sql implementation
-- 
-- FILE IDS: [WOLFIE_AGI_UI_SCHEMA_001, WOLFIE_AGI_UI_024]
-- 
-- VERSION: 1.0.0 - The Captain's Real MySQL Schema
-- STATUS: Active - Based on SalesSyntax 3.7.0
-- ===========================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `wolfie_agi_ui` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `wolfie_agi_ui`;

-- ===========================================================================
-- CORE TABLES (Based on SalesSyntax 3.7.0)
-- ===========================================================================

-- Users table (like livehelp_users in salessyntax3.7.0)
CREATE TABLE IF NOT EXISTS `livehelp_users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `sessionid` VARCHAR(255) UNIQUE NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    `displayname` VARCHAR(255),
    `onchannel` INT DEFAULT 0,
    `status` VARCHAR(50) DEFAULT 'chat',
    `isnamed` TINYINT(1) DEFAULT 0,
    `isoperator` TINYINT(1) DEFAULT 0,
    `lastaction` VARCHAR(14) NOT NULL,
    `chataction` VARCHAR(14),
    `visits` INT DEFAULT 0,
    `user_alert` TINYINT(1) DEFAULT 0,
    `jsrn` INT DEFAULT 1,
    `chattype` VARCHAR(50) DEFAULT 'xmlhttp',
    `department` INT DEFAULT 0,
    `ipaddress` VARCHAR(45),
    `camefrom` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_sessionid` (`sessionid`),
    INDEX `idx_onchannel` (`onchannel`),
    INDEX `idx_status` (`status`),
    INDEX `idx_lastaction` (`lastaction`),
    INDEX `idx_isoperator` (`isoperator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table (like livehelp_messages in salessyntax3.7.0)
CREATE TABLE IF NOT EXISTS `livehelp_messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `message` TEXT NOT NULL,
    `channel` INT NOT NULL,
    `timeof` VARCHAR(14) NOT NULL,
    `saidfrom` INT NOT NULL,
    `saidto` INT DEFAULT 0,
    `typeof` VARCHAR(50) DEFAULT 'HTML',
    `jsrn` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_channel_time` (`channel`, `timeof`),
    INDEX `idx_timeof` (`timeof`),
    INDEX `idx_saidfrom` (`saidfrom`),
    INDEX `idx_saidto` (`saidto`),
    INDEX `idx_typeof` (`typeof`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Operator channels table (like livehelp_operator_channels in salessyntax3.7.0)
CREATE TABLE IF NOT EXISTS `livehelp_operator_channels` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `channel` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY `unique_user_channel` (`user_id`, `channel`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_channel` (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Departments table (like livehelp_departments in salessyntax3.7.0)
-- Note: In salessyntax3.7.0, channels are actually departments
CREATE TABLE IF NOT EXISTS `livehelp_departments` (
    `recno` INT AUTO_INCREMENT PRIMARY KEY,
    `nameof` VARCHAR(255) NOT NULL,
    `topbackground` VARCHAR(255),
    `theme` VARCHAR(255),
    `colorscheme` VARCHAR(255),
    `description` TEXT,
    `status` VARCHAR(50) DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_nameof` (`nameof`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Visit tracking table (like livehelp_visit_track in salessyntax3.7.0)
CREATE TABLE IF NOT EXISTS `livehelp_visit_track` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sessionid` VARCHAR(255) NOT NULL,
    `location` TEXT,
    `whendone` VARCHAR(14) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_sessionid` (`sessionid`),
    INDEX `idx_whendone` (`whendone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- WOLFIE AGI UI SPECIFIC TABLES
-- ===========================================================================

-- Superpositionally headers table
CREATE TABLE IF NOT EXISTS `superpositionally_headers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `file_path` VARCHAR(500) NOT NULL,
    `title` VARCHAR(255),
    `who` VARCHAR(255),
    `what` TEXT,
    `where` VARCHAR(255),
    `when` VARCHAR(255),
    `why` TEXT,
    `how` TEXT,
    `purpose` TEXT,
    `key` VARCHAR(255),
    `superpositionally` TEXT,
    `date` VARCHAR(255),
    `file_type` VARCHAR(50),
    `last_modified` TIMESTAMP,
    `file_size` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_file_path` (`file_path`),
    INDEX `idx_who` (`who`),
    INDEX `idx_what` (`what`(100)),
    INDEX `idx_where` (`where`),
    INDEX `idx_when` (`when`),
    INDEX `idx_key` (`key`),
    INDEX `idx_superpositionally` (`superpositionally`(100)),
    INDEX `idx_file_type` (`file_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting mode data table
CREATE TABLE IF NOT EXISTS `meeting_mode_data` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `meeting_id` VARCHAR(255) NOT NULL,
    `channel_id` INT,
    `meeting_data` JSON,
    `pattern_analysis` TEXT,
    `action_items` JSON,
    `status` VARCHAR(50) DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_meeting_id` (`meeting_id`),
    INDEX `idx_channel_id` (`channel_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No-Casino Mode data table
CREATE TABLE IF NOT EXISTS `no_casino_data` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gig_id` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255),
    `description` TEXT,
    `status` VARCHAR(50) DEFAULT 'active',
    `progress` INT DEFAULT 0,
    `dream_insights` TEXT,
    `alternative_strategies` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_gig_id` (`gig_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_progress` (`progress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Captain intent log table
CREATE TABLE IF NOT EXISTS `captain_intent_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `intent_id` VARCHAR(255) NOT NULL,
    `intent_type` VARCHAR(100),
    `intent_data` JSON,
    `approval_status` VARCHAR(50) DEFAULT 'pending',
    `emotional_resonance` INT DEFAULT 0,
    `symbolic_approval` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_intent_id` (`intent_id`),
    INDEX `idx_intent_type` (`intent_type`),
    INDEX `idx_approval_status` (`approval_status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================================
-- FOREIGN KEY CONSTRAINTS
-- ===========================================================================

-- Add foreign key constraints
ALTER TABLE `livehelp_messages` 
ADD CONSTRAINT `fk_messages_saidfrom` 
FOREIGN KEY (`saidfrom`) REFERENCES `livehelp_users`(`user_id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `livehelp_messages` 
ADD CONSTRAINT `fk_messages_saidto` 
FOREIGN KEY (`saidto`) REFERENCES `livehelp_users`(`user_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `livehelp_operator_channels` 
ADD CONSTRAINT `fk_operator_channels_user` 
FOREIGN KEY (`user_id`) REFERENCES `livehelp_users`(`user_id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `livehelp_operator_channels` 
ADD CONSTRAINT `fk_operator_channels_channel` 
FOREIGN KEY (`channel`) REFERENCES `livehelp_departments`(`recno`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `meeting_mode_data` 
ADD CONSTRAINT `fk_meeting_channel` 
FOREIGN KEY (`channel_id`) REFERENCES `livehelp_departments`(`recno`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- ===========================================================================
-- INITIAL DATA
-- ===========================================================================

-- Insert default department (general channel)
INSERT INTO `livehelp_departments` (`nameof`, `topbackground`, `theme`, `colorscheme`, `description`) 
VALUES ('General Channel', '#ffffff', 'default', 'blue', 'Default general purpose channel for WOLFIE AGI UI');

-- Insert Captain WOLFIE user
INSERT INTO `livehelp_users` (`sessionid`, `username`, `displayname`, `onchannel`, `status`, `isnamed`, `isoperator`, `lastaction`, `jsrn`, `chattype`) 
VALUES ('captain_wolfie_session', 'captain_wolfie', 'Captain WOLFIE', 1, 'chat', 1, 1, '20250926150000', 1, 'xmlhttp');

-- Insert Captain WOLFIE into general channel
INSERT INTO `livehelp_operator_channels` (`user_id`, `channel`) 
VALUES (1, 1);

-- ===========================================================================
-- VIEWS FOR EASY QUERYING
-- ===========================================================================

-- Channel status view
CREATE VIEW `channel_status` AS
SELECT 
    d.recno as channel_id,
    d.nameof as channel_name,
    d.status as channel_status,
    COUNT(DISTINCT u.user_id) as user_count,
    COUNT(DISTINCT m.message_id) as message_count,
    MAX(m.timeof) as last_message_time,
    d.created_at as channel_created
FROM `livehelp_departments` d
LEFT JOIN `livehelp_users` u ON d.recno = u.onchannel
LEFT JOIN `livehelp_messages` m ON d.recno = m.channel
GROUP BY d.recno, d.nameof, d.status, d.created_at;

-- User activity view
CREATE VIEW `user_activity` AS
SELECT 
    u.user_id,
    u.username,
    u.displayname,
    u.status,
    u.onchannel,
    d.nameof as channel_name,
    u.lastaction,
    u.visits,
    u.isoperator,
    COUNT(m.message_id) as message_count
FROM `livehelp_users` u
LEFT JOIN `livehelp_departments` d ON u.onchannel = d.recno
LEFT JOIN `livehelp_messages` m ON u.user_id = m.saidfrom
GROUP BY u.user_id, u.username, u.displayname, u.status, u.onchannel, d.nameof, u.lastaction, u.visits, u.isoperator;

-- ===========================================================================
-- STORED PROCEDURES
-- ===========================================================================

-- Procedure to create a new channel
DELIMITER //
CREATE PROCEDURE `CreateChannel`(
    IN p_name VARCHAR(255),
    IN p_description TEXT,
    IN p_user_id INT
)
BEGIN
    DECLARE v_channel_id INT;
    
    -- Create the channel
    INSERT INTO `livehelp_departments` (`nameof`, `description`) 
    VALUES (p_name, p_description);
    
    SET v_channel_id = LAST_INSERT_ID();
    
    -- Add user to channel
    INSERT INTO `livehelp_operator_channels` (`user_id`, `channel`) 
    VALUES (p_user_id, v_channel_id);
    
    -- Update user's channel
    UPDATE `livehelp_users` 
    SET `onchannel` = v_channel_id, `lastaction` = DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')
    WHERE `user_id` = p_user_id;
    
    SELECT v_channel_id as channel_id;
END //
DELIMITER ;

-- Procedure to send a message
DELIMITER //
CREATE PROCEDURE `SendMessage`(
    IN p_channel_id INT,
    IN p_user_id INT,
    IN p_message TEXT,
    IN p_type VARCHAR(50)
)
BEGIN
    DECLARE v_timeof VARCHAR(14);
    DECLARE v_jsrn INT;
    
    -- Get current timestamp
    SET v_timeof = DATE_FORMAT(NOW(), '%Y%m%d%H%i%s');
    
    -- Check for duplicate timestamps
    WHILE EXISTS (SELECT 1 FROM `livehelp_messages` WHERE `timeof` = v_timeof) DO
        SET v_timeof = DATE_FORMAT(NOW(), '%Y%m%d%H%i%s');
    END WHILE;
    
    -- Get user's JSRN
    SELECT `jsrn` INTO v_jsrn FROM `livehelp_users` WHERE `user_id` = p_user_id;
    
    -- Insert message
    INSERT INTO `livehelp_messages` (`message`, `channel`, `timeof`, `saidfrom`, `typeof`, `jsrn`) 
    VALUES (p_message, p_channel_id, v_timeof, p_user_id, p_type, v_jsrn);
    
    -- Update user's last action
    UPDATE `livehelp_users` 
    SET `lastaction` = v_timeof 
    WHERE `user_id` = p_user_id;
    
    SELECT LAST_INSERT_ID() as message_id, v_timeof as timeof;
END //
DELIMITER ;

-- ===========================================================================
-- TRIGGERS
-- ===========================================================================

-- Trigger to update user's last action when sending message
DELIMITER //
CREATE TRIGGER `tr_update_user_lastaction` 
AFTER INSERT ON `livehelp_messages`
FOR EACH ROW
BEGIN
    UPDATE `livehelp_users` 
    SET `lastaction` = NEW.timeof 
    WHERE `user_id` = NEW.saidfrom;
END //
DELIMITER ;

-- ===========================================================================
-- INDEXES FOR PERFORMANCE
-- ===========================================================================

-- Additional indexes for better performance
CREATE INDEX `idx_messages_channel_type` ON `livehelp_messages` (`channel`, `typeof`);
CREATE INDEX `idx_messages_timeof_desc` ON `livehelp_messages` (`timeof` DESC);
CREATE INDEX `idx_users_status_channel` ON `livehelp_users` (`status`, `onchannel`);
CREATE INDEX `idx_users_lastaction_desc` ON `livehelp_users` (`lastaction` DESC);

-- ===========================================================================
-- GRANTS (Adjust as needed for your server)
-- ===========================================================================

-- Create user for WOLFIE AGI UI (adjust password as needed)
-- CREATE USER 'wolfie_agi_ui'@'localhost' IDENTIFIED BY 'your_secure_password';
-- GRANT ALL PRIVILEGES ON wolfie_agi_ui.* TO 'wolfie_agi_ui'@'localhost';
-- FLUSH PRIVILEGES;

-- ===========================================================================
-- COMPLETION MESSAGE
-- ===========================================================================

SELECT 'WOLFIE AGI UI Database Schema Created Successfully!' as message;
SELECT 'Captain WOLFIE, your MySQL database is ready for action!' as status;
SELECT NOW() as created_at;
