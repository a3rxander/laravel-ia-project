<?php

namespace App\Services\AI;

use App\Services\AI\Interfaces\AIServiceInterface;
use App\Services\AI\Providers\GeminiService;
use Illuminate\Support\Facades\App;

class AIFactory
{
    /**
     * Create an instance of the AI service according to the specified provider
     *
     * @param string $provider The AI provider to use ('gemini', 'openai', etc.)
     * @return AIServiceInterface The instance of the AI service
     * @throws \Exception If the specified provider is not valid
     */
    public static function create(string $provider = null): AIServiceInterface
    {
        // If no provider is specified, use the default configured provider
        $provider = $provider ?? config('services.ai.default_provider');
        
        switch ($provider) {
            case 'gemini':
                return App::make(GeminiService::class);
            // Additional providers can be added in the future
            // case 'openai':
            //     return App::make(OpenAIService::class);
            default:
                throw new \Exception("The AI provider '$provider' is not supported");
        }
    }
}