<x-filament-panels::page>
    <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium">Quarter</label>
            <select wire:model.live="quarter_entry"
                    class="fi-select-input rounded-lg border border-gray-300 text-sm px-3 py-1.5">
                <option value="1">1st</option>
                <option value="2">2nd</option>
            </select>
        </div>

        @if(config('system-features.midquarter'))
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium">Type</label>
            <select wire:model.live="quarter_type"
                    class="fi-select-input rounded-lg border border-gray-300 text-sm px-3 py-1.5">
                <option value="Quarter">Quarter</option>
                <option value="Midquarter">Midquarter</option>
            </select>
        </div>
        @endif
    </div>

    {{ $this->table }}
</x-filament-panels::page>