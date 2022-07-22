<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

class ModelGeneratorCommand extends BaseGeneratorCommand
{

    protected function getStub()
    {
        return __DIR__ . '/../stubs/model.stub';
    }
}
