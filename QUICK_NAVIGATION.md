# 🚀 Jira Clone - Quick Navigation Guide

**Bookmark this page and the Developer Portal!**

---

## 📍 Where To Go For What

### First Time?
1. **[DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md)** ← Start here! (Your main navigation hub)
2. Open: `http://localhost/jira_clone_system/public/developer-dashboard.html` ← Interactive dashboard

### Need Code Standards?
→ **[AGENTS.md](AGENTS.md)** - Your development bible (code style, conventions, architecture)

### Want to Code?
1. Read: [AGENTS.md](AGENTS.md) - Code style guide
2. Check: [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-code-standards) - Conventions
3. Look: Similar code in `src/Controllers/` or `views/` for examples

### Need to Understand Features?
→ **[DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-system-features)** - All features explained with file locations

### Admin & Security Questions?
→ **[DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-admin--permissions)** - Permission matrix and protection details

### Found a Bug or Issue?
→ **[DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-bug-fixes--issues)** - Known issues and solutions

### Testing?
→ **[DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-testing)** - Test guides and commands

### Deploying to Production?
→ **[README.md](README.md#-production-deployment)** - Deployment checklist

### Need Quick Commands?
```bash
# Seed database
php scripts/verify-and-seed.php

# Run tests
php tests/TestRunner.php

# Access application
http://localhost/jira_clone_system/public/

# Open Developer Dashboard
http://localhost/jira_clone_system/public/developer-dashboard.html
```

---

## 🗂️ File Structure Reference

```
Your Project Root
├── DEVELOPER_PORTAL.md ⭐ START HERE (navigation hub)
├── QUICK_NAVIGATION.md (this file)
├── AGENTS.md (code standards - authority)
├── README.md (setup & overview)
├── QUICK_START.md (5-minute setup)
│
├── public/
│   └── developer-dashboard.html ⭐ INTERACTIVE DASHBOARD
│
├── src/Controllers/ (HTTP handlers)
├── src/Services/ (business logic)
├── views/ (PHP templates)
├── routes/ (route definitions)
├── database/ (schema & migrations)
├── config/ (configuration)
└── storage/ (logs & cache)
```

---

## 🎯 Key Sections & Links

| Need | Document | Link |
|------|----------|------|
| **Getting Started** | DEVELOPER_PORTAL | [#-getting-started](DEVELOPER_PORTAL.md#-getting-started) |
| **All Features** | DEVELOPER_PORTAL | [#-system-features](DEVELOPER_PORTAL.md#-system-features) |
| **Code Standards** | AGENTS.md | [AGENTS.md](AGENTS.md#code-style--conventions) |
| **Admin Pages** | ADMIN_PAGES_IMPLEMENTATION.md | [ADMIN_PAGES_IMPLEMENTATION.md](ADMIN_PAGES_IMPLEMENTATION.md) |
| **Comments Feature** | COMMENT_FEATURE_SUMMARY.md | [COMMENT_FEATURE_SUMMARY.md](COMMENT_FEATURE_SUMMARY.md) |
| **Reports** | REPORTS_QUICK_START.md | [REPORTS_QUICK_START.md](REPORTS_QUICK_START.md) |
| **UI Design** | UI_REDESIGN_COMPLETE.md | [UI_REDESIGN_COMPLETE.md](UI_REDESIGN_COMPLETE.md) |
| **Admin Security** | ADMIN_AUTHORITY_VERIFICATION.md | [ADMIN_AUTHORITY_VERIFICATION.md](ADMIN_AUTHORITY_VERIFICATION.md) |
| **Deployment** | README.md | [README.md#-production-deployment](README.md#-production-deployment) |

---

## 🔑 Default Credentials

```
ADMIN
Email:    admin@example.com
Password: Admin@123

USER
Email:    john.smith@example.com
Password: User@123
```

---

## 💻 Common Commands

```bash
# ===== Setup & Database =====
php scripts/verify-and-seed.php     # Seed test data

# ===== Testing =====
php tests/TestRunner.php            # Run all tests
php tests/TestRunner.php --suite=Unit  # Unit tests only

# ===== Access =====
http://localhost/jira_clone_system/public/              # Application
http://localhost/jira_clone_system/public/developer-dashboard.html  # This Portal
```

---

## 📚 Core Documentation (Priority Order)

1. **[DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md)** - Navigation hub & feature overview
2. **[AGENTS.md](AGENTS.md)** - Code standards & conventions (Authority)
3. **[README.md](README.md)** - Installation & setup guide
4. **[QUICK_START.md](QUICK_START.md)** - 5-minute setup
5. Feature-specific docs (as needed)

---

## 🎓 Learning Path (Recommended Order)

### Day 1: Setup & Understand
- [ ] Read: [QUICK_START.md](QUICK_START.md) - Install & login (5 min)
- [ ] Read: [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md) - System overview (10 min)
- [ ] Explore: [developer-dashboard.html](public/developer-dashboard.html) - Interactive tour (5 min)

### Day 2: Code & Architecture
- [ ] Read: [AGENTS.md](AGENTS.md) - Code standards (15 min)
- [ ] Explore: `src/Controllers/` - Controller examples (10 min)
- [ ] Explore: `views/` - View examples (10 min)

### Day 3: Features & Admin
- [ ] Read: [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-system-features) - Features (10 min)
- [ ] Read: [ADMIN_PAGES_IMPLEMENTATION.md](ADMIN_PAGES_IMPLEMENTATION.md) - Admin section (10 min)
- [ ] Test: Admin dashboard at `/admin`

### Day 4: Testing & Deployment
- [ ] Read: [COMPLETE_TEST_WORKFLOW.md](COMPLETE_TEST_WORKFLOW.md) - Testing (10 min)
- [ ] Run: `php tests/TestRunner.php` - See tests in action
- [ ] Read: [README.md](README.md#-production-deployment) - Deployment checklist

---

## 🆘 Quick Help Index

### "How do I...?"

#### Create a New Page?
→ [AGENTS.md](AGENTS.md) → Controllers & Views section

#### Add a Database Query?
→ [AGENTS.md](AGENTS.md) → PHP Conventions → Database Queries

#### Understand Admin Features?
→ [ADMIN_PAGES_IMPLEMENTATION.md](ADMIN_PAGES_IMPLEMENTATION.md)

#### Test My Changes?
→ [COMPLETE_TEST_WORKFLOW.md](COMPLETE_TEST_WORKFLOW.md)

#### Deploy to Production?
→ [README.md](README.md#-production-deployment)

#### Fix a Bug?
→ [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md#-bug-fixes--issues)

#### Understand Security?
→ [ADMIN_AUTHORITY_VERIFICATION.md](ADMIN_AUTHORITY_VERIFICATION.md)

---

## 🏗️ System Architecture (30-Second Overview)

```
REQUEST → public/index.php (Front Controller)
   ↓
routes/web.php (Route Matching)
   ↓
src/Controllers/* (Handle Request)
   ↓
src/Services/* (Business Logic)
   ↓
src/Repositories/* (Database Access)
   ↓
App\Core\Database (PDO Queries)
   ↓
views/* (Render HTML with data)
   ↓
RESPONSE → User's Browser
```

**Key Classes:**
- `App\Core\Database` - Queries
- `App\Core\Request` - Input validation
- `App\Core\Session` - User auth
- `App\Core\Controller` - Base controller

---

## ✅ Before You Code

- [ ] Read [AGENTS.md](AGENTS.md) - Mandatory for all coding
- [ ] Check similar code in the system for patterns
- [ ] Use prepared statements (never trust user input)
- [ ] Add type hints to all methods
- [ ] Test your code: `php tests/TestRunner.php`

---

## 📱 Browser Bookmarks (Recommended)

```
Bookmark 1: Developer Portal (Main)
URL: file:///C:/xampp/htdocs/jira_clone_system/DEVELOPER_PORTAL.md

Bookmark 2: Developer Dashboard (Interactive)
URL: http://localhost/jira_clone_system/public/developer-dashboard.html

Bookmark 3: Application
URL: http://localhost/jira_clone_system/public/

Bookmark 4: Admin Panel
URL: http://localhost/jira_clone_system/public/admin

Bookmark 5: Code Standards (AGENTS.md)
URL: file:///C:/xampp/htdocs/jira_clone_system/AGENTS.md
```

---

## 🎯 Pro Tips

1. **AGENTS.md is the Authority** - All standards, conventions, and patterns are documented there. Refer to it constantly.

2. **Use Developer Portal as Hub** - Links to all important documentation. Bookmark the [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md).

3. **Check Existing Code First** - Before writing new code, look for similar patterns in `src/Controllers/` and `views/`.

4. **Type Everything** - PHP 8.2+ requires type hints on all parameters and return types.

5. **Database First** - Always use prepared statements with `Database::select()`, `insert()`, `update()`, `delete()`.

6. **Test Before Commit** - Run `php tests/TestRunner.php` before pushing any code.

7. **CSS Variables** - Use CSS variables from `:root` for colors (e.g., `var(--jira-blue)`, `var(--text-primary)`).

8. **Mobile First** - Design for mobile first, then scale up. Check responsive breakpoints in media queries.

---

## 📞 Still Lost?

1. Check [DEVELOPER_PORTAL.md](DEVELOPER_PORTAL.md) for your topic
2. Search the relevant .md file
3. Look at similar code in the system
4. Refer to [AGENTS.md](AGENTS.md) for conventions

---

**Last Updated**: December 2025  
**Version**: 1.0.0

> **Bookmark this file and the Developer Portal for quick reference!**
