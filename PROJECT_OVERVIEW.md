# Jira Clone - Enterprise Project Overview

## What This Is
You are building an **enterprise-level Jira clone** - a complete issue tracking and project management system in PHP 8.2+. It's designed for teams to manage projects, track issues, plan sprints, and analyze performance with advanced reporting.

## Tech Stack
- **Backend**: PHP 8.2+ (no frameworks, no Composer)
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5 + Vanilla JavaScript
- **Charts**: Chart.js
- **Authentication**: JWT tokens + Session-based auth
- **API**: RESTful API v1 with JWT and PAT auth

## Architecture

### Directory Structure
```
jira_clone_system/
├── public/                    # Web root - served by Apache
│   ├── index.php             # Front controller
│   ├── .htaccess             # URL rewriting rules
│   ├── assets/               # CSS, JS, images
│   │   └── css/app.css       # Main stylesheet (1100+ lines)
│   └── uploads/              # User-uploaded files
│
├── src/                       # Application source code
│   ├── Controllers/          # HTTP controllers (MVC)
│   │   ├── AuthController.php
│   │   ├── ProjectController.php
│   │   ├── IssueController.php
│   │   ├── ReportController.php    # <-- NEW (7 new methods)
│   │   ├── AdminController.php
│   │   ├── BoardController.php
│   │   ├── SprintController.php
│   │   └── ...
│   ├── Core/                 # Framework core
│   │   ├── Router.php        # URL routing
│   │   ├── Database.php      # PDO wrapper
│   │   ├── Request.php       # Input/validation
│   │   ├── Response.php      # HTTP responses
│   │   ├── Session.php       # Auth sessions
│   │   ├── Controller.php    # Base controller
│   │   └── View.php          # Template engine
│   ├── Services/             # Business logic
│   ├── Repositories/         # Data access layer
│   ├── Middleware/           # HTTP middleware (auth, CSRF, etc.)
│   └── Helpers/              # Utility functions
│
├── routes/                    # Route definitions
│   ├── web.php               # Web routes (141 routes)
│   └── api.php               # API routes
│
├── views/                     # PHP templates
│   ├── layouts/
│   │   └── app.php           # Main layout with navbar
│   ├── auth/                 # Login/logout
│   ├── dashboard/            # Home page
│   ├── projects/             # Project views
│   ├── issues/               # Issue details/list
│   ├── boards/               # Kanban/Scrum boards
│   ├── reports/              # <-- NEW (7 new report views)
│   ├── admin/                # Admin pages
│   └── components/           # Reusable UI components
│
├── database/                  # Database schema
│   ├── schema.sql            # Table definitions
│   └── seed.sql              # Sample data
│
├── bootstrap/                 # App initialization
│   ├── autoload.php          # PSR-4 autoloader
│   └── app.php               # App container
│
├── config/                    # Configuration
│   ├── config.php            # Default config
│   └── config.local.php      # Local overrides (git-ignored)
│
├── storage/                   # Runtime data
│   ├── logs/                 # Log files
│   └── cache/                # Cache files
│
└── tests/                     # Test suite
```

## Core Concepts

### 1. MVC Pattern
- **Models**: Data representation (no separate model files, logic in Repositories)
- **Views**: PHP templates in `views/` directory
- **Controllers**: Handle requests, orchestrate logic, return responses

### 2. Routing
```php
// routes/web.php
$router->get('/projects', [ProjectController::class, 'index'])->name('projects.index');
$router->post('/projects', [ProjectController::class, 'store'])->name('projects.store');
$router->get('/issue/{issueKey}', [IssueController::class, 'show'])->name('issues.show');
```

### 3. Database
- **No ORM**: Uses raw PDO with prepared statements
- **Repositories**: Data access layer (e.g., `ProjectRepository`)
- **Service Layer**: Business logic (e.g., `ProjectService`)

Example:
```php
// Prepared statement
$result = Database::select(
    "SELECT * FROM issues WHERE project_id = ? AND status_id IN (?, ?)",
    [$projectId, $statusId1, $statusId2]
);
```

### 4. Authentication
- Argon2id password hashing
- JWT tokens for API
- Session-based auth for web
- Role-based access control (RBAC)

### 5. Views & Templating
```php
<!-- views/projects/index.php -->
<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>
    <h1><?= e($title) ?></h1>
    <?php foreach ($projects as $project): ?>
        <p><?= e($project['name']) ?></p>
    <?php endforeach; ?>
<?php \App\Core\View::endSection(); ?>
```

## Key Features Implemented

### Project Management ✅
- Create/edit/delete projects
- Project categories
- Project members & permissions
- Project settings & workflows

