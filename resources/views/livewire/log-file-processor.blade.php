<div class="container mt-5">
    <div class="card">
        <div class="card-header">Process Files</div>
        <div class="card-body">
            <div wire:loading>
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
            </div>
            <div>
                <form wire:submit.prevent="scheduleLeads">
                    <div class="mb-3">
                        <label for="schedule_type" class="form-label">Schedule Type:</label>
                        <select id="schedule_type" class="form-control" wire:model.live="schedule_type">
                            <option value="date">Date</option>
                            <option value="leads_per_day">Leads per Day</option>
                        </select>
                    </div>

                    @if ($schedule_type == 'date')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date:</label>
                            <input type="text" id="start_date" class="form-control" wire:model="start_date" />
                            @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date:</label>
                            <input type="text" id="end_date" class="form-control" wire:model="end_date" />
                            @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endif

                    @if ($schedule_type == 'leads_per_day')
                    <div class="mb-3">
                        <label for="leads_per_day_input" class="form-label">Leads per Day:</label>
                        <input type="number" id="leads_per_day_input" class="form-control" wire:model="leads_per_day_input" />
                        @error('leads_per_day_input') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <h5 class="mb-3">Filter Leads by Lead Time (Optional)</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lead_time_start" class="form-label">Lead Time Start:</label>
                            <input type="text" id="lead_time_start" class="form-control" wire:model="lead_time_start" />
                            @error('lead_time_start') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lead_time_end" class="form-label">Lead Time End:</label>
                            <input type="text" id="lead_time_end" class="form-control" wire:model="lead_time_end" />
                            @error('lead_time_end') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="service" class="form-label">Service:</label>
                        <select id="service" class="form-control" wire:model="service">
                            <option value="">Select Service</option>
                            @foreach ($services as $serviceOption)
                            <option value="{{ $serviceOption }}">{{ $serviceOption }}</option>
                            @endforeach
                        </select>
                        @error('service') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" id="country" class="form-control" wire:model="country">
                        @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="limit" class="form-label">Limit:</label>
                        <input type="number" id="limit" class="form-control" wire:model="limit" value="0" />
                        @error('limit') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="declined_filter">Leads Type:</label>
                        <select id="declined_filter" class="form-control" wire:model="declined_filter">
                            <option value="all">All Leads</option>
                            <option value="ignore">NO Declined Leads</option>
                            <option value="only">ONLY Declined Leads</option>
                        </select>
                        @error('declined_filter') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <button type="button" wire:click="calculateLeads" class="btn btn-secondary">Calculate Leads</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="mt-4">
                <p>Leads found: {{ $leads_found }}</p>
                <p>Leads scheduled per day: {{ $leads_per_day }}</p>
                <p>Total days to schedule: {{ $total_days }}</p>
            </div>
        </div>
    </div>




</div>
