<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            color: white;
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            line-height: 1;
            text-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .error-icon {
            font-size: 4rem;
            opacity: 0.8;
        }
        .error-message {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin: 2rem 0;
        }
        .error-message p {
            margin: 0;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="bi bi-shield-lock error-icon d-block mb-3"></i>
        <div class="error-code">403</div>
        <h2 class="mb-4">Access Forbidden</h2>
        
        <?php if (isset($message) && !empty($message)): ?>
        <div class="error-message">
            <p>
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= htmlspecialchars($message) ?>
            </p>
        </div>
        <?php else: ?>
        <p class="lead mb-4 opacity-75">
            You don't have permission to access this resource.
        </p>
        <?php endif; ?>
        
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= url('/') ?>" class="btn btn-dark btn-lg">
                <i class="bi bi-house me-2"></i> Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-dark btn-lg">
                <i class="bi bi-arrow-left me-2"></i> Go Back
            </a>
            <?php if (!isset($user)): ?>
            <a href="<?= url('/login') ?>" class="btn btn-outline-dark btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login
            </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
