# Amp Configuration for Team Development

This directory contains Amp agent configuration for Live Share team development.

## What's Here

- **permissions-global.sh** - Permission rules for all developers
- **.ampignore** - Files to ignore to prevent conflicts

## How It Works

### Non-Conflicting Design

Each developer in Live Share:
- Works independently on their own edits
- Uses Amp for analysis, search, and code understanding
- Can read all shared code without conflicts
- Can create/edit files without stepping on each other

### Shared Tools Available

✅ **Read** - Browse any file  
✅ **Grep/finder** - Search codebase  
✅ **edit_file** - Modify code  
✅ **create_file** - Create new files  
✅ **Bash** - Run commands (tests, Git, PHP scripts)  
✅ **format_file** - Format code  

### Conflict Prevention

1. **Version Control** - Use Git for merging changes
2. **Clear Naming** - New files get unique names (e.g., `USER_fix_feature.php`)
3. **Communication** - Developers coordinate on same files via Live Share chat
4. **Ignore List** - Debug/temp files are excluded (see .ampignore)

## For Live Share Users

When you join this workspace:
1. Amp automatically loads these permissions
2. All tools work for you
3. You can work on different files simultaneously
4. Commit frequently to Git to avoid conflicts

## Adding New Rules

Edit `permissions-global.sh` and commit to Git for all developers to get the rules.

---
**Last Updated:** December 12, 2025  
**Team:** All developers using Jira Clone System
