<div class="col-lg-12">
    <div class="card-xl-stretch-1 mb-4">
        <div>
                <flux:field>
        <flux:label>{{ __('messages.role.show.details.name') }}</flux:label>
        <flux:description>{{ $role?->name ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field>
        <flux:label>{{ __('messages.role.show.details.status') }}</flux:label>
        <flux:description>{{ $role?->status ?? '-' }}</flux:description>
    </flux:field>
        </div>
    </div>
</div>