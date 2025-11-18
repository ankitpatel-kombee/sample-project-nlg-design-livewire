<div>
    <x-show-info-modal modalTitle="{{ __('messages.product.show.label_product') }}" :eventName="$event" :showSaveButton="false" :showCancelButton="false">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
        <flux:label>{{ __('messages.product.show.details.name') }}</flux:label>
        <flux:description>{{ $product?->name ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field>
        <flux:label>{{ __('messages.product.show.details.status') }}</flux:label>
        <flux:description>{{ $product?->status ?? '-' }}</flux:description>
    </flux:field>
            </div>
        </div>
    </x-show-info-modal>
</div>
