# Implementation Guides for Comment Enhancements

## 1. EDIT HISTORY TRACKING

### Database Setup
Run the migration:
```sql
CREATE TABLE IF NOT EXISTS comment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comment_id INT NOT NULL,
    edited_by INT NOT NULL,
    old_body LONGTEXT,
    new_body LONGTEXT,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (edited_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_comment_id (comment_id),
    INDEX idx_edited_at (edited_at)
);
```

### Service Layer Enhancement
Add to IssueService.php:
```php
public function updateCommentWithHistory(int $commentId, string $body, int $userId): array
{
    $comment = Database::selectOne(
        "SELECT * FROM comments WHERE id = ?",
        [$commentId]
    );

    if (!$comment) {
        throw new \InvalidArgumentException('Comment not found');
    }

    // Record history
    Database::insert('comment_history', [
        'comment_id' => $commentId,
        'edited_by' => $userId,
        'old_body' => $comment['body'],
        'new_body' => $body,
    ]);

    // Update comment
    Database::update('comments', [
        'body' => $body,
        'updated_at' => date('Y-m-d H:i:s'),
        'edit_count' => ($comment['edit_count'] ?? 0) + 1,
    ], 'id = ?', [$commentId]);

    return $this->getComment($commentId);
}

public function getCommentHistory(int $commentId): array
{
    return Database::select(
        "SELECT ch.*, u.display_name as editor_name, u.avatar as editor_avatar
         FROM comment_history ch
         LEFT JOIN users u ON ch.edited_by = u.id
         WHERE ch.comment_id = ?
         ORDER BY ch.edited_at DESC",
        [$commentId]
    );
}
```

### UI Enhancement
Add to show.php comment display:
```html
<div class="comment-edit-history">
    <a href="#" onclick="showEditHistory(<?= $comment['id'] ?>)">
        <small class="text-muted">view edit history (<?= $comment['edit_count'] ?? 0 ?>)</small>
    </a>
</div>
```

### JavaScript for History Modal
```javascript
function showEditHistory(commentId) {
    fetch(`/api/comments/${commentId}/history`)
        .then(res => res.json())
        .then(data => {
            showModal('Edit History', data.map(h => `
                <div class="edit-entry">
                    <strong>${h.editor_name}</strong> edited on ${h.edited_at}
                    <button onclick="viewDiff(${h.id})" class="btn btn-sm btn-link">
                        View Changes
                    </button>
                </div>
            `).join(''));
        });
}
```

---

## 2. @MENTIONS SUPPORT

### Database Setup
Add mentions table:
```sql
CREATE TABLE IF NOT EXISTS comment_mentions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comment_id INT NOT NULL,
    mentioned_user_id INT NOT NULL,
    mentioned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (mentioned_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_mention (comment_id, mentioned_user_id)
);
```

### Service Layer
```php
public function processMentions(int $commentId, string $body): void
{
    // Parse @username patterns
    preg_match_all('/@(\w+)/', $body, $matches);
    
    if (empty($matches[1])) {
        return;
    }

    foreach ($matches[1] as $username) {
        $user = Database::selectOne(
            "SELECT id FROM users WHERE username = ?",
            [$username]
        );

        if ($user) {
            Database::insert('comment_mentions', [
                'comment_id' => $commentId,
                'mentioned_user_id' => $user['id'],
            ]);

            // Create notification
            Database::insert('notifications', [
                'user_id' => $user['id'],
                'type' => 'mentioned_in_comment',
                'notifiable_type' => 'comment',
                'notifiable_id' => $commentId,
                'data' => json_encode(['comment_id' => $commentId]),
            ]);
        }
    }
}
```

### View Enhancement
```html
<div class="comment-body">
    <?= preg_replace(
        '/@(\w+)/',
        '<a href="/user/$1" class="mention">@$1</a>',
        nl2br(e($comment['body']))
    ) ?>
</div>
```

### JavaScript Autocomplete
```javascript
// In comment form
const commentInput = document.querySelector('[name="body"]');

commentInput.addEventListener('input', debounce(function(e) {
    const text = this.value;
    const lastAt = text.lastIndexOf('@');
    
    if (lastAt > -1) {
        const query = text.substring(lastAt + 1);
        if (query.length >= 2) {
            fetchUserMentions(query, showAutocomplete);
        }
    }
}, 300));

async function fetchUserMentions(query) {
    const response = await fetch(`/api/users/search?q=${query}`);
    return response.json();
}
```

