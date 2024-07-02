<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function(){
    Route::get('article', [App\Http\Controllers\API\v1\ArticleController::class, 'index']);
    Route::post('article', [App\Http\Controllers\API\v1\ArticleController::class, 'store']);
    Route::get('article/{id}', [App\Http\Controllers\API\v1\ArticleController::class, 'show']);
    Route::put('article/{id}', [App\Http\Controllers\API\v1\ArticleController::class, 'update']);
    Route::delete('article/{id}', [App\Http\Controllers\API\v1\ArticleController::class, 'destroy']);
})->middleware('auth:sanctum');