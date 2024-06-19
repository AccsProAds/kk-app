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
    public $declined_filter = 'all';


    public $lead_time_start;
    public $lead_time_end;

    public $services = ['fitnessxr', 'usadc', 'uprev']; // Add your services here

    public $leads_found = 0;
    public $leads_per_day = 0;
    public $total_days = 0;
    public $leads = [];


    protected $rules = [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service' => 'required|string|in:fitnessxr,usadc,uprev',
        'service' => 'required|string',
        'country' => 'nullable|string',
        'lead_time_start' => 'nullable|date',
        'lead_time_end' => 'nullable|date',
        'limit' => 'sometimes|integer|min:0',
        'declined_filter' => 'required|string|in:all,only,ignore',
    ];


    public function mount()
    {
        //$this->fileExtractService = $fileExtractService;
    }

    public function calculateLeads()
    {
        $this->validate();

        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();
        $leadTimeStart = $this->lead_time_start ? Carbon::parse($this->lead_time_start)->startOfDay() : null;
        $leadTimeEnd = $this->lead_time_end ? Carbon::parse($this->lead_time_end)->endOfDay() : null;
        $limit = $this->limit;
        $service = $this->service ?? 'fitnessxr';
        $country = $this->country;

        $now = Carbon::now();

        if ($startDate->isToday()) {
            $startDate = $now->copy();
        }

        // Adjust the number of days to be inclusive of both start and end date
        $days = $startDate->diffInDays($endDate);

        dd($days);
    
        // If start date is today, schedule within the remaining time of the day
        if ($startDate->isSameDay($endDate)) {
            $days = 1;
        } else {
            $days++;
        }

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

        // Apply declined filter
        if ($this->declined_filter === 'only') {
            $query->where('leads.declined', true);
        } elseif ($this->declined_filter === 'ignore') {
            $query->where('leads.declined', false);
        }

        if ($leadTimeStart && $leadTimeEnd) {
            $query->whereBetween('leads.lead_time', [$leadTimeStart, $leadTimeEnd]);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $this->leads = $query->get();
        $this->leads_found = $this->leads->count();
        $this->total_days = $days;
        $this->leads_per_day = intdiv($this->leads_found, $days) + ($this->leads_found % $days > 0 ? 1 : 0);

        //$this->emit('leadsCalculated');
    }


    public function scheduleLeads()
    {
        /*$this->validate();
    
        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();
        $leadTimeStart = $this->lead_time_start ? Carbon::parse($this->lead_time_start)->startOfDay() : null;
        $leadTimeEnd = $this->lead_time_end ? Carbon::parse($this->lead_time_end)->endOfDay() : null;
        $limit = $this->limit;
        $service = $this->service ?? 'fitnessxr';
        $country = $this->country;
    
        $now = Carbon::now();
    
        // Adjust start date to the current time if the start date is today
        if ($startDate->isToday()) {
            $startDate = $now->copy();
        }
    
        // Adjust the number of days to be inclusive of both start and end date
        $days = $startDate->diffInDays($endDate);
    
        // If start date is today, schedule within the remaining time of the day
        if ($startDate->isSameDay($endDate)) {
            $days = 1;
        } else {
            $days++;
        }
    
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
    
         // Apply declined filter
        if ($this->declined_filter === 'only') {
            $query->where('leads.declined', true);
        } elseif ($this->declined_filter === 'ignore') {
            $query->where('leads.declined', false);
        }
    
        if ($limit > 0) {
            $query->limit($limit);
        }

        if ($leadTimeStart && $leadTimeEnd) {
            $query->whereBetween('leads.lead_time', [$leadTimeStart, $leadTimeEnd]);
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
        }*/

        $this->calculateLeads();

        if ($this->leads_found === 0) {
            flashify([
                'plugin' => 'izi-toast',
                'title' => 'Error',
                'text' => '0 leads found',
                'type' => 'error',
                'livewire' => $this,
            ]);
            return;
        }
    
        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();
        $now = Carbon::now();

        if ($startDate->isToday()) {
            $startDate = $now->copy();
        }

        $days = $this->total_days;
        $leadsPerDay = $this->leads_per_day;
        $extraLeads = $this->leads_found % $days;

        $leadIndex = 0;

        for ($day = 0; $day < $days; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            if ($currentDate->isToday()) {
                $secondsInDay = 86400 - $now->secondsSinceMidnight();
            } else {
                $secondsInDay = 86400;
            }

            $leadsForToday = $leadsPerDay + ($day < $extraLeads ? 1 : 0);

            if ($leadsForToday > 0) {
                $interval = intdiv($secondsInDay, $leadsForToday);
                $currentTime = $currentDate->copy();

                if ($currentDate->isToday()) {
                    $currentTime = $now->copy();
                }

                for ($i = 0; $i < $leadsForToday && $leadIndex < $this->leads_found; $i++) {
                    $scheduledTime = $currentTime->copy()->addSeconds($i * $interval);

                    Lead2External::create([
                        'lead_id' => $this->leads[$leadIndex]->id,
                        'external_service' => $this->service,
                        'scheduled_time' => $scheduledTime
                    ]);

                    $leadIndex++;
                }
            }
        }

        flashify([
            'plugin' => 'izi-toast',
            'title' => 'Success',
            'text' => $this->leads_found.' Leads Scheduled - '.$leadsPerDay.' / day',
            'type' => 'success',
            'livewire' => $this,
        ]);
    }
    
    
    public function render()
    {
        $unprocessedFilesCount = LogFile::where('processed', false)->count();
        return view('livewire.log-file-processor', [
            'unprocessedFilesCount' => $unprocessedFilesCount,
        ])->layout('layouts.app'); // Specify the correct layout here
    }
}
