<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Interfaces\TextProcessingInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepseekService implements TextProcessingInterface
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelId;

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
        $this->baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');
        $this->modelId = config('services.deepseek.model_id', 'deepseek-chat');

        if (empty($this->apiKey)) {
            throw new \Exception('deepseek API key has not been configured. Please check your configuration.');
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
            $messageList = [];
            $messageList[] = ['role' => 'system', 'content' => 'You are a helpful assistant. Please respond to the user\'s input.'];
            $messageList[] = ['role' => 'user', 'content' => $prompt];

            $endpoint = "{$this->baseUrl}/chat/completions";

            $payload = [
                'model' => $this->modelId,
                'messages' => $messageList,
                'stream' => false
            ];

            

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('Error when generating text with deepseek: ' . $response->body());
                throw new \Exception('Error when generating text with deepseek: ' . $response->status());
            }

            $data = $response->json();

            return $this->parseTextResponse($data);
        } catch (\Exception $e) {
            Log::error('It was not possible to generate text with deepseek: ' . $e->getMessage());
            throw $e;
        }
    } 
    public function analyzeSentiment(string $text): array
    {
        //this is a example how to add some instructions before the prompt
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
            //this is a example how to add some instructions before the prompt
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
        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return '';
    }
}