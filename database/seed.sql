-- =====================================================
-- JIRA CLONE - SEED DATA
-- Production sample data for testing
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- ISSUE TYPES
-- =====================================================

INSERT INTO `issue_types` (`name`, `description`, `icon`, `color`, `is_subtask`, `is_default`, `sort_order`) VALUES
('Epic', 'Epic-level initiative', 'epic', '#4A3F93', 0, 0, 1),
('Story', 'User story or feature', 'story', '#0052CC', 0, 1, 2),
('Task', 'General task or activity', 'task', '#0052CC', 0, 0, 3),
('Bug', 'Bug or defect', 'bug', '#AE2A19', 0, 0, 4),
('Subtask', 'Subtask of an issue', 'subtask', '#626F86', 1, 0, 5);

-- =====================================================
-- PRIORITIES
-- =====================================================

INSERT INTO `issue_priorities` (`name`, `description`, `icon`, `color`, `sort_order`, `is_default`) VALUES
('Highest', 'Must be done immediately', 'highest', '#AE2A19', 1, 0),
('High', 'Should be done soon', 'high', '#F15638', 2, 0),
('Medium', 'Normal priority', 'medium', '#FFAB00', 3, 1),
('Low', 'Can be deferred', 'low', '#936B00', 4, 0),
('Lowest', 'Nice to have', 'lowest', '#626F86', 5, 0);

-- =====================================================
-- STATUSES
-- =====================================================

INSERT INTO `statuses` (`name`, `description`, `category`, `color`, `sort_order`) VALUES
('Open', 'New issue waiting to start', 'todo', '#1F77E8', 1),
('To Do', 'Queued for development', 'todo', '#9C27B0', 2),
('In Progress', 'Currently being worked on', 'in_progress', '#FF9800', 3),
('In Review', 'Waiting for code review', 'in_progress', '#4CAF50', 4),
('Testing', 'In QA testing phase', 'in_progress', '#00BCD4', 5),
('Done', 'Completed and released', 'done', '#2E7D32', 6),
('Closed', 'Issue closed/resolved', 'done', '#616161', 7);

-- =====================================================
-- ROLES
-- =====================================================

INSERT INTO `roles` (`name`, `slug`, `description`, `is_system`) VALUES
('Administrator', 'administrator', 'Full system access', 1),
('Developer', 'developer', 'Develop and test features', 1),
('Project Manager', 'project_manager', 'Manage projects and teams', 1),
('QA Tester', 'qa_tester', 'Test and report issues', 1),
('Viewer', 'viewer', 'View-only access', 1),
('Product Owner', 'product_owner', 'Define requirements', 0);

-- =====================================================
-- PERMISSIONS
-- =====================================================

INSERT INTO `permissions` (`name`, `slug`, `description`, `category`) VALUES
('View Issues', 'view_issues', 'View project issues', 'issues'),
('Create Issue', 'create_issue', 'Create new issues', 'issues'),
('Edit Issue', 'edit_issue', 'Edit existing issues', 'issues'),
('Delete Issue', 'delete_issue', 'Delete issues', 'issues'),
('View Projects', 'view_projects', 'View projects', 'projects'),
('Create Project', 'create_project', 'Create new projects', 'projects'),
('Edit Project', 'edit_project', 'Edit project settings', 'projects'),
('Manage Board', 'manage_board', 'Manage project boards', 'projects'),
('Manage Sprints', 'manage_sprints', 'Create and manage sprints', 'sprints'),
('View Reports', 'view_reports', 'View project reports', 'reports'),
('Manage Users', 'manage_users', 'Manage user accounts', 'admin'),
('Manage Roles', 'manage_roles', 'Manage roles and permissions', 'admin');

-- =====================================================
-- ROLE PERMISSIONS
-- =====================================================

-- Administrator: All permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12);

-- Developer: Issue management + project viewing
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1), (2, 2), (2, 3), (2, 5), (2, 7), (2, 8), (2, 10);

-- Project Manager: Full project management + issues
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 7), (3, 8), (3, 9), (3, 10), (3, 11), (3, 12);

-- QA Tester: Issue management + testing focused
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4, 1), (4, 2), (4, 3), (4, 5), (4, 7), (4, 8), (4, 10);

-- Viewer: Read-only access
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(5, 1), (5, 5), (5, 10);

-- Product Owner: Similar to Project Manager but focused on requirements
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(6, 1), (6, 2), (6, 3), (6, 5), (6, 7), (6, 8), (6, 9), (6, 10);

-- =====================================================
-- PROJECT CATEGORIES
-- =====================================================

INSERT INTO `project_categories` (`name`, `description`) VALUES
('Backend Systems', 'Core backend and API development'),
('Frontend', 'Web and mobile front-end projects'),
('Infrastructure', 'DevOps and infrastructure projects'),
('Data & Analytics', 'Data engineering and analytics'),
('Mobile Apps', 'Native mobile applications');

-- =====================================================
-- WORKFLOWS
-- =====================================================

