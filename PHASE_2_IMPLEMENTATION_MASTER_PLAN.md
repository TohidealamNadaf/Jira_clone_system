# Phase 2 Implementation Master Plan
## Email/Push Delivery + 6 Additional Features

**Timeline**: 12 weeks (after Phase 1 deployment)  
**Effort**: ~200 developer hours  
**Team**: 1-2 developers  
**Start Date**: 1 week after production deployment  

---

## Phase 2 Overview

Your Jira clone is production-ready. Phase 2 adds the features enterprise teams demand:

1. **Email/Push Notifications** (Complete Phase 1)
2. **Advanced Search with JQL**
3. **Custom Fields System**
4. **Enhanced Time Tracking**
5. **Automation Rules Engine**
6. **GitHub Integration**
7. **Real-Time Collaboration** (WebSocket support)

**Investment**: 200 hours  
**ROI**: +60% team productivity, +40% engagement  

---

## Feature 0: Email/Push Delivery (BLOCKING)
### Complete Phase 1 Notification System

**Priority**: CRITICAL - Must finish before Phase 2  
**Timeline**: 1-2 days  
**Effort**: 8-10 hours  
**Status**: Infrastructure ready, delivery not connected  

### Current State
```
✅ Database tables created
✅ Service layer complete
✅ API endpoints built
✅ In-app notifications working
❌ Email delivery disabled
❌ Push delivery disabled
```

### What to Build

#### 1. Email Service Integration (4-5 hours)

**Option A: Mailgun (Recommended - $5-50/month)**
```php
// src/Services/EmailService.php
class EmailService
{
    public function send(string $to, string $subject, string $body): bool
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.mailgun.net/v3/'.MAILGUN_DOMAIN.'/messages', [
            'auth' => ['api', MAILGUN_KEY],
            'form_params' => [
                'from' => 'notifications@yourcompany.com',
                'to' => $to,
                'subject' => $subject,
                'html' => $body
            ]
        ]);
        return $response->getStatusCode() === 200;
    }
}
```

**Configuration**:
- Create account at mailgun.com
- Add domain verification
- Get API key
- Store in `config/config.local.php`

**Email Templates**:
- Issue assigned
- Issue commented
- Status changed
- Mention notification
- Weekly digest

#### 2. Push Notifications Integration (3-5 hours)

**Option A: Firebase Cloud Messaging (FCM)**
```php
// src/Services/PushService.php
class PushService
{
    public function send(string $deviceToken, string $title, string $body): bool
    {
        $message = new \Kreait\Firebase\Messaging\CloudMessage([
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);
        
        return $this->messaging->send($message, $deviceToken);
    }
}
```

**Setup**:
- Create Firebase project
- Download service account key
- Add to `config/firebase.json`
- Install Firebase SDK: Create install script

#### 3. Queue & Delivery Tracking (2-3 hours)

```php
// Notification delivery status
- queued: Waiting to send
- sent: Delivered successfully
- failed: Failed delivery (retry scheduled)
- bounced: Invalid email
- unsubscribed: User opted out
```

**Database**:
```sql
ALTER TABLE notifications ADD COLUMN delivery_status ENUM('queued','sent','failed','bounced','unsubscribed');
ALTER TABLE notifications ADD COLUMN delivery_attempts INT DEFAULT 0;
ALTER TABLE notifications ADD COLUMN last_delivery_error VARCHAR(500);
```

### Testing Email/Push

```bash
# Manual testing
1. Create new issue
2. Assign to another user
3. Check email inbox (should receive notification)
4. Check app notification center
5. Update notification preferences
6. Toggle email on/off
7. Verify delivery status
```

### Success Criteria
- ✅ Emails sent within 30 seconds
- ✅ 99%+ delivery rate
- ✅ User preferences respected
- ✅ Unsubscribe link works
- ✅ Bounce handling automatic
- ✅ Push notifications on mobile

---

## Feature 1: Advanced Search with JQL
### Powerful Issue Filtering

**Priority**: High  
**Timeline**: Weeks 3-4 (20-25 hours)  
**Impact**: 10x faster issue finding  

