<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CreatedbyUpdatedby
{
    public static function bootCreatedbyUpdatedby()
    {
        static::creating(function ($model) {
            if ($user = Auth::user()) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });

        static::updating(function ($model) {
            if ($user = Auth::user()) {
                $model->updated_by = $user->id;
            }
        });
    }
}
