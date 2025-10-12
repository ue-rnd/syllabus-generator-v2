# Database Backup Disk Configuration Fix

## Issue
**Error:** `InvalidArgumentException: Disk [backups] does not have a configured driver.`

The application was trying to use a `backups` disk for storing database backup files, but this disk was not configured in the filesystem configuration.

## Root Cause

The `DatabaseBackupService` and `DatabaseBackup` model both reference `Storage::disk('backups')`, but the `config/filesystems.php` file only had `local`, `public`, and `s3` disks configured.

### Code References:
1. **DatabaseBackup Model** (`app/Models/DatabaseBackup.php`):
   ```php
   public function getFileExists(): bool
   {
       return Storage::disk('backups')->exists($this->file_path);
   }
   ```

2. **DatabaseBackupService** (`app/Services/DatabaseBackupService.php`):
   ```php
   Storage::disk($this->backupDisk)->put($filePath, $sqlContent);
   // where $this->backupDisk = 'backups'
   ```

## Solution

### 1. Added Backups Disk Configuration
**File:** `config/filesystems.php`

Added the `backups` disk configuration between `public` and `s3`:

```php
'backups' => [
    'driver' => 'local',
    'root' => storage_path('app/backups'),
    'visibility' => 'private',
    'throw' => false,
    'report' => false,
],
```

**Configuration Details:**
- **Driver:** `local` - stores files on the local filesystem
- **Root:** `storage/app/backups` - dedicated directory for backup files
- **Visibility:** `private` - backups are not publicly accessible (security)
- **Throw:** `false` - returns false on error instead of throwing exceptions
- **Report:** `false` - doesn't report exceptions to error handler

### 2. Created Storage Directory Structure
Created the following directories:
```
storage/app/backups/
├── .gitignore          # Ignore backup files in git
└── database/           # Subdirectory for database backups
```

**File:** `storage/app/backups/.gitignore`
```
*
!.gitignore
```

This ensures:
- ✅ The directory structure exists in version control
- ✅ Backup files are not committed to git (can be large)
- ✅ The `.gitignore` file itself is tracked

### 3. Cleared Configuration Cache
```bash
php artisan config:clear
```

This ensures Laravel picks up the new disk configuration immediately.

## Files Modified

1. ✅ `config/filesystems.php` - Added backups disk configuration
2. ✅ `storage/app/backups/.gitignore` - NEW: Git ignore for backup files
3. ✅ `storage/app/backups/database/` - NEW: Directory structure

## Verification

### Test Disk Configuration:
```bash
php artisan tinker --execute="echo Storage::disk('backups')->path(''); exit;"
```

**Expected Output:**
```
/path/to/project/storage/app/backups/
```

### Directory Structure:
```
storage/
└── app/
    ├── backups/
    │   ├── .gitignore
    │   └── database/
    ├── public/
    └── private/
```

## Security Considerations

✅ **Private Visibility:** Backup files are stored with `private` visibility, meaning they cannot be accessed directly via URL.

✅ **Controlled Access:** Downloads are handled through the `DatabaseBackupController` which checks:
- User authentication
- Proper permissions (`manage backups` or `manage system settings`)
- Backup status (must be `completed`)
- File existence

✅ **No Direct URL Access:** Unlike the `public` disk, files in the `backups` disk cannot be accessed via direct URLs.

## Storage Path Pattern

Backup files are stored with the following pattern:
```
storage/app/backups/database/backup_{id}_{timestamp}.sql
```

Example:
```
storage/app/backups/database/backup_1_2025-10-12_13-26-45.sql
```

## Environment Considerations

### Development (SQLite):
- Works with current configuration
- Backups stored locally

### Production:
Consider configuring a dedicated backup disk using:
- **Local storage** with regular off-site sync
- **S3-compatible storage** for automatic off-site backups
- **Network storage** (NAS/SAN) for centralized backup management

### Example S3 Backup Disk (Optional):
```php
'backups-s3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BACKUP_BUCKET'),
    'root' => 'database-backups',
    'visibility' => 'private',
    'throw' => false,
],
```

Then update `DatabaseBackupService`:
```php
protected string $backupDisk = env('BACKUP_DISK', 'backups');
```

## Testing

1. ✅ Navigate to Database Backups page - should load without errors
2. ✅ Create a new backup - file should be saved to `storage/app/backups/database/`
3. ✅ View backup details - file existence check should work
4. ✅ Download backup - should stream file from disk
5. ✅ Check disk space - monitor backup directory size

## Monitoring Recommendations

### Disk Space:
```bash
# Check backup directory size
du -sh storage/app/backups/

# Check number of backup files
find storage/app/backups/database/ -name "*.sql" | wc -l
```

### Cleanup Old Backups:
The service includes a cleanup method:
```php
DatabaseBackupService::cleanupOldBackups(30); // Keep last 30 days
```

Consider scheduling this in `app/Console/Kernel.php`:
```php
$schedule->call(function () {
    app(DatabaseBackupService::class)->cleanupOldBackups(30);
})->daily();
```

## Date
October 12, 2025
