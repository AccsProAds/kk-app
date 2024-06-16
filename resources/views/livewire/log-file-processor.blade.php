<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Process Files
        </div>
        <div class="card-body">

            <button wire:click="processDirs" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Dirs
            </button>

            <button wire:click="processFiles" class="btn btn-primary mb-3" wire:loading.attr="disabled">
                Process Files
            </button>

            <div wire:loading>
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Processing...</span>
                </div>
            </div>

            <div wire:loading.remove>
                <p>Unprocessed files: {{ $unprocessedFilesCount }}</p>
                @if (!$processing && $unprocessedFilesCount === 0)
                    <div class="alert alert-success" role="alert">
                        All files have been processed.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
