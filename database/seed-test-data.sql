-- ==========================================
-- Comprehensive Test Data for Jira Clone
-- ==========================================
-- This file contains test data to fully test the system
-- with overdue issues, various due dates, comments, and work logs

-- Get user IDs (assuming admin and test users exist)
SET @admin_user_id = (SELECT id FROM users WHERE is_admin = 1 LIMIT 1);
SET @user1_id = (SELECT id FROM users WHERE is_admin = 0 LIMIT 1);
SET @user2_id = (SELECT id FROM users WHERE is_admin = 0 LIMIT 1 OFFSET 1);
SET @user3_id = (SELECT id FROM users WHERE is_admin = 0 LIMIT 1 OFFSET 2);

-- Get issue type IDs
SET @bug_type = (SELECT id FROM issue_types WHERE name = 'Bug' LIMIT 1);
SET @feature_type = (SELECT id FROM issue_types WHERE name = 'Feature' LIMIT 1);
SET @task_type = (SELECT id FROM issue_types WHERE name = 'Task' LIMIT 1);
SET @improvement_type = (SELECT id FROM issue_types WHERE name = 'Improvement' LIMIT 1);

-- Get status IDs
SET @open_status = (SELECT id FROM statuses WHERE name = 'Open' LIMIT 1);
SET @in_progress_status = (SELECT id FROM statuses WHERE name = 'In Progress' LIMIT 1);
SET @done_status = (SELECT id FROM statuses WHERE name = 'Done' LIMIT 1);

-- Get priority IDs (1=Low, 2=Medium, 3=High, 4=Urgent)
SET @low_priority = 1;
SET @medium_priority = 2;
SET @high_priority = 3;
SET @urgent_priority = 4;

-- ==========================================
-- INSERT PROJECTS
-- ==========================================
INSERT IGNORE INTO projects (name, `key`, description, lead_id, created_by, created_at, is_active) VALUES
('E-Commerce Platform', 'ECOM', 'Main e-commerce platform project for online retail business', @admin_user_id, @admin_user_id, NOW(), 1),
('Mobile App', 'MOB', 'iOS and Android mobile application development', @admin_user_id, @admin_user_id, NOW(), 1),
('Backend API', 'API', 'RESTful API development and maintenance', @admin_user_id, @admin_user_id, NOW(), 1),
('DevOps Infrastructure', 'DEVOPS', 'Cloud infrastructure, CI/CD pipelines, and deployment', @admin_user_id, @admin_user_id, NOW(), 1),
('QA & Testing', 'QA', 'Quality assurance and automated testing framework', @admin_user_id, @admin_user_id, NOW(), 1);

-- Get project IDs
SET @ecom_proj = (SELECT id FROM projects WHERE `key` = 'ECOM' LIMIT 1);
SET @mob_proj = (SELECT id FROM projects WHERE `key` = 'MOB' LIMIT 1);
SET @api_proj = (SELECT id FROM projects WHERE `key` = 'API' LIMIT 1);
SET @devops_proj = (SELECT id FROM projects WHERE `key` = 'DEVOPS' LIMIT 1);
SET @qa_proj = (SELECT id FROM projects WHERE `key` = 'QA' LIMIT 1);

-- ==========================================
-- INSERT ISSUES FOR E-COMMERCE PROJECT
-- ==========================================

-- OVERDUE ISSUES (past due dates)
INSERT INTO issues (project_id, issue_key, summary, description, issue_type_id, status_id, priority_id, assignee_id, reporter_id, due_date, start_date, story_points, created_at, updated_at) VALUES
(@ecom_proj, 'ECOM-1', 'Fix critical bug in checkout process', '<p><strong>Critical issue:</strong> Users cannot complete purchases</p><p>The checkout process is <strong>broken</strong> and needs <em>immediate</em> attention.</p>', @bug_type, @open_status, @urgent_priority, @user1_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 12 DAY), 8, DATE_SUB(NOW(), INTERVAL 15 DAY), NOW()),
(@ecom_proj, 'ECOM-2', 'Update user authentication system', '<p>Implement OAuth2.0 authentication</p><ul><li>Support Google login</li><li>Support GitHub login</li><li>Add two-factor authentication</li></ul>', @feature_type, @in_progress_status, @high_priority, @user2_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(CURDATE(), INTERVAL 9 DAY), 13, DATE_SUB(NOW(), INTERVAL 20 DAY), NOW()),
(@ecom_proj, 'ECOM-3', 'Performance optimization for search', '<p>Optimize search queries to reduce response time below 100ms</p><pre><code>SELECT * FROM products WHERE name LIKE ?</code></pre>', @task_type, @open_status, @high_priority, @user3_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 5, DATE_SUB(NOW(), INTERVAL 18 DAY), NOW()),

