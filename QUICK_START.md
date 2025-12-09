# Jira Clone - Quick Start Guide (5 Minutes)

## ⚡ Fast Setup

### 1. Start XAMPP
- Open XAMPP Control Panel
- Click "Start" for Apache and MySQL
- Verify port 8080 is listening

### 2. Import Database
**Option A - phpMyAdmin (Easiest):**
- Go to: `http://localhost/phpmyadmin`
- SQL tab → Paste contents of `database/schema.sql` → Go
- Repeat with `database/seed.sql`

**Option B - Command Line:**
```bash
cd C:\xampp\mysql\bin
mysql -u root jiira_clonee_system < "C:\xampp\htdocs\jira_clone_system\database\schema.sql"
mysql -u root jiira_clonee_system < "C:\xampp\htdocs\jira_clone_system\database\seed.sql"
```

### 3. Configure Application
1. Copy: `config/config.php` → `config/config.local.php`
2. Edit `config/config.local.php`:
```php
'database' => [
    'name' => 'jiira_clonee_system',
    'username' => 'root',
    'password' => '', // if set in MySQL
],
'app' => [
    'url' => 'http://localhost:8080/jira_clone_system/public',
]
```

### 4. Access Application
```
http://localhost:8080/jira_clone_system/public/
```

### 5. Login
- Email: `admin@example.com`
- Password: `Admin@123`

---

## 🎯 What You Can Do Now

✅ Create and manage projects  
✅ Create issues (tasks, bugs, stories, epics)  
✅ Use Scrum & Kanban boards  
✅ Assign issues and track progress  
✅ Add comments and attachments  
✅ Generate reports  
✅ Manage team members  
✅ Control permissions & roles  

---

## 🔧 Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 errors | Check Apache is running on 8080 & mod_rewrite enabled |
| Database not found | Run schema.sql and seed.sql in phpMyAdmin |
| Login fails | Verify config.local.php database settings |
| Can't upload files | Check `public/uploads/` folder exists |

---

## 📚 Full Documentation
See: `SETUP_AND_RUN_INSTRUCTIONS.md` and `README.md`

---

**You're ready! Visit the application now.**
