# Role-Specific User Guides

Guides tailored for each user role in the system.

---

## Table of Contents
1. [Administrator Guide](#administrator-guide)
2. [Project Manager Guide](#project-manager-guide)
3. [Developer Guide](#developer-guide)
4. [QA/Tester Guide](#qatester-guide)
5. [Viewer/Stakeholder Guide](#viewerstakeholder-guide)

---

## Administrator Guide

**Role:** System Owner - Full system access and control

### Your Responsibilities
- Manage user accounts
- Configure system settings
- Monitor system health
- Manage roles and permissions
- Handle user support issues
- Configure email and notifications

### Admin Dashboard (`/admin`)

**What You See:**
- Total users count
- Total projects count
- Total issues count
- Storage usage statistics

### User Management (`/admin/users`)

#### Creating a New User
```
Admin → Users → Create User → Fill Form → Save
```

**Fields:**
- **Name** - Full name of user
- **Email** - Unique email address
- **Password** - Initial password (user should change)
- **Role** - Default system role (Developer, PM, QA, Viewer)
- **Is Active** - Toggle to activate/deactivate

**After Creation:**
- Share login credentials with user
- Tell them to change password on first login
- Add to projects as needed

#### Deactivating a User
- Click user → Click Menu (⋯) → Deactivate
- User can't log in but data remains

#### Reactivating a User
- Click user → Click Menu (⋯) → Activate
- User can log in again

#### Deleting a User
- Click user → Click Delete (⚠️ permanent)
- All user data is removed
- Consider deactivating instead

#### Editing User Details
- Click user from list
- Modify name, role, status
- Click Save

### Role Management (`/admin/roles`)

#### Understanding System Roles
```
👑 Administrator   - Full system access (protected, can't edit)
👨‍💻 Developer        - Create issues, update status, comment
📋 Project Manager  - Manage projects & teams
🧪 QA Tester        - Create issues, view reports
👁️  Viewer          - View-only access
```

**System roles are protected** - You can see them but can't edit/delete them.

#### Creating Custom Roles
```
Admin → Roles → Create Role → Set Name & Permissions → Save
```

**Permissions Available:**
- Projects: Create, View, Edit, Delete
- Issues: Create, View, Edit, Delete, Assign
- Sprints: Create, View, Edit, Delete
- Reports: View Reports
- Boards: View, Create, Edit
- Attachments: Upload, Download
- Comments: Create, Edit, Delete
- Time Tracking: Log time
- Admin: (users, roles, system settings)

#### Editing Custom Roles
- Click role from list
- Modify permissions
- Click Save
- Changes apply to all users with that role

#### Deleting Custom Roles
- Click Delete button
- Can't delete if users have this role
- Reassign users to another role first

### Global Permissions (`/admin/global-permissions`)

System-wide permission descriptions for all features.

**What It Shows:**
- List of all system permissions
- Description of each permission
- Who has access to what

**To Edit:**
- Click Edit
- Modify descriptions
- Click Save

### Project Categories (`/admin/project-categories`)

Create categories for organizing projects.

#### Create Category
```
Admin → Project Categories → Create → Enter Name → Save
```

#### Use in Projects
- When creating project, select category
- Helps organize large project lists
- Users can filter by category

### Issue Types (`/admin/issue-types`)

Manage types of issues (Story, Task, Bug, Epic, Sub-task).

#### Create Custom Issue Type
```
Admin → Issue Types → Create → Set Name & Properties → Save
```

**Properties:**
- **Icon** - Visual representation (select from library)
- **Color** - For board visibility
- **Description** - Explain when to use
- **Is Subtask** - If it's a sub-issue type

#### Edit Issue Type
- Modify color, icon, description
- Changes apply to new issues
- Existing issues keep original type

#### Delete Issue Type
⚠️ Can't delete if issues exist with this type

### System Settings (`/admin/settings`)

#### Email Configuration

**For Email Notifications to Work:**
1. Go to Settings
2. Choose email provider:
   - SendGrid API
   - Mailgun API
   - Custom SMTP

3. Configure:
   - SMTP Host
   - SMTP Port (usually 587 or 465)
   - Username
   - Password/API Key

4. Test with "Send Test Email"
5. Save configuration
6. Users can now receive email notifications

#### Other Settings
- System name and description
- Default language
- Time zone
- Date format

### Audit Log (`/admin/audit-log`)

Track all system changes:
- Who changed what
- When it was changed
- What changed (before/after values)
- IP address

**Use Cases:**
- Troubleshoot user issues
- Track who deleted something
- Security auditing
- Compliance reporting

### Projects Management (`/admin/projects`)

View all projects in system:
- Project name and key
- Number of issues
- Number of team members
- Project category
- Created date

**Actions:**
- Search projects by name/key
- Filter by category
- View project details
- Sort columns

---

## Project Manager Guide

**Role:** Team Lead - Manage projects, sprints, and teams

### Your Responsibilities
- Create and manage projects
- Plan sprints and iterations
- Manage team members
- Monitor progress with reports
- Remove blockers for team
- Ensure project success

### Creating a New Project

```
Dashboard → Projects → Create Project → Fill Details → Create
```

**Required Fields:**
- **Project Name** - "Mobile App", "Backend API", etc.
- **Project Key** - UPPERCASE, 2-5 letters (AUTO: MOBILE, API)
- **Description** - What project does
- **Category** - Optional (organize similar projects)

**After Creation:**
- Project appears in your projects list
- Customize board columns in settings
- Add team members
- Create first sprint
- Create initial issues

### Managing Team Members

#### Add Member to Project
```
Project → Members → Add Member → Select User → Choose Role → Add
```

**Role Options:**
- **Admin** - Full control of project
- **Developer** - Can work on issues
- **Contributor** - Limited access
- **Viewer** - Read-only

#### Remove Member
```
Project → Members → Find Member → Click Remove
```

#### Change Member Role
```
Project → Members → Find Member → Edit Role → Save
```

### Sprint Planning

#### Create Sprint
```
Project → Sprints → Create Sprint → Set Dates → Create
```

**Sprint Setup:**
- **Sprint Name** - "Sprint 1", "Q1 Sprint 2", etc.
- **Start Date** - When sprint begins
- **End Date** - When sprint ends (typically 2 weeks)
- **Sprint Goal** - What you want to accomplish

#### Add Issues to Sprint
1. Go to Project → Backlog
2. Drag issues from "Backlog" into sprint column
3. Prioritize with drag-and-drop order
4. Estimate story points if using points

#### Start Sprint
```
Sprints → Find Sprint → Click Start → Confirm
```

When started:
- Issues move to board
- Team can see on Kanban view
- Burndown tracking begins
- Clock starts counting

#### Sprint Execution (Daily)
- Team pulls issues from "To Do"
- Moves to "In Progress" when working
- Reports progress in standup
- You monitor burndown chart
- Resolve blockers for team

#### Complete Sprint
```
Sprints → Find Sprint → Click Complete → Confirm
```

When completed:
- Issues in "Done" are archived
- Velocity is calculated
- Historical metrics are saved
- New sprint can begin

### Monitoring Progress

#### Sprint Dashboard
```
Project → Board → View Live Board
```

**What to Monitor:**
- Issues in "In Progress" (should be moving)
- Issues stuck in "In Review"
- Any issues moved back to "To Do"
- Unstarted issues in sprint

#### Burndown Chart
```
Reports → Burndown → Select Sprint
```

**Ideal Burndown:**
- Line starts high (at sprint start)
- Decreases steadily throughout sprint
- Reaches zero by sprint end

**Red Flags:**
- Flat line (no progress)
- Line going up (adding work mid-sprint)
- Steep drops (unrealistic)

#### Velocity Report
```
Reports → Velocity
```

**What It Shows:**
- Story points completed per sprint
- Historical trend
- Team capacity

**Use For:**
- Future sprint planning
- Adjusting team size
- Setting realistic goals

### Handling Team Issues

#### Unblocking Stuck Issues
1. Notice issue not progressing
2. Talk to assignee
3. Help remove obstacles
4. Update team on solution
5. Follow up after resolution

#### Reassigning Work
1. If person overloaded, reassign some issues
2. Go to issue → Change Assignee
3. Communicate change to both people

#### Velocity Problems
1. If too many issues incomplete:
   - Reduce scope for next sprint
   - Break down large issues
   - Increase team size if possible

2. If too fast (completing extra):
   - Increase sprint complexity
   - Add stretch goals
   - Maintain consistent pace

### Reporting to Leadership

#### Sprint Report
```
Reports → Sprint Report
```

Shows:
- Total issues planned
- Issues completed
- Issues in progress
- Issues not started

#### Metrics to Share
- **Velocity** - Points completed per sprint
- **Burn rate** - How fast completing work
- **Quality** - Bug-to-feature ratio
- **Team morale** - Informal feedback

#### Generating Report
1. Go to Reports
2. Select type (Sprint, Velocity, etc.)
3. Choose date range
4. View/export results

---

## Developer Guide

**Role:** Team Member - Create and update issues, collaborate

### Your Daily Workflow

#### Morning (10 min)
```
1. Log in
2. Check Dashboard for updates
3. Look at notifications
4. Review your assigned issues
```

#### During Day (Continuous)
```
1. Pick issue from Board (To Do)
2. Move to In Progress
3. Work on issue
4. Add progress comments
5. When done, move to In Review (or Done)
```

#### End of Day (5 min)
```
1. Update issue status
2. Add comments about blockers
3. Log time spent
```

### Working on an Issue

#### Picking Your Next Issue
1. Go to Project → Board
2. Look at "To Do" column
3. Find unassigned issue OR ask for assignment
4. Click to view details
5. Click "Assign to me"

#### Reading Issue Details
- **Title** - What needs to be done
- **Description** - Detailed requirements
- **Comments** - Team discussion
- **Attachments** - Design files, screenshots
- **Linked Issues** - Related work

#### Starting Work
1. On Board: Drag issue to "In Progress"
2. Or click Status button on issue detail
3. Tell team you're working on it (comment)

#### Making Progress
- Add comments with updates
- Share blockers if stuck
- Link related issues if discovered
- Update status when moving between columns

#### Completing Work
1. Make sure all requirements met
2. On Board: Drag to "In Review" or "Done"
3. Add comment that it's complete
4. Ask for review if needed

#### Getting Unblocked
1. Add comment: `@projectlead I'm blocked on X`
2. Mention specific blocker
3. Wait for response
4. PM will help resolve

### Logging Time

#### Log Daily Time Spent
```
Issue → Log Work → Enter Hours → Enter Date → Save
```

**Why Logging Time Matters:**
- Helps team understand effort
- Feeds velocity calculations
- Tracks team capacity
- Estimates get more accurate

**Good Practice:**
- Log same day work was done
- Be reasonably accurate (doesn't need to be exact)
- Include description if needed

### Collaborating

#### Adding Comments
1. Scroll to "Comments" section
2. Click comment box
3. Type message
4. Press Enter or click Send

#### Mentioning Teammates
- Type `@` then person's name
- Select from dropdown
- They'll get notification
- Good for questions/blockers

#### Asking for Help
```
@john Need help understanding the spec for issue. Can you review?
```

#### Requesting Review
```
@qa This is ready for testing. Please verify it works as expected.
```

### Using Attachments

#### Add to Issue
1. Scroll to "Attachments" section
2. Drag file or click upload
3. File appears in list

**When to Attach:**
- Screenshots showing problem
- Design files
- Log files from error
- Test data

#### Add to Comment
1. In comment, click paperclip icon
2. Select file
3. Appears inline with comment

### Watching Issues

#### Why Watch?
- Get notifications of changes
- See when others comment
- Alerted to status changes

#### Watch Issue
```
Issue Detail → Click Watch Button (bell icon)
```

#### Unwatch Issue
```
Issue Detail → Click Watch Button Again to toggle off
```

### Understanding Sprint Workflow

#### Sprint States Explained
```
To Do         - Not started yet
In Progress   - You're actively working
In Review     - Waiting for approval/testing
Done          - Completed and closed
```

#### Move Through States
**Drag on Board:**
- Pick issue from To Do
- Drag to In Progress (you start work)
- Drag to In Review (when for review)
- Drag to Done (when complete)

**Use When:**
- To Do → In Progress: When you start working
- In Progress → In Review: When code/work is ready
- In Review → Done: When approved/verified

### Common Developer Tasks

#### Create Sub-task
```
Issue Detail → Create Sub-task → Fill Details → Create
```

Use for breaking down large issues.

#### Link Related Issue
```
Issue Detail → Link Issue → Select Type → Choose Issue → Link
```

Types:
- Duplicates
- Related to
- Blocks
- Is blocked by
- Depends on

#### Vote on Issue
```
Issue Detail → Click Vote Button (thumbs up)
```

Shows importance to team.

#### Update Estimate
```
Issue Detail → Time Estimate → Enter Hours → Save
```

Helps future planning.

---

## QA/Tester Guide

**Role:** Quality Assurance - Create issues, test features, report bugs

### Your Responsibilities
- Test features and functionality
- Report bugs with clear steps
- Verify fixes are working
- Ensure quality standards
- Create test cases

### Creating Bug Reports

#### Create Issue for Bug Found
```
Dashboard → Create Issue → Select Project → Type: Bug
```

**Required Information:**
1. **Title** - Clear description of problem
   - ✅ "Login button not working on mobile"
   - ❌ "Bug"

2. **Description** - Detailed explanation
   - What were you doing?
   - What happened?
   - What should happen?
   - Browser/device info

3. **Priority** - How critical?
   - Blocker: App crashes
   - High: Major feature broken
   - Medium: Feature partially broken
   - Low: Minor issue

4. **Attachment** - Screenshot showing problem
   - Click attachment
   - Upload screenshot
   - Helps reproduce faster

#### Example Bug Report
```
Title: "Email validation error message not displaying"

Description:
Steps to Reproduce:
1. Go to Create Issue form
2. Enter invalid email in assignee field
3. Click Create
4. Error message should appear

Expected: Red error message below email field
Actual: Form submits without validation

Browser: Chrome 120 on Windows 11
Attachment: screenshot_error.png
```

### Testing Features

#### Test Checklist
Before marking issue "Done":
1. Does it meet requirements?
2. Does it work on different browsers?
3. Does it work on mobile?
4. Are there error messages?
5. Does performance seem acceptable?

#### Create Test Case Issue
```
Create Issue → Type: Task → Title: "Test: Feature Name"
```

List detailed test steps:
1. Step 1
2. Step 2
3. Expected result
4. Actual result
5. Notes

### Verifying Fixes

#### Testing Fix
1. Developer says issue fixed
2. Get code/version with fix
3. Manually test steps from original bug report
4. Does it work now?

#### Approve Fix
```
Issue Detail → Add Comment: "✅ Verified - working correctly"
Move to Done
```

#### Reject Fix
```
Issue Detail → Add Comment: "❌ Still broken - see attached screenshot"
Move back to In Progress
```

### Using Reports for Testing

#### What Reports Show
- **Priority Breakdown** - See high-priority bugs
- **Time Logged** - How much testing effort
- **Created vs Resolved** - Bug fix rate
- **Resolution Time** - How long bugs take to fix

#### Monitor Bug Trends
```
Reports → Created vs Resolved
```

- Is bug count increasing or decreasing?
- Are fixes keeping up with reports?
- What's the trend over time?

### Regression Testing

After updates, test that old features still work:

1. Get list of previously reported bugs
2. Re-run their test steps
3. Confirm they still work
4. Report any regressions as NEW bugs

### Best Practices

#### Good Bug Reports
- ✅ Clear, specific title
- ✅ Steps to reproduce
- ✅ Expected vs actual behavior
- ✅ Environment info (browser, OS)
- ✅ Screenshots or attachments

#### Poor Bug Reports
- ❌ "It's broken"
- ❌ No reproduction steps
- ❌ No expected behavior
- ❌ Screenshots without context
- ❌ Vague descriptions

#### Effective Testing
- Test happy path (normal use)
- Test edge cases (boundaries)
- Test error cases (wrong input)
- Test on multiple devices/browsers
- Test with different data sets

---

## Viewer/Stakeholder Guide

**Role:** Read-Only Access - View projects and issues, monitor progress

### Your Responsibilities
- Monitor project progress
- Understand team's work
- Participate in planning (if invited)
- Review reports and metrics

### What You Can See

#### Dashboard
```
Go to Dashboard
```

Shows:
- All projects
- Recent activity
- Issue counts by status
- Team assignments

#### Projects
```
Go to Projects
```

View all projects you have access to.

#### Issues in Project
```
Go to Project → View Issues
```

See all work items in project.

#### Project Board
```
Go to Project → Board
```

Kanban view of work:
- See what's in progress
- See what's done
- Monitor team progress

### Monitoring Progress

#### Sprint Progress
```
Project → Board → View Board
```

Live board shows:
- Issues in each status column
- How many items done vs to do
- Visual progress indicator

#### Burndown Chart
```
Reports → Burndown
```

Shows:
- Ideal progress (ideal line)
- Actual progress (actual line)
- Are we on track?

**Interpreting Burndown:**
- Line above ideal = Behind schedule
- Line below ideal = Ahead of schedule
- Flat line = No progress

#### Team Velocity
```
Reports → Velocity
```

Shows:
- Historical speed (points/sprint)
- Is team consistent?
- Can we predict completion?

### Understanding Reports

#### Created vs Resolved Report
Shows bug/issue trends:
- More created than resolved = Getting behind
- More resolved than created = Catching up
- Balanced = Healthy rate

#### Priority Breakdown
Shows where focus is:
- How many blockers/critical?
- How many normal/low?
- Balance of work

#### Time Logged Report
Shows team effort:
- Total hours spent
- Per person breakdown
- Compare to estimates

### Participating in Meetings

#### Sprint Planning
- You might be invited
- Listen to team's sprint goal
- Ask clarifying questions
- Help prioritize features

#### Sprint Review (Demo)
- Watch team demo completed work
- Ask questions
- Provide feedback
- See actual progress

#### Standups
- If you attend:
  - Listen to updates
  - Don't interrupt
  - Note blockers
  - Help if you can assist

### What You Cannot Do

As Viewer, you cannot:
- ❌ Create issues
- ❌ Edit issues
- ❌ Assign work
- ❌ Change status
- ❌ Delete anything
- ❌ Access admin panel

**Why?** Keeps stakeholders in monitoring role, prevents accidental changes.

### Asking Questions

#### Ask About Issue
1. Go to issue detail
2. Scroll to Comments
3. Type question with `@username`
4. They get notification
5. Will respond in comment thread

#### Ask About Project
1. Go to Project → Activity
2. See recent changes
3. Click change to see details
4. Ask in comments

### Best Practices

#### When Reporting Issues You Find
- Use Create Issue function
- Be specific about problem
- Include how you found it
- Attach screenshot if possible

#### When Asking Questions
- @mention the right person
- Be specific about question
- Include context
- Ask in issue comments (not separate email)

#### When Attending Meetings
- Come prepared
- Ask questions constructively
- Listen more than talk
- Respect team's decisions

---

## Summary Table

| Role | Create | Edit | Delete | Admin | View |
|------|--------|------|--------|-------|------|
| **Admin** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **PM** | ✅ | ✅ | ✅* | ❌ | ✅ |
| **Developer** | ✅ | ✅* | ✅* | ❌ | ✅ |
| **QA** | ✅ | ❌ | ❌ | ❌ | ✅ |
| **Viewer** | ❌ | ❌ | ❌ | ❌ | ✅ |

*Own items only

---

**Last Updated:** December 2025

**Questions?** See TEAM_DOCUMENTATION.md for full details.
