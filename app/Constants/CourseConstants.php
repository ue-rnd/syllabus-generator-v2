<?php

namespace App\Constants;

class CourseConstants
{
    /**
     * Course types.
     */
    public const TYPES = [
        'pure_onsite' => 'Pure Onsite',
        'pure_offsite' => 'Pure Offsite',
        'hybrid' => 'Hybrid',
        'other' => 'Other',
    ];

    /**
     * Course delivery modes.
     */
    public const DELIVERY_MODES = [
        'face_to_face' => 'Face-to-Face',
        'online_synchronous' => 'Online Synchronous',
        'online_asynchronous' => 'Online Asynchronous',
        'hybrid' => 'Hybrid',
        'hyflex' => 'HyFlex',
        'blended' => 'Blended',
        'distance_learning' => 'Distance Learning',
        'correspondence' => 'Correspondence',
        'field_based' => 'Field-based',
    ];

    /**
     * Course status options.
     */
    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'archived' => 'Archived',
        'under_review' => 'Under Review',
        'deprecated' => 'Deprecated',
    ];

    /**
     * Grade levels/years.
     */
    public const GRADE_LEVELS = [
        'freshman' => 'Freshman (1st Year)',
        'sophomore' => 'Sophomore (2nd Year)',
        'junior' => 'Junior (3rd Year)',
        'senior' => 'Senior (4th Year)',
        'graduate' => 'Graduate Level',
        'postgraduate' => 'Postgraduate Level',
    ];

    /**
     * Get course type color for badges.
     */
    public static function getTypeColor(string $type): string
    {
        return match ($type) {
            'pure_onsite' => 'primary',
            'pure_offsite' => 'warning',
            'hybrid' => 'info',
            'other' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get delivery mode color for badges.
     */
    public static function getDeliveryModeColor(string $mode): string
    {
        return match ($mode) {
            'face_to_face' => 'success',
            'online_synchronous' => 'primary',
            'online_asynchronous' => 'info',
            'hybrid', 'hyflex', 'blended' => 'warning',
            'distance_learning', 'correspondence' => 'purple',
            'field_based' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get status color for badges.
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'active' => 'success',
            'inactive' => 'gray',
            'archived' => 'gray',
            'under_review' => 'warning',
            'deprecated' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get grade level color for badges.
     */
    public static function getGradeLevelColor(string $level): string
    {
        return match ($level) {
            'freshman' => 'success',
            'sophomore' => 'primary',
            'junior' => 'warning',
            'senior' => 'danger',
            'graduate' => 'purple',
            'postgraduate' => 'pink',
            default => 'gray',
        };
    }

    /**
     * Get course types as options for select fields.
     */
    public static function getTypeOptions(): array
    {
        return self::TYPES;
    }

    /**
     * Get delivery modes as options for select fields.
     */
    public static function getDeliveryModeOptions(): array
    {
        return self::DELIVERY_MODES;
    }

    /**
     * Get statuses as options for select fields.
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }

    /**
     * Get grade levels as options for select fields.
     */
    public static function getGradeLevelOptions(): array
    {
        return self::GRADE_LEVELS;
    }
}
