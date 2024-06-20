<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LogFile;
use App\Models\Lead;
use App\Models\Lead2External;
use App\Libraries\FileExtractLibrary;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Calendar extends Component
{
    public $calendar;
    public $scheduledCalendar = [];
    public $leads_found;
    public $leads_per_day;
    public $total_days;

    public function boot()
    {
        // Initialization if needed
        $this->fetchLeadsForCalendar();
    }

    public function fetchLeadsForCalendar()
    {
        // Use caching to improve performance
        $this->leads = Cache::remember('leads', 600, function () {
            return Lead::orderBy('lead_time')->get();
        });

        $leadCountsByDate = $this->calculateLeadCounts($this->leads);

        $scheduledLeads = Cache::remember('scheduled_leads', 600, function () {
            return Lead2External::where('processed', false)
                ->orderBy('scheduled_time')
                ->get();
        });

        $scheduledLeadCountsByDate = $this->calculateScheduledLeadCounts($scheduledLeads);

        $this->leads_found = $this->leads->count();
        $this->leads_per_day = $this->leads_found ? intdiv($this->leads_found, count($leadCountsByDate)) : 0;
        $this->total_days = count($leadCountsByDate);
        $this->total_days = intval(ceil($this->total_days));

        $this->calendar = $leadCountsByDate;
        $this->scheduledCalendar = $scheduledLeadCountsByDate;

        $this->dispatch('leadsCalculated', ['leads' => $leadCountsByDate, 'scheduledLeads' => $scheduledLeadCountsByDate]);
    }

    private function calculateLeadCounts($leads)
    {
        $leadCountsByDate = [];
        foreach ($leads as $lead) {
            $date = Carbon::parse($lead->lead_time)->toDateString();
            if (!isset($leadCountsByDate[$date])) {
                $leadCountsByDate[$date] = [
                    "total" => 0,
                    "declined" => 0,
                    "no_declined" => 0
                ];
            }
            $leadCountsByDate[$date]["total"]++;
            if($lead->declined) {
                $leadCountsByDate[$date]["declined"]++;
            } else {
                $leadCountsByDate[$date]["no_declined"]++;
            }
        }
        return $leadCountsByDate;
    }

    private function calculateScheduledLeadCounts($scheduledLeads)
    {
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
        return $scheduledLeadCountsByDate;
    }

    public function render()
    {
        return view('livewire.calendar')->layout('layouts.app'); // Specify the correct layout here
    }
}
