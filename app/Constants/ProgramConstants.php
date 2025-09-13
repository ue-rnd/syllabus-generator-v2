<?php

namespace App\Constants;

class ProgramConstants
{
    /**
     * Program levels
     */
    public const LEVELS = [
        'associate' => 'Associate',
        'bachelor' => 'Bachelor',
        'masteral' => 'Masteral',
        'doctoral' => 'Doctoral',
    ];

    /**
     * Program status options
     */
    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'archived' => 'Archived',
    ];

    /**
     * Get level color for badges
     */
    public static function getLevelColor(string $level): string
    {
        return match ($level) {
            'associate' => 'info',
            'bachelor' => 'primary',
            'masteral' => 'warning',
            'doctoral' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get status color for badges
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'active' => 'success',
            'inactive' => 'gray',
            'suspended' => 'warning',
            'archived' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get levels as options for select fields
     */
    public static function getLevelOptions(): array
    {
        return self::LEVELS;
    }

    /**
     * Get statuses as options for select fields
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }
}
