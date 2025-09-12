<?php

namespace App\Filament\Admin\Resources\Programs\Schemas;

use App\Models\Program;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('level')
                    ->formatStateUsing(fn (string $state): string => \App\Models\Program::LEVELS[$state] ?? $state),
                TextEntry::make('code'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('outcomes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('objectives')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('department.name')
                    ->label('Department'),
                TextEntry::make('courses.name')
                    ->label('Courses')
                    ->badge()
                    ->separator(',')
                    ->placeholder('No courses assigned')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Program $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
