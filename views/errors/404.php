<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        <i class="bi bi-search error-icon d-block mb-3"></i>
        <div class="error-code">404</div>
        <h2 class="mb-4">Page Not Found</h2>
        <p class="lead mb-4 opacity-75">
            Oops! The page you're looking for doesn't exist or has been moved.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('/') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-house me-2"></i> Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-light btn-lg">
                <i class="bi bi-arrow-left me-2"></i> Go Back
            </a>
        </div>
    </div>
</body>
</html>
