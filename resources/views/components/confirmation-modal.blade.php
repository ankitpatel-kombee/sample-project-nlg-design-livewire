@props([
    'modalName' => 'confirmation-modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmEvent' => 'confirmed',
    'cancelEvent' => 'cancelled',
    'params' => [],
    'variant' => 'danger',
])

<flux:modal name="{{ $modalName }}" class="max-w-md" wire:model="showModal">
    <div class="space-y-6">
        <div class="flex items-center space-x-3">
            @if ($variant === 'danger')
                <div class="flex-shrink-0">
                    <flux:icon.exclamation-triangle class="w-8 h-8 text-red-600" />
                </div>
            @elseif($variant === 'warning')
                <div class="flex-shrink-0">
                    <flux:icon.exclamation-triangle class="w-8 h-8 text-yellow-600" />
                </div>
            @else
                <div class="flex-shrink-0">
                    <flux:icon.information-circle class="w-8 h-8 text-blue-600" />
                </div>
            @endif

            <div>
                <flux:heading size="lg">{{ $title }}</flux:heading>
            </div>
        </div>

        <flux:text variant="subtle">
            {{ $message }}
        </flux:text>

        <div class="flex justify-end space-x-3">
            <flux:button class="cursor-pointer" data-testid="cancel-button" variant="outline" wire:click="hideModal">
                {{ $cancelText }}
            </flux:button>

            <flux:button data-testid="delete-button" variant="{{ $variant }}" wire:click="{{ $confirmEvent }}" class="cursor-pointer">
                {{ $confirmText }}
            </flux:button>
        </div>
    </div>
</flux:modal>
