-- Add indexes for comment-related queries
-- This improves performance for filtering and sorting comments

-- Index on issue_id for faster comment lookups by issue
CREATE INDEX IF NOT EXISTS idx_comments_issue_id ON comments(issue_id);

-- Index on user_id for faster lookups of comments by user
CREATE INDEX IF NOT EXISTS idx_comments_user_id ON comments(user_id);

-- Index on created_at for sorting comments by date
CREATE INDEX IF NOT EXISTS idx_comments_created_at ON comments(created_at DESC);

-- Composite index for efficient queries on issue_id with ordering
CREATE INDEX IF NOT EXISTS idx_comments_issue_created ON comments(issue_id, created_at DESC);

-- Index on updated_at to support edit history queries
CREATE INDEX IF NOT EXISTS idx_comments_updated_at ON comments(updated_at DESC);

-- Show index information
SHOW INDEX FROM comments;
