<?php

namespace App\Console\Commands;

use App\Models\FormDraft;
use Illuminate\Console\Command;

class CleanupOldDrafts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drafts:cleanup {--days=14 : Number of days to keep drafts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete form drafts older than specified days (default: 14 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        if ($days < 1) {
            $this->error('Days must be a positive integer.');
            return 1;
        }

        $deletedCount = FormDraft::olderThan($days)->delete();
        
        $this->info("Deleted {$deletedCount} form drafts older than {$days} days.");
        
        return 0;
    }
}