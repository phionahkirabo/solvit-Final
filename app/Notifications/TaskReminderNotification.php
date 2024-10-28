<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Carbon\Carbon;

class TaskReminderNotification extends Notification
{
    protected $signature = 'notify:tasks';
    protected $description = 'Send reminders to users for tasks ending in two days';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Find tasks with end_date exactly two days from now
        $tasks = Task::whereDate('end_date', Carbon::now()->addDays(2)->toDateString())->get();

        foreach ($tasks as $task) {
            $user = $task->user; // Assuming 'user' is the relationship name

            if ($user) {
                $user->notify(new TaskReminderNotification($task));
            }
        }

        $this->info('Notifications sent for tasks due in two days.');
        return Command::SUCCESS;
    }
}
