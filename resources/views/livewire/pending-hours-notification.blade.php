<div class="inline-flex" wire:poll.10s="updatePendingCount">
    @if($pendingCount > 0)
        <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-500 transform translate-x-1/2 -translate-y-1/2"></span>
    @endif
</div>
