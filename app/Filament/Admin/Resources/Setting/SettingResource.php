<?php

namespace App\Filament\Admin\Resources\Setting;

use App\Constants\SettingConstants;
use App\Filament\Admin\Resources\Setting\Pages\ManageSetting;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'key';

    protected static ?int $navigationSort = 30;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('category')
                    ->formatStateUsing(fn ($state) => SettingConstants::getCategoryOptions()[$state])
                    ->color(fn (string $state) => SettingConstants::getCategoryColor($state))
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('value')
                    ->formatStateUsing(fn ($state) => $state === null ? 'Empty' : $state)
                    ->html(fn ($record) => $record->type == 'richtext')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                EditAction::make()
                    ->modalHeading(fn ($record) => 'Edit ' . $record->label)
                    ->form(function (Setting $record) {
                        return match ($record->type) {
                            'select' => [
                                Select::make('value')
                                    ->label($record->label)
                                    ->options($record->attributes['options'])
                                    ->required()
                            ],
                            'number' => [
                                TextInput::make('value')
                                    ->label($record->label)
                                    ->type('number')
                                    ->required()
                            ],
                            'richtext' => [
                                RichEditor::make('value')
                                    ->label($record->label)
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->required()
                            ],
                            default => [
                                TextInput::make('value')
                                    ->label($record->label)
                            ]
                        };
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSetting::route('/'),
        ];
    }
}
