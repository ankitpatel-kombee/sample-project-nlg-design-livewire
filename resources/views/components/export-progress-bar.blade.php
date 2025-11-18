<div id="progressBarDiv" class="hidden my-3 px-4 py-3 rounded-md shadow-sm bg-white dark:bg-gray-800 text-center border border-gray-200 dark:border-gray-700">
    <p id="waitingMessage" class="text-sm text-gray-700 dark:text-gray-300 mb-2"></p>

    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2 overflow-hidden">
        <div id="progressBar" class="h-2 bg-emerald-500 transition-all duration-300" style="width: 0%;"></div>
    </div>

    <div class="flex items-center justify-center space-x-2">
        <span id="progressText" class="text-sm font-medium text-gray-800 dark:text-gray-200">0%</span>

        <!-- Loading Spinner -->
        <svg id="progressSpinner" class="animate-spin h-5 w-5 text-black dark:text-white hidden"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>
</div>
