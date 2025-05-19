<?php
use App\Http\Controllers\SliController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sli', [SliController::class, 'sli'])->name('sli');
Route::get('/random_response_time', [SliController::class, 'random_response_time'])->name('random_response_time');