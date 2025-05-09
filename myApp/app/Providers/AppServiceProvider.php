<?php

namespace App\Providers;

use App\Contracts\Employee\EmployeeServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\User\UserInterface;
use App\Contracts\User\UserServiceInterface;
use App\Repositories\UserRepository;
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
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
        
        
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
