<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('/user/form', [UserController::class, 'showForm']);
Route::post('/user/process', [UserController::class, 'processForm']);
Route::get('/user/result', [UserController::class, 'result']);
Route::get('/user/generate', [UserController::class, 'generate']);
Route::post('/user/handler', [TestController::class, 'handler'])->name('user.handler');
Route::get('/user/handler', [TestController::class, 'handler'])->name('user.handler');
