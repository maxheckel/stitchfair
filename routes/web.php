<?php

use App\Http\Controllers\PatternController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'title' => 'StitchFair',
    ]);
})->name('index');

Route::resource('pattern', PatternController::class);
Route::get('/wizard', [PatternController::class, 'wizard'])->name('pattern.wizard');
Route::get('/pattern/{uuid}/variants', [PatternController::class, 'variants'])->name('pattern.variants');
Route::get('/pattern/{uuid}/original', [PatternController::class, 'original_image'])->name('pattern.original');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
