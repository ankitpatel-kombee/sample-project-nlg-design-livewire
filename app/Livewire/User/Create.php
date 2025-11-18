<?php

namespace App\Livewire\User;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $id;

    public $name;

    public $email;

    public $password;

    public $role_id;

    public $roles = [];

    public $description;

    public $dob;

    public $profile;

    public $profile_image;

    public $country_id;

    public $countries = [];

    public $state_id;

    public $states = [];

    public $city_id;

    public $cities = [];

    public $gender;

    public $products = [];

    public $product = [];

    public $status = 'Y';

    public $email_verified_at;

    public $remember_token;

    public $locale = 'en';

    public function mount()
    {
        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.user.breadcrumb.title'),
            'item_1' => '<a href="/user" class="text-muted text-hover-primary" wire:navigate>' . __('messages.user.breadcrumb.user') . '</a>',
            'item_2' => __('messages.user.breadcrumb.create'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->roles = Helper::getAllRole();
        $this->countries = Helper::getAllCountry();
        $this->states = Helper::getAllState();
        $this->cities = Helper::getAllCity();
        $this->products = \App\Models\Product::all();
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|max:200|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|min:6|max:191',
            'role_id' => 'required|exists:roles,id,deleted_at,NULL',
            'description' => 'required|max:500',
            'dob' => 'required|date_format:Y-m-d',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'state_id' => 'required|exists:states,id,deleted_at,NULL',
            'city_id' => 'required|exists:cities,id,deleted_at,NULL',
            'gender' => 'required|in:F,M',
            'product' => 'required|array',
            'product.*' => 'required|exists:products,id,deleted_at,NULL',
            'status' => 'required|in:Y,N',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.user.validation.messsage.name.required'),
            'name.max' => __('messages.user.validation.messsage.name.max'),
            'email.required' => __('messages.user.validation.messsage.email.required'),
            'email.max' => __('messages.user.validation.messsage.email.max'),
            'email.email' => __('messages.user.validation.messsage.email.email'),
            'password.required' => __('messages.user.validation.messsage.password.required'),
            'password.min' => __('messages.user.validation.messsage.password.min'),
            'password.max' => __('messages.user.validation.messsage.password.max'),
            'role_id.required' => __('messages.user.validation.messsage.role_id.required'),
            'description.required' => __('messages.user.validation.messsage.description.required'),
            'description.max' => __('messages.user.validation.messsage.description.max'),
            'dob.required' => __('messages.user.validation.messsage.dob.required'),
            'dob.date_format' => __('messages.user.validation.messsage.dob.date_format'),
            'profile_image.required' => __('messages.user.validation.messsage.profile_image.required'),
            'profile_image.max' => __('messages.user.validation.messsage.profile_image.max'),
            'country_id.required' => __('messages.user.validation.messsage.country_id.required'),
            'state_id.required' => __('messages.user.validation.messsage.state_id.required'),
            'city_id.required' => __('messages.user.validation.messsage.city_id.required'),
            'gender.required' => __('messages.user.validation.messsage.gender.required'),
            'gender.in' => __('messages.user.validation.messsage.gender.in'),
            'product.required' => __('messages.user.validation.messsage.product.required'),
            'status.required' => __('messages.user.validation.messsage.status.required'),
            'status.in' => __('messages.user.validation.messsage.status.in'),
        ];
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role_id' => $this->role_id,
            'description' => $this->description,
            'dob' => $this->dob,
            'profile' => $this->profile_image,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'status' => $this->status,
        ];
        $user = User::create($data);

        $user->products()->attach($this->product);

        if ($this->profile_image) {
            $realPath = 'user/' . $user->id . '/';
            $resizeImages = $user->resizeImages($this->profile_image, $realPath, true);
            $imagePath = $realPath . pathinfo($resizeImages['image'], PATHINFO_BASENAME);
            $user->update(['profile' => $imagePath]);
        }

        session()->flash('success', __('messages.user.messages.success'));

        return $this->redirect('/user', navigate: true); // redirect to user listing page
    }

    public function render()
    {
        return view('livewire.user.create')->title(__('messages.meta_title.create_user'));
    }

    public function updatedCountryId()
    {
        // When country_id is updated, load the dependent options
        $this->states = \App\Models\State::where('country_id', $this->country_id)->get();
    }

    public function updatedStateId()
    {
        // When state_id is updated, load the dependent options
        $this->cities = \App\Models\City::where('state_id', $this->state_id)->get();
    }
}