INSERT INTO `workflows` (`name`, `description`, `is_active`, `is_default`) VALUES
('Standard Workflow', 'Default workflow for most projects', 1, 1),
('Agile Workflow', 'Workflow optimized for agile teams', 1, 0),
('Kanban Workflow', 'Simplified workflow for Kanban boards', 1, 0);

-- =====================================================
-- WORKFLOW STATUSES
-- =====================================================

INSERT INTO `workflow_statuses` (`workflow_id`, `status_id`, `is_initial`, `x_position`, `y_position`) VALUES
(1, 1, 1, 0, 0),     -- Open (initial)
(1, 2, 0, 100, 0),   -- To Do
(1, 3, 0, 200, 0),   -- In Progress
(1, 4, 0, 300, 0),   -- In Review
(1, 5, 0, 400, 0),   -- Testing
(1, 6, 0, 500, 0),   -- Done
(1, 7, 0, 600, 0);   -- Closed

-- =====================================================
-- PROJECTS
-- =====================================================

INSERT INTO `projects` (`key`, `name`, `description`, `lead_id`, `category_id`, `default_assignee`, `is_archived`, `created_by`) VALUES
('ECOM', 'E-Commerce Platform', 'Main e-commerce platform and APIs', 2, 1, 'project_lead', 0, 1),
('MOBILE', 'Mobile Apps', 'iOS and Android mobile applications', 3, 5, 'project_lead', 0, 1),
('INFRA', 'Infrastructure', 'DevOps, deployment, and cloud infrastructure', 4, 3, 'unassigned', 0, 1);

-- =====================================================
-- PROJECT MEMBERS
-- =====================================================

INSERT INTO `project_members` (`project_id`, `user_id`, `role_id`) VALUES
(1, 2, 2),  -- John (Developer)
(1, 3, 2),  -- Jane (Developer)
(1, 4, 3),  -- Mike (Project Manager)
(1, 5, 4),  -- Sarah (QA Tester)
(2, 3, 2),  -- Jane (Developer)
(2, 5, 4),  -- Sarah (QA Tester)
(2, 6, 2),  -- David (Developer)
(3, 4, 3),  -- Mike (Project Manager)
(3, 6, 2);  -- David (Developer)

-- =====================================================
-- BOARDS & SPRINTS
-- =====================================================

INSERT INTO `boards` (`project_id`, `name`, `type`, `owner_id`) VALUES
(1, 'ECOM Development Board', 'scrum', 2),
(1, 'ECOM Kanban Board', 'kanban', 3),
(2, 'Mobile Apps Board', 'scrum', 3),
(3, 'Infrastructure Board', 'kanban', 4);

-- =====================================================
-- BOARD COLUMNS
-- =====================================================

INSERT INTO `board_columns` (`board_id`, `name`, `status_ids`, `sort_order`) VALUES
(1, 'To Do', '[1, 2]', 1),
(1, 'In Progress', '[3, 4]', 2),
(1, 'Testing', '[5]', 3),
(1, 'Done', '[6, 7]', 4),
(2, 'Backlog', '[1]', 1),
(2, 'Active', '[2, 3, 4]', 2),
(2, 'Complete', '[6]', 3),
(3, 'To Do', '[1, 2]', 1),
(3, 'In Progress', '[3, 4, 5]', 2),
(3, 'Done', '[6]', 3),
(4, 'Backlog', '[1]', 1),
(4, 'Ready', '[2]', 2),
(4, 'In Progress', '[3, 4]', 3),
(4, 'Complete', '[6]', 4);

-- =====================================================
-- SPRINTS
-- =====================================================

INSERT INTO `sprints` (`board_id`, `name`, `goal`, `start_date`, `end_date`, `status`) VALUES
(1, 'Sprint 1', 'Implement core e-commerce features', '2025-12-09', '2025-12-20', 'active'),
(1, 'Sprint 2', 'Payment integration and testing', '2025-12-21', '2026-01-03', 'future'),
(1, 'Sprint 3', 'Performance optimization', '2026-01-04', '2026-01-17', 'future'),
(3, 'Sprint 1', 'Mobile app core features', '2025-12-09', '2025-12-20', 'active'),
(3, 'Sprint 2', 'Testing and refinement', '2025-12-21', '2026-01-03', 'future');

-- =====================================================
-- VERSIONS/RELEASES
-- =====================================================

INSERT INTO `versions` (`project_id`, `name`, `description`, `start_date`, `release_date`, `is_archived`, `sort_order`) VALUES
(1, '1.0', 'Initial release', '2025-11-01', '2025-12-15', 0, 1),
(1, '1.1', 'Bug fixes and improvements', '2025-12-16', NULL, 0, 2),
(1, '2.0', 'Major feature release', '2026-01-01', NULL, 0, 3),
(2, '1.0', 'iOS and Android launch', '2025-12-01', NULL, 0, 1),
(3, '1.0', 'Initial infrastructure setup', '2025-11-01', '2025-12-01', 1, 1);

-- =====================================================
-- SAMPLE ISSUES
-- =====================================================

