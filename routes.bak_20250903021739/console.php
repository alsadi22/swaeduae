<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('hours:reconcile')->dailyAt('02:10');
Schedule::command('hours:audit')->dailyAt('02:20');
