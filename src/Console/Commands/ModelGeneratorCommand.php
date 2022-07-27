<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;

use Illuminate\Support\Str;

class ModelGeneratorCommand extends GeneratorCommand
{

    protected $name = 'domain:model';

    protected $description = 'Create dry Model on App/Domain/<model>';

    protected $type = 'model';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/model.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return "App\\Domains\\{$this->getCamelName()}\\Models";
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    public function handle()
    {
        parent::handle();
    }

    protected function getNameInput()
    {
        return $this->getCamelName();
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceClass($stub, $name)
    {
        $class = $this->getCamelName();

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }
}
