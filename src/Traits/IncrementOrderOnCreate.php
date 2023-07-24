<?php

namespace KlockTecnologia\KlockHelpers\Traits;

trait IncrementOrderOnCreate
{

    public static function bootIncrementOrderOnCreate()
    {
        static::creating(function ($model) {
            $maxOrder = $model::max('order');

            // Increment the order of the newly created model
            $model->order = $maxOrder + 1;
        });
    }
}
