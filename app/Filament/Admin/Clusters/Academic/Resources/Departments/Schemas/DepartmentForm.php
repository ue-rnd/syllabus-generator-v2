<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    protected static function filterCollegesForUser($query)
    {
        $user = auth()->user();

        if ($user->position === 'superadmin') {
            return $query;
        }

        if (in_array($user->position, ['dean', 'associate_dean'])) {
            return $user->getAccessibleColleges();
        }

        return $query->whereRaw('0 = 1');
    }

    protected static function filterDepartmentChairsForCollege($query, $collegeId = null)
    {
        $query = $query->where('position', 'department_chair');

        if ($collegeId) {
            $query = $query->where('college_id', $collegeId);
        }

        return $query;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Department identification and college association')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        Select::make('college_id')
                            ->relationship(
                                name: 'college',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => self::filterCollegesForUser($query)
                            )
                            ->preload()
                            ->searchable()
                            ->live(),
                        Select::make('department_chair_id')
                            ->label('Department Chair')
                            ->relationship(
                                name: 'departmentChair',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query, $get) => self::filterDepartmentChairsForCollege($query, $get('college_id'))
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['firstname', 'lastname', 'middlename', 'name'])
                            ->preload()
                            ->nullable()
                            ->live()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
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
