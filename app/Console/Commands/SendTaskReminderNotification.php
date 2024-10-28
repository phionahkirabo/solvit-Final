<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Carbon\Carbon;

class SendTaskReminderNotification extends Command
{
   
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to users for tasks ending in two days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Find tasks with end_date exactly two days from now
        $tasks = Task::whereDate('due_date', Carbon::now()->addDays(2)->toDateString())->get();

        foreach ($tasks as $task) {
            $user = $task->user; // Assuming 'user' is the relationship name

            if ($user) {
                $user->notify(new TaskReminderNotification($task));
            }
        }

        return Command::SUCCESS;
    }
}
