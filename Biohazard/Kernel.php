<?php

namespace Biohazard;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule as ScheduleBase;
use Biohazard\Schedule;

class Kernel extends ConsoleKernel {

	protected function defineConsoleSchedule(): void
    {
        $this->app->singleton(Schedule::class, function ($app) {
            return tap(new Schedule($this->scheduleTimezone()), function ($schedule) {
                $this->schedule($schedule->useCache($this->scheduleCache()));
            });
        });
        $this->app->bind(ScheduleBase::class, Schedule::class);
    }

}