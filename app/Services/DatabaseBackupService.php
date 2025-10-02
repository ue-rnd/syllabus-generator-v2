<?php

namespace App\Services;

use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseBackupService
{
    protected string $backupDisk = 'backups';

    public function __construct()
    {
        // Ensure backup disk exists
        if (!Storage::disk($this->backupDisk)->exists('')) {
            Storage::disk($this->backupDisk)->makeDirectory('');
        }
    }

    /**
     * Create a full database backup
     */
    public function createFullBackup(string $name = null, string $description = null): DatabaseBackup
    {
        $tables = array_keys(DatabaseBackup::getMainTables());

        return $this->createBackup(
            tables: $tables,
            name: $name ?: 'Full Database Backup',
            description: $description ?: 'Complete backup of all main tables',
            type: 'full'
        );
    }

    /**
     * Create a selective backup of specific tables
     */
    public function createSelectiveBackup(array $tables, string $name = null, string $description = null): DatabaseBackup
    {
        return $this->createBackup(
            tables: $tables,
            name: $name ?: 'Selective Backup',
            description: $description ?: 'Backup of selected tables: ' . implode(', ', $tables),
            type: 'selective'
        );
    }

    /**
     * Create a backup of specified tables
     */
    protected function createBackup(array $tables, string $name, string $description, string $type): DatabaseBackup
    {
        // Create backup record
        $backup = DatabaseBackup::create([
            'name' => $name,
            'description' => $description,
            'tables_included' => $tables,
            'backup_type' => $type,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        try {
            $backup->update(['status' => 'processing']);

            // Generate filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$backup->id}_{$timestamp}.sql";
            $filePath = "database/{$filename}";

            // Create SQL dump
            $sqlContent = $this->generateSqlDump($tables);

            // Store the backup file
            Storage::disk($this->backupDisk)->put($filePath, $sqlContent);

            // Update backup record
            $backup->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'file_size' => Storage::disk($this->backupDisk)->size($filePath),
                'metadata' => [
                    'record_counts' => $this->getTableRecordCounts($tables),
                    'created_at' => now(),
                    'database_name' => config('database.connections.mysql.database'),
                ],
            ]);

            return $backup;

        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate SQL dump for specified tables
     */
    protected function generateSqlDump(array $tables): string
    {
        $sql = "-- Database Backup Generated: " . now() . "\n";
        $sql .= "-- Tables: " . implode(', ', $tables) . "\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                continue;
            }

            $sql .= $this->getTableDump($table);
            $sql .= "\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return $sql;
    }

    /**
     * Get SQL dump for a specific table
     */
    protected function getTableDump(string $table): string
    {
        $sql = "-- Table: {$table}\n";

        // Get table structure
        $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sql .= $createTable->{"Create Table"} . ";\n\n";

        // Get table data
        $records = DB::table($table)->get();

        if ($records->isNotEmpty()) {
            $sql .= "-- Data for table: {$table}\n";
            $sql .= "INSERT INTO `{$table}` VALUES\n";

            $values = [];
            foreach ($records as $record) {
                $recordArray = (array) $record;
                $escapedValues = array_map(function ($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return "'" . addslashes((string) $value) . "'";
                }, $recordArray);

                $values[] = '(' . implode(', ', $escapedValues) . ')';
            }

            $sql .= implode(",\n", $values) . ";\n";
        }

        return $sql;
    }

    /**
     * Restore database from backup file
     */
    public function restoreFromBackup(DatabaseBackup $backup): bool
    {
        if (!$backup->getFileExists()) {
            throw new \Exception('Backup file not found');
        }

        try {
            $sqlContent = Storage::disk($this->backupDisk)->get($backup->file_path);

            // Split SQL into individual statements
            $statements = $this->splitSqlStatements($sqlContent);

            DB::transaction(function () use ($statements) {
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement) && !str_starts_with($statement, '--')) {
                        DB::unprepared($statement);
                    }
                }
            });

            return true;

        } catch (\Exception $e) {
            throw new \Exception("Failed to restore backup: " . $e->getMessage());
        }
    }

    /**
     * Import SQL file and create restore record
     */
    public function importSqlFile(string $filePath, string $originalName): bool
    {
        try {
            $sqlContent = Storage::disk('temp')->get($filePath);

            // Validate SQL content
            if (!$this->validateSqlContent($sqlContent)) {
                throw new \Exception('Invalid SQL file format');
            }

            // Split and execute SQL statements
            $statements = $this->splitSqlStatements($sqlContent);

            DB::transaction(function () use ($statements) {
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement) && !str_starts_with($statement, '--')) {
                        DB::unprepared($statement);
                    }
                }
            });

            // Create import record
            DatabaseBackup::create([
                'name' => "Imported: {$originalName}",
                'description' => "Data restored from imported file: {$originalName}",
                'backup_type' => 'manual',
                'status' => 'completed',
                'created_by' => auth()->id(),
                'metadata' => [
                    'import_file' => $originalName,
                    'imported_at' => now(),
                ],
            ]);

            return true;

        } catch (\Exception $e) {
            throw new \Exception("Failed to import SQL file: " . $e->getMessage());
        }
    }

    /**
     * Split SQL content into individual statements
     */
    protected function splitSqlStatements(string $sqlContent): array
    {
        // Remove comments and split by semicolon
        $lines = explode("\n", $sqlContent);
        $sql = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (!str_starts_with($line, '--') && !empty($line)) {
                $sql .= $line . "\n";
            }
        }

        return array_filter(explode(';', $sql), fn($statement) => !empty(trim($statement)));
    }

    /**
     * Validate SQL content
     */
    protected function validateSqlContent(string $sqlContent): bool
    {
        // Basic validation - check for common SQL keywords
        $keywords = ['CREATE TABLE', 'INSERT INTO', 'DROP TABLE'];

        foreach ($keywords as $keyword) {
            if (stripos($sqlContent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if table exists
     */
    protected function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }

    /**
     * Get record counts for tables
     */
    protected function getTableRecordCounts(array $tables): array
    {
        $counts = [];

        foreach ($tables as $table) {
            if ($this->tableExists($table)) {
                $counts[$table] = DB::table($table)->count();
            }
        }

        return $counts;
    }

    /**
     * Clean up old backup files
     */
    public function cleanupOldBackups(int $daysToKeep = 30): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        $oldBackups = DatabaseBackup::where('created_at', '<', $cutoffDate)->get();

        $deletedCount = 0;

        foreach ($oldBackups as $backup) {
            if ($backup->deleteFile()) {
                $backup->delete();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get backup statistics
     */
    public function getBackupStatistics(): array
    {
        return [
            'total_backups' => DatabaseBackup::count(),
            'successful_backups' => DatabaseBackup::successful()->count(),
            'failed_backups' => DatabaseBackup::failed()->count(),
            'total_size' => DatabaseBackup::successful()->sum('file_size'),
            'latest_backup' => DatabaseBackup::successful()->latest()->first(),
            'oldest_backup' => DatabaseBackup::successful()->oldest()->first(),
        ];
    }
}