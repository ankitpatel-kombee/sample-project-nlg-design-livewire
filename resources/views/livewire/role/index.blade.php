
<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Session Messages -->
    <x-session-message></x-session-message>
    <x-export-progress-bar></x-export-progress-bar>
    <!-- PowerGrid Table with integrated header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
        <livewire:role.table />
    </div>
    <!-- Delete Component -->
    <livewire:role.delete />
    <!-- Show Component -->
    <x-slide-panel />
    <livewire:common-code />
</div>
