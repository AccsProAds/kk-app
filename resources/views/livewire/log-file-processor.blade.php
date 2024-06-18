<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Process Files
        </div>
        <div class="card-body">
            
            <div wire:loading>
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                </div>
            </div>

            <button wire:click="processDirs" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Directories & Files
            </button>

            <button wire:click="processFiles" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Files > Leads
            </button>

            <button wire:click="processLeads" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Leads > DB
            </button>
            <br>

            <div>
            <div>
            <form wire:submit.prevent="scheduleLeads">
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="text" id="start_date" class="form-control" wire:model="start_date" />
                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
               
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="text" id="end_date" class="form-control" wire:model="end_date" />
                    @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>


                <div class="mb-3">
                    <label for="service">Service</label>
                    <select id="service" class="form-control" wire:model="service">
                        <option value="">Select Service</option>
                        @foreach ($services as $serviceOption)
                            <option value="{{ $serviceOption }}">{{ $serviceOption }}</option>
                        @endforeach
                    </select>
                    @error('service') <span class="text-danger">{{ $message }}</span> @enderror
              
                </div>

                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" class="form-control" wire:model="country">
                    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="limit" class="form-label">Limit:</label>
                    <input type="number" id="limit" class="form-control" wire:model="limit" value="0" />
                    @error('limit') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Add your existing fields here -->

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
         </div>



        </div>
    </div>
</div>
