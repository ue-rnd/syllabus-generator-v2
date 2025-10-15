<?php

namespace App\Filament\Admin\Clusters\SupportTraining\Resources\Tutorials\Schemas;

use App\Models\Tutorial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TutorialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Tutorial')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Section::make('Tutorial Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'edit' ? null : $set('slug', Str::slug($state))),

                                                TextInput::make('slug')
                                                    ->label('URL Slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(Tutorial::class, 'slug', ignoreRecord: true)
                                                    ->rules(['alpha_dash']),
                                            ]),

                                        Textarea::make('description')
                                            ->label('Description')
                                            ->required()
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Brief description of what this tutorial covers'),

                                        Grid::make(3)
                                            ->schema([
                                                Select::make('category')
                                                    ->label('Category')
                                                    ->required()
                                                    ->options(Tutorial::getCategoryOptions()),

                                                Select::make('difficulty_level')
                                                    ->label('Difficulty Level')
                                                    ->required()
                                                    ->options(Tutorial::getDifficultyOptions()),

                                                TextInput::make('duration_minutes')
                                                    ->label('Duration (minutes)')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->helperText('Estimated time to complete'),
                                            ]),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('video_url')
                                                    ->label('Video URL')
                                                    ->url()
                                                    ->helperText('YouTube, Vimeo, or other video platform URL'),

                                                TextInput::make('sort_order')
                                                    ->label('Sort Order')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->helperText('Lower numbers appear first'),
                                            ]),

                                        TagsInput::make('tags')
                                            ->label('Tags')
                                            ->helperText('Keywords to help users find this tutorial'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Content')
                            ->schema([
                                Section::make('Tutorial Content')
                                    ->schema([
                                        RichEditor::make('content')
                                            ->label('Content')
                                            ->required()
                                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                                ['table', 'attachFiles'],
                                                ['undo', 'redo']])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Steps')
                            ->schema([
                                Section::make('Tutorial Steps')
                                    ->description('Break down the tutorial into step-by-step instructions')
                                    ->schema([
                                        Repeater::make('steps')
                                            ->relationship('steps')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Step Title')
                                                    ->required()
                                                    ->maxLength(255),

                                                RichEditor::make('content')
                                                    ->label('Step Content')
                                                    ->required()
                                                    ->toolbarButtons([
                                                        'attachFiles',
                                                        'bold',
                                                        'bulletList',
                                                        'codeBlock',
                                                        'italic',
                                                        'link',
                                                        'orderedList',
                                                        'table',
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('image_url')
                                                            ->label('Image URL')
                                                            ->url(),

                                                        TextInput::make('video_url')
                                                            ->label('Video URL')
                                                            ->url(),
                                                    ]),

                                                RichEditor::make('code_snippet')
                                                    ->label('Code Snippet')
                                                    ->helperText('Optional code example for this step'),

                                                Textarea::make('notes')
                                                    ->label('Notes')
                                                    ->rows(2)
                                                    ->helperText('Additional notes or tips for this step'),

                                                TextInput::make('step_order')
                                                    ->numeric()
                                                    ->hidden()
                                                    ->default(fn (callable $get) => count($get('../../steps')) + 1),
                                            ])
                                            ->orderColumn('step_order')
                                            ->reorderableWithButtons()
                                            ->addActionLabel('Add Step')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                                    ]),
                            ]),

                        Tabs\Tab::make('Media & Files')
                            ->schema([
                                Section::make('Attachments')
                                    ->schema([
                                        FileUpload::make('attachments')
                                            ->label('Tutorial Attachments')
                                            ->multiple()
                                            ->directory('tutorial-attachments')
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                'application/zip',
                                                'image/jpeg',
                                                'image/png',
                                                'text/plain',
                                            ])
                                            ->maxSize(10240) // 10MB
                                            ->helperText('PDF, Word documents, images, text files, and ZIP archives. Max 10MB per file.'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Publishing')
                            ->schema([
                                Section::make('Publication Settings')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_published')
                                                    ->label('Published')
                                                    ->default(false)
                                                    ->helperText('Make this tutorial visible to users'),

                                                Toggle::make('featured')
                                                    ->label('Featured')
                                                    ->default(false)
                                                    ->helperText('Show in featured tutorials section'),
                                            ]),

                                        TextInput::make('author_id')
                                            ->hidden()
                                            ->default(auth()->id()),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
