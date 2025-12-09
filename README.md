# Jira Clone - Enterprise Issue Tracking System

A full-featured, enterprise-grade Jira-like issue tracking and project management system built with Core PHP 8.2+, MySQL 8, Bootstrap 5, and vanilla JavaScript. **No Composer or external frameworks required.**

## 🚀 Features

### Core Functionality
- **Project Management**: Create and manage multiple projects with customizable settings
- **Issue Tracking**: Full issue lifecycle with types (Epic, Story, Task, Bug, Sub-task)
- **Agile Boards**: Scrum and Kanban boards with drag-and-drop
- **Sprints**: Sprint planning, backlog management, velocity tracking
- **Workflows**: Configurable statuses, transitions, and automation

### Collaboration
- **Comments**: Threaded comments with @mentions and edit history
- **Attachments**: File uploads with secure URLs
- **Watchers & Voting**: Subscribe to issues, vote for priorities
- **Notifications**: In-app and email notifications

### Search & Reporting
- **Advanced Search**: JQL-like query language for powerful filtering
- **Saved Filters**: Save and share frequently used searches
- **Dashboards**: Customizable dashboards with gadgets
- **Reports**: Burndown, velocity, cumulative flow, workload charts

### Administration
- **RBAC**: Role-based access control with granular permissions
- **User Management**: User accounts, groups, and roles
- **Audit Logs**: Immutable audit trail for compliance
- **Custom Fields**: Extensible issue fields

### API
- **REST API**: Versioned API (v1) with JWT and PAT authentication
- **Webhooks Ready**: Event-driven architecture for integrations

## 📋 Requirements

- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher
- **Apache**: 2.4+ with mod_rewrite enabled
- **XAMPP**: Recommended for local development

### PHP Extensions Required
- pdo_mysql
- mbstring
- json
- openssl
- fileinfo

## 🛠️ Installation

### 1. Clone or Download

```bash
# Clone the repository
git clone https://github.com/yourorg/jira-clone.git

# Or download and extract to XAMPP htdocs folder
# Place in: C:\xampp\htdocs\jira_clone_system\
```

### 2. Configure Apache

Ensure `mod_rewrite` is enabled in Apache. The `.htaccess` file in `public/` handles URL rewriting.

If using XAMPP, access via: `http://localhost/jira_clone_system/public/`

For cleaner URLs, you can configure a virtual host:

```apache
<VirtualHost *:80>
    ServerName jira.local
    DocumentRoot "C:/xampp/htdocs/jira_clone_system/public"
    
    <Directory "C:/xampp/htdocs/jira_clone_system/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 3. Create Database

Using phpMyAdmin or MySQL CLI:

```sql
CREATE DATABASE jira_clone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Import Schema and Seed Data

```bash
# Using MySQL CLI
mysql -u root -p jira_clone < database/schema.sql
mysql -u root -p jira_clone < database/seed.sql

# Or import via phpMyAdmin:
# 1. Select jira_clone database
# 2. Import database/schema.sql
# 3. Import database/seed.sql
```

### 5. Configure Application

1. Copy the configuration file:
```bash
cp config/config.php config/config.local.php
```

2. Edit `config/config.local.php`:
```php
return [
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'jira_clone',
        'username' => 'root',
        'password' => '',  // Your MySQL password
    ],
    
    'app' => [
        'url' => 'http://localhost/jira_clone_system/public',
        'key' => 'your-32-character-secret-key-here!!',  // Change this!
    ],
    
    'jwt' => [
        'secret' => 'your-jwt-secret-change-me!!',  // Change this!
    ],
];
```

### 6. Create Storage Directories

```bash
mkdir -p storage/logs storage/cache public/uploads
chmod 755 storage/logs storage/cache public/uploads
```

### 7. Access the Application

Open your browser and navigate to:
- URL: `http://localhost/jira_clone_system/public/`

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | Admin@123 |
| User | john.smith@example.com | User@123 |
| User | jane.doe@example.com | User@123 |

**⚠️ Change these passwords immediately in production!**

## 📁 Project Structure

```
jira_clone_system/
├── bootstrap/          # Application bootstrap
│   ├── autoload.php    # Custom PSR-4 autoloader
│   └── app.php         # Application initialization
│
├── config/             # Configuration files
│   ├── config.php      # Main configuration
│   └── config.local.php # Local overrides (git-ignored)
│
├── database/           # Database files
│   ├── schema.sql      # Database schema
│   ├── seed.sql        # Sample data
│   └── migrations/     # Migration files
│
├── public/             # Web root
│   ├── index.php       # Front controller
│   ├── .htaccess       # Apache rewrite rules
│   ├── assets/         # CSS, JS, images
│   └── uploads/        # User uploads
│
├── src/                # Application source code
│   ├── Controllers/    # HTTP controllers
│   ├── Controllers/Api/# API controllers
│   ├── Core/           # Framework core classes
│   ├── Middleware/     # HTTP middleware
│   ├── Models/         # Database models
│   ├── Services/       # Business logic services
│   ├── Repositories/   # Data access layer
│   └── Helpers/        # Helper functions
│
├── routes/             # Route definitions
│   ├── web.php         # Web routes
│   └── api.php         # API routes
│
├── views/              # PHP templates
│   ├── layouts/        # Layout templates
│   ├── components/     # Reusable components
│   ├── auth/           # Authentication views
│   ├── dashboard/      # Dashboard views
│   ├── projects/       # Project views
│   ├── issues/         # Issue views
│   └── ...
│
├── storage/            # Application storage
│   ├── logs/           # Log files
│   └── cache/          # Cache files
│
├── tests/              # Test files
│   ├── Unit/           # Unit tests
│   ├── Integration/    # Integration tests
│   └── TestRunner.php  # Custom test runner
│
├── lang/               # Translation files
│   └── en/             # English translations
│
└── scripts/            # CLI scripts
    ├── migrate.php     # Run migrations
    ├── seed.php        # Run seeds
    └── cache-clear.php # Clear cache
```

