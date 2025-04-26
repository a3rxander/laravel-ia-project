<?php

namespace App\Services\AI\Interfaces;

 /**
 * Interface for  image generation capabilities
 */
interface ImageGenerationInterface extends AIServiceInterface
{
    /**
     * Generate text based on a prompt
     * @param string $prompt The input prompt
     * @param array $options Additional options for the AI provider
     * @return string The generated text
     */
    public function generateImage(string $prompt, array $options = []): string;
    
}