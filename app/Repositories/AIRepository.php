<?php

namespace App\Repositories;

use App\Repositories\Interfaces\AIRepositoryInterface;
use App\Services\AI\AIFactory;
use App\Services\AI\Interfaces\AIServiceInterface;
use App\Services\AI\Interfaces\TextProcessingInterface;
use App\Services\AI\Interfaces\ImageGenerationInterface;
use Illuminate\Support\Facades\Cache;

class AIRepository implements AIRepositoryInterface
{
    protected $service;
    protected $cacheEnabled;
    protected $cacheTtl;
    
    public function __construct()
    {
        $this->service = AIFactory::create();
        $this->cacheEnabled = config('services.ai.cache_enabled', true);
        $this->cacheTtl = config('services.ai.cache_ttl', 60 * 24); // 24 horas por defecto
    }
    
    public function getTextResponse(string $prompt, array $options = []): string
    {

        if (!$this->service instanceof TextProcessingInterface) {
            throw new \Exception('The current AI service does not support text generation.');
        }

        if ($this->cacheEnabled) {
            $cacheKey = 'ai_text_' . md5($prompt . serialize($options));
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($prompt, $options) {
                return $this->service->generateText($prompt, $options);
            });
        }
        
        return $this->service->generateText($prompt, $options);
    } 
    
    public function getSentimentAnalysis(string $text): array
    {
        if (!$this->service instanceof TextProcessingInterface) {
            throw new \Exception('The current AI service does not support sentiment analysis.');
        }

        if ($this->cacheEnabled) {
            $cacheKey = 'ai_sentiment_' . md5($text);
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($text) {
                return $this->service->analyzeSentiment($text);
            });
        }
        
        return $this->service->analyzeSentiment($text);
    }
    
    public function getEntities(string $text): array
    {
        if (!$this->service instanceof TextProcessingInterface) {
            throw new \Exception('The current AI service does not support entity extraction.');

        }
        if ($this->cacheEnabled) {
            $cacheKey = 'ai_entities_' . md5($text);
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($text) {
                return $this->service->extractEntities($text);
            });
        }
        
        return $this->service->extractEntities($text);
    }

    
    
    public function getImageResponse(string $prompt, array $options = []): string
    {
        
        if (!$this->service instanceof ImageGenerationInterface) {
            throw new \Exception('The current AI service does not support image generation.');
        }
        

        if ($this->cacheEnabled) {
            $cacheKey = 'ai_image_' . md5($prompt . serialize($options));
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($prompt, $options) {
                return $this->service->generateImage($prompt, $options);
            });
        }
        
        return $this->service->generateImage($prompt, $options);
    }
    
    public function setProvider(string $provider): void
    {
        $this->service = AIFactory::create($provider);
    }
}