INSERT INTO `issues` (`project_id`, `issue_type_id`, `status_id`, `priority_id`, `issue_key`, `issue_number`, `summary`, `description`, `reporter_id`, `assignee_id`, `sprint_id`, `story_points`, `original_estimate`, `created_at`) VALUES

-- ECOM Project Issues
(1, 2, 2, 3, 'ECOM-1', 1, 'Implement user authentication', 'Set up login/signup functionality with OAuth support', 1, 2, 1, 8, 28800, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 2, 3, 2, 'ECOM-2', 2, 'Create product catalog system', 'Build backend and frontend for product browsing', 1, 3, 1, 13, 46800, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(1, 3, 3, 3, 'ECOM-3', 3, 'Setup database schema', 'Create all required database tables and relationships', 1, 2, 1, 5, 18000, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 4, 1, 1, 'ECOM-4', 4, 'Login page styling issues on mobile', 'Fix responsive design for mobile devices', 2, 3, 1, 3, 10800, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 2, 4, 2, 'ECOM-5', 5, 'Payment gateway integration', 'Integrate Stripe or PayPal for payments', 1, NULL, 2, 13, 46800, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 3, 2, 3, 'ECOM-6', 6, 'Add product search functionality', 'Implement full-text search for products', 2, 4, 1, 5, 18000, NOW()),
(1, 3, 5, 2, 'ECOM-7', 7, 'Email notification system', 'Set up email notifications for orders', 1, 5, NULL, 3, 10800, DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- MOBILE Project Issues
(2, 2, 2, 3, 'MOBILE-1', 1, 'iOS app development', 'Build core features for iOS app', 1, 6, 4, 8, 28800, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 3, 3, 3, 'MOBILE-2', 2, 'API client for mobile', 'Create REST client for mobile apps', 1, 6, 4, 5, 18000, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 4, 1, 2, 'MOBILE-3', 3, 'App crashing on Android devices', 'Fix crash on specific Android versions', 3, 5, 4, 3, 10800, DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- INFRA Project Issues
(3, 3, 3, 2, 'INFRA-1', 1, 'Setup CI/CD pipeline', 'Configure GitHub Actions for automated testing', 1, 4, NULL, 8, 28800, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 3, 2, 3, 'INFRA-2', 2, 'Database backup strategy', 'Implement automated daily backups', 2, NULL, NULL, 5, 18000, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 3, 4, 3, 'INFRA-3', 3, 'Monitor server performance', 'Set up monitoring dashboards and alerts', 1, 6, NULL, 3, 10800, NOW());

-- =====================================================
-- SAMPLE COMMENTS
-- =====================================================

INSERT INTO `comments` (`issue_id`, `user_id`, `body`, `created_at`) VALUES
(1, 3, 'I''ve started working on the authentication module. Initial draft is ready for review.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 4, 'Please make sure to add comprehensive unit tests for the OAuth flow.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 2, 'Tests added and all passing. Ready for code review. @jane.doe', NOW()),
(2, 3, 'Product catalog is looking good. Need to optimize the database queries.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 2, 'Database schema reviewed and approved. Ready to proceed with implementation.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 5, 'I found the mobile styling issue. It''s related to the viewport meta tag.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(6, 3, 'Implemented full-text search using MySQL MATCH/AGAINST. Performance is good.', NOW()),
(7, 4, 'Email templates need to be reviewed by marketing team.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(9, 6, 'API client is complete and tested with all endpoints.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(11, 4, 'CI/CD pipeline is now live and running all tests automatically.', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- =====================================================
-- SAMPLE LABELS
-- =====================================================

INSERT INTO `labels` (`project_id`, `name`, `color`) VALUES
(1, 'urgent', '#AE2A19'),
(1, 'feature', '#0052CC'),
(1, 'bug', '#E5534B'),
(1, 'documentation', '#5243AA'),
(1, 'technical-debt', '#997799'),
(2, 'feature', '#0052CC'),
(2, 'bug', '#E5534B'),
(3, 'infrastructure', '#5243AA'),
(3, 'security', '#AE2A19');

-- =====================================================
-- SAMPLE ISSUE LABELS
-- =====================================================

INSERT INTO `issue_labels` (`issue_id`, `label_id`) VALUES
(1, 2),   -- ECOM-1: feature
(1, 4),   -- ECOM-1: documentation
(2, 2),   -- ECOM-2: feature
(3, 4),   -- ECOM-3: documentation
(4, 3),   -- ECOM-4: bug
(5, 2),   -- ECOM-5: feature
(6, 2),   -- ECOM-6: feature
(7, 4),   -- ECOM-7: documentation
(8, 2),   -- MOBILE-1: feature
(9, 4),   -- MOBILE-2: documentation
(10, 3),  -- MOBILE-3: bug
(11, 1),  -- INFRA-1: urgent
(12, 5),  -- INFRA-2: technical-debt
(13, 1);  -- INFRA-3: urgent

-- =====================================================
-- UPDATE PROJECT ISSUE COUNTS
-- =====================================================

UPDATE `projects` SET `issue_count` = (SELECT COUNT(*) FROM `issues` WHERE `project_id` = `projects`.`id`);

SET FOREIGN_KEY_CHECKS = 1;
