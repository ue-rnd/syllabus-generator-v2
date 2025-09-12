<?php

namespace App\Filament\Admin\Resources\Departments\Schemas;

use App\Models\Department;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('college.name')
                            ->label('College'),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('sort_order')
                            ->numeric(),
                    ]),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn(Department $record): bool => $record->trashed())
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

            ]);
    }
}
