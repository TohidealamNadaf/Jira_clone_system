# Production Deployment Checklist
## Jira Clone - Enterprise Ready

**Deployment Date**: [SET YOUR DATE]  
**Deployment Lead**: [ASSIGN PERSON]  
**Approval**: [SIGN OFF]  

---

## Pre-Deployment (1 Week Before)

### ✅ Security Hardening
- [ ] Change all default passwords (admin@example.com → your admin)
- [ ] Generate new JWT secret in `config/config.php`
- [ ] Update CSRF token salt
- [ ] Review HTTPS certificate (or purchase new)
- [ ] Verify mod_rewrite enabled on production Apache
- [ ] Check firewall rules (open port 443 for HTTPS)
- [ ] Verify .env file not accessible (Apache config prevents access)

### ✅ Database Preparation
- [ ] Full backup of current database (if migrating)
- [ ] Test backup restore procedure
- [ ] Create new database user (not root)
- [ ] Grant minimal privileges to app user
- [ ] Run schema.sql on production database
- [ ] Run seed.sql with production data
- [ ] Verify all tables created (24 tables expected)
- [ ] Verify all indexes present

### ✅ Configuration
- [ ] Update `config/config.php`:
  ```php
  define('ENVIRONMENT', 'production');
  define('DEBUG', false); // CRITICAL: Disable
  define('DB_HOST', 'your-db-host');
  define('DB_NAME', 'jira_clone');
  define('DB_USER', 'app_user'); // Not root
  define('DB_PASS', 'strong-password-32-chars');
  ```
- [ ] Set APP_URL correctly (with HTTPS)
- [ ] Verify email configuration (SMTP, from address)
- [ ] Set up cron jobs for:
  - `php scripts/process-notification-retries.php` (daily)
  - `php scripts/backup-database.php` (daily at 2 AM)
- [ ] Configure log rotation in Apache

### ✅ SSL/HTTPS
- [ ] Purchase or renew SSL certificate
- [ ] Install certificate on web server
- [ ] Configure Apache for SSL
- [ ] Set HSTS header (1 year)
- [ ] Test on https://www.ssllabs.com/ssltest/
- [ ] Force HTTP → HTTPS redirect in .htaccess

### ✅ Backups
- [ ] Set up automated daily backups
- [ ] Store backups off-server (S3, external drive)
- [ ] Document recovery procedure
- [ ] Test restore from backup (critical)
- [ ] Keep 30-day backup retention
- [ ] Document backup location and access

### ✅ Monitoring & Logging
- [ ] Enable error logging to `storage/logs/`
- [ ] Set up log rotation (> 10 MB)
- [ ] Configure uptime monitoring (Pingdom, UptimeRobot)
- [ ] Set up email alerts for errors
- [ ] Configure error reporting (Sentry optional)
- [ ] Verify mail queue processing

### ✅ Performance Tuning
- [ ] Enable query result caching
- [ ] Configure PHP opcache
- [ ] Enable gzip compression in Apache
- [ ] Set MySQL query timeout
- [ ] Verify database indexes
- [ ] Set up CDN for static assets (optional)

### ✅ Testing in Staging
- [ ] Deploy to staging environment first
- [ ] Run `php tests/TestRunner.php` (all tests pass)
- [ ] Load test: 100 concurrent users
- [ ] Performance test: Response time < 2s
- [ ] Security scan: Zero critical vulnerabilities
- [ ] Test all critical workflows:
  - [ ] User login/logout
  - [ ] Create project
  - [ ] Create issue
  - [ ] Assign issue
  - [ ] Add comment
  - [ ] Change status
  - [ ] View reports
  - [ ] Download attachments
  - [ ] Admin pages (users, roles)

### ✅ Documentation
- [ ] Update README.md with production URLs
- [ ] Document admin account details (secure location)
- [ ] Create runbook for common issues
- [ ] Document backup/restore procedures
- [ ] List emergency contacts
- [ ] Prepare user training materials

---

## 24 Hours Before Deployment

### ✅ Final Verification
- [ ] Confirm deployment date/time with team
- [ ] Notify all users of maintenance window (24 hours notice)
- [ ] Backup production database
- [ ] Verify rollback plan
- [ ] Schedule post-deployment monitoring (48 hours)
- [ ] Prepare status page updates

### ✅ Staging Final Test
- [ ] Re-run all tests on staging
- [ ] Verify production configuration
- [ ] Test with production-like data volume
- [ ] Check database backup/restore (1 final time)
- [ ] Document any issues found

