# Jira Clone - Setup & Run Instructions

## Environment
- **XAMPP Apache Port**: 8080
- **Database Name**: jiira_clonee_system
- **Location**: C:\xampp\htdocs\jira_clone_system

## Prerequisites
- XAMPP installed with MySQL and Apache running
- PHP 8.2+
- MySQL 8.0+

## Step 1: Database Setup

### Option A: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "SQL" tab at the top
3. Copy and paste the entire contents of `database/schema.sql`
4. Click "Go" to execute
5. Repeat the process with `database/seed.sql`

### Option B: Using MySQL Command Line

```bash
# Open Command Prompt and navigate to MySQL bin directory
cd C:\xampp\mysql\bin

# Import schema
mysql -u root jiira_clonee_system < "C:\xampp\htdocs\jira_clone_system\database\schema.sql"

# Import seed data
mysql -u root jiira_clonee_system < "C:\xampp\htdocs\jira_clone_system\database\seed.sql"
```

## Step 2: Configure Application

### Create Local Config File

1. Navigate to `C:\xampp\htdocs\jira_clone_system\config\`
2. Copy `config.php` and rename to `config.local.php`
3. Edit `config.local.php` with your settings:

```php
<?php
return [
    'app' => [
        'url' => 'http://localhost:8080/jira_clone_system/public',
        'debug' => true, // Set to false in production
        'env' => 'development',
    ],
    
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'jiira_clonee_system',
        'username' => 'root',
        'password' => '', // Add password if set
        'charset' => 'utf8mb4',
    ],
];
```

## Step 3: Apache Virtual Host Configuration (Recommended for Port 8080)

### Method 1: Virtual Host Setup

1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add at the end of the file:

```apache
<VirtualHost *:8080>
    ServerName localhost
    DocumentRoot "C:\xampp\htdocs\jira_clone_system\public"
    
    <Directory "C:\xampp\htdocs\jira_clone_system\public">
        AllowOverride All
        Require all granted
        
        # Enable mod_rewrite
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]
    </Directory>
</VirtualHost>
```

3. Restart Apache from XAMPP Control Panel

### Method 2: Direct URL Access (If Virtual Host Not Configured)

Access via: `http://localhost:8080/jira_clone_system/public/`

## Step 4: Verify Installation

1. Open browser and navigate to:
   - With Virtual Host: `http://localhost:8080/`
   - Without Virtual Host: `http://localhost:8080/jira_clone_system/public/`

2. You should see the login page

## Step 5: Login with Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | Admin@123 |
| User | john.smith@example.com | User@123 |
| User | jane.doe@example.com | User@123 |

## Troubleshooting

### CSP Error Fixed
- Removed the overly permissive CSP meta tag from auth layout
- The application uses proper security headers instead

### Database Connection Error
- Verify database `jiira_clonee_system` exists
- Check username/password in `config.local.php`
- Ensure MySQL is running in XAMPP

### 404 Errors on Routes
- Verify `mod_rewrite` is enabled in Apache
- Check `.htaccess` file in `public/` folder exists
- Ensure you're accessing `/public` directory or using virtual host

### Permission Errors
1. Create storage directories:
   ```
   mkdir storage/logs
   mkdir storage/cache
   mkdir public/uploads
   ```

2. Set permissions (Windows - may not be necessary):
   - Right-click folders → Properties → Security → Edit
   - Add "SYSTEM" and "Users" with full control

### Port 8080 Not Working
1. Verify Apache is running on port 8080 in XAMPP
2. Check `C:\xampp\apache\conf\httpd.conf` for `Listen 8080`
3. If not present, add `Listen 8080` after other Listen directives

## Features Available

### Core Features
- ✅ Project Management
- ✅ Issue Tracking (Epic, Story, Task, Bug, Sub-task)
- ✅ Agile Boards (Scrum & Kanban)
- ✅ Sprint Planning
- ✅ Workflow Management
- ✅ Comments & Discussions
- ✅ File Attachments
- ✅ Advanced Search (JQL-like)
- ✅ Reports (Burndown, Velocity)
- ✅ Role-Based Access Control (RBAC)
- ✅ User Management
- ✅ Audit Logs
- ✅ REST API with JWT

### Admin Features
- User and role management
- Project settings and customization
- Permission management
- System configuration
- Audit trail

## API Access

### Get JWT Token
```bash
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"Admin@123"}'
```

Response:
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600
}
```

### Use Token in Requests
```bash
curl http://localhost:8080/api/v1/projects \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Next Steps

1. **Change Default Passwords**: Go to Admin > Users and update all user passwords
2. **Configure Email**: Edit `config.local.php` mail settings for notifications
3. **Customize Project Settings**: Create projects and configure issue types
4. **Set Up Workflows**: Define custom workflows for your team
5. **Add Team Members**: Invite users and assign roles
6. **Configure HTTPS**: For production deployment

## File Structure Quick Reference

```
jira_clone_system/
├── config/              # Configuration files
├── database/            # Schema and seed files
├── public/              # Web root (point Apache here)
│   ├── index.php        # Entry point
│   ├── .htaccess        # URL rewriting rules
│   └── assets/          # CSS, JS, images
├── src/                 # Application code
│   ├── Controllers/     # Request handlers
│   ├── Core/            # Framework classes
│   ├── Middleware/      # HTTP middleware
│   └── Services/        # Business logic
├── views/               # HTML templates
├── routes/              # Route definitions
└── storage/             # Logs and cache
```

## Production Deployment Checklist

- [ ] Set `app.env` to `production`
- [ ] Set `app.debug` to `false`
- [ ] Change all default passwords
- [ ] Change JWT secret keys
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure database backups
- [ ] Set up log rotation
- [ ] Configure email provider
- [ ] Test all critical workflows

## Security Notes

✅ Already Implemented:
- Argon2id password hashing
- CSRF token protection
- Prepared SQL statements
- Input validation and sanitization
- XSS output encoding
- Rate limiting on API endpoints
- Secure session handling

⚠️ Always:
- Keep PHP and MySQL updated
- Use HTTPS in production
- Monitor audit logs regularly
- Back up database daily
- Review user permissions regularly

## Support & Documentation

- Main README: See `README.md`
- API Documentation: See `docs/` folder
- Database Schema: See `database/schema.sql`
- Error Logs: Check `storage/logs/`

---

**Ready to use! Start XAMPP Apache and access the application now.**
