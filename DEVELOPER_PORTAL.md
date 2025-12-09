# 🚀 Jira Clone System - Developer Portal

**Last Updated**: December 2025 | **Version**: 1.0.0

---

## 📌 Quick Navigation

### 🆘 I Need Help With...
- [**Getting Started**](#-getting-started) - New to the system?
- [**Running the Application**](#-running-the-application) - How to start developing
- [**System Features**](#-system-features) - What features exist and where
- [**Code Standards**](#-code-standards) - How to write code for this project
- [**Admin & Permissions**](#-admin--permissions) - User management & admin features
- [**Bug Fixes & Issues**](#-bug-fixes--issues) - Known issues and solutions
- [**Testing**](#-testing) - How to test your changes
- [**Deployment**](#-deployment) - Production setup

---

## 🚀 Getting Started

### Installation & Setup
| Task | Documentation |
|------|---|
| **First Time Setup** | [README.md](README.md) - Complete installation guide |
| **Database Configuration** | [SETUP_AND_RUN_INSTRUCTIONS.md](SETUP_AND_RUN_INSTRUCTIONS.md) |
| **Quick Start (5 min)** | [QUICK_START.md](QUICK_START.md) |
| **Architecture Overview** | [AGENTS.md](AGENTS.md) - Tech stack & structure |

### Default Credentials
```
Admin Account:
- Email: admin@example.com
- Password: Admin@123

Test User:
- Email: john.smith@example.com
- Password: User@123
```

---

## 🔧 Running the Application

### Start Development Server
```bash
# Access via browser (XAMPP)
http://localhost/jira_clone_system/public/

# Database seeding (if needed)
php scripts/verify-and-seed.php

# Run tests
php tests/TestRunner.php
```

### Common Commands
| Command | Purpose |
|---------|---------|
| `php scripts/verify-and-seed.php` | Seed database with test data |
| `php tests/TestRunner.php` | Run all tests |
| `php tests/TestRunner.php --suite=Unit` | Run unit tests only |

---

## 📋 System Features

### Core Features & Pages

#### **Projects**
- Create, edit, delete projects
- Project settings and configurations
- Project members management
- **Files**: `views/projects/`, `src/Controllers/ProjectController.php`

#### **Issues**
- Create issues with full lifecycle
- Issue types: Epic, Story, Task, Bug, Sub-task
- Comments, attachments, watchers
- Issue search and filtering
- **Files**: `views/issues/`, `src/Controllers/IssueController.php`
- **Doc**: [COMMENT_FEATURE_SUMMARY.md](COMMENT_FEATURE_SUMMARY.md) - Comment edit/delete

#### **Boards**
- Scrum board with drag-and-drop
- Kanban board
- Issue grouping and filtering
- **Files**: `views/boards/`, `src/Controllers/BoardController.php`

#### **Sprints**
- Sprint planning and management
- Backlog management
- Velocity tracking
- **Files**: `views/sprints/`, `src/Controllers/SprintController.php`

#### **Reports** (Enterprise-Grade)
Seven professional reports with visualization:
1. **Created vs Resolved** - Line chart of creation/resolution rates
2. **Resolution Time** - Average time to resolve issues
3. **Priority Breakdown** - Pie chart of issues by priority
4. **Time Logged** - Team workload by user
5. **Estimate Accuracy** - Estimated vs actual time
6. **Version Progress** - Release tracking
7. **Release Burndown** - Burndown chart for releases

**Documentation**: [REPORT_UI_STANDARDS.md](REPORT_UI_STANDARDS.md), [REPORTS_QUICK_START.md](REPORTS_QUICK_START.md)

#### **Admin Dashboard**
Complete system administration hub:
- **Users Management** - Create/edit/delete users, assign roles
- **Roles Management** - System and custom roles
- **Projects Management** - View all projects, search, filter
- **Project Categories** - Organize projects
- **Issue Types** - Create custom issue types with icons
- **Global Permissions** - System-wide permission settings
- **Workflows** - Manage issue statuses and transitions

**Documentation**: [ADMIN_PAGES_IMPLEMENTATION.md](ADMIN_PAGES_IMPLEMENTATION.md)

#### **UI/Design System**
Modern Atlassian Jira-inspired design:
- Professional color palette (#0052CC primary)
- Responsive Bootstrap 5 components
- Enterprise-grade styling
- Accessibility compliant (WCAG AA)

**Documentation**: [UI_REDESIGN_COMPLETE.md](UI_REDESIGN_COMPLETE.md), [UI_COMPONENT_GUIDE.md](UI_COMPONENT_GUIDE.md)

---

## 💻 Code Standards

### File Structure & Conventions
| Aspect | Standard |
|--------|----------|
| **PHP Version** | 8.2+ with strict types |
| **Namespaces** | `App\{folder}\ClassName` |
| **Type Hints** | All parameters and return types required |
| **Naming** | Classes: PascalCase, methods: camelCase |
| **Database** | PDO prepared statements, snake_case columns |
| **Views** | PHP short tags `<?= $var ?>` in `views/` folder |
| **Controllers** | Extend `App\Core\Controller`, use `$this->view()` |

**See**: [AGENTS.md](AGENTS.md) - Complete code style guide

### Key Classes & APIs
```php
// Database queries
App\Core\Database::select($sql, $params);
App\Core\Database::insert($table, $data);
App\Core\Database::update($table, $data, $where, $params);
App\Core\Database::delete($table, $where, $params);

// Request/validation
$request->validate(['field' => 'required|email|max:255']);

// Sessions & Auth
$request->user(); // Get authenticated user
Session::get('user_id');

// Views
$this->view('template', $data); // Render view
$this->json($data); // Return JSON
```

---

## 👥 Admin & Permissions

### Administrator Authority Model
**Critical**: Admins have RESTRICTED rights for protection

| Action | Regular Users | Admin Users | Custom Roles | System Roles |
|--------|:---:|:---:|:---:|:---:|
| View | ✅ | ✅ | ✅ | ✅ |
| Edit | ✅ | ❌ | ✅ | ❌ |
| Delete | ✅ | ❌ | ✅ | ❌ |

**Key Rules**:
- Admins CANNOT edit/delete other admin users (system protection)
- System roles (Administrator, Developer, etc.) are PROTECTED - cannot be edited/deleted
- Custom roles CAN be edited/deleted by admins

**Implementation Files**:
- `src/Controllers/AdminController.php` - Admin logic
- `views/admin/user-form.php` - User edit (disables admin fields)
- `views/admin/roles/form.php` - Role edit (disables system role fields)

**Documentation**: [ADMIN_AUTHORITY_VERIFICATION.md](ADMIN_AUTHORITY_VERIFICATION.md)

### Test Admin Account
```
Email: admin@example.com
Password: Admin@123
Routes: /admin/users, /admin/roles, /admin/projects
```

---

## 🐛 Bug Fixes & Issues

### Known Issues & Solutions
| Issue | Status | Documentation |
|-------|--------|---|
| Comment Edit/Delete | ✅ Fixed | [COMMENT_FEATURE_SUMMARY.md](COMMENT_FEATURE_SUMMARY.md) |
| Admin User Protection | ✅ Fixed | [ADMIN_PROTECTION_FINAL_SUMMARY.md](ADMIN_PROTECTION_FINAL_SUMMARY.md) |
| System Roles Protection | ✅ Fixed | [SYSTEM_ROLES_PROTECTION_FIX.md](SYSTEM_ROLES_PROTECTION_FIX.md) |
| Dropdown Scrolling | ✅ Fixed | [DROPDOWN_SCROLLING_RESOLVED.md](DROPDOWN_SCROLLING_RESOLVED.md) |
| Create Modal Responsive | ✅ Fixed | [CREATE_MODAL_FIX_COMPLETE.md](CREATE_MODAL_FIX_COMPLETE.md) |
| Cascade Delete | ✅ Fixed | [FOREIGN_KEY_CONSTRAINT_FIX.md](FOREIGN_KEY_CONSTRAINT_FIX.md) |
| Velocity Chart | ✅ Fixed | [VELOCITY_CHART_RESOLUTION_COMPLETE.md](VELOCITY_CHART_RESOLUTION_COMPLETE.md) |
| Reports UI | ✅ Fixed | [REPORTS_REDESIGN_SUMMARY.md](REPORTS_REDESIGN_SUMMARY.md) |

---

## 🧪 Testing

### Running Tests
```bash
# All tests
php tests/TestRunner.php

# Single suite
php tests/TestRunner.php --suite=Unit
php tests/TestRunner.php --suite=Integration
```

### Testing Guides
| Aspect | Documentation |
|--------|---|
| **Complete Test Workflow** | [COMPLETE_TEST_WORKFLOW.md](COMPLETE_TEST_WORKFLOW.md) |
| **Testing Checklist** | [TESTING_CHECKLIST.md](TESTING_CHECKLIST.md) |
| **Comment Feature Tests** | [TEST_COMMENT_EDIT_DELETE.md](TEST_COMMENT_EDIT_DELETE.md) |
| **Modal Responsive Tests** | [test_modal_responsive.html](test_modal_responsive.html) |

---

## 🚀 Deployment

### Production Checklist
| Step | Action | Documentation |
|------|--------|---|
| 1 | Set environment to production | [README.md](README.md) |
| 2 | Change all default passwords | Admin section |
| 3 | Configure HTTPS | [README.md](README.md) |
| 4 | Set secure session cookies | config/config.local.php |
| 5 | Configure email settings | [README.md](README.md) |
| 6 | Set up database backups | DevOps team |
| 7 | Configure cron jobs | Email queue processor |

**Full Guide**: [README.md](README.md#-production-deployment) - Production deployment section

---

## 📊 Feature Documentation Map

### Quick Create Modal
- **Location**: Navbar "Create" button
- **Implementation**: `views/layouts/app.php` (lines 190-230)
- **JavaScript**: Project/Issue Type dropdowns with Select2
- **Styles**: `public/assets/css/app.css`
- **Responsive**: Mobile-first, all breakpoints
- **Doc**: [AGENTS.md](AGENTS.md#quick-create-modal)

### Reports System
- **Routes**: `/reports/{report-name}`
- **Controller**: `src/Controllers/ReportController.php` (7 methods)
- **Views**: `views/reports/` (7 report views)
- **Visualization**: Chart.js
- **Filters**: Project + time range selectors
- **Doc**: [REPORTS_QUICK_START.md](REPORTS_QUICK_START.md)

### Admin Pages
- **Dashboard**: `/admin` - Stats overview
- **Users**: `/admin/users` - User management
- **Roles**: `/admin/roles` - Role management
- **Projects**: `/admin/projects` - Project listing
- **Categories**: `/admin/project-categories` - Project grouping
- **Issue Types**: `/admin/issue-types` - Custom issue types
- **Permissions**: `/admin/global-permissions` - System permissions
- **Doc**: [ADMIN_PAGES_IMPLEMENTATION.md](ADMIN_PAGES_IMPLEMENTATION.md)

---

## 🔐 Security

### Implemented Protections
✅ Argon2id password hashing  
✅ CSRF token protection on all forms  
✅ XSS prevention with output encoding  
✅ SQL injection protection (prepared statements)  
✅ Secure session management  
✅ Content Security Policy headers  
✅ Admin user protection (cannot edit admin users)  
✅ System role protection (immutable)  

**See**: [README.md](README.md#-security) - Security section

---

## 📚 Complete Documentation Index

### Core Documentation
- [AGENTS.md](AGENTS.md) - Developer guide (CODE OF TRUTH)
- [README.md](README.md) - Project overview & installation
- [QUICK_START.md](QUICK_START.md) - 5-minute setup
- [SETUP_AND_RUN_INSTRUCTIONS.md](SETUP_AND_RUN_INSTRUCTIONS.md) - Detailed setup

### Feature Documentation
- **Admin**: [ADMIN_PAGES_IMPLEMENTATION.md](ADMIN_PAGES_IMPLEMENTATION.md)
- **Comments**: [COMMENT_FEATURE_SUMMARY.md](COMMENT_FEATURE_SUMMARY.md)
- **Reports**: [REPORTS_QUICK_START.md](REPORTS_QUICK_START.md), [REPORT_UI_STANDARDS.md](REPORT_UI_STANDARDS.md)
- **UI Design**: [UI_REDESIGN_COMPLETE.md](UI_REDESIGN_COMPLETE.md), [UI_COMPONENT_GUIDE.md](UI_COMPONENT_GUIDE.md)
- **Quick Create Modal**: [AGENTS.md](AGENTS.md#quick-create-modal)

### Security & Permissions
- [ADMIN_AUTHORITY_VERIFICATION.md](ADMIN_AUTHORITY_VERIFICATION.md) - Permission matrix
- [ADMIN_PROTECTION_FINAL_SUMMARY.md](ADMIN_PROTECTION_FINAL_SUMMARY.md) - Admin protection
- [SYSTEM_ROLES_PROTECTION_FIX.md](SYSTEM_ROLES_PROTECTION_FIX.md) - Role protection

### Bug Fixes & Troubleshooting
- [DROPDOWN_SCROLLING_RESOLVED.md](DROPDOWN_SCROLLING_RESOLVED.md)
- [CREATE_MODAL_FIX_COMPLETE.md](CREATE_MODAL_FIX_COMPLETE.md)
- [VELOCITY_CHART_RESOLUTION_COMPLETE.md](VELOCITY_CHART_RESOLUTION_COMPLETE.md)
- [FOREIGN_KEY_CONSTRAINT_FIX.md](FOREIGN_KEY_CONSTRAINT_FIX.md)
- [REPORTS_REDESIGN_SUMMARY.md](REPORTS_REDESIGN_SUMMARY.md)

### Testing & QA
- [COMPLETE_TEST_WORKFLOW.md](COMPLETE_TEST_WORKFLOW.md)
- [TESTING_CHECKLIST.md](TESTING_CHECKLIST.md)
- [TEST_COMMENT_EDIT_DELETE.md](TEST_COMMENT_EDIT_DELETE.md)

---

## 🎯 Quick Reference

### Database Structure
```
Database: jira_clone

Key Tables:
- users (authentication & profiles)
- roles (user roles & permissions)
- projects (project management)
- issues (issue tracking)
- issue_comments (threaded comments)
- boards (sprint/kanban boards)
- sprints (sprint management)
- workflows (status & transitions)
- permissions (role permissions)
```

### Routes Structure
```
Web Routes: routes/web.php
- / (dashboard)
- /projects (project listing)
- /projects/{key} (project detail)
- /issues/{key} (issue detail)
- /boards/{id} (board view)
- /admin/* (admin pages)
- /reports/* (reports)

API Routes: routes/api.php
- /api/v1/auth/login (authentication)
- /api/v1/projects/* (project endpoints)
- /api/v1/issues/* (issue endpoints)
- /api/v1/boards/* (board endpoints)
```

### Directory Quick Links
| Folder | Purpose |
|--------|---------|
| `src/Controllers/` | HTTP request handlers |
| `src/Core/` | Framework core classes |
| `src/Services/` | Business logic |
| `src/Repositories/` | Data access |
| `views/` | PHP templates |
| `public/assets/` | CSS, JS, images |
| `database/` | Schema & seeds |
| `routes/` | Route definitions |
| `config/` | Configuration files |
| `storage/` | Logs & cache |

---

## 💡 Pro Tips

1. **Use AGENTS.md as Ground Truth** - All conventions and standards are documented there
2. **Check UI_REDESIGN_COMPLETE.md** - Before adding UI components
3. **Review ADMIN_PAGES_IMPLEMENTATION.md** - Before modifying admin section
4. **Use Prepared Statements** - Never trust user input
5. **Add Type Hints** - All PHP 8.2+ requires them
6. **Test Before Commit** - Run tests with `php tests/TestRunner.php`

---

## 🆘 Still Need Help?

### Find Answers
1. Check [AGENTS.md](AGENTS.md) for code standards
2. Search documentation in this file for your topic
3. Check specific feature docs (see links above)
4. Review test files for implementation examples

### Common Questions
| Question | Answer |
|----------|--------|
| How do I add a new page? | Create controller in `src/Controllers/`, add route in `routes/web.php`, create view in `views/` |
| How do I query database? | Use `App\Core\Database::select($sql, $params)` with prepared statements |
| How do I add admin permissions? | Edit `src/Controllers/AdminController.php` and add route in `routes/web.php` |
| How do I create a report? | Add controller method in `ReportController.php`, add route, create view in `views/reports/` |
| How do I secure a page? | Extend `App\Core\Controller`, implement `authorize()` method, add middleware check |

---

## 📝 Notes for Developers

### Before You Code
- ✅ Read [AGENTS.md](AGENTS.md) - Your development bible
- ✅ Check this portal for existing features
- ✅ Look for similar code in the system
- ✅ Follow established patterns

### While Coding
- ✅ Use type hints on all methods
- ✅ Add strict types declaration
- ✅ Use prepared statements for DB queries
- ✅ Escape all output in views
- ✅ Add CSRF tokens to forms

### Before Committing
- ✅ Run tests: `php tests/TestRunner.php`
- ✅ Test manually in browser
- ✅ Check for security issues
- ✅ Validate database changes

---

**Last Updated**: December 2025  
**Maintained By**: Development Team  
**Version**: 1.0.0  
**Next Review**: Q1 2026

---

> **This Developer Portal is your single source of truth.** All guides, standards, and documentation are linked from here. Bookmark this page!
