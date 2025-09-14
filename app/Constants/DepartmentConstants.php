<?php

namespace App\Constants;

class DepartmentConstants
{
    /**
     * Department status options
     */
    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'merged' => 'Merged',
        'archived' => 'Archived',
    ];

    /**
     * Department types
     */
    public const TYPES = [
        'academic' => 'Academic Department',
        'research' => 'Research Department',
        'administrative' => 'Administrative Department',
        'service' => 'Service Department',
        'interdisciplinary' => 'Interdisciplinary Department',
        'professional' => 'Professional Department',
        'technical' => 'Technical Department',
        'graduate' => 'Graduate Department',
        'continuing_education' => 'Continuing Education',
        'extension' => 'Extension Department',
    ];

    /**
     * Get status color for badges
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'active' => 'success',
            'inactive' => 'gray',
            'suspended' => 'warning',
            'merged' => 'info',
            'archived' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get type color for badges
     */
    public static function getTypeColor(string $type): string
    {
        return match ($type) {
            'academic' => 'primary',
            'research' => 'purple',
            'administrative' => 'warning',
            'service' => 'success',
            'interdisciplinary' => 'info',
            'professional' => 'danger',
            'technical' => 'orange',
            'graduate' => 'pink',
            'continuing_education' => 'cyan',
            'extension' => 'lime',
            default => 'gray',
        };
    }

    /**
     * Get statuses as options for select fields
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }

    /**
     * Get types as options for select fields
     */
    public static function getTypeOptions(): array
    {
        return self::TYPES;
    }
}
