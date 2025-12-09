# Production Deployment Checklist

**Project**: Jira Clone - Enterprise Edition  
**Version**: 1.0  
**Date**: December 2025  
**Deployed By**: [Your Name]  

---

## PRE-DEPLOYMENT: CODE (Day 1-2)

### Configuration
- [ ] Run: `php scripts/generate-key.php`
- [ ] Update config.php with new keys
- [ ] Change `env` from 'development' to 'production'
- [ ] Change `debug` from true to false
- [ ] Update database host/name/username/password
- [ ] Update SMTP credentials (mail settings)
- [ ] Set session.secure = true (if HTTPS enabled)
- [ ] Update app.url to production domain
- [ ] Verify no hardcoded passwords remain

### Code Cleanup
- [ ] Remove all debug_*.php files
- [ ] Remove all test_*.php files
- [ ] Remove all check_*.php files
- [ ] Remove all verify_*.php files
- [ ] Remove DIAGNOSE_*.php files
- [ ] Remove diagnose_*.php files
- [ ] Verify no console.log() in JavaScript
- [ ] Verify no var_dump() in PHP
- [ ] Verify no echo statements in production code
- [ ] Run: `php tests/TestRunner.php` - All tests pass ✓

### Security Review
- [ ] All SQL queries use prepared statements ✓
- [ ] All output properly escaped ✓
- [ ] CSRF tokens on all forms ✓
- [ ] JWT tokens properly signed ✓
- [ ] Password hashing uses Argon2id ✓
- [ ] No secrets in code (use env vars) ✓
- [ ] Rate limiting rules in place ✓
- [ ] CORS headers configured ✓

---

## PRE-DEPLOYMENT: INFRASTRUCTURE (Day 2-3)

### Database
- [ ] Create production database
- [ ] Create dedicated database user (not root)
- [ ] Set strong password for db user
- [ ] Test database connection
- [ ] Run: `php scripts/run-migrations.php`
- [ ] Verify all tables created (should be 30+)
- [ ] Verify indexes on key columns
- [ ] Backup procedure documented
- [ ] Test backup/restore process

### Web Server (Apache)
- [ ] Enable mod_rewrite
- [ ] Verify .htaccess rules working
- [ ] SSL/HTTPS certificate installed
- [ ] Redirect HTTP → HTTPS
- [ ] Verify document root points to public/
- [ ] Set file permissions correctly:
  - [ ] `storage/` is writable by web server
  - [ ] `public/uploads/` is writable
  - [ ] `bootstrap/` not publicly accessible
  - [ ] `src/` not publicly accessible
  - [ ] `config/` not publicly accessible

### File Permissions
```bash
# Set correct permissions
chmod -R 755 public/
chmod -R 755 storage/
chmod -R 775 storage/logs/
chmod -R 775 storage/cache/
chmod -R 775 public/uploads/
chmod 644 config/config.php
chmod 644 .htaccess
```

### Monitoring & Logging
- [ ] Log directory exists and writable
- [ ] Log rotation configured (prevent disk full)
- [ ] Error logging enabled
- [ ] Access logging enabled
- [ ] Database slow query log enabled
- [ ] Email alerts setup (for errors)
- [ ] Uptime monitoring configured
- [ ] Performance monitoring ready

### Backups
- [ ] Full database backup taken
- [ ] Application code backup taken
- [ ] Backup location documented
- [ ] Backup retention policy set (30 days minimum)
- [ ] Backup restoration tested
- [ ] Automated backup script created
- [ ] Backup schedule set (daily minimum)

---

## PRE-DEPLOYMENT: TEAM & PROCESS (Day 3)

### Documentation
- [ ] Admin runbook documented
- [ ] Emergency contacts listed
- [ ] Troubleshooting guide available
- [ ] Admin password stored securely
- [ ] Database credentials stored securely
- [ ] Rollback procedures documented
- [ ] Scaling guide available

### Team Training
- [ ] Admins trained on dashboard
- [ ] Support team trained on basics
- [ ] Users invited to training session (optional)
- [ ] Quick reference guides distributed
- [ ] Troubleshooting guide shared

### Testing
- [ ] Staging deployment done (dry run)
- [ ] All critical flows tested:
  - [ ] User login works
  - [ ] Create project works
  - [ ] Create issue works
  - [ ] View dashboard works
  - [ ] Comments work
  - [ ] Notifications trigger
  - [ ] Reports load
  - [ ] Admin pages accessible
  - [ ] Email sends (if configured)
  - [ ] File upload works
- [ ] Load testing done (100+ concurrent users)
- [ ] Security testing done
- [ ] Database backup/restore tested

### Communication
- [ ] Deployment window scheduled
- [ ] Team notified of downtime (if any)
- [ ] Stakeholders briefed
- [ ] Rollback contact list created
- [ ] Support team on alert
- [ ] Post-deployment debrief scheduled

---

## DEPLOYMENT DAY (Day 4)

### Pre-Deployment (2 hours before)
- [ ] Clear all cache:
  ```bash
  rm -rf storage/cache/*
  ```
- [ ] Verify database backup successful
- [ ] Verify code backup successful
- [ ] Stop background jobs (if any)
- [ ] Final config verification
- [ ] Team standing by

### Deployment Execution (30-60 minutes)
1. [ ] **Announce**: Notify team deployment starting
2. [ ] **Backup**: Full database backup
   ```bash
   mysqldump -u jira_user -p jira_production > backup-$(date +%s).sql
   ```
