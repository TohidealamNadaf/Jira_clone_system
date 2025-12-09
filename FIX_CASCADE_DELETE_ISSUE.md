# CRITICAL FIX: Database Cascade Delete Issue

## Problem
When you create an issue or project and add comments, if any update fails due to a foreign key constraint violation, the entire project gets deleted from the database due to CASCADE DELETE constraints.

## Root Cause
The database schema has CASCADE DELETE configured on foreign keys:
```sql
CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
```

When an issue fails to create/update properly and is deleted, it cascades up and deletes the project.

## Solution
Disable CASCADE DELETE and use proper transaction rollbacks instead.

Run this SQL:

```sql
-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Drop and recreate constraints WITHOUT CASCADE DELETE

-- projects table constraints
ALTER TABLE `issues` DROP FOREIGN KEY `issues_project_id_fk`;
ALTER TABLE `issues` ADD CONSTRAINT `issues_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

ALTER TABLE `project_members` DROP FOREIGN KEY `project_members_project_id_fk`;
ALTER TABLE `project_members` ADD CONSTRAINT `project_members_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Other project-related constraints
ALTER TABLE `boards` DROP FOREIGN KEY `boards_project_id_fk`;
ALTER TABLE `boards` ADD CONSTRAINT `boards_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

ALTER TABLE `sprints` DROP FOREIGN KEY `sprints_project_id_fk`;
ALTER TABLE `sprints` ADD CONSTRAINT `sprints_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT;

-- Comment constraints - should reference issues, not projects directly
ALTER TABLE `comments` DROP FOREIGN KEY `comments_issue_id_fk`;
ALTER TABLE `comments` ADD CONSTRAINT `comments_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
```

## Additional Fixes Needed

1. **IssueService transaction handling** - Ensure transactions rollback properly on errors
2. **Validation before insert** - Check all required fields before inserting
3. **Error handling** - Don't silently fail, report errors properly

## Testing
After applying the SQL fix:
1. Create a new project (Baramati Project)
2. Create an issue in the project
3. Add a comment
4. Try to edit the comment
5. Reload the page - issue and project should STILL exist

## Status
Execute the SQL above in your database (phpMyAdmin or command line) to prevent projects from being deleted on constraint violations.
