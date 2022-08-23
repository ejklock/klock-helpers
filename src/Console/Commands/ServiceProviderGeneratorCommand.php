<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;


class ServiceProviderGeneratorCommand extends GeneratorCommand
{
    protected $name = 'domain:service-provider';

    protected $description = 'Create service provider on App/Domain/<model>/Providers';

    protected $type = 'ServiceProvider';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/service-provider.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return "App\\Domains\\{$this->getCamelName()}\\Providers";
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    protected function getLowerCaseSingularName()
    {
        return Str::lower(Str::singular($this->argument('name')));
    }

    public function handle()
    {
        parent::handle();
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name) . "ServiceProvider";
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

        $replacements = str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);

        $replacements = str_replace(['dummyclass_singular', '{{ dummyclass_singular }}', '{{dummyclass_singular}}'], $this->getLowerCaseSingularName(), $replacements);

        return $replacements;
    }
}