3. [ ] **Deploy**: Upload code to production
4. [ ] **Config**: Verify config.php is correct
5. [ ] **Migrate**: Run migrations
   ```bash
   php scripts/run-migrations.php
   ```
6. [ ] **Verify**: Check system online
   ```bash
   curl https://your-domain.com/api/v1/health
   ```
7. [ ] **Test**: Test critical flows:
   - [ ] Login with test account
   - [ ] View dashboard
   - [ ] Create project
   - [ ] Create issue
   - [ ] Check notifications

### Post-Deployment (1 hour after)
- [ ] Monitor logs for errors
- [ ] Check performance metrics
- [ ] Verify email delivery (if configured)
- [ ] Check database performance
- [ ] Verify all services up
- [ ] Announce to team: System is live
- [ ] Monitor for first 2 hours
- [ ] Prepare for support calls

---

## ROLLBACK CHECKLIST (If Needed)

### Rollback Decision
- [ ] Error occurred that affects core functionality
- [ ] Database connection lost
- [ ] More than 10 errors in logs per minute
- [ ] Performance degraded > 50%

### Rollback Execution
1. [ ] **Announce**: Deployment failed, rolling back
2. [ ] **Restore**: Database from backup
   ```bash
   mysql -u jira_user -p jira_production < backup-TIMESTAMP.sql
   ```
3. [ ] **Revert**: Code to previous version
4. [ ] **Restart**: Web services
5. [ ] **Verify**: System back online
6. [ ] **Announce**: System restored

**Expected Time**: < 30 minutes

---

## POST-DEPLOYMENT (Week 1)

### Day 1 After Deployment
- [ ] Monitor logs continuously
- [ ] Response time < 200ms (p95)
- [ ] Error rate < 0.1%
- [ ] No database connection errors
- [ ] Users can login
- [ ] Email delivery working
- [ ] Reports accessible
- [ ] No data loss

### Daily (Days 1-7)
- [ ] [ ] Check logs for errors
- [ ] [ ] Verify backups running
- [ ] [ ] Monitor performance
- [ ] [ ] Check user reports
- [ ] [ ] Verify email delivery
- [ ] [ ] Monitor database growth

### Weekly (Week 1 End)
- [ ] Gather user feedback
- [ ] Document any issues found
- [ ] Performance review
- [ ] Security review
- [ ] Plan Phase 2 features

---

## CRITICAL METRICS TO MONITOR

### System Health
| Metric | Target | Alert Threshold |
|--------|--------|-----------------|
| Uptime | 99.9% | < 99% |
| API Response | < 200ms p95 | > 500ms |
| Error Rate | < 0.1% | > 1% |
| DB Response | < 100ms p95 | > 300ms |
| CPU Usage | < 70% | > 85% |
| Memory | < 70% | > 85% |
| Disk Space | < 80% | > 90% |

### Application Metrics
| Metric | Expected | Alert Threshold |
|--------|----------|-----------------|
| Login Success | 99%+ | < 95% |
| API Errors | 0 critical | 1+ critical |
| Email Delivery | 99%+ | < 90% |
| Notification Delivery | 99%+ | < 90% |

---

## ROLLBACK DECISION MATRIX

| Issue | Severity | Action |
|-------|----------|--------|
| Minor UI bug | Low | Monitor, fix in patch |
| Single user can't login | Low | Monitor, investigate |
| 10% of users can't login | High | **ROLLBACK** |
| Database errors | Critical | **ROLLBACK** IMMEDIATELY |
| Data corruption detected | Critical | **ROLLBACK** IMMEDIATELY |
| Performance > 2 seconds | Medium | Investigate, then decide |
| 500 errors > 10/min | Critical | **ROLLBACK** IMMEDIATELY |

---

## SIGN-OFF

- **Deployed By**: ____________________
- **Deployment Date**: ____________________
- **Deployment Time**: ____________________
- **Status**: ☐ Success | ☐ Rollback
- **Issues Found**: ____________________
- **Next Steps**: ____________________

---

## APPENDIX: QUICK COMMANDS

### Before Deployment
```bash
php scripts/generate-key.php                    # Generate secure keys
php tests/TestRunner.php                        # Run all tests
php scripts/run-migrations.php --dry-run        # Test migrations (if --dry-run supported)
```

### During Deployment
```bash
mysqldump -u jira_user -p jira_production > backup.sql
php scripts/run-migrations.php
curl https://your-domain/api/v1/health
```

### After Deployment
```bash
tail -f storage/logs/application.log            # Monitor logs
mysql -u jira_user -p jira_production           # DB access
php scripts/send-notification-emails.php       # Test email cron
```

### Troubleshooting
```bash
# Check database connection
php -r "require 'bootstrap/autoload.php'; var_dump(Database::getConnection());"

# Check configuration
php -r "require 'bootstrap/autoload.php'; var_dump(config());"

# Clear cache
rm -rf storage/cache/*

# View error logs
tail -100 storage/logs/application.log
```

---

## SUPPORT & ESCALATION

### Tier 1 (Deployment Engineer)
- Monitor logs
- Check system health
- Basic troubleshooting
- Escalate if needed

### Tier 2 (System Admin)
- Database investigation
- Infrastructure issues
- Performance analysis
- Escalate if needed

### Tier 3 (Development Lead)
- Code issues
- Logic errors
- Data corruption
- Decision maker for rollback

---

**Deployment Checklist Version**: 1.0  
**Last Updated**: December 2025  
**Created By**: Production Team  
