<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Tab1;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $today = Carbon::today();
            $restrictedDepartments = [1, 2, 4, 5, 6];

            // Use date-only match via whereDate to target today's attendance rows
            Tab1::whereDate('attendance_date', Carbon::today())
                ->whereNull('checkout_status')
                ->whereNotNull('checkin')
                ->whereIn('department_id', $restrictedDepartments)
                ->update([
                    'checkout_status' => 'missing',
                    'checkout'        => Carbon::today()->setTime(16, 30),
                ]);
        })->dailyAt('23:15');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
