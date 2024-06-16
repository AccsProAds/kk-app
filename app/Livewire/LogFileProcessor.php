<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LogFile;
use App\Models\Lead;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;

class LogFileProcessor extends Component
{
    public $batchSize = 10;
    public $processedFiles = [];


    public function mount()
    {
        //$this->fileExtractService = $fileExtractService;
    }

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
        
        $extractFilesData = $this->extractFilesData('public/logs');
        session()->flash('message', 'Files Processed');
    }





    public function processFiles()
    {
        // Fetch unprocessed files in batches
        $files = LogFile::where('processed', false)->take($this->batchSize)->get();
        $fileLibrary = new \App\Libraries\FileExtractLibrary();
        
        foreach ($files as $file) {
            // Check if the file has already been processed
            if ($file->processed) {
                continue;
            }

            // Call the extract and parse logic
            
            $data = $fileLibrary->extractAndParseFile($file->file_path);
            
            // Mark the file as processed
            $file->processed = true;
            $file->data = $data;
            $file->total_leads = count($data);
            $file->save();
        }

        session()->flash('message', 'File Data Processed');
    }

    public function processLeads()
    {

        $unprocessedFiles = LogFile::where('leads_exported', false)->get();
       
        foreach($unprocessedFiles as $file)
        {
            $total = 0;
            $data = json_decode($file->data);
            //dd($data);
            foreach($data as $lead_data)
            {

                if(isset($lead_data->cardNumber) && isset($lead_data->cardMonth) && isset($lead_data->cardYear)) {
                    $lead = new Lead();
                    $lead->log_file_id = $file->id;
                    $lead->first_name = $lead_data->firstName ?? '';
                    $lead->last_name = $lead_data->lastName ?? '';
                    $lead->email = $lead_data->emailAddress ?? '';
                    $lead->address_1 = $lead_data->address1 ?? '';
                    $lead->address_2 = $lead_data->address2 ?? '';
                    $lead->city = $lead_data->city ?? '';
                    $lead->state = $lead_data->state ?? '';
                    $lead->zip = $lead_data->postalCode ?? '';
                    $lead->country = $lead_data->country ?? '';
                    $lead->ip = $lead_data->ipAddress ?? '';
                    $lead->card_month = $lead_data->cardMonth ?? '';
                    $lead->card_year = $lead_data->cardYear ?? '';
                    $lead->card_number = $lead_data->cardNumber ?? '';
                    $lead->card_cvv = $lead_data->cardSecurityCode ?? '';
                    $lead->declined = $lead_data->decline_message == "declined" ? true : false;
                    $lead->save();
                    $total++;
                }

               

            }
            $file->leads_exported = true;
            session()->flash('message', 'File '.$file->file_path.' processed with '.$total.' leads');
        }

        

    }

    public function render()
    {
        $unprocessedFilesCount = LogFile::where('processed', false)->count();
        return view('livewire.log-file-processor', [
            'unprocessedFilesCount' => $unprocessedFilesCount,
        ])->layout('layouts.app'); // Specify the correct layout here
    }
}
