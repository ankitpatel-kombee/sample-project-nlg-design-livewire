<div wire:loading.flex
    class="absolute inset-0 z-[90] w-full h-full items-center justify-center
           bg-gradient-to-br from-white/70 via-white/60 to-white/80
           dark:from-zinc-900/70 dark:via-zinc-900/60 dark:to-zinc-900/80
           backdrop-blur-md transition-all duration-300">

    <div class="flex flex-col items-center space-y-5">
        {{-- Animated Pulse Spinner --}}
        <div class="relative">
            <div class="absolute inset-0 rounded-full border-4 border-blue-500/20 animate-ping"></div>
            <svg class="w-14 h-14 text-blue-600 dark:text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373
                    0 0 5.373 0 12h4zm2 5.291A7.962
                    7.962 0 014 12H0c0 3.042 1.135
                    5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <p class="text-base font-semibold text-gray-700 dark:text-gray-200 tracking-wide">
            Loading data, please wait...
        </p>
    </div>
</div>

{{-- Skeleton Placeholder (visible during defer load) --}}
<div wire:loading.class="opacity-50" class="p-4 space-y-6 animate-pulse">
    {{-- Table Header Skeleton --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="h-8 w-32 rounded-lg bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-8 w-24 rounded-lg bg-gray-200 dark:bg-zinc-700"></div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="h-8 w-20 rounded-lg bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-8 w-20 rounded-lg bg-gray-200 dark:bg-zinc-700"></div>
        </div>
    </div>

    {{-- Table Rows Skeleton --}}
    @for ($row = 0; $row < 8; $row++)
        <div class="flex items-center space-x-4 py-3 border-b border-gray-100 dark:border-zinc-800">
            <div class="h-4 w-8 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-32 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-40 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-20 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-16 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-24 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-28 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-20 rounded bg-gray-200 dark:bg-zinc-700"></div>
            <div class="flex space-x-2 ml-auto">
                <div class="h-6 w-6 rounded bg-gray-200 dark:bg-zinc-700"></div>
                <div class="h-6 w-6 rounded bg-gray-200 dark:bg-zinc-700"></div>
                <div class="h-6 w-6 rounded bg-gray-200 dark:bg-zinc-700"></div>
            </div>
        </div>
    @endfor
</div>
