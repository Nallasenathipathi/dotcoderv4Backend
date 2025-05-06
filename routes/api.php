<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\CollegeController;
use App\Http\Controllers\Api\CompanyTagController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\QuestionTagController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\UserAcademicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('colleges', CollegeController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('batches', BatchController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('useracademics', UserAcademicController::class);
Route::apiResource('questiontags', QuestionTagController::class);
Route::apiResource('companytags', CompanyTagController::class);