---

## 3. RICH TEXT EDITOR

### Install TinyMCE
```html
<script src="https://cdn.tiny.cloud/1/{api-key}/tinymce/6/tinymce.min.js"></script>
```

### Initialize Editor
```javascript
tinymce.init({
    selector: '[name="body"]',
    height: 300,
    plugins: 'lists link image codesample',
    toolbar: 'formatselect | bold italic | bullist numlist | link image | codesample',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto; font-size: 14px; }',
    codesample_languages: [
        { text: 'PHP', value: 'php' },
        { text: 'JavaScript', value: 'javascript' },
        { text: 'SQL', value: 'sql' },
        { text: 'HTML', value: 'html' },
        { text: 'CSS', value: 'css' },
    ],
});
```

### Store HTML Content
Update the comment store/update endpoints to preserve HTML:
```php
public function addComment(int $issueId, string $body, int $userId, bool $isRichText = false): array
{
    // Sanitize if rich text
    if ($isRichText) {
        $body = sanitizeHtml($body);
    }
    
    // ... rest of method
}
```

### Display HTML Content
Update view to display HTML:
```html
<!-- For rich text comments -->
<?php if ($comment['is_rich_text']): ?>
    <div class="comment-body rich-content">
        <?= $comment['body'] ?>
    </div>
<?php else: ?>
    <div class="comment-body">
        <?= nl2br(e($comment['body'])) ?>
    </div>
<?php endif; ?>
```

---

## 4. COMMENT SEARCH/FILTER

### Database Query
```php
public function searchComments(int $issueId, string $query, ?int $userId = null): array
{
    $where = ['c.issue_id = :issue_id'];
    $params = ['issue_id' => $issueId];

    if (!empty($query)) {
        $where[] = "c.body LIKE :query";
        $params['query'] = "%$query%";
    }

    if ($userId) {
        $where[] = "c.user_id = :user_id";
        $params['user_id'] = $userId;
    }

    $whereClause = implode(' AND ', $where);

    return Database::select(
        "SELECT c.*, u.display_name, u.avatar
         FROM comments c
         LEFT JOIN users u ON c.user_id = u.id
         WHERE $whereClause
         ORDER BY c.created_at DESC",
        $params
    );
}
```

### UI Component
```html
<div class="comment-filters mb-3">
    <input type="text" id="comment-search" 
           placeholder="Search comments..." 
           class="form-control form-control-sm">
    
    <select id="comment-filter-user" class="form-select form-select-sm mt-2">
        <option value="">All authors</option>
        <?php foreach ($issue['comment_authors'] ?? [] as $author): ?>
            <option value="<?= $author['id'] ?>">
                <?= e($author['display_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
document.getElementById('comment-search').addEventListener('input', debounce(function() {
    const query = this.value;
    const userId = document.getElementById('comment-filter-user').value;
    
    fetch(`/api/issues/TEST-1/comments/search`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({ query, user_id: userId })
    })
    .then(r => r.json())
    .then(data => renderComments(data));
}, 300));
</script>
```

---

## 5. COMMENT THREADING/REPLIES

### Database Schema
```sql
ALTER TABLE comments ADD COLUMN IF NOT EXISTS parent_comment_id INT;
ALTER TABLE comments ADD FOREIGN KEY (parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE;
CREATE INDEX idx_comments_parent ON comments(parent_comment_id);
```

### Service Layer
```php
public function addReply(int $parentCommentId, string $body, int $userId): array
{
    $parent = Database::selectOne(
        "SELECT * FROM comments WHERE id = ?",
        [$parentCommentId]
    );

    if (!$parent) {
        throw new \InvalidArgumentException('Parent comment not found');
    }

    $replyId = Database::insert('comments', [
        'issue_id' => $parent['issue_id'],
        'parent_comment_id' => $parentCommentId,
        'user_id' => $userId,
        'body' => $body,
    ]);

    return $this->getComment($replyId);
}

public function getReplies(int $commentId): array
{
    return Database::select(
        "SELECT * FROM comments WHERE parent_comment_id = ?",
        [$commentId]
    );
}
```

