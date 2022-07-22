<?php

namespace KlockTecnologia\KlockHelpers;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KlockTecnologia\KlockHelpers\Skeleton\SkeletonClass
 */
class KlockHelpersFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'klock-helpers';
    }
}
