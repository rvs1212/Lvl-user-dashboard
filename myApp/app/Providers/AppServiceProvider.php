<?php

namespace App\Providers;

use App\Contracts\Employee\EmployeeServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\User\UserInterface;
use App\Repositories\UserRepository;
use App\Services\Employee\EmployeeService;
use App\Services\User\TestUserService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, TestUserService::class);
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
        
        // $this->app->singleton(
        //     \App\Services\UserService::class,
        //     fn($app) => new \App\Services\UserService(
        //         $app->make(\App\Contracts\Repositories\UserRepositoryInterface::class)
        //     )
        // );
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
