{{-- resources/views/filament/app/pages/grades-table-page.blade.php --}}
<x-filament-panels::page>
    <div class="flex items-center gap-3 mb-4">
        <label class="text-sm font-medium">Type</label>
        <select wire:model.live="quarter_type"
                class="fi-select-input rounded-lg border border-gray-300 text-sm px-3 py-1.5">
            <option value="Quarter">Quarter</option>
            <option value="Midquarter">Midquarter</option>
        </select>
    </div>

    {{ $this->table }}
</x-filament-panels::page>