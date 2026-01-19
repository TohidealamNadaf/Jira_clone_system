USE cways_prod;

-- 1. Create Board if not exists
INSERT INTO boards (project_id, name, type, is_private, created_at, updated_at)
SELECT id, 'CWays MIS Kanban Board', 'kanban', 0, NOW(), NOW()
FROM projects WHERE `key` = 'CWAYSMIS'
AND NOT EXISTS (SELECT 1 FROM boards WHERE name = 'CWays MIS Kanban Board' AND type = 'kanban');

-- 2. Get IDs
SET @board_id = (SELECT id FROM boards WHERE name = 'CWays MIS Kanban Board' LIMIT 1);
-- Assuming standard status names, checking for existence
SET @todo_id = (SELECT id FROM statuses WHERE category = 'todo' ORDER BY sort_order ASC LIMIT 1);
SET @prog_id = (SELECT id FROM statuses WHERE category = 'in_progress' ORDER BY sort_order ASC LIMIT 1);
SET @done_id = (SELECT id FROM statuses WHERE category = 'done' ORDER BY sort_order ASC LIMIT 1);

-- Fallbacks if NULL (should not happen in prod, but safe)
SET @todo_id = COALESCE(@todo_id, 1);
SET @prog_id = COALESCE(@prog_id, 2);
SET @done_id = COALESCE(@done_id, 3);

-- 3. Insert Columns
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT @board_id, 'To Do', CONCAT('[', @todo_id, ']'), 0
WHERE NOT EXISTS (SELECT 1 FROM board_columns WHERE board_id = @board_id AND name = 'To Do');

INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT @board_id, 'In Progress', CONCAT('[', @prog_id, ']'), 1
WHERE NOT EXISTS (SELECT 1 FROM board_columns WHERE board_id = @board_id AND name = 'In Progress');

INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT @board_id, 'Done', CONCAT('[', @done_id, ']'), 2
WHERE NOT EXISTS (SELECT 1 FROM board_columns WHERE board_id = @board_id AND name = 'Done');
