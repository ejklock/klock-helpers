<?php

namespace KlockTecnologia\KlockHelpers\Console;

use Illuminate\Support\ServiceProvider;

use KlockTecnologia\KlockHelpers\Console\Commands\ModelFromTableGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\ModelGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\DomainMakerCommand;

class BaseCommandsGeneratorsServiceProvider extends ServiceProvider
{

    protected $commands = [
        DomainMakerCommand::class,
        ModelGeneratorCommand::class,
        ModelFromTableGeneratorCommand::class
    ];

    /**
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
