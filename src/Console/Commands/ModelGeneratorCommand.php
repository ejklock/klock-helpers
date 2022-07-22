<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

class ModelGeneratorCommand extends BaseGeneratorCommand
{

    protected $name = 'domain:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model';
    protected function getStub()
    {
        return __DIR__ . '/../stubs/model.stub';
    }
}
