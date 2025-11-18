<div>
    <x-show-info-modal modalTitle="{{ __('messages.brand.show.label_brand') }}" :eventName="$event" :showSaveButton="false" :showCancelButton="false">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
        <flux:label>{{ __('messages.brand.show.details.name') }}</flux:label>
        <flux:description>{{ $brand?->name ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field>
        <flux:label>{{ __('messages.brand.show.details.remark') }}</flux:label>
        <flux:description>{{ $brand?->remark ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field>
        <flux:label>{{ __('messages.brand.show.details.bob') }}</flux:label>
        <flux:description>{{ !is_null($brand) && !is_null($brand->bob)
            ? Carbon\Carbon::parse($brand->bob)->format(config('constants.default_datetime_format'))
            : '-' }}</flux:description>
    </flux:field>
                             <flux:field>
        <flux:label>{{ __('messages.brand.show.details.start_date') }}</flux:label>
        <flux:description>{{ !is_null($brand) && !is_null($brand->start_date)
            ? Carbon\Carbon::parse($brand->start_date)->format(config('constants.default_date_format'))
            : '-' }}</flux:description>
    </flux:field>
                             <flux:field>
        <flux:label>{{ __('messages.brand.show.details.start_time') }}</flux:label>
        <flux:description>{{ !is_null($brand) && !is_null($brand->start_time)
            ? Carbon\Carbon::parse($brand->start_time)->format(config('constants.default_time_format'))
            : '-' }}</flux:description>
    </flux:field>
            </div>
        </div>
    </x-show-info-modal>
</div>
