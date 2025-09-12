<?php

namespace App\Filament\Admin\Resources\Colleges\Schemas;

use App\Models\College;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollegeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        ImageEntry::make('logo_path')
                            ->label('Logo')
                            ->disk('public')
                            ->visibility('public'),
                        TextEntry::make('name'),
                        TextEntry::make('code'), 
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                            TextEntry::make('mission')
                                ->placeholder('-')
                                ->columnSpanFull(),
                            TextEntry::make('vision')
                                ->placeholder('-')
                                ->columnSpanFull(),
                            TextEntry::make('core_values')
                                ->label('Core Values')
                                ->placeholder('-')
                                ->columnSpanFull(),
                            TextEntry::make('objectives')
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
                            ->dateTime()
                            ->visible(fn (College $record): bool => $record->trashed())
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
