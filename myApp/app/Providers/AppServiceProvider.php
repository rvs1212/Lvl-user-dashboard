<?php

namespace App\Providers;

use App\Contracts\Employee\EmployeeServiceInterface;
use App\Contracts\User\UserInterface;
use App\Services\Employee\EmployeeService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserService::class);
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
