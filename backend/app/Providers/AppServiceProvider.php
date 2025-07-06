<?php

namespace App\Providers;

use App\Interfaces\IAiService;
use App\Interfaces\IAuthService;
use App\Interfaces\IChatService;
use App\Interfaces\IEventService;
use App\Services\AiService;
use App\Services\AuthService;
use App\Services\ChatService;
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
        $this->app->bind(IChatService::class, ChatService::class);
        $this->app->bind(IAiService::class, AiService::class);
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
