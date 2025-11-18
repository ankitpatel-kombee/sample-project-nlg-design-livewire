<?php

namespace App\Livewire\Brand;

use App\Livewire\Breadcrumb;
use App\Models\Brand;
use App\Models\BrandDetail;
use Livewire\Component;

use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $id;

    public $name;

    public $remark;

    public $bob;

    public $start_date;

    public $start_time;

    public $countries = [];

    public $states = [];

    public $cities = [];

    public $adds = [];

    public $newAdd = [
        'description' => '',
        'country_id' => '',
        'state_id' => '',
        'city_id' => '',
        'status' => '',
        'id' => 0,
    ];

    public $isEdit = false;

    public function mount()
    {
        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.brand.breadcrumb.title'),
            'item_1' => '<a href="/brand" class="text-muted text-hover-primary" wire:navigate>' . __('messages.brand.breadcrumb.brand') . '</a>',
            'item_2' => __('messages.brand.breadcrumb.create'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->countries = \App\Models\Country::all();
        $this->states = \App\Models\State::all();
        $this->cities = \App\Models\City::all();
        $this->adds[] = $this->newAdd;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|max:191',
            'remark' => 'nullable',
            'bob' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
        ];
        foreach ($this->adds as $index => $add) {
            $rules["adds.$index.description"] = 'required|max:500';
            $rules["adds.$index.country_id"] = 'required|exists:countries,id,deleted_at,NULL';
            $rules["adds.$index.state_id"] = 'required|exists:states,id,deleted_at,NULL';
            $rules["adds.$index.city_id"] = 'required|exists:cities,id,deleted_at,NULL';
            $rules["adds.$index.status"] = 'required|in:Y,N';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.brand.validation.messsage.name.required'),
            'name.max' => __('messages.brand.validation.messsage.name.max'),
            'bob.required' => __('messages.brand.validation.messsage.bob.required'),
            'start_date.required' => __('messages.brand.validation.messsage.start_date.required'),
            'start_time.required' => __('messages.brand.validation.messsage.start_time.required'),
        ];
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'remark' => $this->remark,
            'bob' => $this->bob,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time,
        ];
        $brand = Brand::create($data);

        foreach ($this->adds as $add) {
            $BrandDetailId = $add['id'] ?? 0;
            $BrandDetailInfo = BrandDetail::find($BrandDetailId);
            $BrandDetailData = [
                'description' => $add['description'],
                'country_id' => $add['country_id'],
                'state_id' => $add['state_id'],
                'city_id' => $add['city_id'],
                'status' => $add['status'],
                'brand_id' => $brand->id,
            ];
            if ($BrandDetailInfo) {
                BrandDetail::where('id', $BrandDetailId)->update($BrandDetailData);
            } else {
                $BrandDetailInfo = BrandDetail::create($BrandDetailData);
            }
        }

        session()->flash('success', __('messages.brand.messages.success'));

        return $this->redirect('/brand', navigate: true); // redirect to brand listing page
    }

    public function render()
    {
        return view('livewire.brand.create')->title(__('messages.meta_title.create_brand'));
    }

    public function add()
    {
        if (count($this->adds) < 5) {
            $this->adds[] = $this->newAdd;
        } else {
            $this->dispatch('alert', type: 'error', message: __('messages.maximum_record_limit_error'));
        }
    }

    public function remove($index, $id)
    {
        if ($id != 0) {
            BrandDetail::where('id', $id)->forceDelete();
        }
        unset($this->adds[$index]);
        $this->adds = array_values($this->adds);
    }
}
