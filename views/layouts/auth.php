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
        body {
            background: linear-gradient(135deg, #0052CC 0%, #2684FF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 420px;
            width: 100%;
            padding: 40px;
        }
        .auth-logo {
            font-size: 2rem;
            font-weight: bold;
            color: #0052CC;
            margin-bottom: 30px;
            text-align: center;
        }
        .auth-logo i {
            font-size: 2.5rem;
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
