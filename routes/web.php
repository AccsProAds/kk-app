<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\LogFileProcessor;
use App\Livewire\Calendar;
//Route::get('/process-files', LogFileProcessor::class);




Route::get('/', LogFileProcessor::class);
Route::get('/calendar', Calendar::class);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
