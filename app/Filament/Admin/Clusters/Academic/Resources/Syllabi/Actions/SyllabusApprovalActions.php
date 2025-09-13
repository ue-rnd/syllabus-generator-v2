<?php

namespace App\Filament\Admin\Clusters\Academic\Resources\Syllabi\Actions;

use App\Models\Syllabus;
use App\Models\User;
use App\Services\SyllabusPdfService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;

class SyllabusApprovalActions
{
    /**
     * Submit for approval action
     */
    public static function submitForApproval(): Action
    {
        return Action::make('submitForApproval')
            ->label('Submit for Approval')
            ->icon('heroicon-o-paper-airplane')
            ->color('warning')
            ->visible(fn (Syllabus $record): bool => 
                $record->canSubmitForApproval(auth()->user())
            )
            ->requiresConfirmation()
            ->modalHeading('Submit Syllabus for Approval')
            ->modalDescription('Are you sure you want to submit this syllabus for approval? Once submitted, you will not be able to edit it until the approval process is complete.')
            ->modalSubmitActionLabel('Submit for Approval')
            ->action(function (Syllabus $record) {
                if ($record->submitForApproval(auth()->user())) {
                    Notification::make()
                        ->title('Syllabus submitted for approval')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Failed to submit syllabus')
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * Approve action
     */
    public static function approve(): Action
    {
        return Action::make('approve')
            ->label('Approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (Syllabus $record): bool => 
                $record->canApprove(auth()->user())
            )
            ->form([
                Textarea::make('comments')
                    ->label('Approval Comments (Optional)')
                    ->placeholder('Add any comments about this approval...')
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->modalHeading('Approve Syllabus')
            ->modalDescription('Approve this syllabus to move it to the next stage in the approval process.')
            ->modalSubmitActionLabel('Approve')
            ->action(function (Syllabus $record, array $data) {
                if ($record->approve(auth()->user(), $data['comments'] ?? null)) {
                    Notification::make()
                        ->title('Syllabus approved')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Failed to approve syllabus')
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * Reject action
     */
    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (Syllabus $record): bool => 
                $record->canReject(auth()->user())
            )
            ->form([
                Textarea::make('comments')
                    ->label('Rejection Comments')
                    ->placeholder('Please provide reasons for rejection...')
                    ->required()
                    ->rows(4)
                    ->maxLength(1000),
            ])
            ->modalHeading('Reject Syllabus')
            ->modalDescription('Reject this syllabus and provide feedback for the preparers.')
            ->modalSubmitActionLabel('Reject')
            ->action(function (Syllabus $record, array $data) {
                if ($record->reject(auth()->user(), $data['comments'])) {
                    Notification::make()
                        ->title('Syllabus rejected')
                        ->body('The preparers will be notified and can make revisions.')
                        ->warning()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Failed to reject syllabus')
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * Create revision action
     */
    public static function createRevision(): Action
    {
        return Action::make('createRevision')
            ->label('Create Revision')
            ->icon('heroicon-o-document-duplicate')
            ->color('primary')
            ->visible(fn (Syllabus $record): bool => 
                $record->status === 'rejected' && 
                ($record->principal_prepared_by === auth()->id() || 
                 collect($record->prepared_by)->contains('user_id', auth()->id()))
            )
            ->requiresConfirmation()
            ->modalHeading('Create Revision')
            ->modalDescription('Create a new revision of this syllabus that you can edit and resubmit for approval.')
            ->modalSubmitActionLabel('Create Revision')
            ->action(function (Syllabus $record) {
                $revision = $record->createRevision();
                
                Notification::make()
                    ->title('Revision created')
                    ->body('You can now edit and resubmit the revised syllabus.')
                    ->success()
                    ->send();

                // Redirect to edit the new revision
                redirect()->route('filament.admin.clusters.academic.resources.syllabi.edit', $revision);
            });
    }

    /**
     * View approval history action
     */
    public static function viewApprovalHistory(): Action
    {
        return Action::make('viewApprovalHistory')
            ->label('Approval History')
            ->icon('heroicon-o-clock')
            ->color('gray')
            ->modalHeading('Approval History')
            ->modalContent(function (Syllabus $record) {
                $history = $record->approval_history ?? [];
                
                if (empty($history)) {
                    return view('filament.components.empty-state', [
                        'message' => 'No approval history available.'
                    ]);
                }

                return view('filament.components.approval-history', [
                    'history' => $history,
                    'record' => $record,
                ]);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    /**
     * Export PDF action
     */
    public static function exportPdf(): Action
    {
        return Action::make('exportPdf')
            ->label('Export PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('primary')
            ->action(function (Syllabus $record) {
                try {
                    $pdfService = new SyllabusPdfService();
                    return $pdfService->downloadPdf($record);
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('PDF Export Failed')
                        ->body('Error: ' . $e->getMessage())
                        ->danger()
                        ->send();
                        
                    \Log::error('PDF Export Error', [
                        'syllabus_id' => $record->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            });
    }

    /**
     * View PDF action (stream in browser)
     */
    public static function viewPdf(): Action
    {
        return Action::make('viewPdf')
            ->label('View PDF')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->url(fn (Syllabus $record): string => route('syllabus.pdf.view', $record))
            ->openUrlInNewTab();
    }

    /**
     * Get all available actions for a syllabus
     */
    public static function getActionsForSyllabus(Syllabus $record): array
    {
        return [
            self::submitForApproval(),
            self::approve(),
            self::reject(),
            self::createRevision(),
            // self::viewApprovalHistory(),
            self::viewPdf(),
            // self::exportPdf(),
        ];
    }
}
