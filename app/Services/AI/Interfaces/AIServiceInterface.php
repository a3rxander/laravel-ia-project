<?php

namespace App\Services\AI\Interfaces;

 /**
 * Interface for  image generation capabilities
 */
interface AIServiceInterface 
{
    /**
     * Get the name of the AI provider
     */
    public function getProviderName(): string;

    /**
     * Check if the service is ready/configured
     */
    public function isReady(): bool;

    /**
     * Get the list of capabilities this provider supports
     * @return array<string> e.g. ['text', 'image']
     */
    public function getCapabilities(): array;
    
}