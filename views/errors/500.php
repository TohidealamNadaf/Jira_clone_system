<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            color: white;
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
    </style>
</head>
<body>
    <div class="error-container">
        <i class="bi bi-exclamation-triangle error-icon d-block mb-3"></i>
        <div class="error-code">500</div>
        <h2 class="mb-4">Server Error</h2>
        <p class="lead mb-4 opacity-75">
            Something went wrong on our end. We're working to fix it.
        </p>
        <?php if (config('app.debug') && isset($exception)): ?>
        <div class="card bg-dark text-white text-start mb-4 mx-auto" style="max-width: 600px;">
            <div class="card-header">
                <i class="bi bi-bug me-2"></i> Debug Information
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Message:</strong> <?= e($exception->getMessage()) ?></p>
                <p class="mb-1"><strong>File:</strong> <?= e($exception->getFile()) ?>:<?= e($exception->getLine()) ?></p>
                <details class="mt-3">
                    <summary class="cursor-pointer">Stack Trace</summary>
                    <pre class="mt-2 small overflow-auto" style="max-height: 200px;"><?= e($exception->getTraceAsString()) ?></pre>
                </details>
            </div>
        </div>
        <?php endif; ?>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('/') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-house me-2"></i> Go Home
            </a>
            <a href="javascript:location.reload()" class="btn btn-outline-light btn-lg">
                <i class="bi bi-arrow-clockwise me-2"></i> Try Again
            </a>
        </div>
    </div>
</body>
</html>
