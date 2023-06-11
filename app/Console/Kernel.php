<?php

namespace App\Console;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $today = Carbon::today();

            $users = DB::table('users')
                ->leftJoin('attendances', function ($join) use ($today) {
                    $join->on('users.id', '=', 'attendances.user_id')
                        ->whereDate('attendances.date', $today);
                })
                ->whereNull('attendances.id')
                ->select('users.*')
                ->get();

            foreach ($users as $user) {
                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'status' => 0
                ]);
            }
        })->dailyAt('23:59');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
