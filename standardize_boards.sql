-- 1. Insert Reopened if missing
-- Using INSERT IGNORE based on name unique constraint if strictly enforced, or just check. 
-- Assuming name is unique or we just want to ensure it acts like an upsert.
-- Since we don't know constraints for sure, we can use NOT EXISTS logic
INSERT INTO statuses (name, description, category, color, sort_order)
SELECT 'Reopened', 'Issue reopened', 'todo', '#AB47BC', 8
WHERE NOT EXISTS (SELECT 1 FROM statuses WHERE name = 'Reopened');

-- 2. Delete all existing board columns to reset them
DELETE FROM board_columns;

-- 3. Insert standard columns for ALL boards
-- Open
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'Open', JSON_ARRAY((SELECT id FROM statuses WHERE name='Open' LIMIT 1)), 0 FROM boards;

-- To Do
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'To Do', JSON_ARRAY((SELECT id FROM statuses WHERE name='To Do' LIMIT 1)), 1 FROM boards;

-- In Progress
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'In Progress', JSON_ARRAY((SELECT id FROM statuses WHERE name='In Progress' LIMIT 1)), 2 FROM boards;

-- In Review
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'In Review', JSON_ARRAY((SELECT id FROM statuses WHERE name='In Review' LIMIT 1)), 3 FROM boards;

-- Testing
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'Testing', JSON_ARRAY((SELECT id FROM statuses WHERE name='Testing' LIMIT 1)), 4 FROM boards;

-- Done
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'Done', JSON_ARRAY((SELECT id FROM statuses WHERE name='Done' LIMIT 1)), 5 FROM boards;

-- Closed
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'Closed', JSON_ARRAY((SELECT id FROM statuses WHERE name='Closed' LIMIT 1)), 6 FROM boards;

-- Reopened
INSERT INTO board_columns (board_id, name, status_ids, sort_order)
SELECT id, 'Reopened', JSON_ARRAY((SELECT id FROM statuses WHERE name='Reopened' LIMIT 1)), 7 FROM boards;
