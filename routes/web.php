<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\IsAuth;
use App\Http\Middleware\NoAuth;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Dashboard\Home;
use App\Livewire\Dashboard\Transaction\DailyMonthlyReport;
use App\Livewire\Dashboard\Transaction\DoorReport;
use App\Livewire\Dashboard\Transaction\DoorAlarmReport;  
use App\Livewire\Dashboard\Transaction\MatrixAccess;  
use App\Livewire\Dashboard\Transaction\ListAuthorize;  
use App\Livewire\Dashboard\Transaction\PersonnelReport;  

Route::middleware(NoAuth::class)->group(function () {
    Route::get('/', Login::class)->name('login');
});

Route::middleware(IsAuth::class)->group(function () {
    Route::get('/change-password', ChangePassword::class)->name('change-password');
    Route::get('/home', Home::class)->name('home');
    Route::get('/daily-monthly-report', DailyMonthlyReport::class)->name('daily-monthly-report');
    Route::get('/door-report', DoorReport::class)->name('door-report');
    Route::get('/door-alarm-report', DoorAlarmReport::class)->name('door-alarm-report');
    Route::get('/matrix-access', MatrixAccess::class)->name('matrix-access');
    Route::get('/list-authorize', ListAuthorize::class)->name('list-authorize');
    Route::get('/personnel-report', PersonnelReport::class)->name('personnel-report');
});