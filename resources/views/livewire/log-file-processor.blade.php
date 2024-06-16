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
                Process Dirs
            </button>

            <button wire:click="processFiles" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Files
            </button>

            <button wire:click="processLeads" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Leads
            </button>

            

            <div wire:loading.remove>
                <p>Unprocessed files: {{ $unprocessedFilesCount }}</p>
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