-- DUE SOON (today to 3 days)
(@ecom_proj, 'ECOM-4', 'Implement dark mode theme', '<p>Add dark mode toggle to user settings</p><blockquote>This is a frequently requested feature from users</blockquote>', @feature_type, @open_status, @medium_priority, @user1_id, @admin_user_id, CURDATE(), DATE_SUB(CURDATE(), INTERVAL 7 DAY), 5, DATE_SUB(NOW(), INTERVAL 10 DAY), NOW()),
(@ecom_proj, 'ECOM-5', 'Database migration for schema v2', '<p>Migrate existing data to new schema</p><ol><li>Create new tables</li><li>Migrate data</li><li>Validate migration</li><li>Drop old tables</li></ol>', @task_type, @in_progress_status, @high_priority, @user2_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 1 DAY), DATE_SUB(CURDATE(), INTERVAL 7 DAY), 8, DATE_SUB(NOW(), INTERVAL 12 DAY), NOW()),
(@ecom_proj, 'ECOM-6', 'Write API documentation', '<p>Complete <strong>API documentation</strong> for all endpoints</p>', @task_type, @open_status, @medium_priority, @user3_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), 3, DATE_SUB(NOW(), INTERVAL 8 DAY), NOW()),

-- FUTURE ISSUES (1-2 weeks)
(@ecom_proj, 'ECOM-7', 'Implement push notifications', '<p>Add push notification support for mobile app</p><ul><li>Firebase Cloud Messaging</li><li>Local notifications</li><li>Notification center UI</li></ul>', @feature_type, @open_status, @medium_priority, @user1_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 0 DAY), 8, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()),
(@ecom_proj, 'ECOM-8', 'Refactor API authentication middleware', '<p>Improve JWT token handling and refresh logic</p>', @improvement_type, @open_status, @low_priority, @user2_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 5, DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()),

-- COMPLETED ISSUES
(@ecom_proj, 'ECOM-9', 'Fix responsive design on mobile', '<p>CSS breakpoints are broken on screens < 768px</p>', @bug_type, @done_status, @high_priority, @user3_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 17 DAY), 5, DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY)),
(@ecom_proj, 'ECOM-10', 'Review and optimize database indexes', '<p>Analyze slow queries and add missing indexes</p>', @task_type, @done_status, @medium_priority, @user1_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 14 DAY), 3, DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));

-- ==========================================
-- INSERT ISSUES FOR MOBILE APP PROJECT
-- ==========================================
INSERT INTO issues (project_id, issue_key, summary, description, issue_type_id, status_id, priority_id, assignee_id, reporter_id, due_date, start_date, story_points, created_at, updated_at) VALUES
(@mob_proj, 'MOB-1', 'Fix crash on app startup', '<p><strong>Critical:</strong> App crashes immediately on iOS 14+</p>', @bug_type, @open_status, @urgent_priority, @user2_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 4 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), 5, DATE_SUB(NOW(), INTERVAL 14 DAY), NOW()),
(@mob_proj, 'MOB-2', 'Implement offline mode', '<p>Allow users to use app without internet connection</p><ul><li>Cache data locally</li><li>Sync when online</li></ul>', @feature_type, @in_progress_status, @high_priority, @user3_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), 13, DATE_SUB(NOW(), INTERVAL 18 DAY), NOW()),
(@mob_proj, 'MOB-3', 'Update UI to new design', '<p>Implement new design system across all screens</p>', @task_type, @open_status, @high_priority, @user1_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 2 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 8, DATE_SUB(NOW(), INTERVAL 10 DAY), NOW()),
(@mob_proj, 'MOB-4', 'Add biometric authentication', '<p>Support Face ID and Touch ID</p>', @feature_type, @open_status, @medium_priority, @user2_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 8, DATE_SUB(NOW(), INTERVAL 4 DAY), NOW()),
(@mob_proj, 'MOB-5', 'Fix memory leaks in image loading', '<p>Memory usage keeps increasing when scrolling</p>', @bug_type, @done_status, @high_priority, @user3_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 8 DAY), DATE_SUB(CURDATE(), INTERVAL 15 DAY), 3, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY));

