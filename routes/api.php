<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\BulkUploadController;
use App\Http\Controllers\Api\CollegeController;
use App\Http\Controllers\Api\CompanyTagController;
use App\Http\Controllers\Api\CompilerController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\QbCourseController;
use App\Http\Controllers\Api\QbTopicsController;
use App\Http\Controllers\Api\QuestionTagController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\UserAcademicController;
use App\Http\Middleware\AuthorizeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum', AuthorizeUser::class)->group(function () {
    Route::apiResource('colleges', CollegeController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('batches', BatchController::class);
    Route::apiResource('sections', SectionController::class);
    
    Route::apiResource('qbcourses', QbCourseController::class);
    Route::apiResource('qbtopics', QbTopicsController::class);
    Route::apiResource('questiontags', QuestionTagController::class);
    Route::apiResource('companytags', CompanyTagController::class);
    Route::apiResource('languages', LanguageController::class);
    Route::apiResource('useracademics', UserAcademicController::class);
    Route::post('bulkuploads', [BulkUploadController::class, 'store'])->name('bulkuploads');
});

Route::post('login', [AuthController::class, 'authenticate'])->name('login');
