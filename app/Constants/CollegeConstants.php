<?php

namespace App\Constants;

class CollegeConstants
{
    /**
     * College status options.
     */
    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'merged' => 'Merged',
        'archived' => 'Archived',
    ];

    /**
     * College types.
     */
    public const TYPES = [
        'undergraduate' => 'Undergraduate College',
        'graduate' => 'Graduate College',
        'professional' => 'Professional College',
        'technical' => 'Technical College',
        'community' => 'Community College',
        'liberal_arts' => 'Liberal Arts College',
        'research' => 'Research College',
        'specialized' => 'Specialized College',
        'honors' => 'Honors College',
        'continuing_education' => 'Continuing Education',
    ];

    /**
     * Accreditation levels.
     */
    public const ACCREDITATION_LEVELS = [
        'level_1' => 'Level 1 - Candidate Status',
        'level_2' => 'Level 2 - Accredited',
        'level_3' => 'Level 3 - Re-accredited',
        'level_4' => 'Level 4 - Autonomous',
        'institutional' => 'Institutional Accreditation',
        'program' => 'Program Accreditation',
        'provisional' => 'Provisional Accreditation',
        'not_accredited' => 'Not Accredited',
    ];

    /**
     * Get status color for badges.
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
     * Get type color for badges.
     */
    public static function getTypeColor(string $type): string
    {
        return match ($type) {
            'undergraduate' => 'primary',
            'graduate' => 'purple',
            'professional' => 'danger',
            'technical' => 'warning',
            'community' => 'success',
            'liberal_arts' => 'info',
            'research' => 'pink',
            'specialized' => 'orange',
            'honors' => 'yellow',
            'continuing_education' => 'cyan',
            default => 'gray',
        };
    }

    /**
     * Get accreditation level color for badges.
     */
    public static function getAccreditationLevelColor(string $level): string
    {
        return match ($level) {
            'level_1' => 'warning',
            'level_2' => 'success',
            'level_3' => 'primary',
            'level_4' => 'purple',
            'institutional' => 'success',
            'program' => 'info',
            'provisional' => 'orange',
            'not_accredited' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get statuses as options for select fields.
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }

    /**
     * Get types as options for select fields.
     */
    public static function getTypeOptions(): array
    {
        return self::TYPES;
    }

    /**
     * Get accreditation levels as options for select fields.
     */
    public static function getAccreditationLevelOptions(): array
    {
        return self::ACCREDITATION_LEVELS;
    }
}
