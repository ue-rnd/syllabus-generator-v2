<?php

namespace App\Filament\Admin\Widgets;

use App\Models\SyllabusSuggestion;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingSuggestionsWidget extends TableWidget
{
    protected static ?string $heading = 'Pending Suggestions';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SyllabusSuggestion::query()
                    ->with(['syllabus', 'suggestedBy'])
                    ->where('status', 'pending')
                    ->whereHas('syllabus', function (Builder $query) {
                        $user = auth()->user();

                        if ($user->position === 'superadmin') {
                            return;
                        }

                        // Show suggestions for syllabi where user is principal preparer
                        if ($user->position === 'faculty') {
                            $query->where('principal_prepared_by', $user->id);
                        } else {
                            // For other roles, show suggestions for accessible syllabi
                            $accessibleSyllabi = $user->getAccessibleSyllabi()->pluck('id');
                            $query->whereIn('id', $accessibleSyllabi);
                        }
                    })
                    ->latest()
            )
            ->columns([
                TextColumn::make('syllabus.name')
                    ->label('Syllabus')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 40 ? $state : null;
                    }),

                TextColumn::make('change_description')
                    ->label('Suggested Change')
                    ->limit(30),

                TextColumn::make('suggestedBy.name')
                    ->label('Suggested By')
                    ->limit(25),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->color('warning')
                    ->formatStateUsing(fn (string $state): string => 'Pending'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Review')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn (SyllabusSuggestion $record): string => route('filament.admin.academic.resources.syllabus-suggestions.view', $record)
                    ),
            ])
            ->emptyStateHeading('No pending suggestions')
            ->emptyStateDescription('All suggestions have been reviewed or there are no suggestions yet.')
            ->emptyStateIcon('heroicon-o-light-bulb');
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        // Show widget for faculty and above
        return in_array($user->position, [
            'faculty', 'department_chair', 'associate_dean', 'dean', 'qa_representative', 'superadmin',
        ]);
    }
}
