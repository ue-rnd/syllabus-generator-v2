<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Departments\Schemas;

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
                        TextEntry::make('departmentChair.name')
                            ->label('Department Chair')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                        TextEntry::make('sort_order')
                            ->label('Sort Order')
                            ->numeric(),
                    ]),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime()
                            ->visible(fn(Department $record): bool => $record->trashed())
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

            ]);
    }
}
