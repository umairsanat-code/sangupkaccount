<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\DailySaleTicket;
use App\Mail\TimeLimitReminder;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
// protected function schedule(Schedule $schedule)
// {
//     $schedule->call(function () {
//         $nowSpain = \Carbon\Carbon::now('Europe/Madrid');

//         // 1 hour reminder window
//         $start1 = $nowSpain->copy()->addHour(1)->subMinute(1)->startOfMinute();
//         $end1   = $nowSpain->copy()->addHour(1)->addMinute(1)->endOfMinute();

//         // 20 min reminder window
//         $start2 = $nowSpain->copy()->addMinutes(20)->subMinute(1)->startOfMinute();
//         $end2   = $nowSpain->copy()->addMinutes(20)->addMinute(1)->endOfMinute();

//         // Convert to UTC for DB query
//         $start1Utc = $start1->copy()->timezone('UTC');
//         $end1Utc   = $end1->copy()->timezone('UTC');

//         $start2Utc = $start2->copy()->timezone('UTC');
//         $end2Utc   = $end2->copy()->timezone('UTC');

//         // Fetch tickets
//         $tickets1 = \App\Models\DailySaleTicket::whereBetween('time_limit', [$start1Utc, $end1Utc])->get();
//         $tickets2 = \App\Models\DailySaleTicket::whereBetween('time_limit', [$start2Utc, $end2Utc])->get();

//         // Send emails
//         if ($tickets1->count() > 0) {
//             \Mail::to(['sanat.saad@gmail.com', 'sangutravel@gmail.com','AI92470@GMAIL.COM','Imfa_sango@yahoo.com'])
//                 ->send(new \App\Mail\TimeLimitReminder($tickets1));
//         }
//         if ($tickets2->count() > 0) {
//             \Mail::to(['sanat.saad@gmail.com', 'sangutravel@gmail.com','AI92470@GMAIL.COM','Imfa_sango@yahoo.com'])
//                 ->send(new \App\Mail\TimeLimitReminder($tickets2));
//         }

//         \Log::info("Cron run at (Spain): $nowSpain | Tickets1: {$tickets1->count()} | Tickets2: {$tickets2->count()}");
//     })->everyMinute();
// }



protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $nowSpain = \Carbon\Carbon::now('Europe/Madrid');

        // 1 hour reminder window
        $start1 = $nowSpain->copy()->addHour(1)->subMinute(1)->startOfMinute();
        $end1   = $nowSpain->copy()->addHour(1)->addMinute(1)->endOfMinute();

        // 20 min reminder window
        $start2 = $nowSpain->copy()->addMinutes(20)->subMinute(1)->startOfMinute();
        $end2   = $nowSpain->copy()->addMinutes(20)->addMinute(1)->endOfMinute();

        // Convert to UTC for DB query
        $start1Utc = $start1->copy()->timezone('UTC');
        $end1Utc   = $end1->copy()->timezone('UTC');

        $start2Utc = $start2->copy()->timezone('UTC');
        $end2Utc   = $end2->copy()->timezone('UTC');

        // 1 hour tickets
        $tickets1 = \App\Models\DailySaleTicket::whereBetween('time_limit', [$start1Utc, $end1Utc])->get();

        if ($tickets1->count() > 0) {
            $cacheKey1 = 'time_limit_email_1h_' . $nowSpain->format('YmdHi');

            if (!\Cache::has($cacheKey1)) {
                \Mail::to([
                    'sanat.saad@gmail.com',
                    'sangutravel@gmail.com',
                    'AI92470@GMAIL.COM',
                    'SanguTravel@yahoo.com'
                ])->send(new \App\Mail\TimeLimitReminder($tickets1));

                \Cache::put($cacheKey1, true, now()->addMinutes(2));
                \Log::info("✅ 1 hour reminder sent | Tickets: {$tickets1->count()}");
            }
        }

        // 20 minutes tickets
        $tickets2 = \App\Models\DailySaleTicket::whereBetween('time_limit', [$start2Utc, $end2Utc])->get();

        if ($tickets2->count() > 0) {
            $cacheKey2 = 'time_limit_email_20m_' . $nowSpain->format('YmdHi');

            if (!\Cache::has($cacheKey2)) {
                \Mail::to([
                    'sanat.saad@gmail.com',
                    'sangutravel@gmail.com',
                    'AI92470@GMAIL.COM',
                    'SanguTravel@yahoo.com'
                ])->send(new \App\Mail\TimeLimitReminder($tickets2));

                \Cache::put($cacheKey2, true, now()->addMinutes(2));
                \Log::info("✅ 20 min reminder sent | Tickets: {$tickets2->count()}");
            }
        }
    })
    ->everyMinute()
    ->name('time-limit-reminder')
    ->withoutOverlapping();
}






    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
