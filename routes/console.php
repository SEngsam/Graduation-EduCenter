<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('notify:due-reminders')->hourly();
