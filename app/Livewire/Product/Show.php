<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public $id;

    public $product;

    public $event = 'showproductInfoModal';

    #[On('show-product-info')]
    public function show($id)
    {
        $this->product = null;

        $this->product = Product::select(
            'products.id',
            'products.name',
            DB::raw(
                '(CASE
                                        WHEN products.status = "' . config('constants.product.status.key.active') . '" THEN  "' . config('constants.product.status.value.active') . '"
                                        WHEN products.status = "' . config('constants.product.status.key.inactive') . '" THEN  "' . config('constants.product.status.value.inactive') . '"
                                ELSE " "
                                END) AS status'
            )
        )

            ->where('products.id', $id)

            ->first();

        if (! is_null($this->product)) {
            $this->dispatch('show-modal', id: '#' . $this->event);
        } else {
            session()->flash('error', __('messages.product.messages.record_not_found'));
        }
    }

    public function render()
    {
        return view('livewire.product.show');
    }
}
