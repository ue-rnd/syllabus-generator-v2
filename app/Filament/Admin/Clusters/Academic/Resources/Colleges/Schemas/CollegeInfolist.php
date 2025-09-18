<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Colleges\Schemas;

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
                        TextEntry::make('dean.name')
                            ->label('Dean')
                            ->placeholder('-'),
                        TextEntry::make('associateDean.name')
                            ->label('Associate Dean')
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),
                Section::make()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('mission')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('vision')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('core_values')
                            ->label('Core Values')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('objectives')
                            ->html()
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
                            ->visible(fn (College $record): bool => $record->trashed())
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
