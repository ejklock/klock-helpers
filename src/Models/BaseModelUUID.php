<?php

namespace KlockTecnologia\KlockHelpers\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BaseModelUUID extends Model
{
    use HasUuids;

    public $timestamps = true;
    public $incrementing = false;
    protected $uuidFieldName = 'id';

    public function uuidColumn(): string
    {
        return 'id';
    }
}
