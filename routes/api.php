<?php

use App\Http\Controllers\Api\CMS\CategorySubCategoryController;
use App\Http\Controllers\Api\CMS\ChannelController;
use App\Http\Controllers\Api\CMS\clientController;
use App\Http\Controllers\Api\CMS\EventController as CMSEventController;
use App\Http\Controllers\Api\CMS\FeedbackController;
use App\Http\Controllers\Api\CMS\BucketController;
use App\Http\Controllers\Api\CMS\QuestionController as CMSQuestionController;
use App\Http\Controllers\Api\CMS\RuleController;
use App\Http\Controllers\Api\CMS\SectionController;
use App\Http\Controllers\Api\CMS\SentimentMapperController;
use App\Http\Controllers\Api\CMS\StatusController;
use App\Http\Controllers\Api\CMS\ThemeController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware bucket. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {

    // Version 1 APIs for feedback
    Route::group(["middleware" => ["generic", "query.logger"]], function () {
        Route::post('/event/{client}/{channel}/{event}', [EventController::class, 'trigger'])->middleware(['auth.app-key']);
        Route::get('/questions/{client}/{channel}/{event}/{token}', [QuestionController::class, 'questions'])->middleware('auth.uuid');
        Route::post('/feedback/{client}/{channel}/{event}/{token}', [QuestionController::class, 'feedback'])->middleware('auth.uuid');
    });

    // API for CMS Panel
    Route::group(["prefix" => "cms" , "middleware" => ["auth.ip"]], function () {

        Route::resources([
            'clients' => ClientController::class,
            'channels' => ChannelController::class,
            'events' => CMSEventController::class,
            'buckets' => BucketController::class,
            'questions' => CMSQuestionController::class,
            'categories-subcategories' => CategorySubCategoryController::class,
            'sentiment-mappers' => SentimentMapperController::class,
            'status' => StatusController::class,
            'section' => SectionController::class,
            'feedback' => FeedbackController::class,
            'themes' => ThemeController::class,
        ]);

        // Events Routes
        Route::get('/event/client/{client}', [EventController::class, 'getEventsByclientTag']);
        Route::post('/event/attach-rule', [EventController::class, 'attachRuleToEvent']);

        // Buckets Routes
        Route::put('/bucket/attach-questions', [BucketController::class, 'attachQuestionsToBucket']);

        // Questions Routes
        Route::get('/question/event/{eventId}', [EventController::class, 'getQuestionsByEvent']);
        Route::get('/question/bucket/{bucketId}', [BucketController::class, 'getQuestionsByBucket']);
        Route::get('/selection-types', [CMSQuestionController::class, 'getSelectionTypes']);

        // Rules Routes
        Route::get('/quarantine-rules/list-for-selection', [RuleController::class, 'getRulesForSelection']);
        Route::put('/quarantine-rules/update', [RuleController::class, 'updateRule']);
        Route::get('/quarantine-rules/{eventId}', [EventController::class, 'getRulesByEvent']);

        // Sentiment Routes
        Route::get('sentiment-mapper/categroies', [SentimentMapperController::class, 'category']);
    });
});