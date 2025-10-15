<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Programs\Schemas;

use App\Constants\ProgramConstants;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramForm
{
    protected static function filterDepartmentsForUser($query)
    {
        $user = auth()->user();

        if ($user->position === 'superadmin') {
            return $query;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            // Deans and associate deans can only select departments from colleges they have access to
            $accessibleCollegeIds = $user->getAccessibleColleges()->pluck('id')->toArray();

            return $query->whereIn('college_id', $accessibleCollegeIds);
        }

        if ($user->position === 'department_chair') {
            // Department chairs can only select their own department
            return $query->where('department_chair_id', $user->id)
                ->orWhere('id', $user->department_id);
        }

        return $query->whereRaw('0 = 1');
    }

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
                            ->options(ProgramConstants::getLevelOptions())
                            ->default('associate')
                            ->searchable(),
                        Select::make('department_id')
                            ->label('Department')
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => self::filterDepartmentsForUser($query)
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Academic Content')
                    ->description('Program outcomes, objectives, and detailed description')
                    ->schema([
                        Textarea::make('description')
                            ->columnSpanFull(),
                        RichEditor::make('outcomes')
                            ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo']])
                            ->columnSpanFull(),
                        RichEditor::make('objectives')
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
