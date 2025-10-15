<?php

namespace App\Constants;

class SettingConstants
{
    /**
     * College status options.
     */
    public const CATEGORIES = [
        'academic' => 'Academic',
        'metadata' => 'Metadata',
    ];

    /**
     * Get status color for badges.
     */
    public static function getCategoryColor(string $category): string
    {
        return match ($category) {
            'academic' => 'success',
            'metadata' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get statuses as options for select fields.
     */
    public static function getCategoryOptions(): array
    {
        return self::CATEGORIES;
    }
}
