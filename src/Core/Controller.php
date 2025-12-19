<?php
/**
 * Base Controller
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected Request $request;

    /**
     * Render a view
     */
    protected function view(string $name, array $data = []): string
    {
        // Add common data
        $data['user'] = auth();
        $data['errors'] = errors();
        $data['flash'] = [
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
            'warning' => Session::getFlash('warning'),
            'info' => Session::getFlash('info'),
        ];

        return View::render($name, $data);
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $status = 200): never
    {
        json($data, $status);
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $status = 302): never
    {
        redirect($url, $status);
    }

    /**
     * Redirect back with errors
     */
    protected function back(): never
    {
        back();
    }

    /**
     * Flash message and redirect
     */
    protected function redirectWith(string $url, string $type, string $message): never
    {
        Session::flash($type, $message);
        $this->redirect($url);
    }

    /**
     * Validate request
     */
    protected function validate(Request $request, array $rules): array
    {
        return $request->validate($rules);
    }

    /**
     * Authorize an action
     */
    protected function authorize(string $permission, ?int $projectId = null): void
    {
        if (!can($permission, $projectId)) {
            abort(403, 'Unauthorized action');
        }
    }

    /**
     * Get authenticated user
     */
    protected function user(): ?array
    {
        return auth();
    }

    /**
     * Check if user is authenticated
     */
    protected function authenticated(): bool
    {
        return Session::check();
    }

    /**
     * Get user ID
     */
    protected function userId(): ?int
    {
        return user_id();
    }

    /**
     * Paginate results
     */
    protected function paginate(string $sql, array $params, int $perPage = null): array
    {
        $perPage = $perPage ?? config('pagination.per_page', 25);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countSql = preg_replace('/SELECT .+ FROM/i', 'SELECT COUNT(*) FROM', $sql);
        $countSql = preg_replace('/ORDER BY .+$/i', '', $countSql);
        $total = (int) Database::selectValue($countSql, $params);

        // Get paginated results
        $sql .= " LIMIT $perPage OFFSET $offset";
        $items = Database::select($sql, $params);

        $lastPage = (int) ceil($total / $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'has_more' => $page < $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ];
    }

    /**
     * Handle file upload
     */
    protected function uploadFile(array $file, string $directory = ''): ?array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validate file type
        $allowedTypes = config('upload.allowed_types', []);
        if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Validate file size
        $maxSize = config('upload.max_size', 10 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            return null;
        }

        // Generate secure filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        // Create directory structure (year/month)
        $subPath = date('Y/m');
        if ($directory) {
            $subPath = trim($directory, '/') . '/' . $subPath;
        }

        $uploadPath = config('upload.path', 'uploads');
        $fullPath = public_path($uploadPath . '/' . $subPath);

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Move uploaded file
        $destination = $fullPath . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return null;
        }

        return [
            'filename' => $filename,
            'original_name' => $file['name'],
            'mime_type' => $file['type'],
            'size' => $file['size'],
            'path' => $uploadPath . '/' . $subPath . '/' . $filename,
        ];
    }

    /**
     * Delete uploaded file
     */
    protected function deleteFile(string $path): bool
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
