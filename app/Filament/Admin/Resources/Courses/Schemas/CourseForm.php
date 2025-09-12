<?php

namespace App\Filament\Admin\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use App\Models\College;
use App\Models\Program;

class CourseForm
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
                Textarea::make('outcomes')
                    ->columnSpanFull(),
                Select::make('college_id')
                    ->label('College')
                    ->relationship('college', 'name')
                    ->required()
                    ->searchable(),
                Select::make('programs')
                    ->label('Programs')
                    ->relationship('programs', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                FileUpload::make('logo_path')
                    ->label('Logo')
                    ->image()
                    ->directory('images/logos')
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1'])
                    ->maxSize(2048),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
