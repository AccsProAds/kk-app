<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\LogFileProcessor;

Route::get('/process-files', LogFileProcessor::class);




Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
