<?php
$dueBucket = 'none';
if (!empty($issue['due_date'])) {
    $dueTs = strtotime($issue['due_date']);
    $today = strtotime('today');
    $week = strtotime('+7 days', $today);
    if ($dueTs < $today)
        $dueBucket = 'overdue';
    elseif ($dueTs <= $week)
        $dueBucket = 'due-soon';
}
$updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
$prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name'] ?? ''));
?>
<a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="issue-row" data-priority="<?= e($prioritySlug) ?>"
    data-due="<?= e($dueBucket) ?>" data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
    <div class="issue-type-icon"
        style="background-color: <?= e($issue['issue_type_color'] ?? 'var(--jira-blue)') ?>; color: <?= contrast_color($issue['issue_type_color'] ?? '#0052cc') // Default jira blue approx ?>;">
        <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'list-check') ?>"></i>
    </div>
    <div class="issue-key">
        <?= e($issue['issue_key']) ?>
    </div>
    <div class="issue-summary">
        <?= e($issue['summary']) ?>
    </div>

    <?php if ($dueBucket === 'overdue'): ?>
        <span class="badge badge-danger" title="Overdue"><i class="bi bi-exclamation-triangle"></i></span>
    <?php elseif ($dueBucket === 'due-soon'): ?>
        <span class="badge badge-warning" title="Due Soon"><i class="bi bi-clock"></i></span>
    <?php endif; ?>

    <span class="issue-priority"
        style="background-color: <?= e($issue['priority_color'] ?? '#626F86') ?>; color: <?= contrast_color($issue['priority_color'] ?? '#626F86') ?>;"
        title="<?= e($issue['priority_name'] ?? 'None') ?>">
        <?= e(substr($issue['priority_name'] ?? 'N', 0, 1)) ?>
    </span>
    <span class="issue-status"
        style="background-color: <?= e($issue['status_color'] ?? 'var(--jira-blue)') ?>; color: <?= contrast_color($issue['status_color'] ?? '#0052cc') ?> !important;">
        <?= e($issue['status_name'] ?? 'New') ?>
    </span>
</a>