## 🔑 Roles & Permissions

### Default Roles

| Role | Description |
|------|-------------|
| Administrator | Full system access |
| Project Manager | Manage projects and sprints |
| Developer | Work on issues, log time |
| QA Tester | Test and report issues |
| Viewer | Read-only access |

### Key Permissions

| Category | Permissions |
|----------|-------------|
| Projects | browse, create, edit, delete, manage-members |
| Issues | create, edit, delete, assign, transition, link |
| Comments | add, edit, delete |
| Attachments | add, delete |
| Time Tracking | log-work, edit, delete |
| Boards | manage-boards, manage-sprints |
| Admin | manage-users, manage-roles, view-audit-log |

## 🌐 API Documentation

### Authentication

```bash
# Login and get JWT token
curl -X POST http://localhost/jira_clone_system/public/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"Admin@123"}'

# Response
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600
}

# Use token in requests
curl http://localhost/jira_clone_system/public/api/v1/projects \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/v1/auth/login | Authenticate user |
| GET | /api/v1/projects | List projects |
| POST | /api/v1/projects | Create project |
| GET | /api/v1/projects/{key} | Get project |
| GET | /api/v1/issues | Search issues |
| POST | /api/v1/issues | Create issue |
| GET | /api/v1/issues/{key} | Get issue |
| PUT | /api/v1/issues/{key} | Update issue |
| POST | /api/v1/issues/{key}/transitions | Transition issue |
| GET | /api/v1/boards | List boards |
| GET | /api/v1/sprints/{id} | Get sprint |

See `docs/api.yaml` for complete OpenAPI 3.0 documentation.

## 🧪 Testing

Run the custom test runner:

```bash
php tests/TestRunner.php
```

Run specific test suite:

```bash
php tests/TestRunner.php --suite=Unit
php tests/TestRunner.php --suite=Integration
```

## 📧 Email Configuration

### Using PHP mail()

```php
// config/config.local.php
'mail' => [
    'driver' => 'mail',
    'from_address' => 'noreply@yourdomain.com',
    'from_name' => 'Jira Clone',
],
```

### Using SMTP

```php
'mail' => [
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'your@gmail.com',
    'password' => 'your-app-password',
    'encryption' => 'tls',
    'from_address' => 'your@gmail.com',
    'from_name' => 'Jira Clone',
],
```

### Process Email Queue

Set up a cron job to process queued emails:

```bash
# Run every minute
* * * * * php /path/to/jira_clone_system/scripts/process-emails.php
```

## 🔒 Security

### Implemented Protections

- **Authentication**: Argon2id password hashing, secure sessions
- **CSRF Protection**: Token-based CSRF protection on all forms
- **XSS Prevention**: Output encoding, Content Security Policy
- **SQL Injection**: Prepared statements with PDO
- **Rate Limiting**: Configurable rate limits for API endpoints
- **Input Validation**: Server-side validation on all inputs
- **Secure Headers**: X-Frame-Options, X-XSS-Protection, etc.

### Security Best Practices

1. Change all default passwords
2. Use HTTPS in production
3. Set secure session cookies
4. Regularly update dependencies
5. Monitor audit logs
6. Backup database regularly

## 🚀 Production Deployment

### Checklist

1. [ ] Set `app.env` to `production`
2. [ ] Set `app.debug` to `false`
3. [ ] Change all secret keys
4. [ ] Change default passwords
5. [ ] Enable HTTPS
6. [ ] Set `session.secure` to `true`
7. [ ] Configure proper file permissions
8. [ ] Set up log rotation
9. [ ] Configure email settings
10. [ ] Set up database backups
11. [ ] Configure cron jobs

### Recommended PHP Settings

```ini
; php.ini
display_errors = Off
log_errors = On
error_log = /path/to/logs/php_error.log
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 12M
```

## 📊 Performance Tips

1. **Enable OPcache**: Significantly improves PHP performance
2. **Database Indexes**: Already optimized in schema
3. **Query Optimization**: Use pagination, limit results
4. **Caching**: File cache enabled by default
5. **Asset Optimization**: Minify CSS/JS for production

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## 📄 License

This project is proprietary software. All rights reserved.

## 📞 Support

- **Documentation**: See `/docs` folder
- **Issues**: Open an issue on GitHub
- **Email**: support@example.com

---

Made with ❤️ for enterprise project management
