<?php
/**
 * Activity Service - Handles project and issue activity tracking
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class ActivityService
{
    /**
     * Get recent activities for a project
     */
    public function getProjectActivities(int $projectId, int $limit = 10): array
    {
        $activities = Database::select(
            "SELECT 
                al.id,
                al.action,
                al.entity_type,
                al.entity_id,
                al.created_at,
                u.id as user_id,
                u.display_name,
                u.avatar,
                i.issue_key,
                i.summary
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             LEFT JOIN issues i ON al.entity_type = 'issue' AND al.entity_id = i.id
             WHERE (
                 (al.entity_type = 'issue' AND EXISTS (SELECT 1 FROM issues WHERE id = al.entity_id AND project_id = ?))
                 OR
                 (al.entity_type = 'project' AND al.entity_id = ?)
             )
             AND (al.user_id IS NOT NULL OR al.action IS NOT NULL)
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$projectId, $projectId, $limit]
        );

        return array_map(function ($activity) {
            return [
                'id' => $activity['id'],
                'action' => $activity['action'],
                'type' => $activity['entity_type'],
                'entity_id' => $activity['entity_id'],
                'created_at' => $activity['created_at'],
                'user' => [
                    'id' => $activity['user_id'],
                    'display_name' => $activity['display_name'] ?? 'System',
                    'avatar' => $activity['avatar'] ?? '/images/default-avatar.png'
                ],
                'issue' => $activity['issue_key'] ? [
                    'key' => $activity['issue_key'],
                    'summary' => $activity['summary']
                ] : null,
                'description' => $this->formatActivityDescription($activity)
            ];
        }, $activities);
    }

    /**
     * Get recent activities for an issue
     */
    public function getIssueActivities(int $issueId, int $limit = 20): array
    {
        $activities = Database::select(
            "SELECT 
                al.id,
                al.action,
                al.old_values,
                al.new_values,
                al.created_at,
                u.id as user_id,
                u.display_name,
                u.avatar
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE (al.entity_type = 'issue' AND al.entity_id = ?)
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$issueId, $limit]
        );

        return array_map(function ($activity) {
            return [
                'id' => $activity['id'],
                'action' => $activity['action'],
                'created_at' => $activity['created_at'],
                'user' => [
                    'id' => $activity['user_id'],
                    'display_name' => $activity['display_name'] ?? 'System',
                    'avatar' => $activity['avatar'] ?? '/images/default-avatar.png'
                ],
                'description' => $this->formatIssueActivityDescription($activity),
                'old_values' => $activity['old_values'],
                'new_values' => $activity['new_values']
            ];
        }, $activities);
    }

    /**
     * Format activity description for project activities
     */
    private function formatActivityDescription(array $activity): string
    {
        $action = $activity['action'];
        $type = $activity['entity_type'];
        $issueKey = $activity['issue_key'];

        $actionMap = [
            'issue_created' => 'created issue',
            'issue_updated' => 'updated issue',
            'issue_deleted' => 'deleted issue',
            'issue_transitioned' => 'moved issue',
            'issue_assigned' => 'assigned issue',
            'comment_added' => 'commented on issue',
            'comment_updated' => 'edited comment on issue',
            'comment_deleted' => 'deleted comment from issue',
            'project_created' => 'created project',
            'project_updated' => 'updated project',
            'project_deleted' => 'deleted project',
        ];

        $verb = $actionMap[$action] ?? str_replace('_', ' ', $action);

        if ($issueKey) {
            return "$verb {$issueKey}";
        }

        return $verb;
    }

    /**
     * Format activity description for issue activities
     */
    private function formatIssueActivityDescription(array $activity): string
    {
        $action = $activity['action'];

        $actionMap = [
            'issue_created' => 'created this issue',
            'issue_updated' => 'updated this issue',
            'issue_transitioned' => 'changed status',
            'issue_assigned' => 'assigned this issue',
            'comment_added' => 'added a comment',
            'comment_updated' => 'edited their comment',
            'comment_deleted' => 'deleted their comment',
        ];

        return $actionMap[$action] ?? $action;
    }

    /**
     * Log an activity (wrapper for audit logging)
     */
    public function logActivity(string $action, string $entityType, ?int $entityId, ?int $userId, ?array $oldValues = null, ?array $newValues = null): void
    {
        Database::insert('audit_logs', [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }
}
