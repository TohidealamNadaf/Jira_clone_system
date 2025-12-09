# Jira Clone - Command & Access Cheatsheet

## 🚀 START HERE (3 Simple Steps)

### Step 1: Start Services
```
1. Open XAMPP Control Panel
2. Start Apache (port 8080)
3. Start MySQL
```

### Step 2: Import Database
```
Go to: http://localhost/phpmyadmin
→ SQL tab
→ Paste database/schema.sql
→ Click Go
→ Paste database/seed.sql  
→ Click Go
```

### Step 3: Configure & Access
```
1. Copy config/config.php → config/config.local.php
2. Go to: http://localhost:8080/jira_clone_system/public/
3. Login: admin@example.com / Admin@123
```

---

## 📍 Quick Access URLs

| Page | URL |
|------|-----|
| Login | `http://localhost:8080/jira_clone_system/public/` |
| Dashboard | `/dashboard` |
| Projects | `/projects` |
| Issues | `/search` |
| Boards | `/projects/{key}/board` |
| Sprints | `/sprints` |
| Reports | `/reports` |
| Admin | `/admin` |

---

## 🔐 Default Accounts

```
ADMIN:
Email:    admin@example.com
Password: Admin@123

USER 1:
Email:    john.smith@example.com
Password: User@123

USER 2:
Email:    jane.doe@example.com
Password: User@123
```

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `config/config.local.php` | Your configuration (CREATE THIS) |
| `database/schema.sql` | Database structure |
| `database/seed.sql` | Sample data |
| `public/index.php` | Entry point |
| `routes/web.php` | Web routes |
| `routes/api.php` | API routes |
| `storage/logs/` | Error logs |

---

## 🔌 API Endpoints

### Authentication
```bash
POST /api/v1/auth/login
{
  "email": "admin@example.com",
  "password": "Admin@123"
}
```

### Projects
```bash
GET    /api/v1/projects
POST   /api/v1/projects
GET    /api/v1/projects/{key}
PUT    /api/v1/projects/{key}
DELETE /api/v1/projects/{key}
```

### Issues
```bash
GET    /api/v1/issues
POST   /api/v1/issues
GET    /api/v1/issues/{key}
PUT    /api/v1/issues/{key}
DELETE /api/v1/issues/{key}
POST   /api/v1/issues/{key}/transitions
```

### Boards
```bash
GET    /api/v1/boards
GET    /api/v1/boards/{id}
```

### Sprints
```bash
GET    /api/v1/sprints/{id}
POST   /api/v1/sprints/{id}/start
POST   /api/v1/sprints/{id}/complete
```

---

## 🛠️ Configuration Quick Reference

### Database Settings (`config.local.php`)
```php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'jiira_clonee_system',
    'username' => 'root',
    'password' => '', // if set
]
```

### App Settings (`config.local.php`)
```php
'app' => [
    'url' => 'http://localhost:8080/jira_clone_system/public',
    'debug' => true, // false in production
    'env' => 'development', // or production
]
```

### Email Settings (`config.local.php`)
```php
'mail' => [
    'driver' => 'smtp', // or 'mail'
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'your@gmail.com',
    'password' => 'app-password',
    'encryption' => 'tls',
]
```

---

## 📊 Database Quick Facts

- **Total Tables**: 67
- **Size**: ~100MB with sample data
- **Collation**: utf8mb4_unicode_ci
- **Engine**: InnoDB
- **Key Tables**:
  - users, roles, permissions
  - projects, issues, boards
  - sprints, workflows
  - comments, attachments
  - audit_logs, notifications

---

## 🎯 Common Tasks

### Change Admin Password
1. Login as admin
2. Click profile → Profile Settings
3. Change password
4. Save

### Create New Project
1. Click "Projects" → "Create Project"
2. Fill details
3. Click "Create"
4. Add members

### Create Issue
1. Go to project board
2. Click "+ Create"
3. Fill issue details
4. Click "Create"

### Run Report
1. Click "Reports"
2. Select report type
3. Choose date range
4. View/export results

### Add Team Member
1. Click "Projects" → Select project
2. Click "Members"
3. Click "Add Member"
4. Select user and role
5. Click "Add"

---

## ⚙️ Troubleshooting Commands

### Check MySQL Connection
```bash
mysql -u root -p
mysql> SHOW DATABASES;
mysql> USE jiira_clonee_system;
mysql> SHOW TABLES;
```

### View Error Logs
```
Windows Explorer:
C:\xampp\htdocs\jira_clone_system\storage\logs\
```

### Check PHP Version
```
http://localhost/xampp/phpinfo.php
(Must be 8.2+)
```

### Restart Apache
```
XAMPP Control Panel → Click "Stop" → Click "Start"
```

---

## 🔑 Key Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl+S` | Save (most forms) |
| `Enter` | Search issues |
| `?` | Help/shortcuts on any page |

---

## 📱 Mobile Access

The system is fully responsive. Access on mobile:

```
http://localhost:8080/jira_clone_system/public/

(May need to configure for external access if not localhost)
```

---

## 🚨 If Something Goes Wrong

### Step 1: Check Logs
```
C:\xampp\htdocs\jira_clone_system\storage\logs\
```

### Step 2: Verify Database
```
phpMyAdmin → Check tables exist
```

### Step 3: Check Config
```
config/config.local.php → Verify database settings
```

### Step 4: Restart Services
```
XAMPP Control Panel → Stop all → Start Apache & MySQL
```

### Step 5: Clear Cache (if exists)
```
Delete: storage/cache/* (but not .htaccess)
```

---

## 📚 Documentation Files

| File | Contents |
|------|----------|
| `README.md` | Full documentation |
| `QUICK_START.md` | 5-minute setup |
| `SETUP_AND_RUN_INSTRUCTIONS.md` | Detailed guide |
| `RUN_INSTRUCTIONS.txt` | Step-by-step |
| `COMPLETION_SUMMARY.md` | What's included |

---

## ✅ Pre-Launch Checklist

- [ ] MySQL running in XAMPP
- [ ] Apache running on port 8080
- [ ] Database schema imported
- [ ] Seed data imported
- [ ] config/config.local.php created
- [ ] Can login with admin account
- [ ] Can create project
- [ ] Can create issue
- [ ] Can view board

---

## 🎉 You're Ready!

All systems operational. Access now:

```
http://localhost:8080/jira_clone_system/public/
```

Default Login:
```
Email: admin@example.com
Pass:  Admin@123
```

Enjoy your enterprise Jira clone!

---

**Last updated**: December 5, 2025
**Status**: ✅ Production Ready
