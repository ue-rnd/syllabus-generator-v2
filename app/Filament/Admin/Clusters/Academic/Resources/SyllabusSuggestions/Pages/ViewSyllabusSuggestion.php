<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions\Pages;

use App\Filament\Admin\Clusters\Academic\Resources\SyllabusSuggestions\SyllabusSuggestionsResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewSyllabusSuggestion extends ViewRecord
{
    protected static string $resource = SyllabusSuggestionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Suggestion')
                ->icon('heroicon-o-check')
                ->color('success')
                ->form([
                    Textarea::make('comments')
                        ->label('Comments (optional)')
                        ->placeholder('Add any comments about this approval...')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->approve(auth()->user(), $data['comments'] ?? null);
                    $this->redirect(SyllabusSuggestionsResource::getUrl('index'));
                })
                ->visible(fn () => $this->record->isPending() && $this->record->canBeReviewedBy(auth()->user())
                )
                ->requiresConfirmation()
                ->modalHeading('Approve Suggestion')
                ->modalDescription('This will apply the suggested change to the syllabus.'),

            Action::make('reject')
                ->label('Reject Suggestion')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->form([
                    Textarea::make('comments')
                        ->label('Reason for rejection')
                        ->placeholder('Please explain why this suggestion is being rejected...')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->reject(auth()->user(), $data['comments']);
                    $this->redirect(SyllabusSuggestionsResource::getUrl('index'));
                })
                ->visible(fn () => $this->record->isPending() && $this->record->canBeReviewedBy(auth()->user())
                )
                ->requiresConfirmation()
                ->modalHeading('Reject Suggestion')
                ->modalDescription('This suggestion will be marked as rejected.'),
        ];
    }
}
