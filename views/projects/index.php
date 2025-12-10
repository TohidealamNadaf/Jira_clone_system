<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); margin: -2rem 0 -2rem 0; padding: 2rem 0 2rem 0;">
    <div class="container-fluid px-4" style="max-width: 1400px; margin: 0 auto;">
        
        <!-- Hero Section -->
        <div style="margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: 700; color: #161B22; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;">
                        Projects
                    </h1>
                    <p style="font-size: 1rem; color: #626F86; margin: 0;">
                        Manage and organize your work across projects
                    </p>
                </div>
                <?php if (can('create-projects')): ?>
                <a href="<?= url('/projects/create') ?>" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0, 82, 204, 0.3);">
                    <i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i>
                    <span>Create Project</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filters Section -->
        <div style="background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); overflow: visible;">
            <form method="GET" action="<?= url('/projects') ?>" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: flex-end; position: relative; z-index: 10;">
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #161B22;">Search</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="bi bi-search" style="position: absolute; left: 12px; color: #626F86; font-size: 1rem;"></i>
                        <input type="text" name="search" 
                               value="<?= e($filters['search'] ?? '') ?>" 
                               placeholder="Find projects..." 
                               style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #DFE1E6; border-radius: 6px; font-size: 0.9375rem; transition: all 0.15s; font-family: inherit;" 
                               onmouseover="this.style.borderColor='#0052CC'" 
                               onmouseout="this.style.borderColor='#DFE1E6'" 
                               onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" 
                               onblur="this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; position: relative; z-index: 100;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #161B22;">Category</label>
                    <select name="category" style="padding: 0.625rem 1rem; border: 1px solid #DFE1E6; border-radius: 6px; font-size: 0.9375rem; background-color: white; transition: all 0.15s; font-family: inherit; cursor: pointer;" onchange="this.style.borderColor='#0052CC'" onblur="this.style.borderColor='#DFE1E6'" onmouseover="this.style.borderColor='#0052CC'" onmouseout="this.style.borderColor='#DFE1E6'">
                        <option value="">All Categories</option>
                        <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= e($category['id']) ?>" <?= ($filters['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                            <?= e($category['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; position: relative; z-index: 100;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #161B22;">Status</label>
                    <select name="status" style="padding: 0.625rem 1rem; border: 1px solid #DFE1E6; border-radius: 6px; font-size: 0.9375rem; background-color: white; transition: all 0.15s; font-family: inherit; cursor: pointer;" onchange="this.style.borderColor='#0052CC'" onblur="this.style.borderColor='#DFE1E6'" onmouseover="this.style.borderColor='#0052CC'" onmouseout="this.style.borderColor='#DFE1E6'">
                        <option value="">All Statuses</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>

                <button type="submit" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; border: none; padding: 0.625rem 1.5rem; border-radius: 6px; font-weight: 600; font-size: 0.9375rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; justify-content: center; white-space: nowrap;">
                    <i class="bi bi-funnel" style="font-size: 1rem;"></i>
                    <span>Filter</span>
                </button>
            </form>
        </div>

        <!-- Projects Grid -->
        <?php if (empty($projects['items'] ?? [])): ?>
        <div style="text-align: center; padding: 3rem 2rem; background: white; border-radius: 8px; border: 2px dashed #DFE1E6;">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #B6C2CF; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #161B22; margin: 0 0 0.5rem 0;">No projects found</h3>
            <p style="color: #626F86; margin: 0 0 1.5rem 0;">
                <?php if (can('create-projects')): ?>
                Create your first project to get started
                <?php else: ?>
                No projects are available
                <?php endif; ?>
            </p>
            <?php if (can('create-projects')): ?>
            <a href="<?= url('/projects/create') ?>" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; transition: all 0.2s;">
                <i class="bi bi-plus-lg"></i>
                <span>Create First Project</span>
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 1.5rem;">
            <?php foreach ($projects['items'] as $project): ?>
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); transition: all 0.2s; border: 1px solid #f0f0f0;" 
                 onmouseover="this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.12)'; this.style.transform='translateY(-2px)'" 
                 onmouseout="this.style.boxShadow='0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)'; this.style.transform='translateY(0)'">
                
                <!-- Project Header -->
                <div style="padding: 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="flex-shrink: 0;">
                        <?php if ($project['avatar'] ?? null): ?>
                        <img src="<?= e($project['avatar']) ?>" 
                             style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover;" 
                             alt="<?= e($project['name']) ?>">
                        <?php else: ?>
                        <div style="width: 48px; height: 48px; border-radius: 6px; background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                            <?= strtoupper(substr($project['key'], 0, 2)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.125rem; font-weight: 600; color: #161B22;">
                            <a href="<?= url("/projects/{$project['key']}") ?>" style="color: #0052CC; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#003DA5'" onmouseout="this.style.color='#0052CC'">
                                <?= e($project['name']) ?>
                            </a>
                        </h3>
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #DEEBFF; color: #0052CC; border-radius: 4px; font-size: 0.8125rem; font-weight: 600;">
                                <?= e($project['key']) ?>
                            </span>
                            <?php if ($project['is_archived'] ?? false): ?>
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #FFF3C1; color: #974F0C; border-radius: 4px; font-size: 0.8125rem; font-weight: 600;">
                                Archived
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="flex-shrink: 0; position: relative;">
                        <button class="dropdown-toggle" style="background: none; border: none; color: #626F86; cursor: pointer; padding: 0.5rem; border-radius: 4px; transition: all 0.2s;" 
                                onclick="toggleDropdown(event)" 
                                onmouseover="this.style.backgroundColor='#f0f0f0'; this.style.color='#161B22'" 
                                onmouseout="this.style.backgroundColor='none'; this.style.color='#626F86'">
                            <i class="bi bi-three-dots-vertical" style="font-size: 1.1rem;"></i>
                        </button>
                        <ul class="projects-dropdown-menu" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #DFE1E6; border-radius: 6px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12); list-style: none; margin: 0.5rem 0 0 0; padding: 0.5rem 0; min-width: 150px; z-index: 1000;">
                            <li><a class="dropdown-item" href="<?= url("/projects/{$project['key']}") ?>" 
                                   style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; color: #161B22; text-decoration: none; font-size: 0.9375rem; transition: background 0.2s;" 
                                   onmouseover="this.style.backgroundColor='#f5f7fa'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                <i class="bi bi-eye" style="font-size: 1rem;"></i> View
                            </a></li>
                            <li><a class="dropdown-item" href="<?= url("/projects/{$project['key']}/board") ?>" 
                                   style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; color: #161B22; text-decoration: none; font-size: 0.9375rem; transition: background 0.2s;" 
                                   onmouseover="this.style.backgroundColor='#f5f7fa'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                <i class="bi bi-kanban" style="font-size: 1rem;"></i> Board
                            </a></li>
                            <?php if (can('edit-project', $project['id'])): ?>
                            <li style="border-top: 1px solid #f0f0f0; margin: 0.5rem 0;"></li>
                            <li><a class="dropdown-item" href="<?= url("/projects/{$project['key']}/settings") ?>" 
                                   style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; color: #161B22; text-decoration: none; font-size: 0.9375rem; transition: background 0.2s;" 
                                   onmouseover="this.style.backgroundColor='#f5f7fa'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                <i class="bi bi-gear" style="font-size: 1rem;"></i> Settings
                            </a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Project Description -->
                <?php if ($project['description'] ?? null): ?>
                <div style="padding: 1.5rem; border-bottom: 1px solid #f0f0f0;">
                    <p style="margin: 0; color: #626F86; font-size: 0.9375rem; line-height: 1.5;">
                        <?= e(substr($project['description'], 0, 100)) ?><?= strlen($project['description']) > 100 ? '...' : '' ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Project Stats -->
                <div style="padding: 1.5rem; border-bottom: 1px solid #f0f0f0; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.8125rem; font-weight: 600; color: #626F86; text-transform: uppercase; margin-bottom: 0.5rem;">
                            <i class="bi bi-list-task" style="margin-right: 0.5rem;"></i> Issues
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #161B22;">
                            <?= e($project['issue_count'] ?? 0) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.8125rem; font-weight: 600; color: #626F86; text-transform: uppercase; margin-bottom: 0.5rem;">
                            <i class="bi bi-people" style="margin-right: 0.5rem;"></i> Members
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #161B22;">
                            <?= e($project['member_count'] ?? 0) ?>
                        </div>
                    </div>
                </div>

                <!-- Project Footer -->
                <div style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 0.8125rem; color: #626F86; font-weight: 600;">Lead:</span>
                        <?php if ($project['lead'] ?? null): ?>
                        <img src="<?= e($project['lead']['avatar'] ?? '/images/default-avatar.png') ?>" 
                             style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 2px solid #DFE1E6;" 
                             title="<?= e($project['lead']['display_name']) ?>" 
                             alt="<?= e($project['lead']['display_name']) ?>">
                        <?php else: ?>
                        <span style="font-size: 0.8125rem; color: #97A0AF;">Unassigned</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" 
                       style="padding: 0.5rem 1rem; background: #f5f7fa; color: #0052CC; border: 1px solid #DFE1E6; border-radius: 6px; text-decoration: none; font-size: 0.9375rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; white-space: nowrap;" 
                       onmouseover="this.style.backgroundColor='#DEEBFF'; this.style.borderColor='#0052CC'; this.style.color='#003DA5'" 
                       onmouseout="this.style.backgroundColor='#f5f7fa'; this.style.borderColor='#DFE1E6'; this.style.color='#0052CC'">
                        <i class="bi bi-plus" style="font-size: 1rem;"></i>
                        <span>New Issue</span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($projects['last_page']) && $projects['last_page'] > 1): ?>
        <nav style="margin-top: 2rem; display: flex; justify-content: center;">
            <ul style="display: flex; list-style: none; margin: 0; padding: 0; gap: 0.25rem;">
                <li>
                    <a href="<?= $projects['current_page'] <= 1 ? '#' : url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $projects['current_page'] - 1]))) ?>" 
                       style="padding: 0.5rem 0.75rem; border: 1px solid #DFE1E6; border-radius: 4px; text-decoration: none; color: <?= $projects['current_page'] <= 1 ? '#97A0AF' : '#0052CC' ?>; background: white; font-weight: 600; font-size: 0.9375rem; transition: all 0.2s; cursor: <?= $projects['current_page'] <= 1 ? 'not-allowed' : 'pointer' ?>;" 
                       <?= $projects['current_page'] > 1 ? "onmouseover=\"this.style.borderColor='#0052CC'; this.style.backgroundColor='#DEEBFF'\" onmouseout=\"this.style.borderColor='#DFE1E6'; this.style.backgroundColor='white'\"" : '' ?>>
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                <?php for ($i = 1; $i <= $projects['last_page']; $i++): ?>
                <li>
                    <a href="<?= url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $i]))) ?>" 
                       style="padding: 0.5rem 0.75rem; border: 1px solid <?= $projects['current_page'] == $i ? '#0052CC' : '#DFE1E6' ?>; border-radius: 4px; text-decoration: none; color: <?= $projects['current_page'] == $i ? 'white' : '#0052CC' ?>; background: <?= $projects['current_page'] == $i ? '#0052CC' : 'white' ?>; font-weight: 600; font-size: 0.9375rem; transition: all 0.2s;" 
                       <?= $projects['current_page'] != $i ? "onmouseover=\"this.style.borderColor='#0052CC'; this.style.backgroundColor='#DEEBFF'\" onmouseout=\"this.style.borderColor='#DFE1E6'; this.style.backgroundColor='white'\"" : '' ?>>
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
                <li>
                    <a href="<?= $projects['current_page'] >= $projects['last_page'] ? '#' : url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $projects['current_page'] + 1]))) ?>" 
                       style="padding: 0.5rem 0.75rem; border: 1px solid #DFE1E6; border-radius: 4px; text-decoration: none; color: <?= $projects['current_page'] >= $projects['last_page'] ? '#97A0AF' : '#0052CC' ?>; background: white; font-weight: 600; font-size: 0.9375rem; transition: all 0.2s; cursor: <?= $projects['current_page'] < $projects['last_page'] ? 'pointer' : 'not-allowed' ?>;" 
                       <?= $projects['current_page'] < $projects['last_page'] ? "onmouseover=\"this.style.borderColor='#0052CC'; this.style.backgroundColor='#DEEBFF'\" onmouseout=\"this.style.borderColor='#DFE1E6'; this.style.backgroundColor='white'\"" : '' ?>>
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDropdown(event) {
    event.stopPropagation();
    const menu = event.target.closest('button').nextElementSibling;
    const allMenus = document.querySelectorAll('.projects-dropdown-menu');
    allMenus.forEach(m => {
        if (m !== menu) m.style.display = 'none';
    });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', function() {
    document.querySelectorAll('.projects-dropdown-menu').forEach(m => m.style.display = 'none');
});
</script>

<?php \App\Core\View::endSection(); ?>
