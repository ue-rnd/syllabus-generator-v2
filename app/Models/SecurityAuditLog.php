<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'event_description',
        'ip_address',
        'user_agent',
        'metadata',
        'severity',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
            default => 'gray',
        };
    }

    public function getEventTypeColorAttribute(): string
    {
        return match ($this->event_type) {
            'login_success' => 'success',
            'login_failed' => 'warning',
            'password_changed' => 'info',
            'account_locked' => 'danger',
            'permission_denied' => 'warning',
            'suspicious_activity' => 'danger',
            'data_breach' => 'danger',
            default => 'gray',
        };
    }

    public static function getEventTypeOptions(): array
    {
        return [
            'login_success' => 'Successful Login',
            'login_failed' => 'Failed Login',
            'logout' => 'User Logout',
            'password_changed' => 'Password Changed',
            'password_reset' => 'Password Reset',
            'account_locked' => 'Account Locked',
            'account_unlocked' => 'Account Unlocked',
            'permission_denied' => 'Permission Denied',
            'role_changed' => 'Role Changed',
            'profile_updated' => 'Profile Updated',
            'two_factor_enabled' => 'Two-Factor Enabled',
            'two_factor_disabled' => 'Two-Factor Disabled',
            'suspicious_activity' => 'Suspicious Activity',
            'data_access' => 'Data Access',
            'data_export' => 'Data Export',
            'system_settings_changed' => 'System Settings Changed',
            'bulk_operation' => 'Bulk Operation',
            'api_access' => 'API Access',
            'data_breach' => 'Data Breach',
        ];
    }

    public static function getSeverityOptions(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }

    public static function logEvent(
        string $eventType,
        string $description,
        ?User $user = null,
        string $severity = 'low',
        array $metadata = [],
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'event_description' => $description,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'metadata' => $metadata,
            'severity' => $severity,
            'status' => 'logged',
        ]);
    }

    public static function logLoginSuccess(User $user, string $ip): self
    {
        return self::logEvent(
            'login_success',
            "User {$user->name} logged in successfully",
            $user,
            'low',
            ['user_id' => $user->id, 'email' => $user->email],
            $ip
        );
    }

    public static function logLoginFailed(string $email, string $ip): self
    {
        return self::logEvent(
            'login_failed',
            "Failed login attempt for email: {$email}",
            null,
            'medium',
            ['email' => $email, 'attempt_ip' => $ip],
            $ip
        );
    }

    public static function logPasswordChanged(User $user): self
    {
        return self::logEvent(
            'password_changed',
            "User {$user->name} changed their password",
            $user,
            'medium',
            ['user_id' => $user->id]
        );
    }

    public static function logAccountLocked(User $user): self
    {
        return self::logEvent(
            'account_locked',
            "User account {$user->name} was locked due to multiple failed login attempts",
            $user,
            'high',
            ['user_id' => $user->id, 'login_attempts' => $user->login_attempts]
        );
    }

    public static function logPermissionDenied(User $user, string $resource, string $action): self
    {
        return self::logEvent(
            'permission_denied',
            "User {$user->name} was denied {$action} access to {$resource}",
            $user,
            'medium',
            ['resource' => $resource, 'action' => $action]
        );
    }

    public static function logSuspiciousActivity(User $user, string $description, array $metadata = []): self
    {
        return self::logEvent(
            'suspicious_activity',
            $description,
            $user,
            'high',
            $metadata
        );
    }

    public static function logDataExport(User $user, string $dataType, int $recordCount): self
    {
        return self::logEvent(
            'data_export',
            "User {$user->name} exported {$recordCount} {$dataType} records",
            $user,
            'medium',
            ['data_type' => $dataType, 'record_count' => $recordCount]
        );
    }
}
