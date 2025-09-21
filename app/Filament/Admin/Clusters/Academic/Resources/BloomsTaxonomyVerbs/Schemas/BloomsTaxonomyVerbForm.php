<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\BloomsTaxonomyVerbs\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BloomsTaxonomyVerbForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Verb Details')
                ->description('Configure the action verb for Blooms taxonomy')
                ->schema([
                    TextInput::make('key')
                        ->label('Key')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Unique identifier for this verb (lowercase, underscores for spaces)')
                        ->rules(['regex:/^[a-z_]+$/']),

                    TextInput::make('label')
                        ->label('Display Label')
                        ->required()
                        ->maxLength(255)
                        ->helperText('The verb as it appears to users'),

                    Select::make('category')
                        ->label('Blooms Taxonomy Category')
                        ->options([
                            'Remember' => 'Remember',
                            'Understand' => 'Understand',
                            'Apply' => 'Apply',
                            'Analyze' => 'Analyze',
                            'Evaluate' => 'Evaluate',
                            'Create' => 'Create',
                        ])
                        ->required()
                        ->searchable(),

                    TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Used for ordering within the category'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Inactive verbs will not appear in form options'),
                ])
                ->columns(2),
        ]);
    }
}
