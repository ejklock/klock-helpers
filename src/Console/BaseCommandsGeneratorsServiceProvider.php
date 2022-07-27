<?php

namespace KlockTecnologia\KlockHelpers\Console;

use Illuminate\Support\ServiceProvider;
use KlockTecnologia\KlockHelpers\Console\Commands\ControllerGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\DomainGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\ModelFromTableGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\ModelGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\ServiceGeneratorCommand;

class BaseCommandsGeneratorsServiceProvider extends ServiceProvider
{

    protected $commands = [
        DomainGeneratorCommand::class,
        ModelGeneratorCommand::class,
        ModelFromTableGeneratorCommand::class,
        ServiceGeneratorCommand::class,
        ControllerGeneratorCommand::class,
    ];

    /**
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
