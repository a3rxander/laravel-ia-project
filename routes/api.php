<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AIController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('ai')->group(function () {
    Route::post('/text', [AIController::class, 'generateText']);
    Route::post('/image', [AIController::class, 'generateImage']);
    //this is a example how to add some instructions before the prompt
    Route::post('/sentiment', [AIController::class, 'analyzeSentiment']);
    Route::post('/entities', [AIController::class, 'extractEntities']);
});