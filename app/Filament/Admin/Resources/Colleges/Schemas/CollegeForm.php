<?php

namespace App\Filament\Admin\Resources\Colleges\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CollegeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('mission')
                    ->columnSpanFull(),
                Textarea::make('vision')
                    ->columnSpanFull(),
                Textarea::make('core_values')
                    ->label('Core Values')
                    ->columnSpanFull(),
                Textarea::make('objectives')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Is Active?')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->directory('images/logos')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('1000')
                    ->imageResizeTargetHeight('1000')
                    ->imageEditorAspectRatios([
                        '1:1',
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