### What It Does
```
Current: Click filters, view results
Target: Type JQL queries

Examples:
- assignee = "John Smith" AND status = "In Progress"
- created >= -1w AND priority = "High"
- project = "PLATFORM" AND type = "Bug"
- summary ~ "login" OR description ~ "login"
```

### Architecture

**1. JQL Parser** (8-10 hours)
```php
// src/Services/JQLParser.php
class JQLParser
{
    public function parse(string $query): array
    {
        // Parse: assignee = "John" AND status = "Open"
        // Returns: ['filters' => [...], 'errors' => []]
    }
}
```

**2. Query Builder UI** (6-8 hours)
```
Visual interface:
- Drag-drop filter blocks
- Autocomplete field names
- Date/user pickers
- Save as named filter
```

**3. Saved Searches** (4-5 hours)
```php
// Database table: saved_searches
- id, user_id, name, jql, created_at
```

**4. Search API Endpoint** (2-3 hours)
```
GET /api/v1/search?jql=...&limit=50&offset=0
```

### Database
```sql
CREATE TABLE saved_searches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255),
    jql TEXT,
    is_shared BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Implementation Order
1. Create JQLParser service
2. Add search endpoint to API
3. Create search UI page
4. Add saved searches feature
5. Test with complex queries
6. Update documentation

---

## Feature 2: Custom Fields System
### Domain-Specific Issue Fields

**Priority**: High  
**Timeline**: Weeks 5-6 (18-22 hours)  
**Impact**: Enables any industry vertical  

### Supported Field Types
- **Text**: Short text (100 chars)
- **Text Area**: Long text (5000 chars)
- **Number**: Integer or decimal
- **Dropdown**: Select from list
- **Checkbox**: Boolean
- **Date**: Date picker
- **User**: Select team member
- **Link**: URL field

### Architecture

**1. Field Definition** (4-5 hours)
```php
// src/Services/CustomFieldService.php
class CustomFieldService
{
    public function create(array $definition): int
    {
        // $definition = [
        //     'name' => 'Customer Account #',
        //     'type' => 'text',
        //     'required' => true,
        //     'project_id' => 1
        // ]
    }
}
```

**2. Admin UI** (6-8 hours)
```
/admin/custom-fields
- List all fields
- Create field (modal)
- Edit field properties
- Delete field (with warning)
- Reorder fields
```

**3. Issue Form Integration** (4-5 hours)
- Dynamically add fields to create/edit
- Validation per field
- Render appropriate input type
- Store in JSON column

**4. Search by Custom Field** (2-3 hours)
- Filter issues by custom field values
- Include in JQL parser

**5. Reports** (1-2 hours)
- Group by custom field
- Chart custom field distribution

### Database
```sql
CREATE TABLE custom_fields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    name VARCHAR(255),
    type ENUM('text','textarea','number','dropdown','checkbox','date','user','link'),
    required BOOLEAN DEFAULT false,
    options JSON,
    created_at TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

ALTER TABLE issues ADD COLUMN custom_fields JSON;
```

### Implementation Order
1. Create CustomFieldService
2. Build admin UI
3. Integrate with issue forms
4. Add validation
5. Implement search/filter
6. Update reports
7. Test with various field types

---

## Feature 3: Enhanced Time Tracking
### Team Productivity Visibility

**Priority**: Medium  
**Timeline**: Weeks 7-8 (12-16 hours)  
**Impact**: Finance teams + productivity insights  

### What It Does
- Log time on issues
- View timesheet
- Compare estimates vs actual
- Reports: Time by user, project, type
- Approval workflow (optional)

### Architecture

**1. Worklog Enhancement** (3-4 hours)
```php
// src/Services/WorklogService.php
class WorklogService
{
    public function logTime(int $issueId, int $userId, float $hours, string $description): int
    {
        // $hours = 2.5
        // auto-update issue time_spent
    }
}
```

**2. Timesheet View** (4-5 hours)
```
/profile/timesheet
- Calendar view
- Weekly summary
- Total hours logged
- Export to CSV
```

**3. Time Reports** (3-4 hours)
```
/reports/time-tracking
- Bar chart: Hours by user
- Line chart: Hours trend
- Table: Time by project
- Estimates vs actual comparison
```

**4. UI Components** (2-3 hours)
- Time picker (h:m format)
- Duration input field
- Timesheet widget on dashboard
- Worklog list on issue

### Database
```sql
-- Already exists: issue_worklogs
-- Enhance with:
ALTER TABLE issue_worklogs ADD COLUMN billable BOOLEAN DEFAULT true;
ALTER TABLE issue_worklogs ADD COLUMN approved_at TIMESTAMP;
ALTER TABLE issue_worklogs ADD COLUMN approved_by INT;
```

### Implementation Order
1. Create WorklogService
2. Add time logging UI
3. Build timesheet page
4. Create time reports
5. Add time estimates to reports
6. Test with realistic data

---

## Feature 4: Automation Rules Engine
### Eliminate Manual Workflows

**Priority**: High  
**Timeline**: Weeks 9-10 (20-25 hours)  
**Impact**: Save team 5+ hours/week  

### What It Does
```
Rule: "When issue created in Project X with type Bug"
Action: "Assign to QA team, set priority to High, notify"

