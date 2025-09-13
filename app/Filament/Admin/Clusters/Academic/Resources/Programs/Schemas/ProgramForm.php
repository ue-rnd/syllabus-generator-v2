<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Programs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Course;
use App\Models\Department;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Program identification and department association')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('code')
                            ->required(),
                        Select::make('level')
                            ->required()
                            ->options([
                                'ASSOCIATE' => 'Associate',
                                'BACHELOR' => 'Bachelor',
                                'MASTERAL' => 'Masteral',
                                'DOCTORAL' => 'Doctoral',
                            ])
                            ->default('ASSOCIATE')
                            ->searchable(),
                        Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->required()
                            ->searchable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Academic Content')
                    ->description('Program outcomes, objectives, and detailed description')
                    ->schema([
                        Textarea::make('description')
                            ->columnSpanFull(),
                        RichEditor::make('outcomes')
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
                            ->columnSpanFull(),
                        RichEditor::make('objectives')
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
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Configuration')
                    ->description('Display settings and status')
                    ->schema([
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
