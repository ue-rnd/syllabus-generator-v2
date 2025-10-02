<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'file_path',
        'file_size',
        'tables_included',
        'backup_type',
        'status',
        'created_by',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'tables_included' => 'array',
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('backup_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            default => 'gray',
        };
    }

    public function getBackupTypeColorAttribute(): string
    {
        return match ($this->backup_type) {
            'full' => 'primary',
            'selective' => 'success',
            'manual' => 'info',
            'automated' => 'warning',
            default => 'gray',
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileExists(): bool
    {
        return Storage::disk('backups')->exists($this->file_path);
    }

    public function getDownloadUrl(): ?string
    {
        if (!$this->getFileExists()) {
            return null;
        }

        return Storage::disk('backups')->url($this->file_path);
    }

    public function deleteFile(): bool
    {
        if ($this->getFileExists()) {
            return Storage::disk('backups')->delete($this->file_path);
        }

        return true;
    }

    public static function getBackupTypeOptions(): array
    {
        return [
            'full' => 'Full Database',
            'selective' => 'Selected Tables',
            'manual' => 'Manual Backup',
            'automated' => 'Automated Backup',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];
    }

    public static function getMainTables(): array
    {
        return [
            'users' => 'Users',
            'colleges' => 'Colleges',
            'departments' => 'Departments',
            'programs' => 'Programs',
            'courses' => 'Courses',
            'syllabi' => 'Syllabi',
            'syllabus_suggestions' => 'Syllabus Suggestions',
            'quality_standards' => 'Quality Standards',
            'quality_audits' => 'Quality Audits',
            'quality_checklists' => 'Quality Checklists',
            'tutorials' => 'Tutorials',
            'tutorial_steps' => 'Tutorial Steps',
            'faqs' => 'FAQs',
            'roles' => 'Roles',
            'permissions' => 'Permissions',
            'model_has_permissions' => 'User Permissions',
            'model_has_roles' => 'User Roles',
            'role_has_permissions' => 'Role Permissions',
        ];
    }
}