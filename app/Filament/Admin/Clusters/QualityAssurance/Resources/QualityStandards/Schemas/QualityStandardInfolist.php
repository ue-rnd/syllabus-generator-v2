<?php

namespace App\Filament\Admin\Clusters\QualityAssurance\Resources\QualityStandards\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QualityStandardInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name'),

                                TextEntry::make('code')
                                    ->label('Code'),

                                TextEntry::make('type')
                                    ->label('Type')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'academic' => 'primary',
                                        'administrative' => 'success',
                                        'quality_assurance' => 'warning',
                                        'compliance' => 'danger',
                                        'performance' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
                            ]),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Scope')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('college.name')
                                    ->label('College')
                                    ->default('All Colleges'),

                                TextEntry::make('department.name')
                                    ->label('Department')
                                    ->default('All Departments'),
                            ]),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