### UI Component
```html
<div class="comment-thread">
    <!-- Parent comment -->
    <div class="comment-item">
        <!-- ... comment content ... -->
        <button onclick="toggleReplies(<?= $comment['id'] ?>)" class="btn btn-sm btn-link">
            <i class="bi bi-reply"></i> Reply (<?= count($replies) ?>)
        </button>
    </div>
    
    <!-- Replies -->
    <div class="comment-replies" id="replies-<?= $comment['id'] ?>">
        <?php foreach ($replies ?? [] as $reply): ?>
            <div class="comment-item comment-reply ms-4">
                <!-- ... reply content ... -->
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

---

## 6. COMMENT REACTIONS/EMOJIS

### Database Schema
```sql
CREATE TABLE IF NOT EXISTS comment_reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    reaction VARCHAR(10), -- emoji
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reaction (comment_id, user_id, reaction)
);
```

### Service Layer
```php
public function addReaction(int $commentId, string $reaction, int $userId): bool
{
    return (bool) Database::insert('comment_reactions', [
        'comment_id' => $commentId,
        'user_id' => $userId,
        'reaction' => $reaction,
    ]);
}

public function removeReaction(int $commentId, string $reaction, int $userId): bool
{
    return Database::delete(
        'comment_reactions',
        'comment_id = ? AND reaction = ? AND user_id = ?',
        [$commentId, $reaction, $userId]
    ) > 0;
}

public function getReactions(int $commentId): array
{
    $reactions = Database::select(
        "SELECT reaction, COUNT(*) as count
         FROM comment_reactions
         WHERE comment_id = ?
         GROUP BY reaction
         ORDER BY count DESC",
        [$commentId]
    );

    return $reactions;
}
```

### UI Component
```html
<div class="comment-reactions">
    <?php foreach ($reactions ?? [] as $reaction): ?>
        <button class="btn btn-sm btn-light reaction-btn" 
                data-reaction="<?= $reaction['reaction'] ?>">
            <?= $reaction['reaction'] ?> <?= $reaction['count'] ?>
        </button>
    <?php endforeach; ?>
    
    <button class="btn btn-sm btn-light" onclick="showReactionPicker(<?= $comment['id'] ?>)">
        <i class="bi bi-emoji-smile"></i>
    </button>
</div>

<script>
document.querySelectorAll('.reaction-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const reaction = this.dataset.reaction;
        toggleReaction(commentId, reaction);
    });
});

function showReactionPicker(commentId) {
    const emojis = ['👍', '❤️', '😂', '🎉', '😕', '🚀', '👀'];
    const html = emojis.map(emoji => 
        `<button class="btn btn-sm" onclick="addReaction(${commentId}, '${emoji}')">${emoji}</button>`
    ).join('');
    
    showPopover(html);
}

function toggleReaction(commentId, reaction) {
    fetch(`/api/comments/${commentId}/reactions/${reaction}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
        }
    }).then(() => location.reload());
}
</script>
```

---

## DEPLOYMENT CHECKLIST

Before deploying any of these enhancements:

- [ ] Run database migrations
- [ ] Create backup of comments table
- [ ] Update IssueService with new methods
- [ ] Update API routes if needed
- [ ] Update views/templates
- [ ] Add JavaScript functionality
- [ ] Test all features thoroughly
- [ ] Check for console errors
- [ ] Verify database performance
- [ ] Monitor error logs
- [ ] Update documentation

---

## TESTING PROCEDURES

For each enhancement, test:

1. **Functionality**
   - Create/read/update/delete operations work
   - Permissions are properly enforced
   - Data persists correctly

2. **Performance**
   - Queries are optimized
   - UI remains responsive
   - No memory leaks

3. **Security**
   - Input is properly validated
   - Output is properly escaped
   - Authorization checks work
   - No SQL injection possible

4. **Compatibility**
   - Works in all modern browsers
   - Mobile-friendly
   - Accessibility compliant

5. **Edge Cases**
   - Network failures handled
   - Large data sets handled
   - Concurrent edits handled
   - Deleted users handled
