<?php

namespace Biohazard;

use Illuminate\Console\Scheduling\Schedule as ScheduleBase;

class Schedule extends ScheduleBase {

    public function commandsCallback(array $commands, callable $callback) {
        foreach ($commands as $command) {
            $event = $this->command($command);
            
            $callback($event);
        }

        return $this;
    }

    public function commands(...$commands) {

        $events = [];

        foreach ($commands as $command) {
            $event = $this->command($command);
                
            $events[] = $event;
        }

        return new Events($events);
    }
}
