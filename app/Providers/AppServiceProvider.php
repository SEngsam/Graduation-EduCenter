<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('role', function () {
            return new Role();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        View::composer('admin.*', function ($view) {
            $view->with('me', auth()->user());
        });

        View::composer('layouts.teacher', function ($view) {
            $teacher = optional(auth()->user())->teacher;
            $view->with('navCourses', $teacher ? \App\Models\Course::where('teacher_id', $teacher->id)->orderBy('title')->get(['id', 'title']) : collect());
        });
    }
}
