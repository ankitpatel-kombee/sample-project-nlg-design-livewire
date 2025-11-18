<?php

namespace App\Livewire\Brand;

use App\Models\Brand;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public $id;

    public $brand;

    public $event = 'showbrandInfoModal';

    #[On('show-brand-info')]
    public function show($id)
    {
        $this->brand = null;

        $this->brand = Brand::select(
            'brands.id',
            'brands.name',
            'brands.remark',
            'brands.bob',
            'brands.start_date',
            'brands.start_time'
        )

            ->where('brands.id', $id)

            ->first();

        if (! is_null($this->brand)) {
            $this->dispatch('show-modal', id: '#' . $this->event);
        } else {
            session()->flash('error', __('messages.brand.messages.record_not_found'));
        }
    }

    public function render()
    {
        return view('livewire.brand.show');
    }
}
