<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    /**
     * The base directory allowed for deletion (typically the project root).
     * Change this to restrict which directories are accessible.
     */
    protected string $basePath;

    public function __construct()
    {
        // Restrict deletions to only within the project's base path.
        $this->basePath = base_path();
    }

    /**
     * DELETE /api/file-manager/delete
     *
     * Body (JSON):
     *   - path (string, required): Relative path from the project root to the file/folder.
     *                              e.g. "public/uploads/photo.jpg" or "storage/app/temp"
     *
     * Returns JSON response indicating success or failure.
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $relativePath = $request->input('path');

        // Sanitize: strip leading slashes and resolve the real path safely
        $relativePath = ltrim(str_replace(['../', '..\\', '..'], '', $relativePath), '/\\');

        $targetPath = realpath($this->basePath . DIRECTORY_SEPARATOR . $relativePath);

        // Security: ensure the resolved path is within the allowed base path
        if ($targetPath === false) {
            return response()->json([
                'success' => false,
                'message' => 'Path does not exist: ' . $relativePath,
            ], 404);
        }

        if (!Str::startsWith($targetPath, $this->basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied: path is outside the allowed directory.',
            ], 403);
        }

        // Determine whether it is a file or directory and delete accordingly
        if (is_dir($targetPath)) {
            $deleted = File::deleteDirectory($targetPath);
            $type    = 'Directory';
        } elseif (is_file($targetPath)) {
            $deleted = File::delete($targetPath);
            $type    = 'File';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Target is neither a file nor a directory.',
            ], 422);
        }

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => "{$type} deleted successfully.",
                'path'    => $relativePath,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed to delete {$type}. Check permissions.",
        ], 500);
    }
}