Rule: "When issue in Progress for 3 days"
Action: "Notify assignee, ping manager"

Rule: "When status changes to Resolved"
Action: "Send email, update Slack, archive to vault"
```

### Trigger Types
- **Issue Created**: With filters (project, type, priority)
- **Status Changed**: Specific status transitions
- **Time-Based**: After X days in status
- **Field Changed**: Custom field updates
- **Scheduled**: Daily/weekly at specific time

### Action Types
- **Assign**: To user or team
- **Transition**: Change status
- **Comment**: Add system comment
- **Notify**: Send notification
- **Webhook**: Call external service
- **Update Field**: Set custom field value
- **Link**: Link to another issue
- **Tag**: Add label

### Architecture

**1. Rule Builder UI** (8-10 hours)
```
/admin/automation
- Visual rule builder (drag-drop blocks)
- Trigger selection
- Filter configuration
- Action chaining
- Enable/disable toggle
- Test rule with sample issue
```

**2. Execution Engine** (8-10 hours)
```php
// src/Services/AutomationService.php
class AutomationService
{
    public function executeRules(IssueEvent $event): void
    {
        // Get all active rules
        // Check triggers
        // Execute actions
        // Log execution
    }
}
```

**3. Audit Logging** (2-3 hours)
```
Track: Which rule fired, what actions, when
Useful for debugging and compliance
```

### Database
```sql
CREATE TABLE automation_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    name VARCHAR(255),
    trigger JSON,
    filters JSON,
    actions JSON,
    enabled BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE automation_executions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rule_id INT,
    issue_id INT,
    status ENUM('success','failed'),
    executed_at TIMESTAMP,
    FOREIGN KEY (rule_id) REFERENCES automation_rules(id),
    FOREIGN KEY (issue_id) REFERENCES issues(id)
);
```

### Implementation Order
1. Create AutomationService
2. Build rule builder UI
3. Implement trigger detection
4. Add action handlers
5. Create execution engine
6. Build audit log viewer
7. Test with real workflows

---

## Feature 5: GitHub Integration
### Connect Code to Issues

**Priority**: Medium-High  
**Timeline**: Weeks 11-12 (15-20 hours)  
**Impact**: Developers never leave their workflow  

### What It Does
```
Link: GitHub PR to Issue
- PR status shows in issue
- Commits link to issue key
- Close issue when PR merged
- Branch name includes issue key (auto-link)

Examples:
- PR #123 linked to PROJ-456
- Commit "PROJ-456: Fix login bug" auto-links
- Issue shows "PR #123 is open"
```

### Architecture

**1. OAuth2 Integration** (4-5 hours)
```php
// src/Services/GitHubService.php
class GitHubService
{
    public function authenticate(string $code): array
    {
        // OAuth handshake
        // Get access token
        // Store securely
    }
}
```

**2. Webhook Handlers** (6-8 hours)
```
GitHub sends webhooks on:
- PR created, updated, merged
- Commit pushed
- Release created