-- ==========================================
-- INSERT ISSUES FOR BACKEND API PROJECT
-- ==========================================
INSERT INTO issues (project_id, issue_key, summary, description, issue_type_id, status_id, priority_id, assignee_id, reporter_id, due_date, start_date, story_points, created_at, updated_at) VALUES
(@api_proj, 'API-1', 'Rate limiting not working correctly', '<p>Users can exceed rate limits</p>', @bug_type, @open_status, @high_priority, @user1_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 5, DATE_SUB(NOW(), INTERVAL 16 DAY), NOW()),
(@api_proj, 'API-2', 'Implement GraphQL endpoint', '<p>Add GraphQL support for flexible queries</p><ul><li>Schema design</li><li>Resolver implementation</li><li>Testing</li></ul>', @feature_type, @open_status, @medium_priority, @user2_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 5 DAY), CURDATE(), 13, DATE_SUB(NOW(), INTERVAL 6 DAY), NOW()),
(@api_proj, 'API-3', 'Add webhook support', '<p>Allow clients to subscribe to events</p>', @feature_type, @done_status, @medium_priority, @user3_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 6 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), 8, DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
(@api_proj, 'API-4', 'Improve API response time', '<p>Average response should be < 100ms</p>', @improvement_type, @open_status, @medium_priority, @user1_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 21 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 5, DATE_SUB(NOW(), INTERVAL 2 DAY), NOW());

-- ==========================================
-- INSERT ISSUES FOR DEVOPS PROJECT
-- ==========================================
INSERT INTO issues (project_id, issue_key, summary, description, issue_type_id, status_id, priority_id, assignee_id, reporter_id, due_date, start_date, story_points, created_at, updated_at) VALUES
(@devops_proj, 'DEVOPS-1', 'Upgrade Kubernetes cluster', '<p>Update to latest stable version</p>', @task_type, @in_progress_status, @high_priority, @user2_id, @admin_user_id, CURDATE(), DATE_SUB(CURDATE(), INTERVAL 7 DAY), 8, DATE_SUB(NOW(), INTERVAL 12 DAY), NOW()),
(@devops_proj, 'DEVOPS-2', 'Set up monitoring and alerting', '<p>Implement Prometheus + Grafana stack</p><ul><li>Metrics collection</li><li>Dashboard creation</li><li>Alert rules</li></ul>', @task_type, @open_status, @high_priority, @user3_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 4 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 8, DATE_SUB(NOW(), INTERVAL 8 DAY), NOW()),
(@devops_proj, 'DEVOPS-3', 'Improve CI/CD pipeline performance', '<p>Reduce build time from 30min to 10min</p>', @improvement_type, @open_status, @medium_priority, @user1_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 5, DATE_SUB(NOW(), INTERVAL 4 DAY), NOW());

-- ==========================================
-- INSERT ISSUES FOR QA PROJECT
-- ==========================================
INSERT INTO issues (project_id, issue_key, summary, description, issue_type_id, status_id, priority_id, assignee_id, reporter_id, due_date, start_date, story_points, created_at, updated_at) VALUES
(@qa_proj, 'QA-1', 'Create automated test suite for payment module', '<p>100% code coverage required</p>', @task_type, @in_progress_status, @high_priority, @user3_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 7 DAY), 8, DATE_SUB(NOW(), INTERVAL 14 DAY), NOW()),
(@qa_proj, 'QA-2', 'Test new authentication flow on all devices', '<p>Test on:</p><ul><li>Chrome</li><li>Firefox</li><li>Safari</li><li>Edge</li><li>Mobile browsers</li></ul>', @task_type, @open_status, @high_priority, @user1_id, @admin_user_id, DATE_ADD(CURDATE(), INTERVAL 2 DAY), CURDATE(), 5, DATE_SUB(NOW(), INTERVAL 6 DAY), NOW()),
(@qa_proj, 'QA-3', 'Update test documentation', '<p>Document all test cases and procedures</p>', @task_type, @done_status, @low_priority, @user2_id, @admin_user_id, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 12 DAY), 3, DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY));

