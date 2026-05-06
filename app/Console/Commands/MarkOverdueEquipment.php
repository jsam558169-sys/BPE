<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRecord;
use Carbon\Carbon;

class MarkOverdueEquipment extends Command
{
    // This is what you type in the terminal to run it manually
    protected $signature = 'equipment:check-overdue';

    // A description of what this does
    protected $description = 'Checks if any borrowed equipment has passed its return deadline';

    public function handle()
    {
        // 1. Find records that are 'Borrowed' (status 1) 
        // 2. Where the expected return date/time is EARLIER than right now
        $overdueCount = BorrowRecord::where('status_id', 1)
            ->where('expected_return_date', '<', now())
            ->update(['status_id' => 3]); // Assuming 3 is 'Overdue' in your DB

        $this->info("Checked deadlines. {$overdueCount} records updated to Overdue.");
    }
}
