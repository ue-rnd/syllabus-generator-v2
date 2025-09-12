<?php

namespace App\Filament\Admin\Resources\Courses\Schemas;

use App\Models\Course;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('outcomes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('college.name')
                    ->label('College'),
                TextEntry::make('programs.name')
                    ->label('Programs')
                    ->badge()
                    ->separator(',')
                    ->placeholder('No programs assigned')
                    ->columnSpanFull(),
                ImageEntry::make('logo_path')
                    ->label('Logo')
                    ->placeholder('-')
                    ->disk('public'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Course $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
