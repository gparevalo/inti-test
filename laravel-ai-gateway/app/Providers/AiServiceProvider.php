<?php

namespace App\Providers;

use App\Services\Ai\AiOrchestrator;
use App\Services\Ai\N8nAiOrchestrator;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AiOrchestrator::class,
            N8nAiOrchestrator::class
        );
    }

    public function boot(): void
    {
        //
    }
}