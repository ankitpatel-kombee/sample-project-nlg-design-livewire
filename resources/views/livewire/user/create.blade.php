<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <form wire:submit="store" class="space-y-8">
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="flex-1">
        <flux:field>
            <flux:label for="name" required>{{ __('messages.user.create.label_name') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="text" data-testid="name" id="name" wire:model="name" placeholder="Enter {{ __('messages.user.create.label_name') }}"/>
            <flux:error name="name" data-testid="name_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="email" required>{{ __('messages.user.create.label_email') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="email" data-testid="email" id="email" wire:model="email" placeholder="Enter {{ __('messages.user.create.label_email') }}"/>
            <flux:error name="email" data-testid="email_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="password" required>{{ __('messages.user.create.label_password') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="password" data-testid="password" id="password" wire:model="password" placeholder="Enter {{ __('messages.user.create.label_password') }}"/>
            <flux:error name="password" data-testid="password_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <x-flux.autocomplete
            name="role_id"
            data-testid="role_id"
            labeltext="{{ __('messages.user.create.label_roles') }}"
            placeholder="{{ __('messages.user.create.label_roles') }}"
            :options="$roles"
            displayOptions="10"
            wire:model="role_id"
           :required="true"
        />
        <flux:error name="role_id" data-testid="role_id_error" />
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="description" required>{{ __('messages.user.create.label_description') }}
                <span class="text-red-500">*</span>
            </flux:label>
            <x-flux.editor wireModel="description" data-testid="description" height="300px" toolbar="full" error="description" />
        </flux:field>
    </div>
                             <div class="flex-1">
        <x-flux.date-picker for="dob" wireModel="dob" label="{{ __('messages.user.create.label_dob') }}" :required="true"/>
    </div>
                             <div class="flex-1">
        <x-flux.file-upload
            data-testid="profile_image"
            model="profile_image"
            label="{{ __('messages.user.create.label_profile') }}"
            note="Extensions: jpeg, png, jpg, gif, svg | Size: Maximum 4096 KB"
            accept="image/*"
            :required="true"
            existingValue=""
        />
    </div>
                             <x-flux.single-select id="country" label="{{ __('messages.user.create.label_countries') }}" wire:model.live="country_id" data-testid="country_id" required>
        <option value='' >Select {{ __('messages.user.create.label_countries') }}</option>
   @if (!empty($countries))
       @foreach ($countries as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                             <x-flux.single-select id="state" label="{{ __('messages.user.create.label_states') }}" wire:model.live="state_id" data-testid="state_id" required>
        <option value='' >Select {{ __('messages.user.create.label_states') }}</option>
   @if (!empty($states))
       @foreach ($states as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                             <x-flux.single-select id="city_id" label="{{ __('messages.user.create.label_cities') }}" wire:model.live="city_id" data-testid="city_id" required>
        <option value='' >Select {{ __('messages.user.create.label_cities') }}</option>
   @if (!empty($cities))
       @foreach ($cities as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="gender" required>{{ __('messages.user.create.label_gender') }} <span class="text-red-500">*</span></flux:label>
            <div class="flex gap-6">
            <div class="flex items-center cursor-pointer">
                        <input data-testid="gender" type="radio" value="{{ config('constants.user.gender.key.female') }}" name="gender" wire:model="gender" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
    <label for="gender" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
        {{ config('constants.user.gender.value.female') }}
    </label>&nbsp;&nbsp;    <input data-testid="gender" type="radio" value="{{ config('constants.user.gender.key.male') }}" name="gender" wire:model="gender" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
    <label for="gender" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
        {{ config('constants.user.gender.value.male') }}
    </label>&nbsp;&nbsp;
                </div>
            </div>
            <flux:error name="gender" data-testid="gender_error"/>
        </flux:field>
    </div>
                             <x-flux.multi-select id="product" model="product" label="{{ __('messages.user.create.label_products') }}" required>
        @if ($products)  
@foreach ($products as $value) <label class="flex items-center px-3 py-2 hover:bg-black-50 cursor-pointer transition" @click="open = false">
                            <input type="checkbox" class="mr-2 h-4 w-4 text-black-600 border-gray-300 rounded
                        focus:ring-black-500 focus:ring-2 cursor-pointer" wire:model="product" value="{{ $value->id}}" ><span class="text-gray-700">{{$value->name}}</span></label>@endforeach 
@endif
    </x-flux.multi-select>
                             <div class="flex-1" x-data="{ status: @entangle('status') }">
        <flux:field>
            <flux:label for="status_switch">{{ __('messages.user.create.label_status') }}
                <span class="text-red-500">*</span></flux:label>
            <div class="flex items-center gap-3">
                <flux:switch
                    id="status_switch"
                    data-testid="status"
                    x-bind:checked="status === 'Y'"
                    x-on:change="$wire.set('status', $event.target.checked ? 'Y' : 'N')"
                    class="cursor-pointer"
                />
                <label for="status_switch"
                    class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer"
                    x-text="status === 'Y' ? 'Active' : 'Inactive'">
                </label>
            </div>
            <flux:error name="status" data-testid="status_error" />
        </flux:field>
    </div>
            </div>
        </div>

         

        <!-- Action Buttons -->
        <div class="flex items-center justify-top gap-3 mt-12">

            <flux:button type="submit" variant="primary" data-testid="submit_button" class="cursor-pointer" wire:loading.attr="disabled" wire:target="store">
                {{ __('messages.submit_button_text') }}
            </flux:button>

            <flux:button type="button" data-testid="cancel_button" class="cursor-pointer" variant="outline" href="/user" wire:navigate>
                {{ __('messages.cancel_button_text') }}
            </flux:button>
        </div>
    </form>
</div>
