<?php
/**
 * Issue Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Issue extends Model
{
    protected static string $table = 'issues';
    
    protected static array $fillable = [
        'project_id',
        'issue_type_id',
        'status_id',
        'priority_id',
        'issue_key',
        'issue_number',
        'summary',
        'description',
        'reporter_id',
        'assignee_id',
        'parent_id',
        'epic_id',
        'sprint_id',
        'story_points',
        'original_estimate',
        'remaining_estimate',
        'time_spent',
        'environment',
        'due_date',
        'start_date',
        'end_date',
        'resolution',
        'resolution_date',
        'labels',
        'components',
        'fix_versions'
    ];

    protected static array $guarded = ['id'];

    protected static array $casts = [
        'story_points' => 'float',
        'original_estimate' => 'integer',
        'remaining_estimate' => 'integer',
        'time_spent' => 'integer',
        'due_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'resolution_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static array $hidden = [
        // Add any sensitive fields to hide when converting to array/json
    ];

    protected static bool $timestamps = true;
    protected static ?string $createdAt = 'created_at';
    protected static ?string $updatedAt = 'updated_at';
}