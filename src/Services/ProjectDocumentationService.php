<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * ProjectDocumentationService
 * 
 * Handles all project documentation operations:
 * - Upload and store documents
 * - Retrieve document lists and details
 * - Manage document versions and categories
 * - Track downloads and permissions
 * - Support multiple file types (PDF, DOC, XLS, PPT, videos, audio, etc.)
 */
class ProjectDocumentationService
{
    private const TABLE_DOCUMENTS = 'project_documents';
    private const UPLOAD_PATH = 'uploads/documents/';

    // Allowed file types and their display names
    private const ALLOWED_TYPES = [
        // Documents
        'pdf' => 'PDF Document',
        'doc' => 'Microsoft Word Document',
        'docx' => 'Microsoft Word Document',
        'xls' => 'Microsoft Excel Spreadsheet',
        'xlsx' => 'Microsoft Excel Spreadsheet',
        'ppt' => 'Microsoft PowerPoint Presentation',
        'pptx' => 'Microsoft PowerPoint Presentation',
        'txt' => 'Text File',
        'rtf' => 'Rich Text Format',
        'odt' => 'OpenDocument Text',
        'ods' => 'OpenDocument Spreadsheet',
        'odp' => 'OpenDocument Presentation',

        // Reports
        'rpt' => 'Report File',

        // Images
        'jpg' => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png' => 'PNG Image',
        'gif' => 'GIF Image',
        'bmp' => 'Bitmap Image',
        'svg' => 'SVG Vector Image',

        // Videos
        'mp4' => 'MP4 Video',
        'avi' => 'AVI Video',
        'mov' => 'QuickTime Video',
        'wmv' => 'Windows Media Video',
        'flv' => 'Flash Video',
        'webm' => 'WebM Video',
        'mkv' => 'Matroska Video',

        // Audio
        'mp3' => 'MP3 Audio',
        'wav' => 'WAV Audio',
        'flac' => 'FLAC Audio',
        'aac' => 'AAC Audio',
        'ogg' => 'OGG Audio',
        'wma' => 'Windows Media Audio',

        // Archives
        'zip' => 'ZIP Archive',
        'rar' => 'RAR Archive',
        '7z' => '7-Zip Archive',
        'tar' => 'TAR Archive',
        'gz' => 'GZIP Archive'
    ];

    // Category labels
    private const CATEGORIES = [
        'requirement' => 'Requirements',
        'design' => 'Design',
        'technical' => 'Technical',
        'user_guide' => 'User Guides',
        'training' => 'Training',
        'report' => 'Reports',
        'other' => 'Other'
    ];

