<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupOldTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan tasks:cleanup
     */
    protected $signature = 'tasks:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Delete tasks older than 30 days and log the deletions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateLimit = Carbon::now()->subDays(30);

        $tasksToDelete = Task::where('created_at', '<', $dateLimit)->get();

        foreach ($tasksToDelete as $task) {
            Log::info('Deleted Task', [
                'id' => $task->id,
                'title' => $task->title,
                'deleted_at' => now()->toDateTimeString(),
            ]);
            $task->delete();
        }

        $this->info(count($tasksToDelete) . ' tasks deleted successfully.');
    }
}
