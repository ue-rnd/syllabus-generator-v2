<?php

namespace App\Http\Controllers;

use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    /**
     * Download a database backup file
     */
    public function download(DatabaseBackup $backup): StreamedResponse
    {
        // Check if user has permission
        abort_unless(
            auth()->user()->can('manage backups') || auth()->user()->can('manage system settings'),
            403,
            'You do not have permission to download backups.'
        );

        // Check if backup file exists
        abort_unless(
            $backup->status === 'completed' && $backup->getFileExists(),
            404,
            'Backup file not found or backup is not completed.'
        );

        // Get the file from storage
        $disk = Storage::disk('backups');
        
        // Generate download filename
        $downloadName = sprintf(
            '%s_%s.sql',
            str_replace(' ', '_', strtolower($backup->name)),
            $backup->created_at->format('Y-m-d_H-i-s')
        );

        // Return the file as a download response
        return response()->streamDownload(function () use ($disk, $backup) {
            echo $disk->get($backup->file_path);
        }, $downloadName, [
            'Content-Type' => 'application/sql',
        ]);
    }
}
