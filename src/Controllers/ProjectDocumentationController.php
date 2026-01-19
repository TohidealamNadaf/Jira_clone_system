<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Controller;
use App\Core\Validator;
use App\Services\ProjectDocumentationService;
use App\Services\ProjectService;
use Exception;

/**
 * ProjectDocumentationController
 * 
 * Handles all project documentation operations:
 * - Display documentation hub
 * - Upload, update, delete documents
 * - Download documents with tracking
 */
class ProjectDocumentationController extends Controller
{
    private ProjectDocumentationService $docService;
    private ProjectService $projectService;

    public function __construct()
    {
        $this->docService = new ProjectDocumentationService();
        $this->projectService = new ProjectService();
    }

    /**
     * Display documentation hub for a project
     * 
     * @param Request $request The request object
     * @return string
     */
    public function index(Request $request): string
    {
        try {
            // Get project key from route parameters
            $projectKey = $request->param('key');

            // Get project details
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                return $this->view('errors/404', ['message' => 'Project not found']);
            }

            // Get filters from request
            $request = new Request();
            $filters = [
                'category' => $request->query('category', ''),
                'search' => $request->query('search', '')
            ];

            // Get documents
            $documents = $this->docService->getProjectDocuments($project['id'], $filters);

            // Get statistics
            $stats = $this->docService->getProjectDocumentStats($project['id']);

            // Get categories and allowed types for view
            $categories = ProjectDocumentationService::getCategories();
            $allowedTypes = ProjectDocumentationService::getAllowedTypes();

            return $this->view('projects/documentation', [
                'project' => $project,
                'documents' => $documents,
                'stats' => $stats,
                'categories' => $categories,
                'allowedTypes' => $allowedTypes,
                'filters' => $filters
            ]);

        } catch (Exception $e) {
            error_log("Error in documentation index: " . $e->getMessage());
            return $this->view('errors/500', ['message' => 'Failed to load documentation']);
        }
    }

    /**
     * Upload a new document
     * 
     * @param Request $request The request object
     * @return void JSON response
     */
    public function upload(Request $request): void
    {
        try {
            // Get project key from route parameters
            $projectKey = $request->param('key');

            // Validate project
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                $this->json(['success' => false, 'error' => 'Project not found'], 404);
                return;
            }

            // Validate file upload
            if (!isset($_FILES['documents'])) {
                $this->json(['success' => false, 'error' => 'No files uploaded'], 400);
                return;
            }

            $files = $_FILES['documents'];
            $fileCount = is_array($files['name']) ? count($files['name']) : 1;

            // Normalize files array if it's multiple
            $uploadedFiles = [];
            if (is_array($files['name'])) {
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $uploadedFiles[] = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                    }
                }
            } else {
                if ($files['error'] === UPLOAD_ERR_OK) {
                    $uploadedFiles[] = $files;
                }
            }

            if (empty($uploadedFiles)) {
                $this->json(['success' => false, 'error' => 'No valid files uploaded or upload error occurred'], 400);
                return;
            }

            // Validate required fields (title is only required for single file upload if not provided)
            $isMulti = count($uploadedFiles) > 1;
            $validationRules = [
                'category' => 'required|in:requirement,design,technical,user_guide,training,report,other,specification'
            ];

            if (!$isMulti) {
                $validationRules['title'] = 'required|max:255';
            }

            $validator = new Validator($request->all(), $validationRules);

            if ($validator->fails()) {
                $this->json(['success' => false, 'error' => 'Validation failed', 'errors' => $validator->errors()], 422);
                return;
            }

            $results = [];
            $successCount = 0;
            $userId = $_SESSION['user']['id'] ?? 1;

            foreach ($uploadedFiles as $file) {
                // Determine title: use form title only if it's a single upload, otherwise use filename
                $title = ($isMulti || empty($request->input('title')))
                    ? pathinfo($file['name'], PATHINFO_FILENAME)
                    : $request->input('title');

                $documentData = [
                    'title' => $title,
                    'description' => $request->input('description', ''),
                    'category' => $request->input('category', 'other'),
                    'version' => $request->input('version', '1.0'),
                    'is_public' => $request->input('is_public', '1') === '1'
                ];

                $result = $this->docService->uploadDocument($file, $project['id'], $userId, $documentData);

                if ($result['success']) {
                    $successCount++;
                    $results[] = $result['document_id'];
                }
            }

            if ($successCount > 0) {
                $this->json([
                    'success' => true,
                    'message' => $successCount . " document(s) uploaded successfully",
                    'document_ids' => $results
                ]);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to upload any documents'], 400);
            }

        } catch (Exception $e) {
            error_log("Error uploading document: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Failed to upload document'], 500);
        }
    }

    /**
     * Update document metadata
     * 
     * @param string $projectKey The project key
     * @param int $documentId The document ID
     * @return void JSON response
     */
    public function update(Request $request): void
    {
        try {
            // Get route parameters
            $projectKey = $request->param('key');
            $documentId = (int) $request->param('documentId');

            // Validate project
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                $this->json(['success' => false, 'error' => 'Project not found'], 404);
                return;
            }

            // Validate document exists and belongs to project
            $document = $this->docService->getDocument($documentId);
            if (!$document || $document['project_id'] != $project['id']) {
                $this->json(['success' => false, 'error' => 'Document not found'], 404);
                return;
            }

            // Validate input
            $validator = new Validator($request->all(), [
                'title' => 'required|max:255',
                'category' => 'in:requirement,design,technical,user_guide,training,report,other'
            ]);

            if ($validator->fails()) {
                $this->json(['success' => false, 'error' => 'Validation failed', 'errors' => $validator->errors()], 422);
                return;
            }

            // Update document
            $updateData = [
                'title' => $request->post('title'),
                'description' => $request->post('description', ''),
                'category' => $request->post('category', 'other'),
                'version' => $request->post('version', '1.0'),
                'is_public' => $request->post('is_public', '1') === '1'
            ];

            $success = $this->docService->updateDocument($documentId, $updateData);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Document updated successfully']);
            } else {
                $this->json(['success' => false, 'error' => 'No changes made'], 400);
            }

        } catch (Exception $e) {
            error_log("Error updating document: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Failed to update document'], 500);
        }
    }

    /**
     * Delete a document
     * 
     * @param string $projectKey The project key
     * @param int $documentId The document ID
     * @return void JSON response
     */
    public function delete(Request $request): void
    {
        try {
            // Get route parameters
            $projectKey = $request->param('key');
            $documentId = (int) $request->param('documentId');

            // Validate project
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                $this->json(['success' => false, 'error' => 'Project not found'], 404);
                return;
            }

            // Validate document exists and belongs to project
            $document = $this->docService->getDocument($documentId);
            if (!$document || $document['project_id'] != $project['id']) {
                $this->json(['success' => false, 'error' => 'Document not found'], 404);
                return;
            }

            // Delete document
            $result = $this->docService->deleteDocument($documentId);

            if ($result['success']) {
                $this->json(['success' => true, 'message' => $result['message']]);
            } else {
                $this->json(['success' => false, 'error' => $result['error']], 400);
            }

        } catch (Exception $e) {
            error_log("Error deleting document: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Failed to delete document'], 500);
        }
    }

    /**
     * Download a document
     * 
     * @param string $projectKey The project key
     * @param int $documentId The document ID
     * @return void File download
     */
    public function download(Request $request): void
    {
        try {
            // Get route parameters
            $projectKey = $request->param('key');
            $documentId = (int) $request->param('documentId');

            // Validate project
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                http_response_code(404);
                echo 'Project not found';
                return;
            }

            // Get document
            $document = $this->docService->getDocument($documentId);
            if (!$document || $document['project_id'] != $project['id']) {
                http_response_code(404);
                echo 'Document not found';
                return;
            }

            // Check if file exists
            if (!file_exists($document['path'])) {
                http_response_code(404);
                echo 'File not found on server';
                return;
            }

            // Increment download count only if not preview
            if (!$request->query('preview')) {
                $this->docService->incrementDownloadCount($documentId);
            }

            // Set headers for download/preview
            $disposition = $request->query('preview') ? 'inline' : 'attachment';
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $document['mime_type']);
            header('Content-Disposition: ' . $disposition . '; filename="' . $document['original_filename'] . '"');
            header('Content-Length: ' . filesize($document['path']));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output file
            readfile($document['path']);
            exit;

        } catch (Exception $e) {
            error_log("Error downloading document: " . $e->getMessage());
            http_response_code(500);
            echo 'Failed to download document';
        }
    }

    /**
     * Get document details for AJAX requests
     * 
     * @param string $projectKey The project key
     * @param int $documentId The document ID
     * @return void JSON response
     */
    public function getDocument(Request $request): void
    {
        try {
            // Get route parameters
            $projectKey = $request->param('key');
            $documentId = (int) $request->param('documentId');

            // Validate project
            $project = $this->projectService->getProjectByKey($projectKey);
            if (!$project) {
                $this->json(['success' => false, 'error' => 'Project not found'], 404);
                return;
            }

            // Get document
            $document = $this->docService->getDocument($documentId);
            if (!$document || $document['project_id'] != $project['id']) {
                $this->json(['success' => false, 'error' => 'Document not found'], 404);
                return;
            }

            // Add formatted data
            $document['formatted_size'] = ProjectDocumentationService::formatFileSize($document['size']);
            $document['file_icon'] = ProjectDocumentationService::getFileIcon($document['mime_type'], $document['filename']);

            $this->json(['success' => true, 'document' => $document]);

        } catch (Exception $e) {
            error_log("Error getting document: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Failed to get document'], 500);
        }
    }
}