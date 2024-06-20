<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LogFile;
use App\Models\Lead;
use App\Models\Lead2External;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Calendar extends Component
{
    public $calendar;
    public $scheduledCalendar = [];


    public function boot()
    {
        // Initialization if needed
        $this->fetchLeadsForCalendar();
    }

    public function fetchLeadsForCalendar()
    {
        // Fetching new leads
        $query = Lead::query();
    
        $this->leads = $query->orderBy('lead_time')->get();
    
        $leadCountsByDate = [];
        foreach ($this->leads as $lead) {
            $date = Carbon::parse($lead->lead_time)->toDateString();
            if (!isset($leadCountsByDate[$date])) {
                $leadCountsByDate[$date] = 0;
            }
            $leadCountsByDate[$date]++;
        }
    
        // Fetching scheduled leads
        $scheduledLeads = Lead2External::where('processed', false)
            ->orderBy('scheduled_time')
            ->get();
    
        $scheduledLeadCountsByDate = [];
        foreach ($scheduledLeads as $scheduledLead) {
            $date = Carbon::parse($scheduledLead->scheduled_time)->toDateString();
            $service = $scheduledLead->external_service;
            if (!isset($scheduledLeadCountsByDate[$date])) {
                $scheduledLeadCountsByDate[$date] = [];
            }
            if (!isset($scheduledLeadCountsByDate[$date][$service])) {
                $scheduledLeadCountsByDate[$date][$service] = 0;
            }
            $scheduledLeadCountsByDate[$date][$service]++;
        }
    
        $this->leads_found = $this->leads->count();
        $this->leads_per_day = $this->leads_found ? intdiv($this->leads_found, count($leadCountsByDate)) : 0;
        $this->total_days = count($leadCountsByDate);
        $this->total_days = intval(ceil($this->total_days));
    
        $this->calendar = $leadCountsByDate;
        $this->scheduledCalendar = $scheduledLeadCountsByDate;
    
        $this->dispatch('leadsCalculated', ['leads' => $leadCountsByDate, 'scheduledLeads' => $scheduledLeadCountsByDate]);
    }


    public function render()
    {
        return view('livewire.calendar')->layout('layouts.app'); // Specify the correct layout here
    }
}
