# Daily Standup Template

**Project**: Jira Clone - Production Build  
**Time**: 10:00 AM Daily  
**Duration**: 15 minutes  
**Format**: Synchronous (Team meeting)  

---

## Instructions

Each team member should be prepared to answer these 3 questions in ~2 minutes:

1. **What did I accomplish yesterday?**
   - Specific tasks completed
   - PRs merged or in review
   - Bugs fixed
   - Blockers removed

2. **What will I work on today?**
   - Specific task(s) from TEAM_ACTIVITIES_100_TASKS.md
   - Expected completion time
   - Dependencies needed

3. **What blockers or help do I need?**
   - Waiting on someone else?
   - Technical problems?
   - Need code review?
   - Resource shortage?

---

## Daily Standup Format

```
═══════════════════════════════════════════════════════════════════
  DAILY STANDUP - [DATE] - 10:00 AM
═══════════════════════════════════════════════════════════════════

TEAM MEMBER: [Name] | ROLE: [Backend/Frontend/QA/DevOps]
─────────────────────────────────────────────────────────────────

YESTERDAY'S ACCOMPLISHMENTS:
  ✅ Task 1: [Specific achievement]
  ✅ Task 2: [Specific achievement]
  
TODAY'S PLAN:
  🎯 Task X: [Task name] - Est. 3 hours
  🎯 Task Y: [Task name] - Est. 2 hours
  
BLOCKERS / HELP NEEDED:
  ⚠️  [Specific issue or request]
  
NOTES:
  • [Any additional context]

─────────────────────────────────────────────────────────────────
TIME: 2 min 30 sec | STATUS: [ON TRACK / AT RISK / BLOCKED]
═══════════════════════════════════════════════════════════════════
```

---

## Weekly Standup Summary

Hold a weekly summary on **Friday 4:00 PM**:

```
WEEK OF: [DATE]

TEAM METRICS:
  Total Tasks Completed: [#]
  Total Tasks Planned: [#]
  Completion Rate: [%]
  Blockers Outstanding: [#]
  Critical Issues: [#]

BY TEAM:
  ✅ Backend: [# tasks] completed
  ✅ Frontend: [# tasks] completed
  ✅ QA: [# tasks] completed
  ✅ DevOps: [# tasks] completed

UPCOMING RISKS:
  ⚠️  [Risk 1] - Impact: [HIGH/MEDIUM/LOW]
  ⚠️  [Risk 2] - Impact: [HIGH/MEDIUM/LOW]

NEXT WEEK FOCUS:
  🎯 [Priority 1]
  🎯 [Priority 2]
  🎯 [Priority 3]
```

---

## Sample Standups

### Backend Engineer
```
NAME: John | ROLE: Backend

YESTERDAY:
  ✅ Fixed Create button issue (Task 1) - 1 hour
  ✅ Implemented email service integration (Task 12) - 3 hours
  ✅ Code review for Task 4 - 1 hour

TODAY:
  🎯 Task 13: Email template rendering - Est. 2 hours
  🎯 Task 14: SMTP configuration - Est. 1.5 hours

BLOCKERS:
  ⚠️  Need SMTP credentials from DevOps for testing

STATUS: ON TRACK
```

### Frontend Engineer
```
NAME: Sarah | ROLE: Frontend

YESTERDAY:
  ✅ Completed Activity page redesign (Task 21) - 3 hours
  ✅ Mobile responsive testing on Activity - 1 hour
  ✅ Merged dark mode PR

TODAY:
  🎯 Task 22: Reports page redesign - Est. 4 hours
  🎯 Task 23: User profile styling - Est. 2 hours

BLOCKERS:
  ⚠️  Chart.js library not loading - investigating

STATUS: AT RISK (minor)
```

### QA Engineer
```
NAME: Mike | ROLE: QA

YESTERDAY:
  ✅ Completed unit tests for AuthService (Task 51) - 2 hours
  ✅ Created 10 test cases for comment edit/delete - 1.5 hours
  ✅ Verified board drag-and-drop

TODAY:
  🎯 Task 52: Unit tests for IssueService - Est. 2.5 hours
  🎯 Task 57: Board drag-and-drop integration tests - Est. 2 hours

BLOCKERS:
  None - Ready to proceed

STATUS: ON TRACK
```

### DevOps Engineer
```
NAME: Lisa | ROLE: DevOps

YESTERDAY:
  ✅ Set up production MySQL database (Task 86) - 2 hours
  ✅ Configured PHP production environment (Task 87) - 1.5 hours

TODAY:
  🎯 Task 88: Implement error logging system - Est. 2 hours
  🎯 Task 92: Configure email service - Est. 2 hours

BLOCKERS:
  ⚠️  Waiting for SSL certificate approval

STATUS: ON TRACK
```

---

## Quick Check Dashboard

Print this weekly to track overall progress:

