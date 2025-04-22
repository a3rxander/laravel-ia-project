<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\AIRepositoryInterface;
use Illuminate\Support\Facades\Validator;


class AIController extends Controller
{
    protected $aiRepository;
    
    public function __construct(AIRepositoryInterface $aiRepository)
    {
        $this->aiRepository = $aiRepository;
    }
    
    /*
     * Generate a text response based on a prompt
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:4000',
            'provider' => '|in:gemini,openai',
            'options' => 'nullable|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            // Check if the provider is specified
            if ($request->filled('provider')) {
                $this->aiRepository->setProvider($request->input('provider'));
            }
            
            $result = $this->aiRepository->getTextResponse(
                $request->input('prompt'),
                $request->input('options', [])
            );
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Generates an image based on a prompt
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:1000',
            'provider' => '|in:gemini,openai',
            'options' => 'nullable|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            // Check if the provider is specified
            if ($request->filled('provider')) {
                $this->aiRepository->setProvider($request->input('provider'));
            }
            
            $result = $this->aiRepository->getImageResponse(
                $request->input('prompt'),
                $request->input('options', [])
            );
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * This is a example how to add some instructions before the prompt
     * Analize the sentiment of a text
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeSentiment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:2000',
            'provider' => '|in:gemini,openai',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            // Check if the provider is specified
            if ($request->filled('provider')) {
                $this->aiRepository->setProvider($request->input('provider'));
            }
            
            $result = $this->aiRepository->getSentimentAnalysis($request->input('text'));
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Extract entities from a text
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extractEntities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:2000',
            'provider' => '|in:gemini,openai',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            // Check if the provider is specified
            if ($request->filled('provider')) {
                $this->aiRepository->setProvider($request->input('provider'));
            }
            
            $result = $this->aiRepository->getEntities($request->input('text'));
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}