<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LogFile;
use App\Models\Lead;
use App\Models\Lead2External;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LogFileProcessor extends Component
{
    public $batchSize = 10;
    public $processedFiles = [];
    public $start_date;
    public $end_date;
    public $limit = 0;
    public $service;
    public $country;

    public $services = ['fitnessxr', 'usadc', 'uprev']; // Add your services here


    protected $rules = [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service' => 'required|string|in:fitnessxr,usadc,uprev',
        'service' => 'required|string',
        'country' => 'nullable|string',
        'limit' => 'sometimes|integer|min:0',
    ];


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

    public function scheduleLeads()
    {
        $this->validate();

        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();
        $limit = $this->limit;
        $service = $this->service ?? 'fitnessxr';
        $country = $this->country;

        $days = $startDate->diffInDays($endDate) + 1;

        $query = Lead::leftJoin('lead2_externals', function ($join) use ($service) {
            $join->on('leads.id', '=', 'lead2_externals.lead_id')
                ->where('lead2_externals.external_service', '=', $service);
        })
            ->whereNull('lead2_externals.id')
            ->orderBy('leads.lead_time', 'ASC')
            ->select('leads.*');

        if (!empty($country)) {
            $query->where('leads.country', $country);
        }

        $query->where('leads.declined', false);
    

        if ($limit > 0) {
            $query->limit($limit);
        }

        $leads = $query->get();

        if ($leads->isEmpty()) {
            flashify([
                'plugin' => 'izi-toast',
                'title' => 'Error',
                'text' => '0 leads found',
                'type' => 'error',
                'livewire' => $this,
            ]);
            return;
        }

        $totalLeads = $leads->count();
        $leadsPerDay = intdiv($totalLeads, $days);
        $extraLeads = $totalLeads % $days;

        $leadIndex = 0;

        for ($day = 0; $day < $days; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            $leadsForToday = $leadsPerDay + ($day < $extraLeads ? 1 : 0);

            if ($leadsForToday > 0) {
                $interval = intdiv(86400, $leadsForToday);
                $currentTime = $currentDate->copy();

                for ($i = 0; $i < $leadsForToday && $leadIndex < $totalLeads; $i++) {
                    $scheduledTime = $currentTime->copy()->addSeconds($i * $interval);

                    Lead2External::create([
                        'lead_id' => $leads[$leadIndex]->id,
                        'external_service' => $service,
                        'scheduled_time' => $scheduledTime
                    ]);

                    $leadIndex++;
                }
            }
        }

        flashify([
            'plugin' => 'izi-toast',
            'title' => 'Success',
            'text' => $totalLeads.' Leads Scheduled - '.$leadsPerDay.' / day',
            'type' => 'success',
            'livewire' => $this,
        ]);
    }

    public function processDirs()
    {
        
        $extractFilesData = $this->extractFilesData('public/logs');
        flashify([
            'plugin' => 'izi-toast',
            'title' => 'Success',
            'text' => 'Files Proccessed Ready To Read',
            'type' => 'success',
            'livewire' => $this,
        ]);
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

        flashify([
            'plugin' => 'izi-toast',
            'title' => 'Success',
            'text' => 'File data leads processed',
            'type' => 'success',
            'livewire' => $this,
        ]);
    }

    public function processLeads()
    {

        $unprocessedFiles = LogFile::where('leads_exported', false)
        ->whereNotNull('data')
        ->where('leads_exported', false)
        ->take(5)
        ->get();
       
        foreach($unprocessedFiles as $file)
        {
            $total = 0;
            $data = json_decode($file->data);
            //dd($data);
            foreach($data as $lead_data)
            {

                if(isset($lead_data->cardNumber) && isset($lead_data->cardMonth) && isset($lead_data->cardYear) && isset($lead_data->cardSecurityCode)) {

                    $existingLead = Lead::where('card_number', $lead_data->cardNumber)
                    ->where('card_month', $lead_data->cardMonth)
                    ->where('card_year', $lead_data->cardYear)
                    ->where('card_cvv', $lead_data->cardSecurityCode)
                    ->first();

                    if (!$existingLead) {
                        $lead = new Lead();
                        $lead->log_file_id = $file->id;
                        $lead->first_name = $lead_data->firstName ?? '';
                        $lead->last_name = $lead_data->lastName ?? '';
                        $lead->email = $lead_data->emailAddress ?? '';
                        $lead->phone = $lead_data->phoneNumber;
                        $lead->address_1 = $lead_data->address1 ?? '';
                        $lead->address_2 = $lead_data->address2 ?? '';
                        $lead->city = $lead_data->city ?? '';
                        $lead->state = $lead_data->state ?? '';
                        $lead->zip = $lead_data->zip ?? $lead_data->postalCode ?? '';
                        $lead->country = $lead_data->country ?? '';
                        $lead->ip = $lead_data->ipAddress ?? '';
                        $lead->card_month = $lead_data->cardMonth ?? '';
                        $lead->card_year = $lead_data->cardYear ?? '';
                        $lead->card_number = $lead_data->cardNumber ?? '';
                        $lead->card_cvv = $lead_data->cardSecurityCode ?? '';
                        $lead->creditcard_type = $lead_data->creditCardType;
                        $lead->declined = isset($lead_data->decline_message) ? true : false;
                        
                        if (isset($lead_data->lead_time)) {
                            $lead->lead_time = Carbon::parse($lead_data->lead_time);
                        }

                        $lead->lead_url = '';
                        $lead->aff_id = $lead_data->affid ?? '';
                        $lead->pub = $lead_data->sid ?? '';
                        $lead->click_id = $lead_data->click_id ?? '';
                        $lead->c1 = $lead_data->c1 ?? '';
                        $lead->c2 = $lead_data->c2 ?? '';
                        $lead->c3 = $lead_data->c3 ?? '';


                        $lead->save();
                        $total++;
                    }
                }

               

            }
            $file->leads_exported = true;
            $file->save();

            flashify([
                'plugin' => 'izi-toast',
                'title' => 'Success',
                'text' => 'File '.$file->file_path.' processed with '.$total.' leads',
                'type' => 'success',
                'livewire' => $this,
            ]);

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
