<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ProcessLead;
use App\Models\Lead2External;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// Schedule the job to run every minute
Artisan::command('leads:process', function () {
    // Get the leads that need to be processed
    
    $now = Carbon::now();

    Log::info('Starting leads processing at ' . $now->toDateTimeString());

    $leadsToProcess = Lead2External::where('scheduled_time', '<=', $now)
                                   ->where('processed', false)
                                   ->get();

    Log::info('Found ' . $leadsToProcess->count() . ' leads to process.');

    foreach ($leadsToProcess as $lead2External) {
            ProcessLead::dispatch($lead2External);
            $lead2External->update(['processed' => true]);
            //Log::info('Processed lead with ID: ' . $lead2External->id);
    }
})->purpose('Run the leads')->everyFiveSeconds();

// Register the scheduler command
//Schedule::command('leads:process')->everyMinute();