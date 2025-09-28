-- WOLFIE AGI UI - Complete MySQL Schema
-- 
-- WHO: Captain WOLFIE (Eric Robin Gerdes)
-- WHAT: Complete MySQL schema for WOLFIE AGI UI system
-- WHERE: C:\START\WOLFIE_AGI_UI\database\
-- WHEN: 2025-09-26 17:25:00 CDT
-- WHY: To provide complete MySQL schema for production deployment
-- HOW: SQL schema with all tables, indexes, and constraints
-- 
-- AGAPE: Love, Patience, Kindness, Humility
-- GENESIS: Foundation of MySQL database schema
-- MD: Markdown documentation with .sql implementation
-- 
-- FILE IDS: [WOLFIE_AGI_SCHEMA_001, WOLFIE_AGI_UI_045]
-- 
-- VERSION: 1.0.0 - The Captain's Complete MySQL Schema
-- STATUS: Active - Production Ready

-- Create database
CREATE DATABASE IF NOT EXISTS wolfie_agi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wolfie_agi;

-- Channels table
CREATE TABLE IF NOT EXISTS channels (
    channel_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'general',
    description TEXT,
    created_at VARCHAR(14) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_by VARCHAR(50),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Channel users table
CREATE TABLE IF NOT EXISTS channel_users (
    channel_id VARCHAR(50) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    session_id VARCHAR(50),
    joined_at VARCHAR(14) NOT NULL,
    last_seen VARCHAR(14),
    status VARCHAR(20) DEFAULT 'active',
    PRIMARY KEY (channel_id, user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_joined_at (joined_at),
    FOREIGN KEY (channel_id) REFERENCES channels(channel_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    message_id VARCHAR(50) PRIMARY KEY,
    channel_id VARCHAR(50) NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    timeof VARCHAR(14) NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'HTML',
    jsrn INT NOT NULL,
    parent_message_id VARCHAR(50),
    is_edited BOOLEAN DEFAULT FALSE,
    edited_at VARCHAR(14),
    INDEX idx_channel_id (channel_id),
    INDEX idx_user_id (user_id),
    INDEX idx_timeof (timeof),
    INDEX idx_type (type),
    INDEX idx_parent_message (parent_message_id),
    FOREIGN KEY (channel_id) REFERENCES channels(channel_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Superpositionally headers table
CREATE TABLE IF NOT EXISTS superpositionally_headers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_id VARCHAR(100) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    title VARCHAR(255),
    who VARCHAR(100),
    what TEXT,
    where_field VARCHAR(255),
    when_field VARCHAR(50),
    why TEXT,
    how TEXT,
    purpose TEXT,
    key_field TEXT,
    super_positionally TEXT,
    date_field VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_file_id (file_id),
    INDEX idx_file_path (file_path),
    INDEX idx_who (who),
    INDEX idx_what (what(100)),
    INDEX idx_when (when_field),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- File relationships table
CREATE TABLE IF NOT EXISTS file_relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_file_id VARCHAR(100) NOT NULL,
    target_file_id VARCHAR(100) NOT NULL,
    relationship_type VARCHAR(50) NOT NULL,
    strength DECIMAL(3,2) DEFAULT 1.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_source_file (source_file_id),
    INDEX idx_target_file (target_file_id),
    INDEX idx_relationship_type (relationship_type),
    UNIQUE KEY unique_relationship (source_file_id, target_file_id, relationship_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agent sessions table
CREATE TABLE IF NOT EXISTS agent_sessions (
    session_id VARCHAR(50) PRIMARY KEY,
    agent_id VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    last_activity VARCHAR(14) NOT NULL,
    capabilities TEXT,
    security_level VARCHAR(20) DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_agent_id (agent_id),
    INDEX idx_status (status),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Task queue table
CREATE TABLE IF NOT EXISTS task_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id VARCHAR(50) NOT NULL,
    agent_id VARCHAR(50) NOT NULL,
    channel_id VARCHAR(50),
    task_type VARCHAR(50) NOT NULL,
    task_data TEXT,
    priority INT DEFAULT 5,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    INDEX idx_agent_id (agent_id),
    INDEX idx_channel_id (channel_id),
    INDEX idx_task_type (task_type),
    INDEX idx_priority (priority),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting data table
CREATE TABLE IF NOT EXISTS meeting_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_id VARCHAR(50) NOT NULL,
    channel_id VARCHAR(50) NOT NULL,
    meeting_type VARCHAR(50) NOT NULL,
    participants TEXT,
    agenda TEXT,
    notes TEXT,
    action_items TEXT,
    status VARCHAR(20) DEFAULT 'scheduled',
    scheduled_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    ended_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_meeting_id (meeting_id),
    INDEX idx_channel_id (channel_id),
    INDEX idx_meeting_type (meeting_type),
    INDEX idx_status (status),
    INDEX idx_scheduled_at (scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No-casino mode data table
CREATE TABLE IF NOT EXISTS no_casino_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gig_id VARCHAR(50) NOT NULL,
    gig_title VARCHAR(255) NOT NULL,
    gig_description TEXT,
    gig_type VARCHAR(50) NOT NULL,
    platform VARCHAR(50) NOT NULL,
    budget DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending',
    assigned_agent VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gig_id (gig_id),
    INDEX idx_gig_type (gig_type),
    INDEX idx_platform (platform),
    INDEX idx_status (status),
    INDEX idx_assigned_agent (assigned_agent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System logs table
CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_level VARCHAR(20) NOT NULL,
    component VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    context TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_log_level (log_level),
    INDEX idx_component (component),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project tracking table
CREATE TABLE IF NOT EXISTS project_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id VARCHAR(50) NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    progress INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'pending',
    description TEXT,
    assigned_agents TEXT,
    milestones TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project_id (project_id),
    INDEX idx_status (status),
    INDEX idx_progress (progress),
    INDEX idx_last_updated (last_updated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default data
INSERT INTO channels (channel_id, name, type, description, created_at, status, created_by) VALUES
('channel_default', 'General Discussion', 'general', 'Default channel for general discussion', '20250926170000', 'active', 'captain_wolfie'),
('channel_agi_coordination', 'AGI Coordination', 'general', 'Channel for AGI system coordination', '20250926170000', 'active', 'captain_wolfie'),
('channel_meeting', 'Meeting Channel', 'meeting', 'Channel for meeting coordination', '20250926170000', 'active', 'captain_wolfie'),
('channel_support', 'Support Channel', 'support', 'Channel for technical support', '20250926170000', 'active', 'captain_wolfie');

INSERT INTO project_tracking (project_id, project_name, progress, status, description, assigned_agents) VALUES
('wolfie_agi_ui', 'WOLFIE AGI UI', 75, 'In Progress', 'Main UI system for WOLFIE AGI', 'captain_wolfie,cursor,copilot'),
('superpositionally_search', 'Superpositionally Search', 90, 'Near Complete', 'Advanced search system with headers', 'grok,claude,deepseek'),
('mysql_integration', 'MySQL Integration', 95, 'Near Complete', 'MySQL database integration for production', 'captain_wolfie,ara'),
('xss_protection', 'XSS Protection', 100, 'Complete', 'XSS vulnerability protection implementation', 'captain_wolfie,ethical_guardian');

-- Create views for common queries
CREATE VIEW active_channels AS
SELECT c.*, COUNT(cu.user_id) as user_count, COUNT(m.message_id) as message_count
FROM channels c
LEFT JOIN channel_users cu ON c.channel_id = cu.channel_id
LEFT JOIN messages m ON c.channel_id = m.channel_id
WHERE c.status = 'active'
GROUP BY c.channel_id;

CREATE VIEW recent_messages AS
SELECT m.*, c.name as channel_name, c.type as channel_type
FROM messages m
JOIN channels c ON m.channel_id = c.channel_id
WHERE m.timeof > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 24 HOUR), '%Y%m%d%H%i%s')
ORDER BY m.timeof DESC;

CREATE VIEW agent_activity AS
SELECT 
    agent_id,
    COUNT(DISTINCT channel_id) as active_channels,
    COUNT(message_id) as messages_sent,
    MAX(timeof) as last_message_time
FROM messages
WHERE timeof > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 7 DAY), '%Y%m%d%H%i%s')
GROUP BY agent_id;

-- Create stored procedures
DELIMITER //

CREATE PROCEDURE CleanupOldMessages(IN days_to_keep INT)
BEGIN
    DECLARE cutoff_date VARCHAR(14);
    SET cutoff_date = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL days_to_keep DAY), '%Y%m%d%H%i%s');
    
    DELETE FROM messages WHERE timeof < cutoff_date;
    
    SELECT ROW_COUNT() as deleted_messages;
END //

CREATE PROCEDURE GetChannelStats(IN channel_id_param VARCHAR(50))
BEGIN
    SELECT 
        c.name as channel_name,
        c.type as channel_type,
        COUNT(DISTINCT cu.user_id) as user_count,
        COUNT(m.message_id) as message_count,
        MAX(m.timeof) as last_message_time,
        COUNT(CASE WHEN m.timeof > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 24 HOUR), '%Y%m%d%H%i%s') THEN 1 END) as messages_last_24h
    FROM channels c
    LEFT JOIN channel_users cu ON c.channel_id = cu.channel_id
    LEFT JOIN messages m ON c.channel_id = m.channel_id
    WHERE c.channel_id = channel_id_param
    GROUP BY c.channel_id, c.name, c.type;
END //

CREATE PROCEDURE GetAgentStats(IN agent_id_param VARCHAR(50))
BEGIN
    SELECT 
        agent_id_param as agent_id,
        COUNT(DISTINCT m.channel_id) as active_channels,
        COUNT(m.message_id) as total_messages,
        COUNT(CASE WHEN m.timeof > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 24 HOUR), '%Y%m%d%H%i%s') THEN 1 END) as messages_last_24h,
        MAX(m.timeof) as last_message_time
    FROM messages m
    WHERE m.user_id = agent_id_param;
END //

DELIMITER ;

-- Create triggers for automatic updates
DELIMITER //

CREATE TRIGGER update_channel_activity 
AFTER INSERT ON messages
FOR EACH ROW
BEGIN
    UPDATE channels 
    SET status = 'active' 
    WHERE channel_id = NEW.channel_id;
END //

CREATE TRIGGER log_message_activity
AFTER INSERT ON messages
FOR EACH ROW
BEGIN
    INSERT INTO system_logs (log_level, component, message, context)
    VALUES ('INFO', 'messages', 'New message created', CONCAT('message_id:', NEW.message_id, ',channel_id:', NEW.channel_id, ',user_id:', NEW.user_id));
END //

DELIMITER ;

-- Grant permissions (adjust as needed for your environment)
-- CREATE USER 'wolfie_user'@'localhost' IDENTIFIED BY 'secure_password';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON wolfie_agi.* TO 'wolfie_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Show table information
SHOW TABLES;
SELECT 'Database schema created successfully!' as status;
