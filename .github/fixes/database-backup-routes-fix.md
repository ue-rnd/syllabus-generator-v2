# Database Backup Routes Fix

## Issue
**Error:** `Symfony\Component\Routing\Exception\RouteNotFoundException: Route [filament.admin.resources.database-backups.view] not defined.`

The DatabaseBackupsTable was using an incorrect route name that didn't account for the resource being inside a cluster. Additionally, the download route was missing entirely.

## Root Cause

### 1. Incorrect Route Name in Table
The table was using:
```php
route('filament.admin.resources.database-backups.view', $record)
```

But since the resource is inside the `BackupRecoveryCluster`, the correct route name is:
```php
route('filament.admin.backup-recovery.resources.database-backups.view', $record)
```

### 2. Missing Download Route
The table and view page referenced:
```php
route('admin.backups.download', $record)
```

But this route didn't exist in the application.

## Solution

### 1. Fixed Filament Route Name
**File:** `app/Filament/Admin/Clusters/BackupRecovery/Resources/DatabaseBackups/Tables/DatabaseBackupsTable.php`

Changed:
```php
->recordUrl(fn ($record) => route('filament.admin.resources.database-backups.view', $record));
```

To:
```php
->recordUrl(fn ($record) => route('filament.admin.backup-recovery.resources.database-backups.view', $record));
```

### 2. Created Download Controller
**File:** `app/Http/Controllers/DatabaseBackupController.php` (NEW)

Created a controller to handle backup file downloads with:
- Permission checks (`manage backups` or `manage system settings`)
- File existence validation
- Status verification (only completed backups)
- Proper streaming response with SQL content type

```php
public function download(DatabaseBackup $backup): StreamedResponse
{
    // Permission check
    abort_unless(
        auth()->user()->can('manage backups') || auth()->user()->can('manage system settings'),
        403
    );

    // File validation
    abort_unless(
        $backup->status === 'completed' && $backup->getFileExists(),
        404
    );

    // Stream download
    return response()->streamDownload(function () use ($disk, $backup) {
        echo $disk->get($backup->file_path);
    }, $downloadName, ['Content-Type' => 'application/sql']);
}
```

### 3. Added Download Route
**File:** `routes/web.php`

Added:
```php
Route::get('admin/backups/{backup}/download', [\App\Http\Controllers\DatabaseBackupController::class, 'download'])
    ->middleware(['auth', 'can:manage backups'])
    ->name('admin.backups.download');
```

## Files Modified

1. ✅ `app/Filament/Admin/Clusters/BackupRecovery/Resources/DatabaseBackups/Tables/DatabaseBackupsTable.php` - Fixed route name
2. ✅ `app/Http/Controllers/DatabaseBackupController.php` - NEW: Created download controller
3. ✅ `routes/web.php` - Added download route

## Route Naming Pattern

For Filament resources inside clusters, the route naming pattern is:
```
filament.{panel}.{cluster-slug}.resources.{resource-slug}.{action}
```

Example for Database Backups:
- Index: `filament.admin.backup-recovery.resources.database-backups.index`
- Create: `filament.admin.backup-recovery.resources.database-backups.create`
- View: `filament.admin.backup-recovery.resources.database-backups.view`

## Verification

### Route List Output:
```bash
php artisan route:list --name=filament.admin.backup-recovery
```

```
GET|HEAD  admin/backup-recovery/database-backups
          filament.admin.backup-recovery.resources.database-backups.index
          
GET|HEAD  admin/backup-recovery/database-backups/create
          filament.admin.backup-recovery.resources.database-backups.create
          
GET|HEAD  admin/backup-recovery/database-backups/{record}
          filament.admin.backup-recovery.resources.database-backups.view
```

```bash
php artisan route:list --name=admin.backups
```

```
GET|HEAD  admin/backups/{backup}/download
          admin.backups.download
```

## Testing

1. ✅ Navigate to Database Backups list - records should be clickable
2. ✅ Click a backup record - should navigate to view page
3. ✅ Click download button - should download the SQL file
4. ✅ Download requires proper permissions (`manage backups`)
5. ✅ Only completed backups with existing files can be downloaded

## Security

- Download route protected with `auth` and `can:manage backups` middleware
- Controller performs additional permission checks
- Validates backup status is 'completed'
- Verifies file exists before attempting download
- Uses streamed response for efficient file delivery

## Date
October 12, 2025
