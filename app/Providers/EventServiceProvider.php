<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\AttendancePermission;
use App\Observers\AttendanceObserver;
use App\Observers\AttendancePermissionObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Attendance::observe(AttendanceObserver::class);
        AttendancePermission::observe(AttendancePermissionObserver::class);
    }
}
