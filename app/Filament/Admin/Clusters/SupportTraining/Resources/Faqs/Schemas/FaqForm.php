<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Faqs\Schemas;

use App\Models\Faq;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('FAQ Details')
                    ->schema([
                        Textarea::make('question')
                            ->label('Question')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),

                        RichEditor::make('answer')
                            ->label('Answer')
                            ->required()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h3',
                                'h4',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->required()
                                    ->options(Faq::getCategoryOptions()),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),
                            ]),

                        TagsInput::make('tags')
                            ->label('Tags')
                            ->helperText('Keywords to help users find this FAQ'),
                    ]),

                Section::make('Publication Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(true)
                                    ->helperText('Make this FAQ visible to users'),

                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->default(false)
                                    ->helperText('Show in featured FAQs section'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Hidden::make('author_id')
                                    ->default(auth()->id()),

                                Hidden::make('last_updated_by')
                                    ->default(auth()->id()),
                            ]),
                    ]),
            ]);
    }
}
