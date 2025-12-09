# Jira Clone System - Quick Reference Card

Print this or bookmark for fast access to common tasks.

---

## 🚀 Access & Login

| Item | Value |
|------|-------|
| **URL** | `http://localhost/jira_clone_system/public/` |
| **Admin Email** | `admin@example.com` |
| **Admin Password** | `Admin@123` |
| **Test User Email** | `user@example.com` |
| **Test User Password** | `User@123` |

---

## 📊 Main Pages (Navbar)

| Page | Icon | Purpose |
|------|------|---------|
| **Dashboard** | 📈 | Project overview & stats |
| **Projects** | 🗂️ | All projects list |
| **Search** | 🔍 | Find issues |
| **Reports** | 📊 | Analytics & metrics |
| **Notifications** | 🔔 | Message inbox |
| **Profile** | 👤 | Personal settings |
| **Admin** | ⚙️ | System management (admin only) |

---

## 🎯 Issue Types & Priorities

### Issue Types
- **Story** - User requirement/feature
- **Task** - Generic work
- **Bug** - Defect/error
- **Epic** - Large feature
- **Sub-task** - Child issue

### Priority Levels
```
🔴 Blocker > 🟠 High > 🟡 Medium > 🟢 Low
```

---

## 📋 Issue Workflow States

```
To Do → In Progress → In Review → Done
```

**How to Change Status:**
- On Board: Drag issue card between columns
- On Issue Detail: Click status button, select new status

---

## ⚡ Quick Actions

### Create New Issue
```
Click "Create" (navbar) → Select Project → Fill Details → Submit
```

### Add Comment
```
Issue Detail → Scroll to Comments → Type & Submit
```

### Log Time
```
Issue Detail → Log Work → Enter Hours → Submit
```

### Assign Issue
```
Issue Detail → Assignee → Select Team Member → Save
```

### Watch Issue
```
Issue Detail → Click Watch Button (bell icon)
```

### Create Sprint
```
Project → Sprints → Create Sprint → Add Issues → Start
```

---

## 🔔 Notification Management

**View Notifications:**
- Bell icon (navbar) → Click notification → Goes to issue

**Configure Preferences:**
- Profile → Notifications → Toggle channels → Save

**Mention Someone:**
- In comment: Type `@username` → Select from dropdown

---

## 📊 Common Reports

| Report | Access | Use |
|--------|--------|-----|
| **Sprint** | Reports → Sprint | Sprint progress |
| **Velocity** | Reports → Velocity | Historical trends |
| **Burndown** | Reports → Burndown | Sprint remaining work |
| **Time Logged** | Reports → Time Logged | Team hours |
| **Priority** | Reports → Priority | Issue distribution |

---

## 👥 Team Management (Admin)

| Task | Steps |
|------|-------|
| **Add User** | Admin → Users → Create User → Fill → Save |
| **Edit User** | Admin → Users → Click User → Modify → Save |
| **Deactivate User** | Admin → Users → Click Menu → Deactivate |
| **Assign Role** | Admin → Users → Edit User → Select Role → Save |
| **Create Role** | Admin → Roles → Create → Set Permissions → Save |

---

## 🔐 User Roles

| Role | Best For | Capabilities |
|------|----------|--------------|
| **Admin** | System owner | Everything |
| **Developer** | Dev team | Issues, comments, time log |
| **PM** | Team lead | Projects, sprints, team |
| **QA** | Test team | Create issues, reports |
| **Viewer** | Stakeholders | Read-only access |

---

## 🔗 Project Member Roles

When adding member to project:
- **Admin** - Full control
- **Developer** - Create/edit issues
- **Contributor** - Limited access
- **Viewer** - Read-only

---

## 💡 Pro Tips

| Tip | Benefit |
|-----|---------|
| Pin projects to sidebar | Quick access |
| Save custom filters | Reuse searches |
| Watch important issues | Get notifications |
| Use labels for tagging | Better organization |
| Log time daily | Accurate tracking |
| Use sprints | Better planning |
| Review reports weekly | Track progress |

---

## 🔍 Search Syntax

| Query | Result |
|-------|--------|
| `text` | Issues containing text |
| `status:Done` | Issues with Done status |
| `assignee:username` | Issues assigned to user |
| `priority:High` | High priority issues |
| `label:bug` | Issues with bug label |
| `created >= 2025-01-01` | Issues created after date |

---

## 📱 Keyboard Shortcuts

```
Ctrl+K     Quick search
G + P      Go to Projects
G + D      Go to Dashboard
G + R      Go to Reports
Escape     Close modal/dialog
Enter      Submit form
```

---

## 🆘 When Things Go Wrong

### Can't Log In
→ Check email/password  
→ Use "Forgot Password" link  
→ Contact admin

### Can't See Project
→ Ask to be added as member  
→ Check project visibility  
→ Verify your role

### Issue Not on Board
→ Check issue status  
→ Clear filters  
→ Refresh page

### No Notifications
→ Check preferences (Profile → Notifications)  
→ Verify you're watching issue  
→ Check if issue has changes

### Permission Denied
→ Check your role  
→ Ask admin for permission  
→ Verify project membership

---

## 📞 Common Contacts

| Need | Contact |
|------|---------|
| Can't log in | System Admin |
| Missing from project | Project Lead |
| Bug in system | IT Team |
| Feature request | Product Owner |
| Access issue | Admin Panel |

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `TEAM_DOCUMENTATION.md` | Full feature guide |
| `AGENTS.md` | Development standards |
| `/api/docs` | REST API reference |
| `DEVELOPER_PORTAL.md` | Tech navigation |

---

## 🎓 Learning Path

### Day 1: Basics
1. Log in to system
2. View Dashboard
3. Explore Projects
4. Look at one Board

### Day 2: Working with Issues
1. Create an issue
2. Add comment
3. Assign to someone
4. Change status on board
5. Watch issue

### Day 3: Sprints & Reports
1. View sprint schedule
2. Look at velocity report
3. Check burndown chart
4. Run custom report

### Day 4: Collaboration
1. Use search filters
2. Mention a teammate
3. Create custom filter
4. Log time on issue

### Day 5: Mastery
1. Plan a sprint
2. Generate sprint report
3. Configure notifications
4. Explore API

---

## ✅ Daily Checklist

- [ ] Check dashboard
- [ ] Review notifications
- [ ] Update issue status
- [ ] Add progress comment
- [ ] Log time spent
- [ ] Check team's activity

---

## 🚀 Getting Started

1. **Day 1:** Read "System Overview" section in full documentation
2. **Day 2:** Complete "Learning Path" above
3. **Day 3:** Refer to "Common Tasks" for specific help
4. **Ongoing:** Use this quick reference for quick lookups

---

**Last Updated:** December 2025  
**Status:** Ready for Production  
**Questions?** Contact your System Administrator