```
┌─────────────────────────────────────────────────────────────────┐
│                   PROJECT HEALTH DASHBOARD                      │
│                    Week of [DATE]                               │
├─────────────────────────────────────────────────────────────────┤

TASKS COMPLETED: [████████░░] 80% (88/110 tasks)
BUGS FIXED:      [██████░░░░] 60% (18/30 known)
TESTS PASSING:   [███████░░░] 70% (35/50 unit tests)
DOCUMENTATION:   [█████░░░░░] 50% (25/50 docs)

TEAM VELOCITY:
  Week 1: 30 tasks
  Week 2: 35 tasks
  Week 3: 32 tasks
  Week 4: 28 tasks (current week)
  Average: 31.25 tasks/week

ON-TIME DELIVERY:
  Critical (Tasks 1-10): 100% ✅
  High (Tasks 11-50): 85% 🟡
  Medium (Tasks 51-85): 70% 🟠
  Low (Tasks 86+): 50% 🔴

CURRENT BLOCKERS: 2
  🔴 SSL certificate pending (1 day)
  🟡 Chart.js library issue (2 hours to fix)

COMING UP:
  ✓ Code review Thursday
  ✓ Production readiness audit Friday
  ✓ Deployment window Monday Dec 15

CONFIDENCE: 95% READY FOR PRODUCTION
├─────────────────────────────────────────────────────────────────┤
│ Next Action: Address 2 blockers, proceed with deployment plan   │
└─────────────────────────────────────────────────────────────────┘
```

---

## Escalation Flowchart

```
ISSUE DISCOVERED
       │
       ├─ Is it critical? (breaks production)
       │         YES → Contact Tech Lead IMMEDIATELY
       │         NO  → Continue
       │
       ├─ Is it a blocker for other team members?
       │         YES → Report in standup, escalate
       │         NO  → Continue
       │
       ├─ Can I fix it in 2 hours?
       │         YES → Fix it now
       │         NO  → Continue
       │
       ├─ Create GitHub issue with details
       ├─ Assign to appropriate person
       ├─ Add to blockers list
       └─ Mention in standup

RESOLUTION:
  1. Tech lead reviews
  2. Assign to available resource
  3. Re-prioritize if needed
  4. Update timeline
  5. Follow up next standup
```

---

## Remote Standups Best Practices

### For Everyone
- ✅ Be on time
- ✅ Have notes ready (don't wing it)
- ✅ Be specific (not "working on stuff")
- ✅ Mention blockers early
- ✅ Keep to 2 minutes
- ✅ Respect others' time

### For Tech Lead
- ✅ Track blockers
- ✅ Identify risks
- ✅ Reassign if needed
- ✅ Update stakeholders
- ✅ Keep team motivated

### For Distributed Teams
- ✅ Record for async team members
- ✅ Post written summary after
- ✅ Use shared document (Google Docs)
- ✅ Rotate time for different zones
- ✅ Have async option available

---

## Standups This Week

**Monday Dec 10**: Project kickoff + Task assignment  
**Tuesday Dec 11**: Fix critical issues (Tasks 1-10)  
**Wednesday Dec 12**: Email integration progress check  
**Thursday Dec 13**: Code review day + risk assessment  
**Friday Dec 14**: Weekly summary + deployment readiness  

---

## Standup Metrics to Track

Capture these metrics in each standup:

```
METRICS TO TRACK:
  • Tasks completed today: [#]
  • PRs merged today: [#]
  • Bugs fixed today: [#]
  • Blockers resolved: [#]
  • New blockers created: [#]
  • Team morale: [1-10]
  • Confidence level: [1-10]
```

---

## End-of-Day Checklist

Each team member should do at the end of their day:

```
☐ Commit code to feature branch
☐ Push to GitHub
☐ Create/update PR with description
☐ Add comments for code review points
☐ Update task status in task tracker
☐ Document any blockers/learnings
☐ Prepare notes for next standup
☐ Check Slack/email for team messages
```

---

## Sample Weekly Report

```
JIRA CLONE PROJECT - WEEKLY REPORT
Week of: December 10-14, 2025

EXECUTIVE SUMMARY:
  Project is on track for December 15 deployment.
  95% of critical tasks completed.
  2 minor blockers, all manageable.

COMPLETION METRICS:
  Planned: 50 tasks
  Completed: 47 tasks (94%)
  In Progress: 3 tasks (6%)
  Blocked: 0 tasks (0%)

TEAM PERFORMANCE:
  Backend: 12 tasks ✅
  Frontend: 14 tasks ✅
  QA: 11 tasks ✅
  DevOps: 10 tasks ✅

RISK ASSESSMENT:
  Critical: 0
  High: 2 (minor, manageable)
  Medium: 1

BLOCKERS OUTSTANDING:
  1. SSL certificate (ETA: tomorrow)
  2. Chart.js library (ETA: 2 hours)

NEXT WEEK FORECAST:
  Deploy to production Monday December 15
  Monitor for 48 hours
  Team training Tuesday/Wednesday

RECOMMENDATION:
  PROCEED WITH DEPLOYMENT - System is production-ready
```

---

## Standup Tracking Spreadsheet

Create a Google Sheet with these columns:

```
Date | Team Member | Task # | Accomplished | Today's Plan | Blockers | Status | Notes
─────────────────────────────────────────────────────────────────────────────────
12/10| John | 1 | Create btn fix | Task 12 | SMTP creds | ON TRACK | Need ASAP
12/10| Sarah | 21 | Activity page | Task 22 | Chart.js | AT RISK | Minor issue
12/10| Mike | 51 | Auth tests | Task 52 | None | ON TRACK | Moving fast
12/10| Lisa | 86 | MySQL setup | Task 92 | SSL cert | ON TRACK | Approved today
```

---

**Last Updated**: December 10, 2025  
**Next Review**: Daily at 10:00 AM  
**Owner**: Tech Lead / Scrum Master