### ✅ Team Briefing
- [ ] Brief all stakeholders
- [ ] Assign roles (deployer, monitor, comms)
- [ ] Test communication channels
- [ ] Prepare rollback command
- [ ] Set monitoring alerts

---

## Deployment Day (Morning)

### ✅ Pre-Deployment Checks
- [ ] Network connectivity verified
- [ ] Database online and responsive
- [ ] Apache/PHP services running
- [ ] Backup locations accessible
- [ ] Team on standby
- [ ] Maintenance window announced

### ✅ Deployment Steps (Follow in Order)

#### Step 1: Start Maintenance Mode (5 min)
```bash
# Create maintenance page (block users)
cp public/maintenance.html public/index-old.html
# Or in Apache config: maintenance.html as default
echo "Maintenance in progress" > public/index.html
```

#### Step 2: Backup Current System (10 min)
```bash
# Full database backup
mysqldump -u root -p jira_clone > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup application code (if needed)
tar -czf jira_clone_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/html/jira_clone/
```

#### Step 3: Deploy Code (5 min)
```bash
# Upload files to production
# Via SFTP, Git pull, or file copy
cd /var/www/html/jira_clone_system/
git pull origin main # or upload files
chmod -R 755 public/ storage/ views/
chmod -R 777 storage/logs/ storage/cache/
```

#### Step 4: Initialize Database (10 min)
```bash
# Run migrations (idempotent - safe if already run)
php scripts/run-migrations.php

# Initialize notification preferences
php scripts/initialize-notifications.php

# Seed test data (optional, comment out for production)
# mysql jira_clone < database/seed.sql
```

#### Step 5: Cache Clear & Warmup (5 min)
```bash
# Clear any cached files
rm -rf storage/cache/*

# Warm up cache (optional)
php public/index.php # Triggers cache initialization
```

#### Step 6: End Maintenance Mode (2 min)
```bash
# Remove maintenance page
rm public/index.html
# Restore app
cp public/index-old.html public/index.html
```

#### Step 7: Verify Deployment (5 min)
```bash
# Test homepage loads
curl https://your-domain.com/

# Check logs for errors
tail -f storage/logs/error.log

# Verify database connection
# Login and check dashboard
```

**Total Deployment Time**: ~45 minutes

---

## Post-Deployment Verification (Day 1)

### ✅ System Health (Every 30 min, first 4 hours)
- [ ] Application responding (HTTPS only)
- [ ] Database queries < 200ms
- [ ] No 500 errors in logs
- [ ] SSL certificate valid
- [ ] Static assets loading
- [ ] API endpoints responding

### ✅ Functionality Checks
- [ ] User login works
- [ ] Project list displays
- [ ] Issues load correctly
- [ ] Create issue modal works
- [ ] Comments functional
- [ ] Notifications appear
- [ ] Reports render
- [ ] Admin pages accessible

### ✅ User Feedback
- [ ] Monitor team Slack/email
- [ ] Check for reported issues
- [ ] Document any bugs
- [ ] Response time acceptable
- [ ] No data loss reported

### ✅ Monitoring (24 hours)
- [ ] Error rate normal
- [ ] Database performance normal
- [ ] API response times acceptable
- [ ] Uptime 100%
- [ ] Backup successful
- [ ] Email deliveries working

---

## Post-Deployment Week 1

### ✅ Daily Monitoring
- [ ] Check logs for errors
- [ ] Monitor user adoption
- [ ] Track performance metrics
- [ ] Verify backups running
- [ ] Check email queue
- [ ] Monitor disk usage

### ✅ User Training
- [ ] Conduct team training session (2-3 hours)
- [ ] Create user guide documents
- [ ] Establish support process
- [ ] Set up help desk
- [ ] Document common issues

### ✅ Documentation Updates
- [ ] Update README with production URLs
- [ ] Document known issues
- [ ] Create troubleshooting guide
- [ ] Update runbook
- [ ] Share with team

### ✅ Feedback Collection
- [ ] Survey users
- [ ] Identify missing features
- [ ] Document feature requests
- [ ] Plan Phase 2 features
- [ ] Set up feedback channel

---

## Rollback Plan (If Needed)

### Decision Point: When to Rollback
Rollback if ANY of these occur and can't fix quickly:
- Database corruption
- Cannot restore functionality
- Critical security issue
- Data loss detected
- System unavailable > 2 hours

