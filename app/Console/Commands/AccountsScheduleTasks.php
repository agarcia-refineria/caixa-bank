<?php

namespace App\Console\Commands;

use App\Http\Controllers\NordigenController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class AccountsScheduleTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:scheduletasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::with('schedule')->get();
        $ahora = now()->format('H:i');

        foreach ($users as $user) {
            $logger = $user->logger;

            foreach ($user->schedule as $task) {
                if ($task->hourSimple === $ahora) {
                    $user->executeAccountTasks();
                    $logger->info("✅ Ejecutando tarea programada para {$user->email} a las {$task->hour}");
                }
            }
        }
    }
}
