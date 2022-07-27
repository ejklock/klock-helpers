<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;

use Illuminate\Support\Str;

class ServiceGeneratorCommand extends GeneratorCommand
{

    protected $name = 'domain:service';

    protected $description = 'Create service  on App/Domain/<model>/Services';

    protected $type = 'service';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/service.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return "App\\Domains\\{$this->getCamelName()}\\Services";
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    public function handle()
    {
        parent::handle();
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name) . "Service";
        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
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
