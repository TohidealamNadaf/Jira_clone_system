# Amp Permission Rules - Team Development
# These rules apply to ALL Live Share users
# Non-conflicting - each user operates independently

[
  {
    "tool": "Bash",
    "action": "allow",
    "description": "Allow read-only file operations"
  },
  {
    "tool": "Bash",
    "action": "allow",
    "matches": {
      "cmd": ["find"]
    },
    "description": "Find files and patterns"
  },
  {
    "tool": "Bash",
    "action": "allow",
    "matches": {
      "cmd": ["grep", "-r"]
    },
    "description": "Search across codebase"
  },
  {
    "tool": "Bash",
    "action": "allow",
    "matches": {
      "cmd": ["php", "-f"]
    },
    "description": "Run PHP scripts"
  },
  {
    "tool": "Bash",
    "action": "allow",
    "matches": {
      "cmd": ["php", "tests/TestRunner.php"]
    },
    "description": "Run test suite"
  },
  {
    "tool": "Bash",
    "action": "allow",
    "matches": {
      "cmd": ["git"]
    },
    "description": "Git operations for version control"
  },
  {
    "tool": "Read",
    "action": "allow",
    "description": "Read all files in workspace"
  },
  {
    "tool": "Grep",
    "action": "allow",
    "description": "Search with ripgrep"
  },
  {
    "tool": "glob",
    "action": "allow",
    "description": "Find files by pattern"
  },
  {
    "tool": "finder",
    "action": "allow",
    "description": "Intelligent code search"
  },
  {
    "tool": "edit_file",
    "action": "allow",
    "description": "Edit files (each user edits independently)"
  },
  {
    "tool": "create_file",
    "action": "allow",
    "description": "Create new files (avoid conflicts by using distinct names)"
  },
  {
    "tool": "format_file",
    "action": "allow",
    "description": "Format code files"
  }
]
