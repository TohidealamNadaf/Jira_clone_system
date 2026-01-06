<?php
/**
 * Health API Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;

class HealthApiController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check(): never
    {
        json([
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => '1.0.0',
        ]);
    }
}