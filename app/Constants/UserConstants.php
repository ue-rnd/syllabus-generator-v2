<?php

namespace App\Constants;

class UserConstants
{
    /**
     * User positions/roles
     */
    public const POSITIONS = [
        'superadmin' => 'Super Admin',
        'professor' => 'Professor',
        'associate_professor' => 'Associate Professor',
        'assistant_professor' => 'Assistant Professor',
        'instructor' => 'Instructor',
        'lecturer' => 'Lecturer',
        'department_chair' => 'Department Chair',
        'associate_dean' => 'Associate Dean',
        'dean' => 'Dean',
        'vice_president' => 'Vice President',
        'president' => 'President',
        'research_associate' => 'Research Associate',
        'postdoctoral_researcher' => 'Postdoctoral Researcher',
        'graduate_assistant' => 'Graduate Assistant',
        'adjunct_faculty' => 'Adjunct Faculty',
        'visiting_professor' => 'Visiting Professor',
        'emeritus_professor' => 'Professor Emeritus',
        'clinical_instructor' => 'Clinical Instructor',
        'librarian' => 'Librarian',
        'registrar' => 'Registrar',
        'administrative_staff' => 'Administrative Staff',
        'other' => 'Other',
    ];

    /**
     * User status options
     */
    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'on_leave' => 'On Leave',
        'retired' => 'Retired',
    ];

    /**
     * Employment types
     */
    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full-time',
        'part_time' => 'Part-time',
        'contractual' => 'Contractual',
        'visiting' => 'Visiting',
        'emeritus' => 'Emeritus',
        'adjunct' => 'Adjunct',
    ];

    /**
     * Get position color for badges
     */
    public static function getPositionColor(string $position): string
    {
        return match ($position) {
            'professor', 'emeritus_professor' => 'purple',
            'associate_professor' => 'primary',
            'assistant_professor' => 'info',
            'instructor', 'lecturer' => 'warning',
            'department_chair' => 'success',
            'associate_dean', 'dean' => 'danger',
            'vice_president', 'president' => 'rose',
            'adjunct_faculty', 'visiting_professor' => 'orange',
            'graduate_assistant', 'research_associate' => 'cyan',
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
            'suspended' => 'danger',
            'on_leave' => 'warning',
            'retired' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get employment type color for badges
     */
    public static function getEmploymentTypeColor(string $type): string
    {
        return match ($type) {
            'full_time' => 'success',
            'part_time' => 'warning',
            'contractual' => 'info',
            'visiting' => 'purple',
            'emeritus' => 'gray',
            'adjunct' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get positions as options for select fields
     */
    public static function getPositionOptions(): array
    {
        return self::POSITIONS;
    }

    /**
     * Get statuses as options for select fields
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }

    /**
     * Get employment types as options for select fields
     */
    public static function getEmploymentTypeOptions(): array
    {
        return self::EMPLOYMENT_TYPES;
    }
}
