<?php

namespace App\Domains\Dummy\Services;

use App\Domains\Dummy\Models\Dummy;
use Illuminate\Database\Eloquent\Model;
use KlockTecnologia\KlockHelpers\Services\AbstractModelService;

class DummyService extends AbstractModelService
{

    public function model(): Model
    {

        return new Dummy();
    }
}
