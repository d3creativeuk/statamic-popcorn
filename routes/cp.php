<?php

use D3Creative\Popcorn\Http\Controllers\MuxAssetController;
use D3Creative\Popcorn\Http\Controllers\MuxUploadController;
use Illuminate\Support\Facades\Route;

// Each throttle names its own bucket. Without the third argument Laravel keys
// the limiter on the user alone, so every route here would draw from one
// shared counter, and a field polling for status would use up the allowance
// for uploading.
Route::middleware('can:use popcorn')->prefix('popcorn/mux')->name('popcorn.mux.')->group(function () {
    Route::post('uploads', [MuxUploadController::class, 'store'])
        ->middleware('throttle:30,1,popcorn.mux.uploads.store')
        ->name('uploads.store');

    Route::post('uploads/from-asset', [MuxUploadController::class, 'fromAsset'])
        ->middleware('throttle:20,1,popcorn.mux.uploads.from-asset')
        ->name('uploads.from-asset');

    Route::get('uploads/{upload}', [MuxUploadController::class, 'show'])
        ->where('upload', '[A-Za-z0-9]{1,128}')
        ->middleware('throttle:240,1,popcorn.mux.uploads.show')
        ->name('uploads.show');

    Route::get('assets', [MuxAssetController::class, 'index'])
        ->middleware('throttle:120,1,popcorn.mux.assets.index')
        ->name('assets.index');

    Route::get('assets/{video}', [MuxAssetController::class, 'show'])
        ->where('video', '[A-Za-z0-9]{1,128}')
        ->middleware('throttle:240,1,popcorn.mux.assets.show')
        ->name('assets.show');

    Route::patch('assets/{video}/title', [MuxAssetController::class, 'title'])
        ->where('video', '[A-Za-z0-9]{1,128}')
        ->middleware('throttle:60,1,popcorn.mux.assets.title')
        ->name('assets.title');
});
