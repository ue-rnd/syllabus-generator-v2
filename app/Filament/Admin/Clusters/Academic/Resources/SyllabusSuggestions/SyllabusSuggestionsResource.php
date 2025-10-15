<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions;

use App\Filament\Admin\Clusters\Academic\AcademicCluster;
use App\Models\SyllabusSuggestion;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SyllabusSuggestionsResource extends Resource
{
    protected static ?string $model = SyllabusSuggestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::LightBulb;

    protected static ?string $navigationLabel = 'Suggestions';

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('syllabus_id')
                    ->relationship('syllabus', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('field_name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('current_value')
                    ->label('Current Value')
                    ->rows(3)
                    ->columnSpanFull(),

                RichEditor::make('suggested_value')
                    ->label('Suggested Value')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('reason')
                    ->label('Reason for Change')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),

                Textarea::make('review_comments')
                    ->label('Review Comments')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('syllabus.name')
                    ->label('Syllabus')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('change_description')
                    ->label('Change')
                    ->searchable(),

                TextColumn::make('suggestedBy.name')
                    ->label('Suggested By')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not reviewed')
                    ->toggleable(),

                TextColumn::make('reviewedBy.name')
                    ->label('Reviewed By')
                    ->searchable()
                    ->placeholder('Not reviewed')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple(),

                SelectFilter::make('syllabus')
                    ->relationship('syllabus', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Textarea::make('comments')
                            ->label('Comments (optional)')
                            ->placeholder('Add any comments about this approval...')
                            ->rows(3),
                    ])
                    ->action(function (SyllabusSuggestion $record, array $data) {
                        $record->approve(auth()->user(), $data['comments'] ?? null);
                    })
                    ->visible(fn (SyllabusSuggestion $record) => $record->isPending() && $record->canBeReviewedBy(auth()->user())
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Approve Suggestion')
                    ->modalDescription('This will apply the suggested change to the syllabus.'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Textarea::make('comments')
                            ->label('Reason for rejection')
                            ->placeholder('Please explain why this suggestion is being rejected...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (SyllabusSuggestion $record, array $data) {
                        $record->reject(auth()->user(), $data['comments']);
                    })
                    ->visible(fn (SyllabusSuggestion $record) => $record->isPending() && $record->canBeReviewedBy(auth()->user())
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Reject Suggestion')
                    ->modalDescription('This suggestion will be marked as rejected.'),

                Action::make('view_diff')
                    ->label('View Changes')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(function (SyllabusSuggestion $record) {
                        return view('filament.admin.suggestions.diff-modal', [
                            'suggestion' => $record,
                        ]);
                    })
                    ->modalHeading('Proposed Changes')
                    ->slideOver(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()
            ->with(['syllabus', 'suggestedBy', 'reviewedBy']);

        // Superadmin can see all suggestions
        if ($user->position === 'superadmin') {
            return $query;
        }

        // Filter based on accessible syllabi
        $accessibleSyllabi = $user->getAccessibleSyllabi()->pluck('id');

        return $query->whereHas('syllabus', function (Builder $syllabusQuery) use ($accessibleSyllabi) {
            $syllabusQuery->whereIn('id', $accessibleSyllabi);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions\Pages\ListSyllabusSuggestions::route('/'),
            'view' => \App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions\Pages\ViewSyllabusSuggestion::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        // Suggestions are created through the syllabus interface, not directly
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if ($user->position === 'faculty') {
            // Show pending suggestions for syllabi where user is principal preparer
            $count = static::getEloquentQuery()
                ->whereHas('syllabus', function (Builder $query) use ($user) {
                    $query->where('principal_prepared_by', $user->id);
                })
                ->where('status', 'pending')
                ->count();

            return $count > 0 ? (string) $count : null;
        }

        return null;
    }
}
