<?php

namespace App\Providers;


use App\Interfaces\IAuth;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(IAuth::class, AuthService::class);
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
