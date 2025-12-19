<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?> - <?= e(config('app.name')) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* ===================================
           Enterprise Jira-Like Auth Layout
           =================================== */
        
        :root {
            --jira-blue: #8B1956;
            --text-primary: #161B22;
            --text-secondary: #626F86;
            --bg-primary: #FFFFFF;
            --bg-secondary: #F7F8FA;
            --border-color: #DFE1E6;
            --shadow-md: 0 4px 12px rgba(9, 30, 66, 0.15);
        }
        
        body {
            background: linear-gradient(135deg, #F7F8FA 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            color: var(--text-primary);
        }
        
        body::before {
            content: '';
            position: fixed;
            top: -250px;
            right: -250px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 25, 86, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        
        .auth-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            max-width: 420px;
            width: 100%;
            padding: 40px;
            position: relative;
            z-index: 1;
        }
        
        .auth-logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            text-align: center;
            letter-spacing: -0.3px;
        }
        
        .auth-logo i {
            display: block;
            font-size: 2rem;
            color: var(--jira-blue);
            margin-bottom: 12px;
            width: 56px;
            height: 56px;
            background: #f0dce5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Alert Styling */
        .alert {
            font-size: 14px;
            border-radius: 6px;
        }
        
        .alert-success {
            background-color: #DFFCF0;
            border-color: #216E4E;
            color: #216E4E;
        }
        
        .alert-danger {
            background-color: #FFECEB;
            border-color: #ED3C32;
            color: #AE2A19;
        }
        
        .alert-warning {
            background-color: #FFF3C1;
            border-color: #974F0C;
            color: #7F5F01;
        }
        
        /* Footer */
        .text-muted {
            color: var(--text-secondary) !important;
            font-size: 13px;
        }
        
        /* Responsive */
        @media (max-width: 767px) {
            .auth-card {
                padding: 24px;
                border-radius: 12px 12px 0 0;
                max-width: 100%;
            }
            
            .auth-logo {
                margin-bottom: 6px;
            }
            
            .auth-logo i {
                width: 48px;
                height: 48px;
                font-size: 24px;
            }
        }
        
        @media (max-width: 479px) {
            .auth-card {
                padding: 20px;
            }
            
            .auth-logo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-kanban d-block mb-2"></i>
            <?= e(config('app.name')) ?>
        </div>
        
        <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning'] as $type => $class): ?>
        <?php if ($message = ($flash[$type] ?? null)): ?>
        <div class="alert alert-<?= $class ?> alert-dismissible fade show" role="alert">
            <?= e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>

        <?= \App\Core\View::yield('content') ?>
        
        <div class="text-center mt-4 text-muted small">
            &copy; <?= date('Y') ?> <?= e(config('app.name')) ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
