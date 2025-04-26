<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\AIRepositoryInterface;
use App\Repositories\AIRepository;
use App\Services\AI\Interfaces\AIServiceInterface;
use App\Services\AI\Providers\GeminiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         
        
        // Binding  for AI repository
        $this->app->bind(AIRepositoryInterface::class, function ($app) {
            return new AIRepository();
        }); 
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        
        $this->publishes([
            __DIR__.'/../../config/ai.php' => config_path('ai.php'),
        ], 'config');
    }
}