### Rollback Steps (15 minutes)
```bash
# 1. Enable maintenance mode
echo "System maintenance" > public/index.html

# 2. Restore database from pre-deployment backup
mysql -u root -p jira_clone < backup_YYYYMMDD_HHMMSS.sql

# 3. Restore previous code version
cd /var/www/html/jira_clone_system/
git checkout [previous-commit-hash]
# OR copy from backup
tar -xzf jira_clone_backup_YYYYMMDD_HHMMSS.tar.gz

# 4. Verify backup integrity
# Login to app, check data

# 5. Restore normal mode
rm public/index.html

# 6. Notify team
# Send message: "Issue detected, system rolled back. Details in Slack."
```

### Post-Rollback
- [ ] Analyze what went wrong
- [ ] Fix issue in development
- [ ] Re-test thoroughly
- [ ] Schedule new deployment
- [ ] Notify users

---

## Emergency Contacts

| Role | Name | Phone | Email |
|------|------|-------|-------|
| Deployment Lead | [NAME] | [PHONE] | [EMAIL] |
| Database Admin | [NAME] | [PHONE] | [EMAIL] |
| System Admin | [NAME] | [PHONE] | [EMAIL] |
| CTO/Manager | [NAME] | [PHONE] | [EMAIL] |

---

## Deployment Communication Template

### Pre-Deployment Notification
```
Subject: Jira Clone Deployment - Scheduled Maintenance

Team,

We're deploying the new Jira Clone system tomorrow at [TIME] UTC.

Maintenance Window: [START TIME] - [END TIME]
Duration: ~45 minutes

During this time:
- Jira will be unavailable
- No new issues/comments can be created
- You can read existing data (if we keep it in read-only mode)

After deployment:
- All your existing projects and issues will be available
- Some new features will be enabled
- Please test and report issues to [SUPPORT EMAIL]

Questions? Reach out to [MANAGER NAME]

Thanks for your patience!
```

### Post-Deployment Notification
```
Subject: ✅ Jira Clone Successfully Deployed

Team,

The new Jira Clone system is now live!

New Features:
✅ Modern interface matching Jira
✅ Improved performance (2x faster)
✅ Notification system with email
✅ Advanced reports
✅ Better admin tools

Access: https://jira.yourcompany.com

Getting Started:
1. Login with your credentials
2. Visit the Help section for tutorials
3. Report issues to [SUPPORT EMAIL]

Migration Notes:
- All existing data has been migrated
- No manual action needed
- Please test your workflows

Next Steps:
We'll monitor the system closely and gather feedback for improvements.

Thanks!
```

---

## Success Criteria

### Day 1
- ✅ Zero critical errors
- ✅ All core functions working
- ✅ Users can login
- ✅ Projects and issues display
- ✅ API responding

### Week 1
- ✅ 95% of users logged in once
- ✅ Zero data loss
- ✅ Response time < 2 seconds
- ✅ Uptime 99.9%
- ✅ Backup/restore verified

### Month 1
- ✅ 100% of team using daily
- ✅ Zero critical issues
- ✅ User satisfaction > 4/5
- ✅ Phase 2 planning started
- ✅ All issues documented

---

## Sign-Off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Project Lead | | | |
| Technical Lead | | | |
| Security Officer | | | |
| Operations | | | |

---

## Appendix: Useful Commands

### Database Operations
```bash
# Backup
mysqldump -u app_user -p jira_clone > backup.sql

# Restore
mysql -u app_user -p jira_clone < backup.sql

# Check tables
mysql -u app_user -p -e "USE jira_clone; SHOW TABLES;"

# Check size
mysql -u app_user -p -e "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb FROM information_schema.tables WHERE table_schema = 'jira_clone';"
```

### Application Operations
```bash
# Clear cache
rm -rf storage/cache/*

# Check logs
tail -f storage/logs/error.log
tail -f storage/logs/access.log

# Run migrations
php scripts/run-migrations.php

# Seed database
php scripts/verify-and-seed.php

# Run tests
php tests/TestRunner.php
```

### Server Operations
```bash
# Apache restart
sudo service apache2 restart
sudo /usr/sbin/apachectl restart

# MySQL status
sudo service mysql status

# Check disk usage
df -h

# Check memory
free -h

# Check PHP version
php -v
```

---

## Document Version

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 12/2025 | Initial checklist |

---

**Status**: Ready for Deployment  
**Last Updated**: December 2025  
**Maintained By**: Operations Team  

> Print this checklist and keep it with you during deployment!
