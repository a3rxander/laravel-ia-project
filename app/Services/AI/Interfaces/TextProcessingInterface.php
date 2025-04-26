<?php

namespace App\Services\AI\Interfaces;

/**
 * Interface for all text processing capabilities
 */
interface TextProcessingInterface extends AIServiceInterface
{
    /**
     * Generate text based on a prompt
     *
     * @param string $prompt The input prompt
     * @param array $options Additional options for the AI provider
     * @return string The generated text
     */
    public function generateText(string $prompt, array $options = []): string;
    
    /**
     * Analyze sentiment of provided text
     *
     * @param string $text The text to analyze
     * @return array Sentiment analysis results with scores
     */
    public function analyzeSentiment(string $text): array;
    
    /**
     * Extract entities from provided text
     *
     * @param string $text The text to analyze
     * @return array Extracted entities with their types
     */
    public function extractEntities(string $text): array;
}
 