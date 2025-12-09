-- Push Device Tokens Table for Firebase Cloud Messaging
-- Stores device registration tokens for push notifications
-- Run: mysql -u root jira_clone < this_file.sql

CREATE TABLE IF NOT EXISTS push_device_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(500) NOT NULL UNIQUE KEY,
    platform ENUM('ios', 'android', 'web') NOT NULL DEFAULT 'web',
    active TINYINT(1) NOT NULL DEFAULT 1,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    CONSTRAINT fk_push_device_tokens_user FOREIGN KEY (user_id) 
        REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_user_active (user_id, active),
    INDEX idx_active (active),
    INDEX idx_created_at (created_at)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comment on table
ALTER TABLE push_device_tokens COMMENT='User device tokens for Firebase Cloud Messaging push notifications';
