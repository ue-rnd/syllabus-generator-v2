<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Colleges\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollegeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Essential college identification and details')
                    ->schema([
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
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('code')
                            ->required(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Select::make('dean_id')
                            ->label('Dean')
                            ->relationship('dean', 'name')
                            ->options(
                                User::where('position', 'dean')
                                    ->pluck('name', 'id')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload()
                            ->nullable()
                            ->required(),
                        Select::make('associate_dean_id')
                            ->label('Associate Dean')
                            ->relationship('associateDean', 'name')
                            ->options(
                                User::where('position', 'associate_dean')
                                    ->pluck('name', 'id')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload()
                            ->nullable()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Mission & Vision')
                    ->description('Core institutional values and objectives')
                    ->schema([
                        RichEditor::make('mission')
                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo']])
                            ->columnSpanFull(),
                        RichEditor::make('vision')
                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo']])
                            ->columnSpanFull(),
                        RichEditor::make('core_values')
                            ->label('Core Values')
                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo']])
                            ->columnSpanFull(),
                        RichEditor::make('objectives')
                            ->label('Objectives')
                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo']])
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
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Is Active?')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
