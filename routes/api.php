<?php

use App\Http\Controllers\QueueController;
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

Route::post('/enqueue', [QueueController::class, 'enqueue'])->name('enqueue');
Route::delete('/dequeue', [QueueController::class, 'dequeue'])->name('dequeue');
Route::get('/front', [QueueController::class, 'front'])->name('front');
Route::get('/peek', [QueueController::class, 'peek'])->name('peek');
Route::get('/rear', [QueueController::class, 'rear'])->name('rear');
Route::get('/is-empty', [QueueController::class, 'isEmpty'])->name('isEmpty');
Route::get('/size', [QueueController::class, 'size'])->name('size');
