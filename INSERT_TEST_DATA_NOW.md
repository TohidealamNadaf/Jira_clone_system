# Insert Test Data NOW - 30 Seconds

## 🚀 FASTEST WAY TO INSERT DATA

Open your browser and visit this URL:

```
http://localhost:8080/jira_clone_system/public/run-seed-now.php
```

That's it! The script will:
- ✅ Create 5 projects (ECOM, MOB, API, DEVOPS, QA)
- ✅ Create 32 issues with various statuses
- ✅ Add 10+ overdue issues (for testing)
- ✅ Add issues with all due dates (today, tomorrow, future)
- ✅ Add 30+ comments
- ✅ Add 20+ work logs
- ✅ All formatted descriptions ready

---

## What Gets Inserted

### 5 Projects
1. **ECOM** - E-Commerce Platform (10 issues)
2. **MOB** - Mobile App (5 issues)
3. **API** - Backend API (4 issues)
4. **DEVOPS** - DevOps Infrastructure (3 issues)
5. **QA** - QA & Testing (3 issues)

### Issues by Status
- 🔴 **Open**: 20 issues
- 🟡 **In Progress**: 8 issues
- ✅ **Done**: 7 issues

### Overdue Issues (For Testing)
- ECOM-1: 5 days overdue
- ECOM-2: 2 days overdue
- ECOM-3: 3 days overdue
- MOB-1: 4 days overdue
- API-1: 3 days overdue
- + more...

### By Due Date
- 🚨 **Overdue**: 10+ issues
- ⚡ **Due Today**: 2 issues
- 📅 **Due Soon** (1-3 days): 4 issues
- 📆 **Future** (7+ days): 8 issues

---

## After Insertion

1. ✅ Visit Dashboard: `http://localhost/jira_clone_system/public/`
2. ✅ Check overdue count (should be 10+)
3. ✅ Navigate to projects
4. ✅ View Kanban board
5. ✅ Filter and sort issues
6. ✅ Check reports

---

## Verify Data Was Inserted

Check the output page shows:
```
✅ Projects Created: 5
✅ Total Issues: 32
✅ Overdue Issues: 10+
✅ Comments Added: 30+
✅ Work Logs Added: 20+
```

Then click: **✅ Go to Dashboard to View Data**

---

## What You Can Test Now

### Dashboard
- [ ] See all projects
- [ ] Overdue count displayed
- [ ] Due soon items listed
- [ ] Team workload shown

### Projects
- [ ] All 5 projects visible
- [ ] Each has issues
- [ ] Statistics display
- [ ] Team members show

### Issues
- [ ] Overdue in red
- [ ] Due dates show
- [ ] Status colors correct
- [ ] Priority badges visible
- [ ] Formatted descriptions display

### Board
- [ ] Drag/drop issues
- [ ] Status updates
- [ ] All columns visible
- [ ] Count badges update

### Search & Filter
- [ ] Filter by status
- [ ] Filter by priority
- [ ] Filter by assignee
- [ ] Search by text
- [ ] Sort by due date

### Reports
- [ ] Created vs Resolved
- [ ] Resolution time
- [ ] Priority breakdown
- [ ] Team workload
- [ ] Time logged

---

## One Click Setup

**Just visit this URL and wait 10 seconds:**

👇 **CLICK HERE** 👇
```
http://localhost:8080/jira_clone_system/public/run-seed-now.php
```

Done! All test data inserted.

---

## If You Need to Delete Data

Run these SQL commands:

```sql
DELETE FROM comments WHERE issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')));

DELETE FROM worklogs WHERE issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')));

DELETE FROM issue_links WHERE from_issue_id IN (SELECT id FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA')));

DELETE FROM issues WHERE project_id IN (SELECT id FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA'));

DELETE FROM projects WHERE `key` IN ('ECOM', 'MOB', 'API', 'DEVOPS', 'QA');
```

---

✅ **Ready to test the system with real data!**