-- ==========================================
-- INSERT COMMENTS
-- ==========================================
INSERT INTO comments (issue_id, user_id, comment, created_at, updated_at) VALUES
((SELECT id FROM issues WHERE issue_key = 'ECOM-1'), @user2_id, 'This needs to be reviewed before deployment.', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-1'), @user3_id, 'I can take this on immediately.', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-2'), @user1_id, 'Great progress on this! Keep up the good work.', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-3'), @admin_user_id, 'I think we should discuss this in the next standup.', DATE_SUB(NOW(), INTERVAL 4 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-5'), @user3_id, 'This is blocking several other tasks.', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'MOB-1'), @user1_id, 'Can we prioritize this? It is causing issues for users.', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'API-1'), @user2_id, 'Waiting for design review before implementation.', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()),
((SELECT id FROM issues WHERE issue_key = 'DEVOPS-1'), @user3_id, 'All tests passing, ready for production.', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW());

-- ==========================================
-- INSERT WORK LOGS
-- ==========================================
INSERT INTO worklogs (issue_id, user_id, hours_logged, description, logged_date, created_at, updated_at) VALUES
((SELECT id FROM issues WHERE issue_key = 'ECOM-2'), @user2_id, 4, 'Implemented Google OAuth integration', DATE_SUB(CURDATE(), INTERVAL 2 DAY), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-2'), @user2_id, 3, 'Added two-factor authentication', DATE_SUB(CURDATE(), INTERVAL 1 DAY), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-5'), @user2_id, 6, 'Created migration scripts', DATE_SUB(CURDATE(), INTERVAL 3 DAY), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'MOB-2'), @user3_id, 5, 'Implemented local caching layer', DATE_SUB(CURDATE(), INTERVAL 2 DAY), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'MOB-2'), @user3_id, 4, 'Added sync mechanism', DATE_SUB(CURDATE(), INTERVAL 1 DAY), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'API-2'), @user2_id, 3, 'Designed GraphQL schema', CURDATE(), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'DEVOPS-1'), @user2_id, 5, 'Prepared cluster upgrade plan', DATE_SUB(CURDATE(), INTERVAL 2 DAY), NOW(), NOW()),
((SELECT id FROM issues WHERE issue_key = 'QA-1'), @user3_id, 4, 'Created test suite for payments', DATE_SUB(CURDATE(), INTERVAL 1 DAY), NOW(), NOW());

-- ==========================================
-- INSERT ISSUE LINKS (Related Issues)
-- ==========================================
INSERT INTO issue_links (from_issue_id, to_issue_id, link_type, created_at) VALUES
((SELECT id FROM issues WHERE issue_key = 'ECOM-1'), (SELECT id FROM issues WHERE issue_key = 'ECOM-2'), 'relates_to', NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-2'), (SELECT id FROM issues WHERE issue_key = 'MOB-2'), 'relates_to', NOW()),
((SELECT id FROM issues WHERE issue_key = 'ECOM-5'), (SELECT id FROM issues WHERE issue_key = 'API-1'), 'blocks', NOW()),
((SELECT id FROM issues WHERE issue_key = 'MOB-1'), (SELECT id FROM issues WHERE issue_key = 'ECOM-1'), 'relates_to', NOW()),
((SELECT id FROM issues WHERE issue_key = 'API-1'), (SELECT id FROM issues WHERE issue_key = 'DEVOPS-1'), 'relates_to', NOW());

-- ==========================================
-- DISPLAY SUMMARY
-- ==========================================
SELECT 
    'SEEDING COMPLETE' AS status,
    (SELECT COUNT(*) FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')) AS projects_created,
    (SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA'))) AS total_issues,
    (SELECT COUNT(*) FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')) AND due_date < CURDATE() AND status_id != (SELECT id FROM statuses WHERE name = 'Done')) AS overdue_issues,
    (SELECT COUNT(*) FROM comments WHERE issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')))) AS comments_added,
    (SELECT COUNT(*) FROM worklogs WHERE issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')))) AS work_logs_added;
