<?php

use App\Http\Controllers\Api\CMS\CategorySubCategoryController;
use App\Http\Controllers\Api\CMS\clientController;
use App\Http\Controllers\Api\CMS\EventController as CMSEventController;
use App\Http\Controllers\Api\CMS\FeedbackController;
use App\Http\Controllers\Api\CMS\GroupController;
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
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {

    // Version 1 APIs for feedback
    Route::group(["middleware" => ["generic", "query.logger"]], function () {
        Route::post('/event/{client}/{event}', [EventController::class, 'trigger'])->middleware(['auth.app-key', 'guest.event']);
        Route::get('/questions/{client}/{event}/{uuid}', [QuestionController::class, 'questions'])->middleware('auth.uuid');
        Route::post('/feedback/{client}/{event}/{uuid}', [QuestionController::class, 'feedback'])->middleware('auth.uuid');
    });

    // API for CMS Panel
    Route::group(["prefix" => "cms" , "middleware" => ["auth.ip"]], function () {

        Route::resources([
            'clients' => ClientController::class,
            'events' => CMSEventController::class,
            'groups' => GroupController::class,
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

        // Groups Routes
        Route::put('/group/attach-questions', [GroupController::class, 'attachQuestionsToGroup']);

        // Questions Routes
        Route::get('/question/event/{eventId}', [EventController::class, 'getQuestionsByEvent']);
        Route::get('/question/group/{groupId}', [GroupController::class, 'getQuestionsByGroup']);
        Route::get('/selection-types', [CMSQuestionController::class, 'getSelectionTypes']);

        // Rules Routes
        Route::get('/rules/list-for-selection', [RuleController::class, 'getRulesForSelection']);
        Route::put('/rules/update', [RuleController::class, 'updateRule']);
        Route::get('/rules/{eventId}', [EventController::class, 'getRulesByEvent']);

        // Sentiment Routes
        Route::get('sentiment-mapper/categroies', [SentimentMapperController::class, 'category']);
    });
});