### Issue Tracking ✅
- Create/edit/delete issues
- Issue types (Epic, Story, Task, Bug, Sub-task)
- Issue priorities & status
- Issue linking & dependencies
- Comments with edit/delete
- Attachments
- Time tracking (worklogs)
- Watchers & voting

### Agile Tools ✅
- Kanban & Scrum boards
- Sprint planning
- Backlog management
- Drag-and-drop issues on board
- Sprint velocity tracking

### Advanced Reporting ✅ NEW (Dec 2025)
- Created vs Resolved trends
- Resolution time analysis
- Priority breakdown
- Team time tracking
- Estimate accuracy
- Version progress
- Release burndown

### Administration ✅
- User management
- Role management
- Global permissions
- Issue type management
- Project category management
- Audit logs
- System settings

### Search & Filtering ✅
- Advanced JQL-like search
- Saved filters
- Quick search
- Project-scoped search

## Recent Changes (December 2025)

### 1. Modern UI Redesign
- Professional Jira-inspired design
- Bootstrap 5 components
- Consistent color palette (#0052CC primary)
- Smooth animations & transitions
- Mobile-responsive design
- Print-friendly styles

### 2. Admin Protection
- Admins can't be edited/deleted by other admins
- System roles can't be modified
- Multi-layer security (client + server)
- Non-bypassable protections

### 3. Reports System (Just Added!)
- 7 new report types
- Chart.js visualizations
- Real-time metrics
- Project filtering
- Time-range selection
- Complete implementation guide

## The Code You're Working With

### Custom Framework (Not a Package)
Unlike Laravel/Symfony, this has a **custom lightweight framework**:
- `App\Core\Router` - Routes requests
- `App\Core\Database` - PDO wrapper
- `App\Core\Request` - Input validation
- `App\Core\Session` - Authentication
- `App\Core\View` - Template rendering

### Code Style Standards
```php
<?php declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;

class ProjectController extends Controller
{
    public function index(Request $request): string
    {
        $projects = Database::select(
            "SELECT id, name FROM projects WHERE is_archived = 0 ORDER BY name"
        );
        
        return $this->view('projects.index', ['projects' => $projects]);
    }
}
```

## Running the System

### Access
```
URL: http://localhost:8080/jira_clone_system/public/
Admin: admin@example.com / Admin@123
User: john.smith@example.com / User@123
```

### Key Routes
```
/                          - Home/redirect
/login                     - Login page
/dashboard                 - Main dashboard
/projects                  - Projects list
/projects/{key}            - Project detail
/projects/{key}/board      - Kanban board
/reports                   - Reports home
/reports/created-vs-resolved - Created vs Resolved report
/admin/                    - Admin dashboard
/admin/users               - User management
/admin/roles               - Role management
/api/v1/...               - REST API endpoints
```

### Database
```
Name: jiira_clonee_system
User: root
Password: (empty by default)
```

## Quality Standards

### Security ✅
- Prepared statements (no SQL injection)
- Password hashing (Argon2id)
- CSRF protection on forms
- XSS prevention (output encoding)
- Secure headers
- Input validation
- RBAC enforcement

### Code Quality ✅
- Strict types everywhere
- Type hints on all methods
- Proper error handling
- Meaningful exceptions
- Consistent naming
- Well-organized structure

### Testing ✅
- Custom test runner in `tests/TestRunner.php`
- Unit & integration tests
- Test database seeding

## Performance

### Database
- Proper indexing on tables
- Query optimization
- Prepared statements
- Result limiting
- Join optimization

### Frontend
- CSS in single file (app.css)
- Chart.js for visualizations
- Vanilla JS (no jQuery)
- Bootstrap 5 (minimal overhead)

### Caching
- File-based cache
- Configurable TTL
- Cache clearing command

## What You're Managing

As the developer of this system, you:
1. **Maintain** the custom PHP framework
2. **Add features** through Controllers → Services → Repositories
3. **Update database** schema as needed
4. **Design views** with Bootstrap 5
5. **Write API** endpoints with JWT auth
6. **Manage reports** and analytics
7. **Administer** users, roles, permissions

## Next Steps for Enhancement

Popular additions:
- [ ] Notification system (email/in-app)
- [ ] Webhook integrations
- [ ] Custom fields per project
- [ ] Advanced workflow automation
- [ ] Bulk operations
- [ ] Export to Excel/PDF
- [ ] Integration with git/GitHub
- [ ] Mobile app (React Native)

---

**Bottom Line**: You have a professional, enterprise-ready issue tracking system built from scratch in PHP, with no external framework dependencies. The codebase is clean, follows enterprise patterns, and is ready for production with proper configuration.
