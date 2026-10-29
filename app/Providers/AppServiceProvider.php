<?php

namespace App\Providers;

use App\Models\Course;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        View::composer('admin.*', function ($view) {
            $view->with('me', auth()->user());
        });

        View::composer('layouts.teacher', function ($view) {
            $teacher = optional(auth()->user())->teacher;
            $view->with('navCourses', $teacher ? Course::where('teacher_id', $teacher->id)->orderBy('title')->get(['id', 'title']) : collect());
        });
    }
}
