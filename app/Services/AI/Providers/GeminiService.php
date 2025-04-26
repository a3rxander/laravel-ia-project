<?php

namespace App\Services\AI\Providers;


use App\Services\AI\Interfaces\TextProcessingInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements TextProcessingInterface
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelId;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        $this->modelId = config('services.gemini.model_id', 'gemini-2.0-flash');

        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API key has not been configured. Please check your configuration.');
        }
    }

    public function getProviderName(): string
    {
        return 'DeepSeek';
    }

    public function isReady(): bool
    {
        return $this->apiKey !== null;
    }

    public function getCapabilities(): array
    {
        return ['text'];
    }

    public function generateText(string $prompt, array $options = []): string
    {
        try {
            $endpoint = "{$this->baseUrl}/models/{$this->modelId}:generateContent?key={$this->apiKey}";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ];

            // add generation options if provided
            if (!empty($options)) {
                $generationConfig = [];

                if (isset($options['temperature'])) {
                    $generationConfig['temperature'] = (float)$options['temperature'];
                }

                if (isset($options['max_tokens'])) {
                    $generationConfig['maxOutputTokens'] = (int)$options['max_tokens'];
                }

                if (isset($options['top_p'])) {
                    $generationConfig['topP'] = (float)$options['top_p'];
                }

                if (isset($options['top_k'])) {
                    $generationConfig['topK'] = (int)$options['top_k'];
                }

                if (!empty($generationConfig)) {
                    $payload['generationConfig'] = $generationConfig;
                }
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('Error when generating text with Gemini: ' . $response->body());
                throw new \Exception('Error when generating text with Gemini: ' . $response->status());
            }

            $data = $response->json();

            return $this->parseTextResponse($data);
        } catch (\Exception $e) {
            Log::error('It was not possible to generate text with Gemini: ' . $e->getMessage());
            throw $e;
        }
    }

    public function analyzeSentiment(string $text): array
    {
        try {
            $prompt = "Analize the sentiment of the following text and return a JSON with the keys 'sentiment' (positive, negative or neutral) and 'score' (from 0 to 1): \n\n" . $text; 

            $response = $this->generateText($prompt);

            // extract the JSON from the response
            preg_match('/\{.*\}/s', $response, $matches);
            if (count($matches) > 0) {
                return json_decode($matches[0], true) ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error in sentiment analysis: ' . $e->getMessage());
            throw $e;
        }
    }

    public function extractEntities(string $text): array
    {
        try {
            $prompt = "Extract the named entities (people, organizations, places, dates) from the following text and return a JSON with the entities grouped by type: \n\n" . $text;

            $response = $this->generateText($prompt);

            // extract the JSON from the response
            preg_match('/\{.*\}/s', $response, $matches);
            if (count($matches) > 0) {
                return json_decode($matches[0], true) ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error in entity extraction: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function parseTextResponse(array $response): string
    {
        $result = '';

        if (isset($response['candidates'][0]['content']['parts'])) {
            foreach ($response['candidates'][0]['content']['parts'] as $part) {
                if (isset($part['text'])) {
                    $result .= $part['text'];
                }
            }
        }

        return $result;
    }
}