<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <form wire:submit="store" class="space-y-8">
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="flex-1">
        <flux:field>
            <flux:label for="name" required>{{ __('messages.brand.create.label_name') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="text" data-testid="name" id="name" wire:model="name" placeholder="Enter {{ __('messages.brand.create.label_name') }}"/>
            <flux:error name="name" data-testid="name_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="remark" >{{ __('messages.brand.create.label_remark') }} </flux:label>
            <flux:input type="text" data-testid="remark" id="remark" wire:model="remark" placeholder="Enter {{ __('messages.brand.create.label_remark') }}"/>
            <flux:error name="remark" data-testid="remark_error"/>
        </flux:field>
    </div>
                         <div class="flex-1">
    <x-flux.date-time-picker wireModel='bob'
    for="bob"
    label="{{ __('messages.brand.create.label_bob') }}"
    :required="true"
    />
</div>
                             <div class="flex-1">
        <x-flux.date-picker for="start_date" wireModel="start_date" label="{{ __('messages.brand.create.label_start_date') }}" :required="true"/>
    </div>
                             <div class="flex-1">
        <x-flux.time-picker wireModel="start_time"
        for="start_time"
        label="{{ __('messages.brand.create.label_start_time') }}"
        :required="true"
        />
    </div>
            </div>
        </div>

         <div class="bg-white flex flex-row justify-between dark:bg-gray-800 shadow rounded-xl p-6 space-y-4">
    <h3 class="font-bold text-lg mb-4">Add New Entries</h3>
    <flux:button icon:trailing="plus" wire:click.prevent="add" variant="primary" data-testid="plus_button" class="cursor-pointer"/>
</div>
<div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 space-y-4">
      @if (!empty($adds))
        <div class="space-y-4">
            @foreach ($adds as $index => $add)
                @php
                    $hasError = $errors->getBag('default')->keys()
                        ? collect($errors->getBag('default')->keys())->contains(
                            fn($key) => Str::startsWith($key, "adds.$index"),
                        )
                        : false;

                    $showAccordion = $isEdit || $hasError || $index === 0;
                @endphp
                <div x-data="{ open: {{ $showAccordion ? 'true' : 'false' }} }" class="border rounded shadow-sm">
                    <!-- Accordion Header -->
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex cursor-pointer justify-between items-center w-full px-4 py-2 font-semibold text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 rounded-t hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <span>Add New {{ $index + 1 }}</span>
                        <span class="flex items-center gap-2">
                            @if ($index > 0)
                            <flux:icon.trash variant="solid" data-testid="remove_{{ $add['id'] }}" wire:click.prevent="remove({{ $index }}, {{ $add['id'] ?? 0 }})" class="w-5 h-5" />
                            @endif
                            <!-- Chevron Icon with rotation -->
                            <flux:icon.chevron-down :class="{ 'rotate-180': open }" class="transition-transform duration-200" />
                        </span>
                    </button>
                    <!-- Accordion Body -->
                    <div x-show="open" x-transition class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 gap-4">
                            <input type="hidden" name="add_id[]" value="{{ $add['id'] }}">
                                <div class="flex-1">
        <flux:field>
            <flux:label for="description_{{$index}}" required>{{ __('messages.brand.create.label_description') }} <span class="text-red-500">*</span></flux:label>
            <flux:textarea rows="3" wire:model="adds.{{$index}}.description" id="description_{{$index}}" data-testid="adds.{{$index}}.description"  placeholder="Enter {{ __('messages.brand.create.label_description') }}" />
            <flux:error name="adds.{{$index}}.description" data-testid="adds.{{$index}}.description_error"/>
        </flux:field>
    </div>
                      <x-flux.single-select id="country_id_{{$index}}" label="{{ __('messages.brand.create.label_countries') }}" wire:model="adds.{{$index}}.country_id" data-testid="adds.{{$index}}.country_id" required>
        <option value='' >Select {{ __('messages.brand.create.label_countries') }}</option>
   @if (!empty($countries))
       @foreach ($countries as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                      <x-flux.single-select id="state_id_{{$index}}" label="{{ __('messages.brand.create.label_states') }}" wire:model="adds.{{$index}}.state_id" data-testid="adds.{{$index}}.state_id" required>
        <option value='' >Select {{ __('messages.brand.create.label_states') }}</option>
   @if (!empty($states))
       @foreach ($states as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                      <x-flux.single-select id="city_id_{{$index}}" label="{{ __('messages.brand.create.label_cities') }}" wire:model="adds.{{$index}}.city_id" data-testid="adds.{{$index}}.city_id" required>
        <option value='' >Select {{ __('messages.brand.create.label_cities') }}</option>
   @if (!empty($cities))
       @foreach ($cities as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                     <div class="flex-1">
        <flux:field>
            <flux:label for="status_{{$index}}" required>{{ __('messages.brand.create.label_status') }} <span class="text-red-500">*</span></flux:label>
            <div class="flex gap-6">
            <div class="flex items-center cursor-pointer">
                        <input data-testid="adds.{{$index}}.status" type="radio" value="{{ config('constants.brand.status.key.active') }}"  name="status_{{$index}}" wire:model="adds.{{$index}}.status" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
    <label for="status_{{$index}}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
        {{ config('constants.brand.status.value.active') }}
    </label>&nbsp;&nbsp;    <input data-testid="adds.{{$index}}.status" type="radio" value="{{ config('constants.brand.status.key.inactive') }}"  name="status_{{$index}}" wire:model="adds.{{$index}}.status" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
    <label for="status_{{$index}}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
        {{ config('constants.brand.status.value.inactive') }}
    </label>&nbsp;&nbsp;
                </div>
            </div>
            <flux:error name="adds.{{$index}}.status" data-testid="adds.{{$index}}.status_error"/>
        </flux:field>
    </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-top gap-3 mt-12">

            <flux:button type="submit" variant="primary" data-testid="submit_button" class="cursor-pointer" wire:loading.attr="disabled" wire:target="store">
                {{ __('messages.update_button_text') }}
            </flux:button>

            <flux:button type="button" data-testid="cancel_button" class="cursor-pointer" variant="outline" href="/brand" wire:navigate>
                {{ __('messages.cancel_button_text') }}
            </flux:button>
        </div>
    </form>
</div>
