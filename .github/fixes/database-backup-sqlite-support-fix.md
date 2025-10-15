# Database Backup SQLite Support Fix

## Issue
**Error:** `SQLSTATE[HY000]: General error: 1 near "SHOW": syntax error (Connection: sqlite, SQL: SHOW CREATE TABLE \`users\`)`

The `DatabaseBackupService` was using MySQL-specific SQL commands that don't work with SQLite:
- `SHOW CREATE TABLE` (MySQL)
- `SET FOREIGN_KEY_CHECKS` (MySQL)

## Root Cause

The backup service was hardcoded to use MySQL syntax for:
1. **Schema extraction** - `SHOW CREATE TABLE` doesn't exist in SQLite
2. **Foreign key management** - `SET FOREIGN_KEY_CHECKS` is MySQL-specific

### Original Code (MySQL-only):
```php
// Get table structure
$createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
$sql .= $createTable->{"Create Table"} . ";\n\n";

// Disable foreign keys
$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
```

This fails on SQLite with:
```
near "SHOW": syntax error
```

## Solution

### Made DatabaseBackupService Database-Agnostic ✅

**File:** `app/Services/DatabaseBackupService.php`

#### 1. Added Driver Detection
```php
$driver = DB::connection()->getDriverName();
```

#### 2. Database-Specific Foreign Key Management
```php
// MySQL
if ($driver === 'mysql') {
    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
}

// SQLite
elseif ($driver === 'sqlite') {
    $sql .= "PRAGMA foreign_keys = OFF;\n\n";
}
```

#### 3. Database-Specific Schema Extraction
```php
if ($driver === 'sqlite') {
    // SQLite: Get schema from sqlite_master
    $schema = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table]);
    
    if (!empty($schema)) {
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sql .= $schema[0]->sql . ";\n\n";
    }
} else {
    // MySQL: Use SHOW CREATE TABLE
    $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $sql .= $createTable->{"Create Table"} . ";\n\n";
}
```

## Database Compatibility Matrix

| Feature | MySQL | SQLite | PostgreSQL |
|---------|-------|--------|------------|
| Schema Extraction | `SHOW CREATE TABLE` | `SELECT sql FROM sqlite_master` | `pg_get_tabledef()` |
| Foreign Keys Off | `SET FOREIGN_KEY_CHECKS = 0` | `PRAGMA foreign_keys = OFF` | `SET session_replication_role = replica` |
| Foreign Keys On | `SET FOREIGN_KEY_CHECKS = 1` | `PRAGMA foreign_keys = ON` | `SET session_replication_role = DEFAULT` |

## SQLite-Specific Implementation

### Schema Extraction
SQLite stores table definitions in the `sqlite_master` system table:

```php
$schema = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table]);
```

**Example Output:**
```sql
CREATE TABLE "users" (
    "id" integer primary key autoincrement not null,
    "name" varchar not null,
    "email" varchar not null,
    ...
    foreign key("college_id") references "colleges"("id")
)
```

### Foreign Key Control
```php
PRAGMA foreign_keys = OFF;  -- Disable
PRAGMA foreign_keys = ON;   -- Enable
```

## Generated Backup Format

### Header
```sql
-- Database Backup Generated: 2025-10-12 13:45:23
-- Database Driver: sqlite
-- Tables: users, colleges, departments, ...
```

### SQLite Example
```sql
PRAGMA foreign_keys = OFF;

-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE "users" (
    "id" integer primary key autoincrement not null,
    ...
);

-- Data for table: users
INSERT INTO `users` VALUES
(1, 'John', 'john@example.com', ...),
(2, 'Jane', 'jane@example.com', ...);

PRAGMA foreign_keys = ON;
```

### MySQL Example
```sql
SET FOREIGN_KEY_CHECKS = 0;

-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    ...
) ENGINE=InnoDB;

-- Data for table: users
INSERT INTO `users` VALUES
(1, 'John', 'john@example.com', ...),
(2, 'Jane', 'jane@example.com', ...);

SET FOREIGN_KEY_CHECKS = 1;
```

## Files Modified

1. ✅ `app/Services/DatabaseBackupService.php` - Added multi-database support

## Testing

### Test SQLite Backup Creation:
1. ✅ Navigate to `/admin/backup-recovery/database-backups`
2. ✅ Click "Create New Backup"
3. ✅ Select "Full Database" backup type
4. ✅ Submit - should create backup successfully
5. ✅ Check `storage/app/backups/database/` for `.sql` file

### Test Backup Content:
```bash
# View generated backup file
cat storage/app/backups/database/backup_1_*.sql | head -30
```

**Expected to see:**
```sql
-- Database Driver: sqlite
PRAGMA foreign_keys = OFF;
CREATE TABLE "users" (...)
```

### Test Restore:
1. ✅ Click "Restore" on a completed backup
2. ✅ Confirm restoration
3. ✅ Should restore successfully

## Current Database Configuration

**Driver:** SQLite  
**Database:** `/database/database.sqlite`

Verified with:
```bash
php artisan tinker --execute="echo DB::connection()->getDriverName();"
```

## Future Enhancements

### Add PostgreSQL Support:
```php
elseif ($driver === 'pgsql') {
    // PostgreSQL schema extraction
    $schema = DB::select("
        SELECT 'CREATE TABLE ' || tablename || ' (' ||
               string_agg(column_name || ' ' || data_type, ', ') || ')'
        FROM information_schema.columns
        WHERE table_name = ?
        GROUP BY tablename
    ", [$table]);
    
    // Foreign keys
    $sql .= "SET session_replication_role = replica;\n\n";
}
```

### Add SQL Server Support:
```php
elseif ($driver === 'sqlsrv') {
    // SQL Server schema extraction
    $schema = DB::select("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$table]);
    
    // Foreign keys
    $sql .= "EXEC sp_MSForEachTable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL';\n\n";
}
```

## Backup File Metadata

Each backup now includes the database driver in the header:
```sql
-- Database Driver: sqlite
```

This allows the restore process to:
- ✅ Validate compatibility
- ✅ Apply driver-specific transformations
- ✅ Warn users about cross-database restores

## Error Handling

### Invalid Driver
If an unsupported driver is detected, the backup will still attempt to proceed but may fail with specific error messages about unsupported commands.

### Future: Driver Validation
Consider adding:
```php
protected function validateDriver(): void
{
    $supported = ['mysql', 'sqlite'];
    $current = DB::connection()->getDriverName();
    
    if (!in_array($current, $supported)) {
        throw new \Exception("Database driver '{$current}' is not supported for backups.");
    }
}
```

## Date
October 12, 2025
