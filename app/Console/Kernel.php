<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $oneWeekAgo = \Carbon\Carbon::now()->subWeek();

            $posts = \App\Models\Post::whereDoesntHave('comments', function ($q) use ($oneWeekAgo) {
                $q->where('created_at', '>=', $oneWeekAgo);
            })->get();

            foreach ($posts as $post) {
                $post->delete();
            }
        })->daily(); // her gün 1 defa çalışsın
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
