<?php

namespace KlockTecnologia\KlockHelpers\Models;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Model;

class BaseModelUUID extends Model
{
    use GeneratesUuid;

    public $timestamps = true;
    public $incrementing = false;
    protected $uuidFieldName = 'id';

    public function uuidColumn(): string
    {
        return 'id';
    }
}
