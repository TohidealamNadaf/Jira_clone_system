-- =====================================================
-- WORKFLOW TRANSITIONS SEED DATA
-- Defines allowed status transitions in workflows
-- =====================================================

-- Standard Workflow (ID: 1) Transitions
-- Status IDs: 1=Open, 2=To Do, 3=In Progress, 4=In Review, 5=Testing, 6=Done, 7=Closed

INSERT INTO `workflow_transitions` (`workflow_id`, `name`, `from_status_id`, `to_status_id`) VALUES
-- From Open (1)
(1, 'Open → To Do', 1, 2),
(1, 'Open → Closed', 1, 7),

-- From To Do (2)
(1, 'To Do → In Progress', 2, 3),
(1, 'To Do → Open', 2, 1),

-- From In Progress (3)
(1, 'In Progress → In Review', 3, 4),
(1, 'In Progress → Testing', 3, 5),
(1, 'In Progress → To Do', 3, 2),

-- From In Review (4)
(1, 'In Review → In Progress', 4, 3),
(1, 'In Review → Testing', 4, 5),
(1, 'In Review → To Do', 4, 2),

-- From Testing (5)
(1, 'Testing → In Progress', 5, 3),
(1, 'Testing → Done', 5, 6),
(1, 'Testing → In Review', 5, 4),

-- From Done (6)
(1, 'Done → Closed', 6, 7),
(1, 'Done → In Progress', 6, 3),

-- From Closed (7)
(1, 'Closed → To Do', 7, 2),

-- Agile Workflow (ID: 2) - similar to Standard
INSERT INTO `workflow_transitions` (`workflow_id`, `name`, `from_status_id`, `to_status_id`) VALUES
(2, 'Open → To Do', 1, 2),
(2, 'To Do → In Progress', 2, 3),
(2, 'In Progress → In Review', 3, 4),
(2, 'In Review → Testing', 4, 5),
(2, 'Testing → Done', 5, 6),
(2, 'Done → Closed', 6, 7),
(2, 'In Progress → To Do', 3, 2),
(2, 'In Review → In Progress', 4, 3),

-- Kanban Workflow (ID: 3) - simplified
INSERT INTO `workflow_transitions` (`workflow_id`, `name`, `from_status_id`, `to_status_id`) VALUES
(3, 'Open → To Do', 1, 2),
(3, 'To Do → In Progress', 2, 3),
(3, 'In Progress → Done', 3, 6),
(3, 'In Progress → Testing', 3, 5),
(3, 'Testing → Done', 5, 6),
(3, 'Done → To Do', 6, 2),
(3, 'To Do → Open', 2, 1);
