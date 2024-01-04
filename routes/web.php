<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\OutlookCalendarController;
use Carbon\Carbon;
use App\Http\Controllers\TestConroller;

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

Route::get('/google/calendar/auth/{token}', [GoogleCalendarController::class,'auth']);
Route::get('/google/calendar/callback', [GoogleCalendarController::class,'callback']);

Route::get('/outlook/calendar/auth/{token}', [OutlookCalendarController::class,'auth']);
Route::get('/outlook/calendar/callback', [OutlookCalendarController::class,'callback']);

Route::get('/test', [TestConroller::class,'testing']);
