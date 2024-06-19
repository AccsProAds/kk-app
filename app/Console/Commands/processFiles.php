<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogFile;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class processFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-files {--batch=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the files and extract the data on each file specifying the batch, default is 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $batchSize =  $this->option('batch');

        //$this->info($batchSize); return;

        $files = LogFile::where('processed', false)
        ->where('is_processing', false)
        ->take($batchSize)->get();


        $fileLibrary = new \App\Libraries\FileExtractLibrary();

        $table = [];
        $i = 0;
        foreach ($files as $file) {
            // Check if the file has already been processed
            if ($file->is_processing || $file->processed) {
                continue;
            }

            $file->is_processing = true;
            $file->save();

            // Call the extract and parse logic
            
            $data = $fileLibrary->extractAndParseFile($file->file_path);
            
            // Mark the file as processed
            $file->processed = true;
            $file->is_processing = false;
            $file->data = $data;
            $file->total_leads = count($data);
            $file->save();

            $table[$i]['file'] = $file->file_path; 
            $table[$i]['total_leads'] = count($data);
            $i++;
        }


        $this->table(
                ['File', 'Total Leads'],
                $table
        );
    }
}
