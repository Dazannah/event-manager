<?php

namespace App\Providers;


use App\Interfaces\IAuthService;
use App\Interfaces\IEventService;
use App\Services\AuthService;
use App\Services\EventService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IEventService::class, EventService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Passport::tokensCan([
            'helpdeskAgent' => 'Can access helpdesk interface.'
        ]);
    }
}
