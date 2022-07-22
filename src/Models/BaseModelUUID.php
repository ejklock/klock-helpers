<?php

namespace KlockTecnologia\KlockHelpers\Models;

use Dyrynda\Database\Support\GeneratesUuid;

class BaseModelUUID
{
    use GeneratesUuid;

    public $timestamps = true;
    public $incrementing = false;
    protected $uuidFieldName = 'id';

    public function uuidColumn(): string
    {
        return 'id';
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'] ? 'Ativado' : 'Desativado';
    }
}
