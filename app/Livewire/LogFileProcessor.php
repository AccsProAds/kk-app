<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LogFile;
use App\Http\Controllers\FileExtractController;
use Illuminate\Support\Facades\Storage;

class LogFileProcessor extends Component
{
    public $batchSize = 10;
    public $processing = false;
    public $processedFiles = [];


    private function extractFilesData($dir)
    {
       
        $result = [];
        //$dir = 'public/logs';
        $files = Storage::files($dir); // Only get files, not directories
        foreach ($files as $file) {
            if (substr($file, -7) === '.tsv.gz') {
                $result[] = $file;
                LogFile::firstOrCreate(['file_path' => $file]);
            }
        }

        $directories = Storage::directories($dir); // Only get directories
        foreach ($directories as $directory) {
            $result = array_merge($result, $this->extractFilesData($directory));
        }
        return $result;
        
    }

    public function processDirs()
    {
        $this->processing = true;
        $extractFilesData = $this->extractFilesData('public/logs');
        $this->processing = false;
    }

    public function mount()
    {
        $this->processFiles = LogFile::where('processed', true)->get();
    }



    public function processFiles()
    {
        $this->processing = true;

        // Fetch unprocessed files in batches
        $files = LogFile::where('processed', false)->take($this->batchSize)->get();

        foreach ($files as $file) {
            // Check if the file has already been processed
            if ($file->processed) {
                continue;
            }

            // Call the extract and parse logic
            $controller = new FileExtractController();
            $controller->extractAndParseFile($file->file_path);

            // Mark the file as processed
            $file->processed = true;
            $file->save();
        }

        $this->processing = false;
    }

    public function render()
    {
        $unprocessedFilesCount = LogFile::where('processed', false)->count();
        return view('livewire.log-file-processor', [
            'unprocessedFilesCount' => $unprocessedFilesCount,
        ])->layout('layouts.app'); // Specify the correct layout here
    }
}
