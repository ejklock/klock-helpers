<?php

namespace KlockTecnologia\KlockHelpers\Console;

use Illuminate\Support\ServiceProvider;
use KlockTecnologia\KlockHelpers\Console\Commands\DomainGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\ModelFromTableGeneratorCommand;
use KlockTecnologia\KlockHelpers\Console\Commands\ModelGeneratorCommand;


class BaseCommandsGeneratorsServiceProvider extends ServiceProvider
{

    protected $commands = [
        DomainGeneratorCommand::class,
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
