-- Create comment_history table to track edit history
-- This allows users to see who edited a comment and when

CREATE TABLE IF NOT EXISTS comment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comment_id INT NOT NULL,
    edited_by INT NOT NULL,
    old_body LONGTEXT,
    new_body LONGTEXT,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    change_reason VARCHAR(255),
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (edited_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_comment_id (comment_id),
    INDEX idx_edited_at (edited_at),
    INDEX idx_edited_by (edited_by)
);

-- Add column to comments table to track edit count (optional, denormalized)
ALTER TABLE comments ADD COLUMN IF NOT EXISTS edit_count INT DEFAULT 0;

-- Add column to track if a comment is deleted (soft delete - optional)
ALTER TABLE comments ADD COLUMN IF NOT EXISTS is_deleted BOOLEAN DEFAULT FALSE;

-- Show table structure
DESCRIBE comment_history;
DESCRIBE comments;
