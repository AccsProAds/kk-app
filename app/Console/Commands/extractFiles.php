<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\Models\LogFile;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class extractFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command find the files with extension .tsv.gz recursively and populates them on the DB';

    private function extractFilesData($dir)
    {
       
        $result = [];
        //$dir = 'public/logs';
        $files = Storage::files($dir); // Only get files, not directories
        foreach ($files as $file) {
            if (substr($file, -7) === '.tsv.gz') {
                
                $logfile = LogFile::firstOrCreate(['file_path' => $file]);
                if($logfile->wasRecentlyCreated) {
                    $result[]["file"] = $file;
                }
            }
        }

        $directories = Storage::directories($dir); // Only get directories
        foreach ($directories as $directory) {
            $result = array_merge($result, $this->extractFilesData($directory));
        }
        
        return $result;
        
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $extractFilesData = $this->extractFilesData('public/logs');
        $this->info(count($extractFilesData).' files found and extracted and ready to be processed');

        if(!empty($extractFilesData)) {
            $this->table(
                ['File'],
                $extractFilesData
            );
        }
        /*$this->table(
            ['File'],
            $extractFilesData
        );*/

    }
}
