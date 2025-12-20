<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background-color: transparent; padding: 0; gap: 8px;">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>" style="color: var(--jira-blue); text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>" style="color: var(--jira-blue); text-decoration: none;">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>" style="color: var(--jira-blue); text-decoration: none;"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active" style="color: #626F86;">Sprints</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4" style="gap: 24px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin: 0 0 4px 0; letter-spacing: -0.2px;">Sprints</h1>
            <p style="font-size: 15px; color: #626F86; margin: 0;">View and manage project sprints</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button id="createSprintBtn" style="background-color: var(--jira-blue); color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='var(--jira-blue-dark)'; this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.backgroundColor='var(--jira-blue)'; this.style.transform='translateY(0)'">
                <i class="bi bi-plus-lg"></i> Create Sprint
            </button>
            <a href="<?= url("/projects/{$project['key']}") ?>" 
               style="background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <!-- Sprints List -->
    <?php if (empty($sprints)): ?>
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 60px 20px; text-align: center; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <div style="font-size: 48px; margin-bottom: 16px;">⚡</div>
        <p style="font-size: 15px; color: #626F86; margin: 0;">No sprints have been created yet.</p>
    </div>
    <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
        <?php foreach ($sprints as $sprint): ?>
        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); transition: all 0.2s; cursor: pointer;"
             onmouseover="this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.12)'; this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)'; this.style.transform='translateY(0)'">
            
            <!-- Header -->
            <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0; margin-bottom: 4px;">
                            <?= e($sprint['name']) ?>
                        </h3>
                    </div>
                    <span style="background-color: 
                        <?php 
                            switch($sprint['status']) {
                                case 'planning': echo '#DEEAFE; color: var(--jira-blue);'; break;
                                case 'active': echo '#DFFCF0; color: #216E4E;'; break;
                                case 'completed': echo '#F3F0FF; color: #352C63;'; break;
                                default: echo '#F1F2F4; color: #626F86;';
                            }
                        ?>; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 20px;">
                <!-- Status Detail -->
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Status</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0;">
                        <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                    </p>
                </div>

                <!-- Start Date -->
                <?php if ($sprint['start_date']): ?>
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Start Date</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-calendar3" style="color: var(--jira-blue);"></i>
                        <?= date('M j, Y', strtotime($sprint['start_date'])) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- End Date -->
                <?php if ($sprint['end_date']): ?>
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">End Date</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-calendar3-range" style="color: var(--jira-blue);"></i>
                        <?= date('M j, Y', strtotime($sprint['end_date'])) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Goal -->
                <?php if ($sprint['goal']): ?>
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Goal</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0; line-height: 1.5;">
                        <?= e($sprint['goal']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div style="padding: 12px 20px; background-color: #F7F8FA; border-top: 1px solid #DFE1E6; display: flex; gap: 8px;">
                <a href="<?= url("/projects/{$project['key']}/sprints/{$sprint['id']}/board") ?>" 
                   style="flex: 1; background-color: var(--jira-blue); color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: 500; font-size: 13px; cursor: pointer; display: block; text-align: center; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.opacity='0.9'"
                   onmouseout="this.style.opacity='1'">
                    <i class="bi bi-kanban"></i> View Board
                </a>
                <a href="<?= url("/projects/{$project['key']}/sprints/{$sprint['id']}") ?>" 
                   style="flex: 1; background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 12px; border-radius: 4px; font-weight: 500; font-size: 13px; cursor: pointer; display: block; text-align: center; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.backgroundColor='#DEEAFE'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    <i class="bi bi-gear"></i> Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    </div>

    <!-- Create Sprint Modal -->
    <div id="createSprintModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
       <!-- Modal Header -->
       <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
           <h2 style="font-size: 24px; font-weight: 700; color: #161B22; margin: 0;">Create Sprint</h2>
           <button type="button" onclick="document.getElementById('createSprintModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #626F86;">
               ×
           </button>
       </div>

       <!-- Form -->
       <form id="createSprintForm" style="display: flex; flex-direction: column; gap: 16px;">
           <!-- Sprint Name -->
           <div>
               <label style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                   Sprint Name <span style="color: #AE2A19;">*</span>
               </label>
               <input type="text" name="name" id="sprintName" 
                      style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit;"
                      placeholder="e.g., Sprint 5" required>
           </div>

           <!-- Sprint Goal -->
           <div>
               <label style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                   Sprint Goal
               </label>
               <textarea name="goal" id="sprintGoal" 
                         style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 100px;"
                         placeholder="What do you want to accomplish?"></textarea>
           </div>

           <!-- Start Date -->
           <div>
               <label style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                   Start Date
               </label>
               <input type="date" name="start_date" id="startDate"
                      style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit;">
           </div>

           <!-- End Date -->
           <div>
               <label style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                   End Date
               </label>
               <input type="date" name="end_date" id="endDate"
                      style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit;">
           </div>

           <!-- Error Message -->
           <div id="sprintError" style="display: none; padding: 12px; background-color: #FFECEB; color: #AE2A19; border-radius: 4px; font-size: 13px;"></div>

           <!-- Buttons -->
           <div style="display: flex; gap: 12px; margin-top: 16px;">
               <button type="button" onclick="document.getElementById('createSprintModal').style.display='none'"
                       style="flex: 1; padding: 10px 16px; background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                   Cancel
               </button>
               <button type="submit" id="submitSprintBtn"
                       style="flex: 1; padding: 10px 16px; background-color: var(--jira-blue); color: white; border: none; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                   Create Sprint
               </button>
           </div>
       </form>
    </div>
    </div>

    <style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #626F86;
        margin: 0 8px;
    }
    </style>

    <script>
    document.getElementById('createSprintBtn').addEventListener('click', function() {
    document.getElementById('createSprintModal').style.display = 'flex';
    });

    document.getElementById('createSprintModal').addEventListener('click', function(e) {
    if (e.target === this) {
       this.style.display = 'none';
    }
    });

    document.getElementById('createSprintForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const errorDiv = document.getElementById('sprintError');
    errorDiv.style.display = 'none';
    
    const formData = {
       name: document.getElementById('sprintName').value,
       goal: document.getElementById('sprintGoal').value || null,
       start_date: document.getElementById('startDate').value || null,
       end_date: document.getElementById('endDate').value || null,
    };
    
    try {
       const response = await fetch('<?= url("/projects/{$project['key']}/sprints") ?>', {
           method: 'POST',
           headers: {
               'Content-Type': 'application/json',
               'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
           },
           body: JSON.stringify(formData)
       });
       
       if (response.ok) {
           location.reload();
       } else {
           const data = await response.json();
           errorDiv.textContent = data.error || 'Failed to create sprint';
           errorDiv.style.display = 'block';
       }
    } catch (error) {
       errorDiv.textContent = 'Error creating sprint: ' + error.message;
       errorDiv.style.display = 'block';
    }
    });
    </script>

    <?php \App\Core\View::endSection(); ?>
