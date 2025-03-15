<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DeleteExpiredGuests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired guest users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::where('is_guest', true)
            ->where('expires_at', '<=', Carbon::now())
            ->delete();

        $this->info('Expired guest users deleted successfully.');
    }
}