    /**
     * Get all documents for a project
     * 
     * @param int $projectId The project ID
     * @param array $filters Optional filters (category, search)
     * @return array List of documents
     */
    public function getProjectDocuments(int $projectId, array $filters = []): array
    {
        try {
            $sql = "
                SELECT d.*, u.first_name, u.last_name, u.email, p.name as project_name, p.key as project_key
                FROM " . self::TABLE_DOCUMENTS . " d
                INNER JOIN users u ON d.user_id = u.id
                INNER JOIN projects p ON d.project_id = p.id
                WHERE d.project_id = ?
            ";

            $params = [$projectId];

            // Apply category filter
            if (!empty($filters['category'])) {
                $sql .= " AND d.category = ?";
                $params[] = $filters['category'];
            }

            // Apply search filter
            if (!empty($filters['search'])) {
                $sql .= " AND (d.title LIKE ? OR d.description LIKE ? OR d.original_filename LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $sql .= " ORDER BY d.created_at DESC";

            return Database::select($sql, $params);

        } catch (Exception $e) {
            error_log("Error getting project documents: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single document by ID
     * 
     * @param int $documentId The document ID
     * @return array|null Document data or null if not found
     */
    public function getDocument(int $documentId): ?array
    {
        try {
            $sql = "
                SELECT d.*, u.first_name, u.last_name, u.email, p.name as project_name, p.key as project_key
                FROM " . self::TABLE_DOCUMENTS . " d
                INNER JOIN users u ON d.user_id = u.id
                INNER JOIN projects p ON d.project_id = p.id
                WHERE d.id = ?
            ";

            $result = Database::select($sql, [$documentId]);
            return $result[0] ?? null;

        } catch (Exception $e) {
            error_log("Error getting document: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload and save a new document
     * 
     * @param array $file The uploaded file data from $_FILES
     * @param int $projectId The project ID
     * @param int $userId The user uploading the file
     * @param array $documentData Document metadata (title, description, category, etc.)
     * @return array Result with success status and document ID or error
     */
    public function uploadDocument(array $file, int $projectId, int $userId, array $documentData): array
    {
        try {
            // Validate file
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return ['success' => false, 'error' => $validation['error']];
            }

            // Generate unique filename
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $uniqueFilename = uniqid('doc_', true) . '.' . $fileExtension;

            // Define absolute upload path
            $baseUploadPath = public_path(self::UPLOAD_PATH);
            $fullFilePath = $baseUploadPath . $uniqueFilename;
            $dbPath = self::UPLOAD_PATH . $uniqueFilename;

            // Create upload directory if it doesn't exist
            if (!is_dir($baseUploadPath)) {
                mkdir($baseUploadPath, 0755, true);
            }

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $fullFilePath)) {
                return ['success' => false, 'error' => 'Failed to save uploaded file'];
            }

            // Insert database record
            $insertData = [
                'project_id' => $projectId,
                'user_id' => $userId,
                'title' => $documentData['title'] ?? $file['name'],
                'description' => $documentData['description'] ?? null,
                'filename' => $uniqueFilename,
                'original_filename' => $file['name'],
                'mime_type' => $file['type'],
                'size' => $file['size'],
                'path' => $dbPath,
                'category' => $documentData['category'] ?? 'other',
                'version' => $documentData['version'] ?? '1.0',
                'is_public' => $documentData['is_public'] ? 1 : 0
            ];

            $documentId = Database::insert(self::TABLE_DOCUMENTS, $insertData);

            return [
                'success' => true,
                'document_id' => $documentId,
                'filename' => $uniqueFilename,
                'message' => 'Document uploaded successfully'
            ];

        } catch (Exception $e) {
            error_log("Error uploading document: " . $e->getMessage());
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }

    /**
     * Update document metadata
     * 
     * @param int $documentId The document ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateDocument(int $documentId, array $data): bool
    {
        try {
            $fields = [];
            $params = [];

            foreach (['title', 'description', 'category', 'version', 'is_public'] as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            if (empty($fields)) {
                return false;
            }

            $setData = [];
            foreach ($fields as $index => $field) {
                $paramName = "field_$index";
                $setData[trim($field, ' ?')] = $params[$index];
            }

            return Database::update(self::TABLE_DOCUMENTS, $setData, 'id = ?', [$documentId]) > 0;

        } catch (Exception $e) {
            error_log("Error updating document: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a document
     * 
     * @param int $documentId The document ID
     * @return array Result with success status
     */
    public function deleteDocument(int $documentId): array
    {
        try {
            // Get document info for file deletion
            $document = $this->getDocument($documentId);
            if (!$document) {
                return ['success' => false, 'error' => 'Document not found'];
            }

            // Delete from database
            $result = Database::delete(self::TABLE_DOCUMENTS, 'id = ?', [$documentId]);

            if ($result > 0) {
                // Delete physical file
                if (file_exists($document['path'])) {
                    unlink($document['path']);
                }

                return ['success' => true, 'message' => 'Document deleted successfully'];
            }

            return ['success' => false, 'error' => 'Failed to delete document'];

        } catch (Exception $e) {
            error_log("Error deleting document: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete document'];
        }
    }

    /**
     * Increment download count
     * 
     * @param int $documentId The document ID
     * @return bool Success status
     */
    public function incrementDownloadCount(int $documentId): bool
    {
        try {
            return Database::update(self::TABLE_DOCUMENTS, ['download_count' => 'download_count + 1'], 'id = ?', [$documentId]) > 0;

        } catch (Exception $e) {
            error_log("Error incrementing download count: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get document statistics for a project
     * 
     * @param int $projectId The project ID
     * @return array Statistics
     */
    public function getProjectDocumentStats(int $projectId): array
    {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN category = 'requirement' THEN 1 ELSE 0 END) as requirements,
                    SUM(CASE WHEN category = 'design' THEN 1 ELSE 0 END) as designs,
                    SUM(CASE WHEN category = 'technical' THEN 1 ELSE 0 END) as technical,
                    SUM(CASE WHEN category = 'user_guide' THEN 1 ELSE 0 END) as user_guides,
                    SUM(CASE WHEN category = 'training' THEN 1 ELSE 0 END) as training,
                    SUM(CASE WHEN category = 'report' THEN 1 ELSE 0 END) as reports,
                    SUM(CASE WHEN category = 'other' THEN 1 ELSE 0 END) as other,
                    SUM(size) as total_size,
                    MAX(created_at) as latest_upload
                FROM " . self::TABLE_DOCUMENTS . "
                WHERE project_id = ?
            ";

            $result = Database::select($sql, [$projectId]);
            $stats = $result[0] ?? [
                'total_documents' => 0,
                'requirements' => 0,
                'designs' => 0,
                'technical' => 0,
                'user_guides' => 0,
                'training' => 0,
                'reports' => 0,
                'other' => 0,
                'total_size' => 0,
                'latest_upload' => null
            ];

            // Ensure all keys are integers
            $stats['total_documents'] = (int) ($stats['total_documents'] ?? 0);
            $stats['requirements'] = (int) ($stats['requirements'] ?? 0);
            $stats['designs'] = (int) ($stats['designs'] ?? 0);
            $stats['technical'] = (int) ($stats['technical'] ?? 0);
            $stats['user_guides'] = (int) ($stats['user_guides'] ?? 0); // Fixed typo
            $stats['training'] = (int) ($stats['training'] ?? 0);
            $stats['reports'] = (int) ($stats['reports'] ?? 0);
            $stats['other'] = (int) ($stats['other'] ?? 0);
            $stats['total_size'] = (int) ($stats['total_size'] ?? 0);

            return $stats;

        } catch (Exception $e) {
            error_log("Error getting document stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validate uploaded file
     * 
     * @param array $file File data from $_FILES
     * @return array Validation result
     */
    private function validateFile(array $file): array
    {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded or upload error'];
        }

        // Check file size (50MB max)
        $maxSize = 50 * 1024 * 1024; // 50MB in bytes
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File size exceeds 50MB limit'];
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($extension, self::ALLOWED_TYPES)) {
            return ['valid' => false, 'error' => 'File type not allowed. Allowed types: ' . implode(', ', array_keys(self::ALLOWED_TYPES))];
        }

        return ['valid' => true];
    }

    /**
     * Get allowed file types
     * 
     * @return array Allowed file types with display names
     */
    public static function getAllowedTypes(): array
    {
        return self::ALLOWED_TYPES;
    }

    /**
     * Get document categories
     * 
     * @return array Categories with labels
     */
    public static function getCategories(): array
    {
        return self::CATEGORIES;
    }

    /**
     * Format file size for display
     * 
     * @param int $bytes Size in bytes
     * @return string Formatted size
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get file type icon based on mime type or extension
     * 
     * @param string $mimeType The MIME type
     * @param string $filename The filename
     * @return string Bootstrap icon class
     */
    public static function getFileIcon(string $mimeType, string $filename = ''): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Documents
        if (in_array($extension, ['pdf'])) {
            return 'bi-file-earmark-pdf';
        }
        if (in_array($extension, ['doc', 'docx', 'rtf', 'odt'])) {
            return 'bi-file-earmark-word';
        }
        if (in_array($extension, ['xls', 'xlsx', 'ods'])) {
            return 'bi-file-earmark-excel';
        }
        if (in_array($extension, ['ppt', 'pptx', 'odp'])) {
            return 'bi-file-earmark-slides';
        }

        // Reports
        if (in_array($extension, ['rpt'])) {
            return 'bi-file-earmark-bar-graph';
        }

        // Images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
            return 'bi-file-earmark-image';
        }

        // Videos
        if (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'])) {
            return 'bi-file-earmark-play';
        }

        // Audio
        if (in_array($extension, ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'])) {
            return 'bi-file-earmark-music';
        }

        // Archives
        if (in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return 'bi-file-earmark-zip';
        }

        // Text
        if (in_array($extension, ['txt'])) {
            return 'bi-file-earmark-text';
        }

        return 'bi-file-earmark'; // Default icon
    }
}