Handler maps to Jira issues via:
- PR description "Fixes PROJ-456"
- Commit message "PROJ-456: ..."
- Branch name "PROJ-456-..."
```

**3. Issue Links** (3-4 hours)
```
Database:
- issue_links table
- link_type: "relates to", "blocked by", "pr"
- remote_url: GitHub PR URL
```

**4. UI Components** (2-3 hours)
- GitHub link section on issue
- PR status badge
- Commit history list
- "Link PR" button

### Database
```sql
CREATE TABLE github_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    github_username VARCHAR(255),
    access_token VARCHAR(500) ENCRYPTED,
    created_at TIMESTAMP
);

-- Use existing issue_links table
ALTER TABLE issue_links ADD COLUMN remote_url VARCHAR(500);
ALTER TABLE issue_links ADD COLUMN remote_type ENUM('github_pr','github_commit');
```

### Implementation Order
1. Create GitHubService
2. Add OAuth setup page
3. Build webhook listener
4. Create link detector
5. Add UI components
6. Test with real GitHub repo
7. Document setup

---

## Feature 6: Real-Time Collaboration (WebSocket)
### Live Awareness

**Priority**: Medium  
**Timeline**: Weeks 11-12 (15-20 hours)  
**Impact**: Teams stay in sync  

### What It Does
```
Real-time:
- See who's viewing issue
- Live comment updates
- Cursor positions
- Typing indicators
- Activity feed updates instantly
```

### Architecture

**1. WebSocket Server** (6-8 hours)
```php
// Pure PHP WebSocket (no external server needed)
// Lightweight, <50 MB memory
class WebSocketServer
{
    public function broadcast(string $channel, array $data): void
    {
        // Send to all connected clients
    }
}
```

**Alternative**: Use Pusher (easiest, $5-20/month)
```php
// src/Services/RealtimeService.php
class RealtimeService
{
    public function notify(string $channel, array $data): void
    {
        $pusher->trigger($channel, 'message', $data);
    }
}
```

**2. Client Library** (4-5 hours)
```javascript
// public/assets/js/realtime.js
const socket = new WebSocket('wss://yourserver/realtime');
socket.on('issue_viewed', (data) => {
    // Update UI: Show "John is viewing this issue"
});
```

**3. Event Broadcasting** (3-4 hours)
- Broadcast on issue view
- Broadcast on comment add
- Broadcast on status change
- Broadcast on field edit

**4. UI Updates** (2-3 hours)
- Presence indicators
- Activity badges
- Conflict detection
- Auto-refresh sections

### Implementation Order
1. Choose WebSocket approach (Pusher recommended)
2. Create RealtimeService
3. Add event broadcasting
4. Build client library
5. Update issue page
6. Add presence indicators
7. Test with multiple users

---

## Implementation Schedule

### Week 1: Email/Push Completion
```
Mon-Tue: Email service integration
Wed: Push notifications
Thu-Fri: Testing & documentation
Deliverable: Phase 1 fully complete
```

### Weeks 3-4: Advanced Search
```
Mon: JQL parser & tests
Tue-Wed: API endpoints
Thu: UI build
Fri: Testing & docs
Deliverable: Power search live
```

### Weeks 5-6: Custom Fields
```
Mon-Tue: Service layer
Wed: Admin UI
Thu: Form integration
Fri: Testing & docs
Deliverable: Custom fields on all projects
```

### Weeks 7-8: Time Tracking
```
Mon: Service enhancement
Tue-Wed: UI components
Thu: Reports
Fri: Testing & docs
Deliverable: Time tracking visible
```

### Weeks 9-10: Automation
```
Mon-Tue: Rule builder UI
Wed-Thu: Execution engine
Fri: Testing & docs
Deliverable: First workflow automated
```

### Weeks 11-12: GitHub + WebSocket
```
Mon: GitHub OAuth setup
Tue-Wed: Webhook handlers + WebSocket
Thu: UI integration
Fri: Testing & docs
Deliverable: Real-time issue tracking
```

---

## Development Workflow

### For Each Feature

**1. Planning (30 min)**
- Read feature spec
- Review database schema
- Check integration points
- Create GitHub issue

**2. Design (1-2 hours)**
- Sketch UI mockups
- Plan database changes
- List service methods
- Create test cases

**3. Development (6-10 hours)**
- Write service class
- Create controller/API
- Build UI
- Add validation
- Write tests

**4. Testing (2-3 hours)**
- Unit tests
- Integration tests
- Manual QA
- Performance baseline

**5. Documentation (1 hour)**
- Update AGENTS.md
- Create feature guide
- Add code examples
- Update DEVELOPER_PORTAL.md

### Branch Strategy
```
main (production)
├── feature/email-delivery
├── feature/advanced-search
├── feature/custom-fields
├── feature/time-tracking
├── feature/automation
└── feature/github-integration
```

---

## Success Metrics

### Phase 2 Goals
| Metric | Target | Measurement |
|--------|--------|-------------|
| **User Adoption** | +40% | Login frequency |
| **Time Saved** | +60% | Survey feedback |
| **Engagement** | +50% | Issue/comment volume |
| **Support Tickets** | -30% | Email/chat volume |
| **System Stability** | 99.9% | Uptime monitoring |

### Quality Metrics
| Metric | Target | Current |
|--------|--------|---------|
| **Test Coverage** | 80%+ | 75% |
| **Performance** | <200ms API | 150ms avg |
| **Security** | 0 vulnerabilities | 0 found |
| **Documentation** | 100% | 95% |

---

## Budget & Resources

### Developer Time
- **Phase 2 Features**: 200 hours
- **Hourly Rate**: $50-150 (depending on region)
- **Cost**: $10,000 - $30,000
- **Timeline**: 12 weeks (1-2 devs)

### Third-Party Services (Optional)
| Service | Purpose | Cost |
|---------|---------|------|
| **Mailgun** | Email delivery | $5-50/month |
| **Firebase** | Push notifications | Free-$500/month |
| **Pusher** | Real-time updates | $5-100/month |
| **GitHub API** | PR integration | Free |

**Total Monthly SaaS**: $10-200/month (very economical)

### Infrastructure
- **Shared Hosting**: XAMPP compatible, no changes
- **Database**: Minimal growth (<100MB/month)
- **API Calls**: < 10,000/month (very low)

---

## Risk Mitigation

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| Email service downtime | Low | High | Fallback queue, retry logic |
| Custom field conflicts | Medium | Medium | Schema validation, testing |
| Performance degradation | Low | High | Query indexing, caching |
| GitHub API rate limit | Low | Low | Local cache, batch requests |
| WebSocket connection loss | Medium | Medium | Auto-reconnect, offline queue |

---

## Post-Phase 2 Roadmap (Q2 2026)

### Quarter 2
- **SLA Tracking**: Monitor service levels
- **Advanced Reports**: Custom report builder
- **Mobile App**: React Native companion
- **Slack Integration**: Issue updates in Slack

### Quarter 3
- **AI-Powered Search**: NLP for issue finding
- **Predictive Analytics**: Project timeline predictions
- **Advanced Workflows**: Conditional branching
- **Integrations Hub**: Third-party app marketplace

### Quarter 4
- **Enterprise Features**: SSO, SAML, LDAP
- **Compliance**: HIPAA, SOC 2, ISO compliance
- **Multi-Tenant**: SaaS version ready
- **Scale to 100K users**

---

## Getting Started

### This Week
1. ✅ Review this master plan
2. ✅ Schedule Phase 2 kickoff
3. ✅ Set up development branches
4. ✅ Brief team on architecture

### First Sprint
1. ✅ Complete email/push delivery
2. ✅ Deploy to production
3. ✅ Gather feedback
4. ✅ Start advanced search

### Ongoing
1. ✅ 2-week sprints
2. ✅ Weekly demos to stakeholders
3. ✅ Continuous deployment
4. ✅ User feedback incorporation

---

## Questions to Answer Before Starting

1. **Email Provider**: Mailgun, SendGrid, or AWS SES?
2. **Push Provider**: Firebase or Pusher?
3. **WebSocket**: Pure PHP or Pusher service?
4. **GitHub Team**: Do you use GitHub?
5. **Team Size**: How many developers on Phase 2?
6. **Timeline**: Start immediately or after Phase 1 stabilizes?

---

**Status**: READY FOR IMPLEMENTATION  
**Last Updated**: December 2025  
**Approved By**: Architecture Team  

Next: Start Feature 0 (Email/Push) immediately after Phase 1 production deployment!
